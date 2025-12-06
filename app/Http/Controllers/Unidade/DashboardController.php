<?php

namespace App\Http\Controllers\Unidade;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Medico;
use App\Models\Enfermeiro;
use App\Models\Recepcionista;
use App\Models\Unidade;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $unidade = Auth::guard('unidade')->user();

        if (!$unidade) {
            return redirect()->route('unidade.login')->with('error', 'Sessão expirada. Faça login novamente.');
        }

        $nomeUnidade = $unidade->nomeUnidade ?? 'Unidade';
        $unidadeId = $unidade->idUnidadePK;
        $periodoMeses = 6; 

        $medicosCount = $unidade->medicos()->count();
        $nursesCount = $unidade->enfermeiros()->count();
        $recepcionistasCount = $unidade->recepcionistas()->count();

        $medicosPorEspecialidade = DB::table('tbMedico')
            ->join('tbMedicoUnidade', 'tbMedico.idMedicoPK', '=', 'tbMedicoUnidade.idMedicoFK')
            ->where('tbMedicoUnidade.idUnidadeFK', $unidadeId)
            ->select('tbMedico.especialidadeMedico', DB::raw('COUNT(*) as total'))
            ->groupBy('tbMedico.especialidadeMedico')
            ->orderBy('total', 'desc')
            ->get();

        $mesesData = collect();
        for ($i = $periodoMeses - 1; $i >= 0; $i--) {
            $mes = Carbon::now()->subMonths($i);
            $mes->locale('pt_BR');
            $mesesData->put($mes->format('Y-m'), $mes->shortMonthName);
        }

        $consultasMensais = DB::table('tbConsulta') 
            ->where('idUnidadeFK', $unidadeId)
            ->where('status_atendimento', 'FINALIZADO')
            ->where('dataConsulta', '>=', Carbon::now()->subMonths($periodoMeses)->startOfMonth()) 
            ->select(
                DB::raw("DATE_FORMAT(dataConsulta, '%Y-%m') as mes_ano"), 
                DB::raw('COUNT(*) as total_consultas') 
            )
            ->groupBy('mes_ano')
            ->get()
            ->keyBy('mes_ano');

        $consultasMensal = [ 
            'meses' => $mesesData->values()->toArray(), 
            'totais' => [], 
        ];

        foreach ($mesesData->keys() as $chaveMes) {
            $consultasMensal['totais'][] = $consultasMensais->get($chaveMes)->total_consultas ?? 0;
        } 

        $generoMedicos = $unidade->medicos()
            ->select('genero', DB::raw('COUNT(*) as total'))
            ->groupBy('genero')
            ->pluck('total', 'genero')
            ->toArray();

        $generoEnfermeiros = $unidade->enfermeiros()
            ->select('genero', DB::raw('COUNT(*) as total'))
            ->groupBy('genero')
            ->pluck('total', 'genero')
            ->toArray();

        $generoRecepcionistas = $unidade->recepcionistas()
            ->select('genero', DB::raw('COUNT(*) as total'))
            ->groupBy('genero')
            ->pluck('total', 'genero')
            ->toArray();

        $totalHomens = ($generoMedicos['Masculino'] ?? 0) + 
                    ($generoEnfermeiros['Masculino'] ?? 0) + 
                    ($generoRecepcionistas['Masculino'] ?? 0);

        $totalMulheres = ($generoMedicos['Feminino'] ?? 0) + 
                        ($generoEnfermeiros['Feminino'] ?? 0) + 
                        ($generoRecepcionistas['Feminino'] ?? 0);
           
        $totaloutros = ($generoMedicos['Outro'] ?? 0) + 
                        ($generoEnfermeiros['Outro'] ?? 0) + 
                        ($generoRecepcionistas['Outro'] ?? 0);                  

        $dadosGenero = [
            'Homens' => $totalHomens,
            'Mulheres' => $totalMulheres,
            'Outros' => $totaloutros
        ];

        $medicosDaUnidade = $unidade->medicos()->get(['idMedicoPK', 'nomeMedico']);

        $medicosData = [];
        if ($medicosDaUnidade->isNotEmpty()) {
            $dataInicial = Carbon::now()->startOfMonth()->toDateString(); 
            $dataFinal = Carbon::now()->endOfMonth()->toDateString();   

            $consultasPorMedicoQuery = DB::table('tbConsulta')
                ->where('idUnidadeFK', $unidadeId)
                ->where('status_atendimento', 'FINALIZADO') 
                ->whereBetween('dataConsulta', [$dataInicial, $dataFinal])
                ->select('idMedicoFK', DB::raw('COUNT(*) as total_consultas'))
                ->groupBy('idMedicoFK')
                ->get()
                ->keyBy('idMedicoFK');

            foreach ($medicosDaUnidade as $medico) {
                $consultas = $consultasPorMedicoQuery->get($medico->idMedicoPK)->total_consultas ?? 0;
                $diasUteisNoMes = 22; 
                $media = $diasUteisNoMes > 0 ? round($consultas / $diasUteisNoMes, 1) : 0;
                
                $medicosData[] = [
                    'nome' => $medico->nomeMedico,
                    'media' => $media,
                    'totalConsultas' => $consultas
                ];
            }
        }
        
        $consultasPorMedico = collect($medicosData)->sortByDesc('media')->values()->toArray();

        // ============================================================
        // GRÁFICO 1: Produtividade por Especialidade
        // Mostra quantas consultas cada especialidade realizou
        // ============================================================
        $produtividadeEspecialidade = [];
        foreach ($medicosPorEspecialidade as $especialidade) {
            $consultasDaEspecialidade = DB::table('tbConsulta')
                ->join('tbMedico', 'tbConsulta.idMedicoFK', '=', 'tbMedico.idMedicoPK')
                ->where('tbConsulta.idUnidadeFK', $unidadeId)
                ->where('tbConsulta.status_atendimento', 'FINALIZADO')
                ->where('tbMedico.especialidadeMedico', $especialidade->especialidadeMedico)
                ->whereBetween('tbConsulta.dataConsulta', [
                    Carbon::now()->startOfMonth()->toDateString(),
                    Carbon::now()->endOfMonth()->toDateString()
                ])
                ->count();

            $produtividadeEspecialidade[] = [
                'especialidade' => $especialidade->especialidadeMedico,
                'consultas' => $consultasDaEspecialidade,
                'medicos' => $especialidade->total,
                'mediaPorMedico' => $especialidade->total > 0 ? round($consultasDaEspecialidade / $especialidade->total, 1) : 0
            ];
        }

        // ============================================================
        // GRÁFICO 2: Distribuição da Equipe por Categoria
        // Mostra composição do time: Médicos, Enfermeiros, Recepcionistas
        // ============================================================
        $composicaoEquipe = [
            'categorias' => ['Médicos', 'Enfermeiros', 'Recepcionistas'],
            'totais' => [$medicosCount, $nursesCount, $recepcionistasCount],
            'percentuais' => []
        ];

        $totalProfissionais = $medicosCount + $nursesCount + $recepcionistasCount;
        if ($totalProfissionais > 0) {
            $composicaoEquipe['percentuais'] = [
                round(($medicosCount / $totalProfissionais) * 100, 1),
                round(($nursesCount / $totalProfissionais) * 100, 1),
                round(($recepcionistasCount / $totalProfissionais) * 100, 1)
            ];
        }

        return view('unidade.dashboardUnidade', compact(
            'nomeUnidade',
            'medicosCount',
            'nursesCount',
            'recepcionistasCount', 
            'medicosPorEspecialidade',
            'consultasMensal', 
            'dadosGenero',
            'consultasPorMedico',
            'produtividadeEspecialidade',
            'composicaoEquipe'
        ));
    }
}