<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>SOLICITAÇÃO DE EXAMES</title>
<style>
    @page {
        size: A4;
        margin: 0;
    }

    body {
        margin: 0;
        padding: 0;
        background: #f8f9fa;
    }

    .container {
        max-width: 900px;
        min-height: 100vh;
        margin: 0 auto;
        font-family: 'Segoe UI', 'DejaVu Sans', Arial, sans-serif;
        color: #1a1a1a;
        padding: 40px 60px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-sizing: border-box;
        background: white;
    }

    /* --- HEADER (LOGO E TÍTULO) --- */
    .header {
        text-align: center;
        border-bottom: 3px solid #7b0e06; 
        padding-bottom: 20px;
        margin-bottom: 30px;
    }

    /* Bloco para a logo */
    .header-logo-container {
        margin-bottom: 10px; 
    }
    
    .banner-logo {
      /* Estilo da logo */
      width: 200px; /* Ajuste este valor conforme o tamanho desejado para a logo */
      height: auto;
      display: block;
      margin: 0 auto; /* Centraliza a imagem */
    }


    /* Estilos do Título Principal */
    .header h1 {
        color: #7b0e06;
        font-size: 32px;
        letter-spacing: 2px;
        margin: 0;
        font-weight: 700;
        text-transform: uppercase;
    }

    .header p {
        font-weight: 500;
        font-size: 16px;
        color: #555;
        margin: 6px 0 0 0;
    }

    /* --- BLOCOS DE INFORMAÇÃO --- */
    .info-block {
        background: #f8f9fa;
        border-left: 4px solid #7b0e06; 
        padding: 20px 24px;
        margin-top: 20px;
        border-radius: 4px;
    }

    .info-block h2 {
        font-size: 18px;
        font-weight: 700;
        color: #7b0e06;
        margin: 0 0 15px 0;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .clinical-reason p {
        font-size: 15px;
        line-height: 1.6;
        color: #333;
    }


    .patient-info {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 12px;
        line-height: 1.8;
    }

    .info-row {
        font-size: 15px;
    }

    .info-label {
        font-weight: 600;
        color: #333;
        min-width: 100px;
        margin-right: 5px;
    }
    
    .info-value {
        color: #555;
    }

    /* --- EXAMES SOLICITADOS (DESTAQUE) --- */
    .exames-section {
        border: 2px solid #7b0e06;
        background: #fff5f5; 
        padding: 24px;
        margin-top: 40px;
        border-radius: 4px;
        box-shadow: 0 2px 8px rgba(123, 14, 6, 0.1);
    }

    .exames-section h2 {
        color: #7b0e06;
        text-align: center;
        font-size: 20px;
        font-weight: 700;
        margin: 0 0 20px 0;
        text-transform: uppercase;
        letter-spacing: 1px;
        padding-bottom: 12px;
        border-bottom: 2px solid #7b0e06;
    }

    .exames-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .exames-list li {
        position: relative;
        margin-bottom: 10px;
        padding-left: 25px;
        font-size: 15px;
        color: #1a1a1a;
        line-height: 1.6;
    }

    .exames-list li::before {
        content: "•";
        color: #7b0e06;
        font-size: 20px;
        font-weight: 700;
        position: absolute;
        left: 0;
        top: 0;
    }

    /* --- RODAPÉ E ASSINATURA --- */
    .signature-area {
        width: 100%;
        margin-top: 60px;
    }

    .date-section {
        text-align: right;
        margin-bottom: 50px;
        font-weight: 500;
        font-size: 15px;
        color: #555;
    }

    .signature {
        text-align: center;
    }

    .signature-line {
        height: 2px;
        background-color: #333;
        width: 50%;
        margin: 0 auto 10px;
    }

    .medico-info {
        font-size: 16px;
        font-weight: 600;
        color: #1a1a1a;
        line-height: 1.6;
    }

    .medico-crm {
        font-size: 14px;
        color: #666;
        font-weight: 500;
    }

    .footer {
        text-align: center;
        font-size: 13px;
        color: #999;
        border-top: 1px solid #e0e0e0;
        padding-top: 20px;
        margin-top: 40px;
    }

    @media print {
        .container {
            box-shadow: none;
            padding: 30px 50px;
            min-height: initial; 
        }
    }
</style>
</head>
<body>
     <div class="container">
        
        <div>
            <div class="header">
                <div class="header-logo-container">
                    <img src="{{ asset('img/medico-logo1.png') }}" alt="Logo Prontuário" class="banner-logo">
                </div>
                
                <h1>SOLICITAÇÃO DE EXAMES</h1>
                <p>Unidade: {{ $consulta->unidade ?? 'Hospital Municipal Central' }}</p>
            </div>

            <div class="info-block">
                <h2>Dados do Paciente</h2>
                <div class="patient-info">
                    <div class="info-row"><span class="info-label">Nome:</span> <span class="info-value">{{ $paciente->nomePaciente ?? 'N/A' }}</span></div>
                    <div class="info-row"><span class="info-label">CPF:</span> <span class="info-value">{{ $paciente->cpfPaciente ?? 'N/A' }}</span></div>
                    <div class="info-row"><span class="info-label">Nascimento:</span> <span class="info-value">{{ isset($paciente->dataNascPaciente) ? \Carbon\Carbon::parse($paciente->dataNascPaciente)->format('d/m/Y') : 'N/A' }}</span></div>
                </div>
            </div>

            <div class="info-block">
                <h2>Dados do Médico Solicitante</h2>
                <div class="patient-info" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
                    <div class="info-row"><span class="info-label">Nome:</span> <span class="info-value">Dr(a). {{ $medico->nomeMedico ?? 'N/A' }}</span></div>
                    <div class="info-row"><span class="info-label">CRM:</span> <span class="info-value">{{ $medico->crmMedico ?? 'N/A' }}</span></div>
                </div>
            </div>

            <div class="info-block clinical-reason">
                <h2>Indicação Clínica</h2>
                <p>{{ $consulta->observacoes ?? 'Avaliação médica geral (Motivo da Solicitação)' }}</p>
            </div>

            <div class="exames-section">
                <h2>EXAMES SOLICITADOS</h2>
                <div class="exames-content">
                    @php
                        // Simulação
                        $examesArray = $examesArray ?? ['Hemograma Completo', 'Glicemia em Jejum', 'Urina Tipo 1', 'Radiografia de Tórax (PA e Perfil)'];
                    @endphp
                    
                    @if(isset($examesArray) && count($examesArray) > 0)
                        <ul class="exames-list">
                            @foreach($examesArray as $exame)
                                <li>{{ $exame }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p style="text-align: center; color: #666; font-style: italic;">Nenhum exame solicitado.</p>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="signature-area">
            
            <div class="date-section">
                Data de Emissão: {{ isset($consulta->dataConsulta) ? \Carbon\Carbon::parse($consulta->dataConsulta)->format('d/m/Y') : 'N/A' }}
            </div>

            <div class="signature">
                <div class="signature-line"></div>
                <div class="medico-info">
                    Dr(a). {{ $medico->nomeMedico ?? 'N/A' }}
                </div>
                <div class="medico-crm">
                    CRM: {{ $medico->crmMedico ?? 'N/A' }}
                </div>
            </div>

            <div class="footer">
                Documento gerado eletronicamente pelo Sistema Médico.<br>
                Válido sem assinatura física de acordo com a legislação vigente.
            </div>
        </div>
    </div>
</body>
</html>