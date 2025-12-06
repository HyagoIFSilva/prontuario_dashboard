<?php

namespace App\Http\Controllers\Enfermeiro;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Enfermeiro;
use App\Models\Paciente;
use App\Models\Prontuario; 
use App\Models\Consulta;
use App\Models\AnotacaoEnfermagem;
use App\Models\Alergia;
use Carbon\Carbon;

class DashboardController extends Controller
{
   public function index()
{
    // 1. Autenticação e Busca do Enfermeiro
    $usuario = Auth::guard('enfermeiro')->user();

    if (!$usuario) {
        return redirect()->route('enfermeiro.login')->with('error', 'Faça login para acessar o dashboard.');
    }

    $enfermeiro = Enfermeiro::where('id_usuario', $usuario->idUsuarioPK)->first();

    if (!$enfermeiro) {
        return redirect()->route('enfermeiro.login')->with('error', 'Não foi possível carregar os dados do enfermeiro.');
    }

    // 2. Busca da Unidade de Atuação (Filtro Chave)
    $unidadeEnfermeiro = $enfermeiro->unidades()->first();
    $unidadeId = $unidadeEnfermeiro ? $unidadeEnfermeiro->idUnidadePK : null;
    $unidadeNome = $unidadeEnfermeiro->nomeUnidade ?? 'N/A';
    $hoje = Carbon::today();

    // 3. 📊 Lógica das Métricas (Filtradas por Enfermeiro/Unidade)
    
    // 3.1. Triagens Concluídas Hoje
    $atendimentosDia = 0;
    if ($enfermeiro->idEnfermeiroPK) {
        $atendimentosDia = AnotacaoEnfermagem::where('idEnfermeiroFK', $enfermeiro->idEnfermeiroPK)
            ->whereDate('data_hora', $hoje)
            ->count();
    }

    // 3.2. Pacientes Próprios
    $pacientesProprios = AnotacaoEnfermagem::where('idEnfermeiroFK', $enfermeiro->idEnfermeiroPK)
        ->distinct('idPacienteFK')
        ->count('idPacienteFK');

    // 3.3. Agendamentos Hoje
    $agendamentosHoje = 0;
    if ($unidadeId) {
        $agendamentosHoje = Consulta::where('idUnidadeFK', $unidadeId)
            ->whereDate('created_at', $hoje)
            ->where('status_atendimento', 'AGUARDANDO_TRIAGEM')
            ->count();
    }
    
    // 4. 📈 Dados para os Gráficos
    
     // Gráfico de Triagens por Mês
    $dadosTriagensMes = $this->getTriagensPorMes($enfermeiro->idEnfermeiroPK);
    
    // SUBSTITUIR: Gráfico de Tipos de Alergia (Barras)
    $dadosTiposAlergia = $this->getTiposAlergiaPorPeriodo(null);

    // Gráfico de Classificações de Risco
    $dadosClassificacoesRisco = $this->getClassificacoesRiscoPorMes($enfermeiro->idEnfermeiroPK);

    // 5. Retorna a view
    return view('enfermeiro.dashboardEnfermeiro', [
        'enfermeiro' => $enfermeiro,
        'nomeEnfermeiro' => $enfermeiro->nomeEnfermeiro,
        'unidadeAtuacao' => $unidadeNome,
        
        'atendimentosDia' => $atendimentosDia,
        'pacientesProprios' => $pacientesProprios,
        'agendamentosHoje' => $agendamentosHoje,
        
        'dadosTriagensMes' => $dadosTriagensMes,
        'dadosTiposAlergia' => $dadosTiposAlergia, 
        'dadosClassificacoesRisco' => $dadosClassificacoesRisco,
    ]);
}

    /**
     * Busca o número de triagens realizadas pelo enfermeiro logado nos últimos 6 meses.
     * @param int $enfermeiroId O PK do enfermeiro logado
     * @return array
     */
    private function getTriagensPorMes(int $enfermeiroId)
    {
        $mesesParaRotulo = [];
        $meses = [];
        $hoje = Carbon::now();
        
        for ($i = 5; $i >= 0; $i--) {
            $data = $hoje->copy()->subMonths($i);
            $mesNome = $data->locale('pt_BR')->isoFormat('MMM'); 
            $mesesParaRotulo[$data->format('Y-m')] = ucfirst($mesNome); 
            $meses[$data->format('Y-m')] = 0;
        }

        // 2. Consulta ao banco
        $dataInicial = $hoje->copy()->subMonths(5)->startOfMonth();

        $resultados = AnotacaoEnfermagem::select(
                DB::raw('COUNT(*) as total'),
                DB::raw('DATE_FORMAT(data_hora, "%Y-%m") as ano_mes')
            )
            ->where('idEnfermeiroFK', $enfermeiroId)
            ->where('data_hora', '>=', $dataInicial)
            ->groupBy('ano_mes')
            ->orderBy('ano_mes', 'asc')
            ->get();
        
        // 3. Mapeia os resultados
        foreach ($resultados as $resultado) {
            $meses[$resultado->ano_mes] = $resultado->total;
        }

        // 4. Retorna no formato [labels, data]
        return [
            'labels' => array_values($mesesParaRotulo),
            'data' => array_values($meses)
        ];
    }

