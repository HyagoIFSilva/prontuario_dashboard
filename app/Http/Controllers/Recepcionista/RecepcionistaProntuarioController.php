<?php

namespace App\Http\Controllers\Recepcionista;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Prontuario;
use App\Models\Paciente; 
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log; 

class ProntuarioController extends Controller
{
    public function buscar(Request $request)
    {
        $dadosValidados = $request->validate([
            'paciente_id' => 'required|exists:tbPaciente,idPaciente',
        ]);

        try {
            $pacienteId = $dadosValidados['paciente_id'];

            $prontuario = Prontuario::firstOrCreate(
                ['idPacienteFK' => $pacienteId],
                ['dataAbertura' => now()] 
            );

            if (!$prontuario) {
                 throw new \Exception('Não foi possível encontrar ou criar o prontuário.');
            }

            $prontuario->load('paciente');
            $paciente = $prontuario->paciente; 
            $statusPaciente = 'INATIVO'; 
            if ($paciente && isset($paciente->status)) { 
            
                 $statusPaciente = $paciente->status;
            } elseif ($paciente) {
                 Log::warning("Coluna 'status' não encontrada ou nula no Paciente ID: " . optional($paciente)->idPaciente); 
          
                 $statusPaciente = 'Status Desconhecido';
            } else {
                 Log::warning("Relação 'paciente' não carregada para Prontuario ID: " . $prontuario->idProntuarioPK);
                 $statusPaciente = 'Paciente Não Encontrado';
            }

            $dataAberturaFormatada = 'N/A';
            if ($prontuario->dataAbertura) {
                 $dataAbertura = $prontuario->dataAbertura instanceof Carbon ? $prontuario->dataAbertura : Carbon::parse($prontuario->dataAbertura);
                 $dataAberturaFormatada = $dataAbertura->format('d/m/Y');
            }

            $totalConsultas = 0;

            if (method_exists($prontuario, 'consultas')) {
                $totalConsultas = $prontuario->consultas()->count();
            } else {
                Log::warning("Relação 'consultas' não encontrada no Model Prontuario.");
            }

            return response()->json([
                'success' => true,
                'prontuarioId' => $prontuario->idProntuarioPK,
                'status' => $statusPaciente,
                'dataAbertura' => $dataAberturaFormatada,
                'totalConsultas' => $totalConsultas
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
   
             Log::error('Erro de validação ao buscar prontuário: ' . $e->getMessage(), $e->errors());
             return response()->json(['success' => false, 'message' => 'Dados inválidos.', 'errors' => $e->errors()], 422);
        }
        catch (\Exception $e) {
     
            Log::error('Erro ao buscar prontuário: ' . $e->getMessage() . ' no arquivo ' . $e->getFile() . ' na linha ' . $e->getLine());


            return response()->json(['success' => false, 'message' => 'Ocorreu um erro interno ao buscar o prontuário.'], 500);
        }
    }
}

