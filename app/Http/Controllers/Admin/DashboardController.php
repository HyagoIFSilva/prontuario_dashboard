<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Medico;
use App\Models\Paciente;
use App\Models\Enfermeiro;
use App\Models\Unidade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $admin = Auth::guard('admin')->user();
        $nomeAdmin = $admin->nomeAdmin ?? 'Administrador';
        
        // 📊 CONTADORES PRINCIPAIS
        $medicosCount = Medico::count();
        $patientsCount = Paciente::count();
        $nursesCount = Enfermeiro::count();
        $unidadesCount = Unidade::count();

        // 📊 MÉDICOS POR ESPECIALIDADE (Top 10)
        DB::statement("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
        $medicosPorEspecialidade = DB::table('tbMedico')
            ->select('especialidadeMedico', DB::raw('count(*) as total'))
            ->whereNotNull('especialidadeMedico')
            ->where(DB::raw("TRIM(especialidadeMedico)"), '!=', '')
            ->groupBy('especialidadeMedico')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();
        
        // 📊 COMPARAÇÃO DE PROFISSIONAIS
        $comparacaoProfissionais = [
            'categorias' => ['Médicos', 'Enfermeiros', 'Pacientes'],
            'valores' => [$medicosCount, $nursesCount, $patientsCount]
        ];

        // 📊 DISTRIBUIÇÃO DE GÊNERO
        $totalPacientes = Paciente::count();
        $homens = Paciente::where('generoPaciente', 'Masculino')->count();
        $mulheres = Paciente::where('generoPaciente', 'Feminino')->count();
        
        $dadosGenero = [
            'Homens' => $homens,
            'Mulheres' => $mulheres,
            'percentualHomens' => $totalPacientes > 0 ? round(($homens / $totalPacientes) * 100, 1) : 0,
            'percentualMulheres' => $totalPacientes > 0 ? round(($mulheres / $totalPacientes) * 100, 1) : 0,
        ];

        // 📊 FAIXA ETÁRIA DOS PACIENTES (GRÁFICO NOVO 1)
        $faixasEtarias = [
            '0-17 anos' => Paciente::whereRaw("TIMESTAMPDIFF(YEAR, dataNascPaciente, CURDATE()) BETWEEN 0 AND 17")->count(),
            '18-29 anos' => Paciente::whereRaw("TIMESTAMPDIFF(YEAR, dataNascPaciente, CURDATE()) BETWEEN 18 AND 29")->count(),
            '30-44 anos' => Paciente::whereRaw("TIMESTAMPDIFF(YEAR, dataNascPaciente, CURDATE()) BETWEEN 30 AND 44")->count(),
            '45-59 anos' => Paciente::whereRaw("TIMESTAMPDIFF(YEAR, dataNascPaciente, CURDATE()) BETWEEN 45 AND 59")->count(),
            '60+ anos' => Paciente::whereRaw("TIMESTAMPDIFF(YEAR, dataNascPaciente, CURDATE()) >= 60")->count(),
        ];
        
        $idososCount = $faixasEtarias['60+ anos'];
        $percentualIdosos = $totalPacientes > 0 ? round(($idososCount / $totalPacientes) * 100) : 0;

        // 📊 UNIDADES POR REGIÃO DO BRASIL
        $regioesBrasil = [
            'Norte' => ['AC', 'AP', 'AM', 'PA', 'RO', 'RR', 'TO'],
            'Nordeste' => ['AL', 'BA', 'CE', 'MA', 'PB', 'PE', 'PI', 'RN', 'SE'],
            'Centro-Oeste' => ['DF', 'GO', 'MT', 'MS'],
            'Sudeste' => ['ES', 'MG', 'RJ', 'SP'],
            'Sul' => ['PR', 'RS', 'SC']
        ];
        
        $unidadesPorRegiao = [];
        foreach ($regioesBrasil as $regiao => $ufs) {
            $unidadesPorRegiao[$regiao] = Unidade::whereIn('ufUnidade', $ufs)->count();
        }

        // 📊 TOP 5 ESTADOS COM MAIS UNIDADES (GRÁFICO NOVO 2)
        $unidadesPorEstado = DB::table('tbUnidade')
            ->select('ufUnidade', DB::raw('count(*) as total'))
            ->whereNotNull('ufUnidade')
            ->groupBy('ufUnidade')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        // 📊 MÉDIA DE IDADE DOS PACIENTES
        $mediaIdadePacientes = Paciente::selectRaw('AVG(TIMESTAMPDIFF(YEAR, dataNascPaciente, CURDATE())) as media')
            ->value('media');
        $mediaIdadePacientes = $mediaIdadePacientes ? round($mediaIdadePacientes, 1) : 0;

        // 📊 DISTRIBUIÇÃO GÊNERO DOS MÉDICOS
        $medicosPorGenero = [
            'Masculino' => Medico::where('genero', 'Masculino')->count(),
            'Feminino' => Medico::where('genero', 'Feminino')->count(),
        ];

        return view('admin.dashboard', compact(
            'nomeAdmin',
            'medicosCount',
            'patientsCount',
            'nursesCount',
            'unidadesCount',
            'medicosPorEspecialidade',
            'comparacaoProfissionais',
            'dadosGenero',
            'faixasEtarias',
            'percentualIdosos',
            'unidadesPorRegiao',
            'unidadesPorEstado',
            'mediaIdadePacientes',
            'medicosPorGenero'
        ));
    }
}