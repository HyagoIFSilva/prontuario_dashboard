<?php

namespace App\Http\Controllers\Recepcionista;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Consulta;
use App\Models\Unidade;

class RecepcionistaDashboardController extends Controller
{

    public function index()
    {
        return view('recepcionista.dashboardRecepcionista');
    }

    public function store(Request $request)
    {
        $dadosValidados = $request->validate([
            'paciente_id' => 'required|exists:tbPaciente,idPaciente',
            'queixa_principal' => 'required|string|min:10',
        ]);

        $consulta = new Consulta(); 
        
        $consulta->idPacienteFK = $dadosValidados['paciente_id'];
        $consulta->queixa_principal = $dadosValidados['queixa_principal'];
        
        $unidadePadraoId = Auth::user()->idUnidadeFK ?? 1; 

        $consulta->idUnidadeFK = $unidadePadraoId;
        
        $unidade = Unidade::find($unidadePadraoId);
        $consulta->unidade = $unidade?->nomeUnidade ?? 'Unidade Padrão Fixa';
        
        
        $consulta->idRecepcionistaFK = Auth::guard('recepcionista')->id(); 
        $consulta->dataConsulta = now();
        $consulta->status_atendimento = 'AGUARDANDO_TRIAGEM';
        
        $consulta->save();

        return redirect()->route('recepcionista.dashboard')
                             ->with('success', 'Paciente encaminhado para triagem!');
    }
}