    /**
     * Busca as alergias mais registradas nos últimos 6 meses
     * @param int|null $enfermeiroId O PK do enfermeiro (null = todas as alergias)
     * @return array
     */
   private function getTiposAlergiaPorPeriodo($enfermeiroId = null)
{
    $hoje = Carbon::now();
    $dataInicial = $hoje->copy()->subMonths(5)->startOfMonth();
    
    // 1. Busca alergias registradas nos últimos 6 meses
    $query = Alergia::select(
            'tipoAlergia',
            DB::raw('COUNT(*) as total')
        )
        ->where('created_at', '>=', $dataInicial)
        ->whereNotNull('tipoAlergia')
        ->where('tipoAlergia', '!=', '');

    // Filtrar por enfermeiro se necessário
    if ($enfermeiroId) {
        $query->whereHas('paciente.anotacoesEnfermagem', function($q) use ($enfermeiroId) {
            $q->where('idEnfermeiroFK', $enfermeiroId);
        });
    }

    $resultados = $query->groupBy('tipoAlergia')
        ->orderBy('total', 'desc')
        ->get();

    // 2. Prepara os dados para o gráfico
    $labels = [];
    $dados = [];
    $cores = [];

    // 3. Configuração de cores para cada tipo
    $coresTipos = [
        'Alimentar' => 'rgba(46, 125, 50, 0.1)',    
        'Medicamentosa' => 'rgba(46, 125, 50, 0.1)',  
        'Ambiental' => 'rgba(46, 125, 50, 0.1)',      
        'Contato' => 'rgba(46, 125, 50, 0.1)',      
        'Respiratória' => 'rgba(46, 125, 50, 0.1)',  
        'Cutânea' => 'rgba(46, 125, 50, 0.1)',       
        'Ocular' => 'rgba(46, 125, 50, 0.1)',         
        'Outra' => 'rgba(46, 125, 50, 0.1)',        
    ];

    foreach ($resultados as $resultado) {
        $tipo = $resultado->tipoAlergia;
        $labels[] = $tipo;
        $dados[] = $resultado->total;
        $cores[] = $coresTipos[$tipo] ?? 'rgba(100, 100, 100, 0.8)';
    }

    return [
        'labels' => $labels,
        'data' => $dados,
        'colors' => $cores
    ];
}

   
    private function getClassificacoesRiscoPorMes($enfermeiroId = null)
    {
    $hoje = Carbon::now();
    $dataInicial = $hoje->copy()->subMonths(5)->startOfMonth();
    
    // 1. Prepara os rótulos dos meses
    $mesesParaRotulo = [];
    $mesesChave = [];
    
    for ($i = 5; $i >= 0; $i--) {
        $data = $hoje->copy()->subMonths($i);
        $mesNome = $data->locale('pt_BR')->isoFormat('MMM');
        $mesesParaRotulo[] = ucfirst($mesNome);
        $mesesChave[] = $data->format('Y-m');
    }

    // 2. Busca classificações registradas nos últimos 6 meses
    $query = Consulta::select(
            'classificacao_risco',
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as ano_mes'),
            DB::raw('COUNT(*) as total')
        )
        ->where('created_at', '>=', $dataInicial)
        ->whereNotNull('classificacao_risco')
        ->where('classificacao_risco', '!=', '');

    // Filtrar por enfermeiro se necessário
    if ($enfermeiroId) {
        $query->where('idEnfermeiroFK', $enfermeiroId);
    }

    $resultados = $query->groupBy('classificacao_risco', 'ano_mes')
        ->orderBy('ano_mes', 'asc')
        ->get();

    // 3. Define as classificações na ordem correta
    $classificacoes = ['vermelho', 'laranja', 'amarelo', 'verde', 'azul'];
    
    // Inicializa arrays
    $classificacoesPorMes = [];
    foreach ($classificacoes as $class) {
        $classificacoesPorMes[$class] = array_fill_keys($mesesChave, 0);
    }

    // 4. Preenche com os dados do banco
    foreach ($resultados as $resultado) {
        $classificacao = strtolower($resultado->classificacao_risco);
        $anoMes = $resultado->ano_mes;
        $total = $resultado->total;

        if (in_array($classificacao, $classificacoes)) {
            $classificacoesPorMes[$classificacao][$anoMes] = $total;
        }
    }

    // 5. Prepara os datasets para Chart.js
    $datasets = [];
    $configClassificacoes = [
        'vermelho' => [
            'label' => 'Vermelho (Emergência)',
            'borderColor' => 'rgba(211, 47, 47, 1)',
            'backgroundColor' => 'rgba(211, 47, 47, 0.1)',
        ],
        'laranja' => [
            'label' => 'Laranja (Muito Urgente)',
            'borderColor' => 'rgba(255, 152, 0, 1)',
            'backgroundColor' => 'rgba(255, 152, 0, 0.1)',
        ],
        'amarelo' => [
            'label' => 'Amarelo (Urgente)',
            'borderColor' => 'rgba(255, 193, 7, 1)',
            'backgroundColor' => 'rgba(255, 193, 7, 0.1)',
        ],
        'verde' => [
            'label' => 'Verde (Pouco Urgente)',
            'borderColor' => 'rgba(46, 125, 50, 1)',
            'backgroundColor' => 'rgba(46, 125, 50, 0.1)',
        ],
        'azul' => [
            'label' => 'Azul (Não Urgente)',
            'borderColor' => 'rgba(33, 150, 243, 1)',
            'backgroundColor' => 'rgba(33, 150, 243, 0.1)',
        ],
    ];

    foreach ($classificacoes as $classificacao) {
        $dados = array_values($classificacoesPorMes[$classificacao]);
        $config = $configClassificacoes[$classificacao];

        $datasets[] = [
            'label' => $config['label'],
            'data' => $dados,
            'borderColor' => $config['borderColor'],
            'backgroundColor' => 'transparent',
            'tension' => 0.4,
            'borderWidth' => 3,
            'pointRadius' => 5,
            'pointHoverRadius' => 7,
            'pointBackgroundColor' => $config['borderColor'],
            'pointBorderColor' => '#fff',
            'pointBorderWidth' => 2,
        ];
    }

    return [
        'labels' => $mesesParaRotulo,
        'datasets' => $datasets
    ];
}

}