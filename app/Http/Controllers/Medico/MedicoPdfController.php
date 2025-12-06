<?php

namespace App\Http\Controllers\Medico;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Consulta;
use App\Models\Exame;
use App\Models\Medicamento;
use Carbon\Carbon;

class MedicoPdfController extends Controller
{ 
    private function getMedicoData($consulta)
    {
        if ($consulta->medico) {
            return $consulta->medico;
        }

        return (object) [
            'nomeMedico' => $consulta->nomeMedico ?? 'N/A',
            'crmMedico' => $consulta->crmMedico ?? 'N/A',
            'especialidadeMedico' => $consulta->medico->especialidadeMedico ?? 'N/A',
        ];
    }

    public function gerarPdfExames($idConsulta)
    {
        try {
            $consulta = Consulta::with([
                'paciente', 
                'medico', 
                'prontuario',
                'exames'
            ])->where('idConsultaPK', $idConsulta)->firstOrFail();

            $medicoInfo = $this->getMedicoData($consulta);

            $exames = $consulta->exames;
            
            $examesFormatados = [];
            foreach ($exames as $exame) {
                $exameInfo = $exame->nomeExame;
                if ($exame->tipoExame) {
                    $exameInfo .= " ({$exame->tipoExame})";
                }
                if ($exame->descExame) {
                    $exameInfo .= " - {$exame->descExame}";
                }
                $examesFormatados[] = $exameInfo;
            }

            $data = [
                'paciente' => $consulta->paciente,
                'medico' => $medicoInfo,
                'consulta' => $consulta,
                'exames' => !empty($examesFormatados) ? implode("\n", $examesFormatados) : 'Nenhum exame solicitado.',
                'examesArray' => $examesFormatados,
                'numProntuario' => $consulta->prontuario 
                    ? str_pad($consulta->prontuario->idProntuarioPK, 6, '0', STR_PAD_LEFT) 
                    : 'N/A',
                'dataEmissao' => Carbon::now()->format('d/m/Y'),
            ];

            $pdf = PDF::loadView('medico.pdf.pedidoExames', $data);

            $pdf->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'isPhpEnabled' => true,
                'isFontSubsettingEnabled' => true,
                'chroot' => public_path(),
                'defaultPaperSize' => 'a4',
                'defaultOrientation' => 'portrait',
                'marginBottom' => 5,
                'marginLeft' => 10,
                'marginRight' => 10,
                'marginTop' => 10,
                'dpi' => 150,
            ]);

            $nomeArquivo = 'Solicitacao_Exames_' . 
                preg_replace('/[^a-zA-Z0-9]/', '_', $consulta->paciente->nomePaciente) . '_' . 
                Carbon::parse($consulta->dataConsulta)->format('Ymd') . '.pdf';

            return $pdf->download($nomeArquivo);

        } catch (\Exception $e) {
            \Log::error('Erro ao gerar PDF de Exames: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao gerar PDF de Exames: ' . $e->getMessage());
        }
    }

    public function gerarPdfReceita($idConsulta)
    {
        try {
            $consulta = Consulta::with([
                'paciente', 
                'medico', 
                'prontuario',
                'medicamentos'
            ])->where('idConsultaPK', $idConsulta)->firstOrFail();

            $medicoInfo = $this->getMedicoData($consulta);

            $medicamentos = $consulta->medicamentos;
            
            $medicamentosFormatados = [];
            foreach ($medicamentos as $med) {
                $medInfo = "• {$med->nomeMedicamento}";
                
                if ($med->dosagemMedicamento) {
                    $medInfo .= " - {$med->dosagemMedicamento}";
                }
                if ($med->frequenciaMedicamento) {
                    $medInfo .= " ({$med->frequenciaMedicamento})";
                }
                if ($med->periodoMedicamento) {
                    $medInfo .= " por {$med->periodoMedicamento}";
                }
                if ($med->tipoMedicamento) {
                    $medInfo .= " [{$med->tipoMedicamento}]";
                }
                
                $medicamentosFormatados[] = $medInfo;
            }

            $data = [
                'paciente' => $consulta->paciente,
                'medico' => $medicoInfo,
                'consulta' => $consulta,
                'medicamentos' => !empty($medicamentosFormatados) ? implode("\n", $medicamentosFormatados) : 'Nenhum medicamento prescrito.',
                'medicamentosArray' => $medicamentosFormatados,
                'numProntuario' => $consulta->prontuario 
                    ? str_pad($consulta->prontuario->idProntuarioPK, 6, '0', STR_PAD_LEFT) 
                    : 'N/A',
                'dataEmissao' => Carbon::now()->format('d/m/Y'),
            ];

            $pdf = PDF::loadView('medico.pdf.receitaMedica', $data);

            $pdf->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'isPhpEnabled' => true,
                'isFontSubsettingEnabled' => true,
                'chroot' => public_path(),
                'defaultPaperSize' => 'a4',
                'defaultOrientation' => 'portrait',
                'marginBottom' => 5,
                'marginLeft' => 10,
                'marginRight' => 10,
                'marginTop' => 10,
                'dpi' => 150,
            ]);

            $nomeArquivo = 'Receita_Medica_' . 
                preg_replace('/[^a-zA-Z0-9]/', '_', $consulta->paciente->nomePaciente) . '_' . 
                Carbon::parse($consulta->dataConsulta)->format('Ymd') . '.pdf';

            return $pdf->download($nomeArquivo);

        } catch (\Exception $e) {
            \Log::error('Erro ao gerar PDF da Receita: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao gerar PDF da Receita: ' . $e->getMessage());
        }
    }
}