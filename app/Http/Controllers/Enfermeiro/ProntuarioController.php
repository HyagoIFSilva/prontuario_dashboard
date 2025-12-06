<?php

namespace App\Http\Controllers\Enfermeiro;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Paciente;
use App\Models\Unidade;
use App\Models\AnotacaoEnfermagem;
use App\Models\Enfermeiro;
use App\Models\Consulta;
use App\Models\Alergia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProntuarioController extends Controller
{
    public function index()
    {
        $enfermeiro = Enfermeiro::where('id_usuario', Auth::id())->first();
        if (!$enfermeiro) {
             return redirect()->route('enfermeiro.login')->with('error', 'Enfermeiro não encontrado.');
        }

        // Busca a unidade do enfermeiro
        $unidadeEnfermeiro = $enfermeiro->unidades()->first();

        // Pacientes AGUARDANDO TRIAGEM (lógica existente)
        $pacientes_na_fila = Paciente::whereHas('consultas', function ($query) {
            $query->where('status_atendimento', 'AGUARDANDO_TRIAGEM');
        })
        ->orderBy('nomePaciente', 'asc')
        ->get();

        // --- CORREÇÃO: Busca pacientes ATENDIDOS pelo enfermeiro logado ---
        $pacientes_atendidos = $this->getPacientesAtendidos($enfermeiro->idEnfermeiroPK);

        return view('enfermeiro.prontuarioEnfermeiro', compact('pacientes_na_fila', 'pacientes_atendidos', 'enfermeiro', 'unidadeEnfermeiro'));
    }

    /**
     * Busca pacientes que foram atendidos (triados) pelo enfermeiro logado
     */
    private function getPacientesAtendidos($idEnfermeiro)
    {
        // CORREÇÃO: Busca pacientes únicos que foram triados pelo enfermeiro logado
        // e estão aguardando consulta ou já foram finalizados
        $pacientes_ids = Consulta::where(function($query) use ($idEnfermeiro) {
                $query->where('status_atendimento', 'AGUARDANDO_CONSULTA')
                      ->orWhere('status_atendimento', 'FINALIZADO');
            })
            ->where('idEnfermeiroFK', $idEnfermeiro) // Apenas pacientes triados por este enfermeiro
            ->whereNotNull('idEnfermeiroFK') // Garante que a triagem foi feita
            ->pluck('idPacienteFK')
            ->unique()
            ->values();

        // Busca os dados desses pacientes
        return Paciente::whereIn('idPaciente', $pacientes_ids)
            ->with(['anotacoesEnfermagem' => function($query) {
                $query->orderBy('data_hora', 'desc');
            }])
            ->orderBy('nomePaciente', 'asc')
            ->get();
    }

    public function create($pacienteId)
    {
        $paciente = Paciente::findOrFail($pacienteId);
        $unidades = Unidade::orderBy('nomeUnidade')->get();

        $consulta = Consulta::where('idPacienteFK', $pacienteId)
                            ->where('status_atendimento', 'AGUARDANDO_TRIAGEM')
                            ->orderBy('dataConsulta', 'desc')
                            ->firstOrFail(); 

        $enfermeiro = Enfermeiro::where('id_usuario', Auth::id())->firstOrFail();
        if (!$enfermeiro) {
             return redirect()->route('enfermeiro.prontuario')->with('error', 'Enfermeiro não encontrado.');
        }

        // Obter a primeira unidade associada a este enfermeiro
        $unidadeEnfermeiro = $enfermeiro->unidades()->first();

        return view('enfermeiro.cadastrarProntuarioEnfermeiro', compact('paciente', 'unidades', 'consulta', 'enfermeiro', 'unidadeEnfermeiro'));
    }

    public function store(Request $request, $pacienteId)
    {
        $validatedData = $request->validate([
            'idConsulta' => 'required|exists:tbConsulta,idConsultaPK',
            'classificacao_risco' => 'required|string',
            'tipo_registro' => 'required|string',
            'data_hora' => 'required|date',
            'descricao' => 'required|string',
            'unidade_atendimento' => 'required|exists:tbUnidade,idUnidadePK',
            'pressao_arterial' => 'nullable|string',
            'temperatura' => 'nullable|string',
            'frequencia_cardiaca' => 'nullable|string',
            'frequencia_respiratoria' => 'nullable|string',
            'saturacao' => 'nullable|string',
            'dor' => 'nullable|integer|min:0|max:10',
            
            'alergias' => 'nullable|array', 
            'alergias.*' => 'nullable|string',
            'alergia_tipos' => 'nullable|array',
            'alergia_severidades' => 'nullable|array',
            'medicacoes_ministradas' => 'nullable|array', 
            'medicamentos_ministradas.*' => 'nullable|string',
        ]);

        $enfermeiro = Enfermeiro::where('id_usuario', Auth::id())->firstOrFail();
        
        $alergias_str = !empty($validatedData['alergias']) ? implode(', ', $validatedData['alergias']) : null;
        $medicacoes_str = !empty($validatedData['medicacoes_ministradas']) ? implode(', ', $validatedData['medicacoes_ministradas']) : null;
        
        $anotacao = new AnotacaoEnfermagem();
        $anotacao->idPacienteFK = $pacienteId;
        $anotacao->idEnfermeiroFK = $enfermeiro->idEnfermeiroPK;
        $anotacao->tipo_registro = $validatedData['tipo_registro'];
        $anotacao->data_hora = $validatedData['data_hora'];
        $anotacao->descricao = $validatedData['descricao'];
        $anotacao->unidade_atendimento = $validatedData['unidade_atendimento'];
        $anotacao->pressao_arterial = $validatedData['pressao_arterial'];
        $anotacao->temperatura = $validatedData['temperatura'];
        $anotacao->frequencia_cardiaca = $validatedData['frequencia_cardiaca'];
        $anotacao->frequencia_respiratoria = $validatedData['frequencia_respiratoria'];
        $anotacao->saturacao = $validatedData['saturacao'] ? str_replace('%', '', $validatedData['saturacao']) : null;
        $anotacao->dor = $validatedData['dor'];
        $anotacao->alergias = $alergias_str;
        $anotacao->medicacoes_ministradas = $medicacoes_str;
        
        // Persistência atômica da anotação, atualização da consulta e inserção de alergias
        DB::transaction(function () use ($anotacao, $validatedData, $enfermeiro, $request) {
            $anotacao->save();

            // Atualiza a consulta
            $consulta = Consulta::findOrFail($validatedData['idConsulta']);
            $consulta->status_atendimento = 'AGUARDANDO_CONSULTA';
            $consulta->classificacao_risco = $validatedData['classificacao_risco'];
            $consulta->idEnfermeiroFK = $enfermeiro->idEnfermeiroPK;
            
            if (!$consulta->idUnidadeFK && !empty($validatedData['unidade_atendimento'])) {
                $consulta->idUnidadeFK = $validatedData['unidade_atendimento'];
            }
            $consulta->save();

            // Inserir alergias do paciente com base nas anotações, se informado
            if (!empty($validatedData['alergias'])) {
                $alergia_tipos = $request->input('alergia_tipos', []);
                $alergia_severidades = $request->input('alergia_severidades', []);
                
                foreach ($validatedData['alergias'] as $alergia) {
                    $tipo = $alergia_tipos[$alergia] ?? 'Não especificado';
                    $severidade = $alergia_severidades[$alergia] ?? 'Não especificado';
                    
                    Alergia::updateOrCreate(
                        [
                            'idPacienteFK' => $anotacao->idPacienteFK,
                            'descAlergia' => $alergia, 
                        ],
                        [
                            'tipoAlergia' => $tipo,
                            'severidadeAlergia' => $severidade,
                        ]
                    );
                }
            }
        });
    
        return redirect()->route('enfermeiro.prontuario')
            ->with('success', 'Triagem realizada! Paciente encaminhado para consulta.');
    }

    public function show($pacienteId)
    {
        $paciente = Paciente::findOrFail($pacienteId);

        $anotacoes = AnotacaoEnfermagem::where('idPacienteFK', $pacienteId)
            ->with('unidadeAtendimento') // Carrega o relacionamento definido no Model
            ->orderBy('data_hora', 'desc')
            ->get();

        return view('enfermeiro.visualizarProntuarioEnfermeiro', compact('paciente', 'anotacoes'));
    }
}