<?php

namespace App\Http\Controllers\Medico;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Medico;
use App\Models\Paciente;
use App\Models\Prontuario;
use App\Models\Consulta;
use App\Models\Exame;
use Illuminate\Support\Facades\DB; 
use Carbon\Carbon;

class MedicoDashboardController extends Controller
{
    public function index()
    {
        $usuario = Auth::user();

        if (!$usuario) {
            return redirect()->route('medico.login')->with('error', 'Faça login para acessar o dashboard.');
        }

        $medico = Medico::where('id_usuarioFK', $usuario->idUsuarioPK)->first();

        if (!$medico) {
            return redirect()->route('medico.login')->with('error', 'Médico não encontrado.');
        }

        $medicoId = $medico->idMedicoPK;

        $patientsCount = Paciente::where('statusPaciente', 1)->count();
        $prontuariosCount = Prontuario::count();
        $totalExamsCount = Exame::count();

        $consultasEmAberto = Consulta::where('idMedicoFK', $medicoId)
            ->whereIn('status_atendimento', ['aguardando', 'triagem', 'em_atendimento'])
            ->with(['paciente', 'unidade'])
            ->orderBy('dataConsulta', 'asc')
            ->take(5) 
            ->get();

        $totalConsultasEmAberto = Consulta::where('idMedicoFK', $medicoId)
            ->whereIn('status_atendimento', ['aguardando', 'triagem', 'em_atendimento'])
            ->count();


            $atendimentosPorMes = Consulta::select(
                DB::raw('MONTH(dataConsulta) as mes'),
                DB::raw('COUNT(*) as total')
            )
            ->where('idMedicoFK', $medicoId)
            ->whereYear('dataConsulta', Carbon::now()->year)
            ->groupBy('mes')
            ->orderBy('mes')
            ->get()
            ->mapWithKeys(function ($item) {
                Carbon::setLocale('pt_BR');
                return [ucfirst(Carbon::create()->month($item->mes)->translatedFormat('M')) => $item->total];
            });
        

            $evolucaoAtendimentos = Consulta::select(
                DB::raw('YEAR(dataConsulta) as ano'),
                DB::raw('MONTH(dataConsulta) as mes'),
                DB::raw('COUNT(*) as total')
            )
            ->where('idMedicoFK', $medicoId)
            ->where('dataConsulta', '>=', Carbon::now()->subYear())
            ->groupBy('ano', 'mes')
            ->orderBy('ano')
            ->orderBy('mes')
            ->get()
            ->map(function ($item) {
                Carbon::setLocale('pt_BR');
                return [
                    'label' => Carbon::create($item->ano, $item->mes)->format('M/Y'),
                    'total' => $item->total,
                ];
            });

     
        $atendimentosPorRisco = Consulta::select(
                'classificacao_risco',
                DB::raw('COUNT(*) as total')
            )
            ->where('idMedicoFK', $medicoId)
            ->whereNotNull('classificacao_risco')
            ->groupBy('classificacao_risco')
            ->get()
            ->map(function ($item) {
                $labels = [
                    'vermelho' => 'Emergência',
                    'laranja' => 'Urgência',
                    'amarelo' => 'Pouco Urgente',
                    'verde' => 'Não Urgente',
                    'azul' => 'Eletivo'
                ];
                $item->label = $labels[$item->classificacao_risco] ?? 'Sem Classificação';
                return $item;
            });


        $tiposAtendimento = Consulta::select(
                'pacientes.statusPaciente as label',
                DB::raw('COUNT(tbConsulta.idConsultaPK) as total')
            )
            ->join('tbPaciente as pacientes', 'tbConsulta.idPacienteFK', '=', 'pacientes.idPaciente')
            ->where('tbConsulta.idMedicoFK', $medicoId) 
            ->groupBy('pacientes.statusPaciente')
            ->get()
            ->map(function ($item) {
                $item->label = ($item->label == 1) ? 'Pacientes Ativos' : 'Pacientes Inativos';
                return $item;
            });


            $mediaDiaria = Consulta::where('idMedicoFK', $medicoId)
            ->where('dataConsulta', '>=', Carbon::now()->subDays(30))
            ->count() / 30;

      
            $consultasHoje = Consulta::where('idMedicoFK', $medicoId)
            ->whereDate('dataConsulta', Carbon::today())
            ->count();

        return view('medico.dashboardMedico', [

            'nome' => $medico->nomeMedico, 
            'crm' => $medico->crmMedico,
            

            'patientsCount' => $patientsCount,
            'prontuariosCount' => $prontuariosCount,
            'totalExamsCount' => $totalExamsCount,
            

            'consultasEmAberto' => $consultasEmAberto,
            'totalConsultasEmAberto' => $totalConsultasEmAberto,
            'consultasHoje' => $consultasHoje,
            'mediaDiaria' => round($mediaDiaria, 1),
            

            'atendimentosPorMes' => $atendimentosPorMes,
            'evolucaoAtendimentos' => $evolucaoAtendimentos,
            'tiposAtendimento' => $tiposAtendimento,
            'atendimentosPorRisco' => $atendimentosPorRisco,
        ]);
    }
}