<?php

namespace App\Http\Controllers\Medico;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Paciente;
use App\Models\Prontuario;
use App\Models\Consulta;
use App\Models\Medico;
use Illuminate\Support\Facades\Auth;
use App\Models\Unidade;
use App\Models\Medicamento;
use App\Models\Exame;
use App\Models\AnotacaoEnfermagem;
use Illuminate\Support\Facades\DB;

class MedicoProntuarioController extends Controller
{
    public function index()
    {
        $medico = Medico::where('id_usuarioFK', Auth::user()->idUsuarioPK)->first();
        if (!$medico) {
            return redirect()->route('medico.login')->with('error', 'Médico não encontrado.');
        }

        $unidadeMedico = $medico->unidades()->first();

        $consultas_na_fila = Consulta::where('status_atendimento', 'AGUARDANDO_CONSULTA')
            ->with('paciente')
            ->orderByRaw("
                CASE
                    WHEN classificacao_risco = 'vermelho' THEN 1
                    WHEN classificacao_risco = 'laranja' THEN 2
                    WHEN classificacao_risco = 'amarelo' THEN 3
                    WHEN classificacao_risco = 'verde' THEN 4
                    WHEN classificacao_risco = 'azul' THEN 5
                    ELSE 6
                END
            ")
            ->orderBy('dataConsulta', 'asc')
            ->get();

        $consultas_finalizadas = Consulta::where('status_atendimento', 'FINALIZADO')
            ->with('paciente')
            ->orderBy('dataConsulta', 'desc')
            ->limit(30)
            ->get();

        $pacientes_historico = Paciente::orderBy('nomePaciente', 'asc')->get();

        return view('medico.prontuarioMedico', [
            'consultas_na_fila' => $consultas_na_fila,
            'consultas_finalizadas' => $consultas_finalizadas,
            'pacientes' => $pacientes_historico,
            'medico' => $medico,
            'unidadeMedico' => $unidadeMedico
        ]);
    }

 public function show($id)
{
    $paciente = Paciente::findOrFail($id);
    $prontuario = Prontuario::where('idPacienteFK', $paciente->idPaciente)->first();

    if (!$prontuario) {
        return redirect()
            ->route('medico.prontuario')
            ->with('error', 'Este paciente ainda não possui prontuário.');
    }

    $consultas = Consulta::where('idProntuarioFK', $prontuario->idProntuarioPK)
        ->whereNotNull('idMedicoFK')
        ->with(['exames', 'medicamentos']) 
        ->orderBy('dataConsulta', 'desc')
        ->get();

    $anotacoesEnfermagem = AnotacaoEnfermagem::where('idPacienteFK', $paciente->idPaciente)
        ->orderBy('data_hora', 'desc')
        ->get();

    return view('medico.visualizarProntuario', compact('paciente', 'prontuario', 'consultas', 'anotacoesEnfermagem'));
}

    public function create($id)
    {
        $paciente = Paciente::findOrFail($id);

        if (!$paciente->statusPaciente) {
            return redirect()->route('medico.prontuario')->with('error', 'Paciente inativo.');
        }

        $medico = Auth::user()->medico;
        if (!$medico) {
            return redirect()->route('medico.prontuario')->with('error', 'Médico não encontrado.');
        }

        $unidadeMedico = $medico->unidades()->first();

        Prontuario::firstOrCreate(
            ['idPacienteFK' => $paciente->idPaciente],
            ['dataAbertura' => now()->toDateString()]
        );

        return view('medico.cadastrarProntuario', [
            'paciente' => $paciente,
            'medico' => $medico,
            'unidadeMedico' => $unidadeMedico,
            'consulta' => null,
            'anotacoesEnfermagem' => null
        ]);
    }

    public function store(Request $request, $id)
    {
        $validated = $request->validate([
            'dataConsulta' => 'required|date',
            'observacoes' => 'nullable|string|max:2000',
            'descExame' => 'nullable|string|max:2000',
            'exames_solicitados' => 'nullable|array',
            'exames_solicitados.*' => 'string|max:255',
            'exame_tipos' => 'nullable|array',
            'medicamentos_prescritos' => 'nullable|array',
            'medicamentos_prescritos.*' => 'string|max:255',
            'medicamento_tipos' => 'nullable|array',
            'medicamento_dosagens' => 'nullable|array',
            'medicamento_frequencias' => 'nullable|array',
            'medicamento_periodos' => 'nullable|array',
        ]);

        $paciente = Paciente::findOrFail($id);
        $medico = Auth::user()->medico;

        if (!$medico) {
            return redirect()->route('medico.prontuario')->with('error', 'Médico não encontrado.');
        }

        $prontuario = Prontuario::firstOrCreate(
            ['idPacienteFK' => $paciente->idPaciente],
            ['dataAbertura' => now()->toDateString()]
        );

        $consulta = new Consulta();
        $consulta->idProntuarioFK = $prontuario->idProntuarioPK;
        $consulta->idMedicoFK = $medico->idMedicoPK;
        $consulta->nomeMedico = $medico->nomeMedico;
        $consulta->crmMedico = $medico->crmMedico;
        $consulta->dataConsulta = $validated['dataConsulta'];
        $consulta->observacoes = $validated['observacoes'] ?? null;
        $consulta->status_atendimento = 'FINALIZADO';
        $consulta->idPacienteFK = $paciente->idPaciente;

        DB::transaction(function () use ($consulta, $paciente, $prontuario, $request, $validated) {
            $consulta->save();

            // Salvar MEDICAMENTOS com detalhes nas tabelas corretas
            $medicamentos_prescritos = $validated['medicamentos_prescritos'] ?? [];
            $medicamento_tipos = $request->input('medicamento_tipos', []);
            $medicamento_dosagens = $request->input('medicamento_dosagens', []);
            $medicamento_frequencias = $request->input('medicamento_frequencias', []);
            $medicamento_periodos = $request->input('medicamento_periodos', []);

            if (!empty($medicamentos_prescritos)) {
                foreach ($medicamentos_prescritos as $medicamento) {
                    Medicamento::create([
                        'idConsultaFK' => $consulta->idConsultaPK,
                        'idProntuarioFK' => $prontuario->idProntuarioPK,
                        'idPacienteFK' => $paciente->idPaciente,
                        'descMedicamento' => $medicamento,
                        'nomeMedicamento' => $medicamento,
                        'tipoMedicamento' => $medicamento_tipos[$medicamento] ?? null,
                        'dosagemMedicamento' => $medicamento_dosagens[$medicamento] ?? null,
                        'frequenciaMedicamento' => $medicamento_frequencias[$medicamento] ?? null,
                        'periodoMedicamento' => $medicamento_periodos[$medicamento] ?? null,
                    ]);
                }
            }

            // Salvar EXAMES com detalhes nas tabelas corretas
            $exames_solicitados = $validated['exames_solicitados'] ?? [];
            $exame_tipos = $request->input('exame_tipos', []);

            if (!empty($exames_solicitados)) {
                foreach ($exames_solicitados as $exame) {
                    Exame::updateOrCreate(
                        [
                            'idConsultaFK' => $consulta->idConsultaPK,
                            'descExame' => $validated['descExame'] ?? null,
                            'dataExame' => $consulta->dataConsulta ?? now(),
                        ],
                        [
                            'idProntuarioFK' => $prontuario->idProntuarioPK ?? $consulta->idProntuarioFK,
                            'idPacienteFK' => $paciente->idPaciente ?? $consulta->idPacienteFK,
                            'nomeExame' => $exame,
                            'statusExame' => 'SOLICITADO',
                            'tipoExame' => $exame_tipos[$exame] ?? null,
                        ]
                    );
                }
            }
        });

        return redirect()
            ->route('medico.visualizarProntuario', $id)
            ->with('success', 'Consulta registrada com sucesso!');
    }

    public function edit($idConsulta)
    {
        $consulta = Consulta::with(['paciente', 'prontuario'])->findOrFail($idConsulta);
        $paciente = $consulta->paciente;
        $prontuario = $consulta->prontuario;
        $medico = Auth::user()->medico;

        $unidadeMedico = $medico->unidades()->first();

        if (!$medico) {
            return redirect()->route('medico.prontuario')->with('error', 'Médico não encontrado.');
        }

        $anotacoesEnfermagem = AnotacaoEnfermagem::where('idPacienteFK', $paciente->idPaciente)
            ->where('created_at', '>=', $consulta->created_at->subDay())
            ->orderBy('data_hora', 'desc')
            ->get();

        if (!$prontuario && $paciente) {
            $prontuario = Prontuario::firstOrCreate(
                ['idPacienteFK' => $paciente->idPaciente],
                ['dataAbertura' => now()->toDateString()]
            );
            $consulta->idProntuarioFK = $prontuario->idProntuarioPK;
            $consulta->save();
        }

        return view('medico.cadastrarProntuario', compact('consulta', 'paciente', 'medico', 'unidadeMedico', 'anotacoesEnfermagem'));
    }

    public function update(Request $request, $idConsulta)
    {
        $consulta = Consulta::findOrFail($idConsulta);
        $medico = Auth::user()->medico;

        if (!$medico) {
            return redirect()->route('medico.prontuario')->with('error', 'Médico não encontrado.');
        }

        $validated = $request->validate([
            'dataConsulta' => 'required|date',
            'observacoes' => 'nullable|string|max:2000',
            'descExame' => 'nullable|string|max:2000',
            'exames_solicitados' => 'nullable|array',
            'exames_solicitados.*' => 'string|max:255',
            'exame_tipos' => 'nullable|array',
            'medicamentos_prescritos' => 'nullable|array',
            'medicamentos_prescritos.*' => 'string|max:255',
            'medicamento_tipos' => 'nullable|array',
            'medicamento_dosagens' => 'nullable|array',
            'medicamento_frequencias' => 'nullable|array',
            'medicamento_periodos' => 'nullable|array',
        ]);

        $consulta->idMedicoFK = $medico->idMedicoPK;
        $consulta->nomeMedico = $medico->nomeMedico;
        $consulta->crmMedico = $medico->crmMedico;
        $consulta->status_atendimento = 'FINALIZADO';
        $consulta->dataConsulta = $validated['dataConsulta'];
        $consulta->observacoes = $validated['observacoes'] ?? null;

        if (!$consulta->idProntuarioFK && $consulta->paciente) {
            $prontuario = Prontuario::firstOrCreate(
                ['idPacienteFK' => $consulta->paciente->idPaciente],
                ['dataAbertura' => now()->toDateString()]
            );
            $consulta->idProntuarioFK = $prontuario->idProntuarioPK;
        }

        DB::transaction(function () use ($consulta, $request, $validated) {
            $consulta->save();

            // Apagar registros antigos para evitar duplicatas
            Medicamento::where('idConsultaFK', $consulta->idConsultaPK)->delete();
            Exame::where('idConsultaFK', $consulta->idConsultaPK)->delete();

            // Salvar MEDICAMENTOS com detalhes nas tabelas corretas
            $medicamentos_prescritos = $request->input('medicamentos_prescritos', []);
            $medicamento_tipos = $request->input('medicamento_tipos', []);
            $medicamento_dosagens = $request->input('medicamento_dosagens', []);
            $medicamento_frequencias = $request->input('medicamento_frequencias', []);
            $medicamento_periodos = $request->input('medicamento_periodos', []);

            if (!empty($medicamentos_prescritos)) {
                foreach ($medicamentos_prescritos as $medicamento) {
                    Medicamento::create([
                        'idConsultaFK' => $consulta->idConsultaPK,
                        'idProntuarioFK' => $consulta->idProntuarioFK,
                        'idPacienteFK' => $consulta->idPacienteFK,
                        'descMedicamento' => $medicamento,
                        'nomeMedicamento' => $medicamento,
                        'tipoMedicamento' => $medicamento_tipos[$medicamento] ?? null,
                        'dosagemMedicamento' => $medicamento_dosagens[$medicamento] ?? null,
                        'frequenciaMedicamento' => $medicamento_frequencias[$medicamento] ?? null,
                        'periodoMedicamento' => $medicamento_periodos[$medicamento] ?? null,
                    ]);
                }
            }

            // Salvar EXAMES com detalhes nas tabelas corretas
            $exames_solicitados = $request->input('exames_solicitados', []);
            $exame_tipos = $request->input('exame_tipos', []);

            if (!empty($exames_solicitados)) {
                foreach ($exames_solicitados as $exame) {
                    Exame::create([
                        'idConsultaFK' => $consulta->idConsultaPK,
                        'idProntuarioFK' => $consulta->idProntuarioFK,
                        'idPacienteFK' => $consulta->idPacienteFK,
                        'nomeExame' => $exame,
                        'descExame' => $validated['descExame'] ?? null,
                        'dataExame' => $consulta->dataConsulta ?? now(),
                        'statusExame' => 'SOLICITADO',
                        'tipoExame' => $exame_tipos[$exame] ?? null,
                    ]);
                }
            }
        });

        return redirect()
            ->route('medico.prontuario')
            ->with('success', 'Atendimento finalizado com sucesso!');
    }


    public function destroy($idConsulta)
    {
        $consulta = Consulta::findOrFail($idConsulta);
        $medico = Auth::user()->medico;
        if (!$medico) {
            return redirect()->route('medico.prontuario')->with('error', 'Médico não encontrado.');
        }

        if ($consulta->idMedicoFK !== $medico->idMedicoPK) {
            return redirect()
                ->route('medico.prontuario')
                ->with('error', 'Você não tem permissão para excluir esta consulta.');
        }

        $pacienteId = null;
        if ($consulta->prontuario) {
            $pacienteId = $consulta->prontuario->idPacienteFK;
        } elseif ($consulta->paciente) {
            $pacienteId = $consulta->paciente->idPaciente;
        }

        $consulta->delete();

        if ($pacienteId) {
            return redirect()
                ->route('medico.visualizarProntuario', $pacienteId)
                ->with('success', 'Consulta excluída com sucesso!');
        }

        return redirect()
            ->route('medico.prontuario')
            ->with('success', 'Consulta excluída com sucesso!');
    }
}
