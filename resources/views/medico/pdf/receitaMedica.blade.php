<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Receita Médica</title>

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
      background: white;
      box-sizing: border-box;
    }

    /* --- AJUSTES PARA CENTRALIZAR O HEADER --- */
    .header {
      /* Remove o display: flex para permitir a centralização de blocos */
      /* display: flex; */
      /* Ocupa 100% da largura do container */
      width: 100%; 
      text-align: center; /* Centraliza todo o conteúdo (logo e textos) */
      border-bottom: 3px solid #7b0e06;
      padding-bottom: 20px;
      margin-bottom: 30px;
    }

    .header-content {
        /* Container para centralizar o logo (que é uma imagem/bloco) */
        display: inline-block;
        margin-bottom: 10px; /* Espaço entre o logo e o título "RECEITA MÉDICA" */
    }

    /* O logo (imagem ou simulação) agora será centralizado com text-align: center no .header */
    .banner-logo {
      width: 200px;
      height: auto;
      display: block; /* Garante que a imagem se comporte como bloco para centralização */
      margin: 0 auto; /* Centraliza a imagem se ela estiver envolvida por um bloco */
    }

    /* Estilos do Título Principal */
    .header h1 {
      color: #7b0e06;
      font-size: 32px;
      font-weight: 700;
      text-transform: uppercase;
      margin: 0 0 8px 0;
      letter-spacing: 2px;
      line-height: 1.2;
      text-align: center; /* Garante que o H1 esteja centralizado */
    }

    .header p {
      font-weight: 500;
      font-size: 18px;
      color: #555;
      margin: 0;
      line-height: 1.4;
      text-align: center; /* Garante que a Unidade esteja centralizada */
    }
    /* --- FIM DOS AJUSTES DO HEADER --- */


    /* Blocos de informação */
    .info-block {
      background: #f8f9fa;
      border-left: 4px solid #7b0e06;
      padding: 20px 24px;
      margin-bottom: 20px;
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

    .patient-info {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 12px;
      line-height: 1.8;
    }

    .info-row {
      display: flex;
      align-items: baseline;
    }

    .info-label {
      font-weight: 600;
      color: #333;
      font-size: 15px;
      min-width: 100px;
    }

    .info-value {
      color: #555;
      font-size: 15px;
    }

    /* Queixa Principal */
    .clinical-reason {
      background: #fff;
      border: 1px solid #ddd;
      padding: 20px 24px;
      margin-bottom: 20px;
      border-radius: 4px;
    }

    .clinical-reason h3 {
      font-size: 16px;
      font-weight: 700;
      color: #333;
      margin: 0 0 12px 0;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .clinical-reason p {
      margin: 0;
      line-height: 1.6;
      color: #555;
      font-size: 15px;
    }

    /* Prescrição - Destaque */
    .prescription-section {
      border: 2px solid #7b0e06;
      background: #fff5f5;
      padding: 24px;
      margin: 30px 0;
      border-radius: 4px;
      box-shadow: 0 2px 8px rgba(123, 14, 6, 0.1);
    }

    .prescription-section h2 {
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

    .prescription-content {
      font-size: 15px;
      line-height: 1.8;
      color: #1a1a1a;
      /* Ajuste para remover o list-style e usar blocos simples */
    }

    /* Removemos os estilos de lista e do 'Rx' */
    /* .prescription-list, .prescription-list li, .prescription-list li::before foram removidos */
    .prescription-item {
        margin-bottom: 10px;
        line-height: 1.6;
        padding-left: 10px; /* Pequeno recuo para visual */
    }


    /* Data */
    .date-section {
      text-align: right;
      margin-top: 40px;
      font-weight: 500;
      font-size: 15px;
      color: #555;
    }

    /* Assinatura */
    .signature {
      margin-top: 60px;
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

    /* Footer */
    .footer {
      text-align: center;
      font-size: 13px;
      color: #999;
      border-top: 1px solid #e0e0e0;
      margin-top: 50px;
      padding-top: 20px;
    }

    @media print {
      body { 
        background: white;
      }
      .container {
        box-shadow: none;
        padding: 30px 50px;
      }
    }

  </style>
</head>

<body>
  <div class="container">

    <div class="header">
      <div class="header-content">
        <img src="{{ asset('img/medico-logo1.png') }}" alt="Logo Prontuário+" class="banner-logo" style="width: 250px;">
      </div>
      
      <h1>RECEITA MÉDICA</h1>
      <p>{{ $consulta->unidade ?? 'Hospital Municipal Central' }}</p>
    </div>

    <div class="info-block">
      <h2>Dados do Paciente</h2>
      <div class="patient-info">
        <div class="info-row">
          <span class="info-label">Nome:</span>
          <span class="info-value">{{ $paciente->nomePaciente ?? 'Fernanda Pereira' }}</span>
        </div>
        <div class="info-row">
          <span class="info-label">CPF:</span>
          <span class="info-value">{{ $paciente->cpfPaciente ?? '10834384418' }}</span>
        </div>
        <div class="info-row">
          <span class="info-label">Nascimento:</span>
          <span class="info-value">{{ \Carbon\Carbon::parse($paciente->dataNascPaciente ?? '1991-05-27')->format('d/m/Y') }}</span>
        </div>
      </div>
    </div>

    <div class="clinical-reason">
      <h3>Queixa Principal / Motivo da Consulta</h3>
      <p>{{ $consulta->queixa_principal ?? 'Ela está com dor de barriga' }}</p>
    </div>

    <div class="prescription-section">
      <h2>Prescrição Médica</h2>
      <div class="prescription-content">
        @php
            $medicamentosArray = $medicamentosArray ?? ['Ibuprofeno 250mg (8/8) por 7 dias [Analgésico]'];
        @endphp

        @if(isset($medicamentosArray) && count($medicamentosArray) > 0)
            @foreach($medicamentosArray as $medicamento)
                <p class="prescription-item">
                    {{ $medicamento }}
                </p>
            @endforeach
        @else
            <p style="text-align: center; color: #666;">Nenhum medicamento prescrito.</p>
        @endif
      </div>
    </div>

    <div class="date-section">
      {{ $dataEmissao ?? 'Data: 26/11/2025' }}
    </div>

    <div class="signature">
      <div class="signature-line"></div>
      <div class="medico-info">
        Dr(a). {{ $medico->nomeMedico ?? 'Esther Nascimento dos Santos' }}
      </div>
      <div class="medico-crm">
        CRM: {{ $medico->crmMedico ?? '123456' }}
      </div>
    </div>

    <div class="footer">
      Documento gerado eletronicamente pelo Sistema Médico.<br>
      Válido sem assinatura física de acordo com a legislação vigente.
    </div>

  </div>
</body>
</html>