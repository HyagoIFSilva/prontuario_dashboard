@extends('medico.templates.medicoTemplate')

@section('title', isset($consulta) ? 'Finalizar Atendimento' : 'Cadastrar Consulta')

@section('content')
<link rel="stylesheet" href="{{ asset('css/medico/cadastrarProntuario.css') }}">

<main class="main-dashboard">
    <div class="cadastrar-wrapper">
        {{-- HEADER PREMIUM --}}
        <div class="page-header-cadastro">
            <div class="header-background-pattern"></div>
            <div class="header-content">
                <div class="header-left-section">
                    <div class="header-icon-container">
                        <div class="icon-ring-outer"></div>
                        <div class="icon-ring-inner"></div>
                        @if (isset($consulta))
                        <i class="bi bi-journal-check"></i>
                        @else
                        <i class="bi bi-journal-medical"></i>
                        @endif
                    </div>
                    <div class="header-text-content">
                        <h1>
                            @if (isset($consulta))
                            Finalizar Atendimento
                            @else
                            Cadastrar Consulta ao Prontuário
                            @endif
                        </h1>
                        <p>Registro completo do atendimento médico</p>
                        <div class="header-breadcrumb">
                            <span><i class="bi bi-house"></i> Dashboard</span>
                            <i class="bi bi-chevron-right"></i>
                            <span><i class="bi bi-journal-text"></i> Prontuário</span>
                            <i class="bi bi-chevron-right"></i>
                            <span class="active">
                                @if (isset($consulta))
                                <i class="bi bi-check-circle"></i> Finalizar
                                @else
                                <i class="bi bi-plus-circle"></i> Cadastrar
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
                <div class="header-stats">
                    <div class="stat-badge">
                        <i class="bi bi-person"></i>
                        <div>
                            <span class="stat-label">Paciente</span>
                            <span class="stat-value">{{ $paciente->nomePaciente }}</span>
                        </div>
                    </div>
                    <div class="stat-badge">
                        <i class="bi bi-calendar-check"></i>
                        <div>
                            <span class="stat-label">Data</span>
                            <span class="stat-value">{{ \Carbon\Carbon::now()->format('d/m/Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- CARD DE INFORMAÇÕES DO PACIENTE --}}
        <div class="info-card-container">
            <div class="info-card-header">
                <div class="info-card-title">
                    <div class="title-icon">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <div>
                        <h2>Informações do Atendimento</h2>
                        <p>Dados completos do paciente e informações da consulta</p>
                    </div>
                </div>
                <div class="info-card-badge">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>Ativo</span>
                </div>
            </div>
            <div class="info-card-body">
                <div class="info-section">
                    <div class="section-header">
                        <i class="bi bi-person-vcard"></i>
                        <span>Dados Pessoais</span>
                    </div>
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Paciente</span>
                            <span class="info-value">{{ $paciente->nomePaciente }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">CPF</span>
                            <span class="info-value">{{ $paciente->cpfPaciente }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Data de Nascimento</span>
                            <span class="info-value">
                                {{ \Carbon\Carbon::parse($paciente->dataNascPaciente)->format('d/m/Y') }}
                                <span class="info-age">({{ \Carbon\Carbon::parse($paciente->dataNascPaciente)->age }} anos)</span>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="info-section">
                    <div class="section-header">
                        <i class="bi bi-heart-pulse"></i>
                        <span>Profissional Responsável</span>
                    </div>
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Médico</span>
                            <span class="info-value">{{ $medico->nomeMedico }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">CRM</span>
                            <span class="info-value">{{ $medico->crmMedico }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Especialidade</span>
                            <span class="info-value">{{ $medico->especialidadeMedico }}</span>
                        </div>
                    </div>
                </div>

                <div class="info-section">
                    <div class="section-header">
                        <i class="bi bi-building"></i>
                        <span>Local de Atendimento</span>
                    </div>
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Unidade</span>
                            <span class="info-value">{{ $unidadeMedico->nomeUnidade ?? 'Não especificada' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Data e Hora</span>
                            <span class="info-value">
                                {{ isset($consulta) && $consulta->dataConsulta ? \Carbon\Carbon::parse($consulta->dataConsulta)->format('d/m/Y H:i') : \Carbon\Carbon::now()->format('d/m/Y H:i') }}
                            </span>
                        </div>
                        @if (isset($consulta) && $consulta->classificacao_risco)
                        <div class="info-item">
                            <span class="info-label">Classificação de Risco</span>
                            <span class="classificacao-badge classificacao-{{ $consulta->classificacao_risco }}">
                                {{ ucfirst($consulta->classificacao_risco) }}
                            </span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- TRIAGEM DA ENFERMAGEM --}}
        @if (isset($consulta) && $anotacoesEnfermagem && $anotacoesEnfermagem->isNotEmpty())
        <div class="triagem-container" id="triagemContainer">
            <div class="triagem-header">
                <div class="triagem-title">
                    <div class="triagem-icon">
                        <i class="bi bi-heart-pulse-fill"></i>
                    </div>
                    <div>
                        <h3>Triagem da Enfermagem</h3>
                        <p>Registros e sinais vitais coletados pela equipe de enfermagem</p>
                    </div>
                </div>
                <button type="button" class="btn-expand-triagem" onclick="toggleTriagem()">
                    <i class="bi bi-arrows-expand"></i>
                    <span>Expandir</span>
                </button>
            </div>
            <div class="triagem-body">
                @foreach ($anotacoesEnfermagem as $anotacao)
                <div class="anotacao-card">
                    <div class="anotacao-header">
                        <div class="anotacao-timestamp">
                            <i class="bi bi-calendar-check"></i>
                            <strong>{{ \Carbon\Carbon::parse($anotacao->data_hora)->format('d/m/Y H:i') }}</strong>
                        </div>
                        <div class="anotacao-author">
                            <i class="bi bi-person-badge"></i>
                            <span>{{ $anotacao->enfermeiro->nomeEnfermeiro ?? 'Enfermeiro(a)' }}</span>
                        </div>
                    </div>
                    <div class="anotacao-content">
                        @if ($anotacao->descricao)
                        <div class="anotacao-item">
                            <strong><i class="bi bi-chat-left-text"></i> Descrição</strong>
                            <p>{{ $anotacao->descricao }}</p>
                        </div>
                        @endif

                        @if ($anotacao->alergias)
                        <div class="anotacao-item alergias">
                            <strong><i class="bi bi-exclamation-triangle"></i> Alergias</strong>
                            <p>{{ $anotacao->alergias }}</p>
                        </div>
                        @endif

                        @if ($anotacao->medicacoes_ministradas)
                        <div class="anotacao-item">
                            <strong><i class="bi bi-capsule"></i> Medicações/Procedimentos</strong>
                            <p>{{ $anotacao->medicacoes_ministradas }}</p>
                        </div>
                        @endif

                        <div class="sinais-vitais-section">
                            <h4><i class="bi bi-activity"></i> Sinais Vitais</h4>
                            <div class="sinais-vitais-grid">
                                @if($anotacao->pressao_arterial)
                                <div class="sinal-vital-card">
                                    <div class="sinal-icon">
                                        <i class="bi bi-speedometer2"></i>
                                    </div>
                                    <div class="sinal-content">
                                        <span class="sinal-label">Pressão Arterial</span>
                                        <span class="sinal-value">{{ $anotacao->pressao_arterial }} <small>mmHg</small></span>
                                    </div>
                                </div>
                                @endif

                                @if($anotacao->temperatura)
                                <div class="sinal-vital-card">
                                    <div class="sinal-icon">
                                        <i class="bi bi-thermometer"></i>
                                    </div>
                                    <div class="sinal-content">
                                        <span class="sinal-label">Temperatura</span>
                                        <span class="sinal-value">{{ $anotacao->temperatura }} <small>°C</small></span>
                                    </div>
                                </div>
                                @endif

                                @if($anotacao->frequencia_cardiaca)
                                <div class="sinal-vital-card">
                                    <div class="sinal-icon">
                                        <i class="bi bi-heart"></i>
                                    </div>
                                    <div class="sinal-content">
                                        <span class="sinal-label">Frequência Cardíaca</span>
                                        <span class="sinal-value">{{ $anotacao->frequencia_cardiaca }} <small>bpm</small></span>
                                    </div>
                                </div>
                                @endif

                                @if($anotacao->frequencia_respiratoria)
                                <div class="sinal-vital-card">
                                    <div class="sinal-icon">
                                        <i class="bi bi-lungs"></i>
                                    </div>
                                    <div class="sinal-content">
                                        <span class="sinal-label">Frequência Respiratória</span>
                                        <span class="sinal-value">{{ $anotacao->frequencia_respiratoria }} <small>rpm</small></span>
                                    </div>
                                </div>
                                @endif

                                @if($anotacao->saturacao)
                                <div class="sinal-vital-card">
                                    <div class="sinal-icon">
                                        <i class="bi bi-moisture"></i>
                                    </div>
                                    <div class="sinal-content">
                                        <span class="sinal-label">Saturação O₂</span>
                                        <span class="sinal-value">{{ $anotacao->saturacao }} <small>%</small></span>
                                    </div>
                                </div>
                                @endif

                                @if($anotacao->dor !== null)
                                <div class="sinal-vital-card">
                                    <div class="sinal-icon">
                                        <i class="bi bi-emoji-dizzy"></i>
                                    </div>
                                    <div class="sinal-content">
                                        <span class="sinal-label">Escala de Dor</span>
                                        <span class="sinal-value">{{ $anotacao->dor }}/10</span>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- FORMULÁRIO DE CONSULTA --}}
        <div class="form-container">
            <form action="{{ isset($consulta) ? route('medico.prontuario.update', $consulta->idConsultaPK) : route('medico.prontuario.store', $paciente->idPaciente) }}" method="POST" id="prontuarioForm">
                @csrf
                @if (isset($consulta))
                @method('PUT')
                @endif

                {{-- DADOS DA CONSULTA MÉDICA --}}
                <div class="form-section">
                    <div class="form-section-header">
                        <div class="section-icon">
                            <i class="bi bi-clipboard2-pulse"></i>
                        </div>
                        <div>
                            <h3>Dados da Consulta Médica</h3>
                            <p>Registro completo do atendimento e diagnóstico</p>
                        </div>
                    </div>

                    <div class="input-wrapper">
                        <label class="input-label">
                            <i class="bi bi-calendar-check"></i>
                            Data e Hora do Registro
                            <span class="required-indicator">*</span>
                        </label>
                        <div class="input-with-icon">
                            <i class="input-icon-left bi bi-clock"></i>
                            <input
                                type="datetime-local"
                                id="dataConsulta"
                                name="dataConsulta"
                                value="{{ old('dataConsulta', \Carbon\Carbon::now()->format('Y-m-d\TH:i')) }}"
                                required
                                class="form-input">
                        </div>
                        @error('dataConsulta')
                        <div class="input-error">
                            <i class="bi bi-exclamation-circle"></i>
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="input-wrapper">
                        <label class="input-label">
                            <i class="bi bi-file-text"></i>
                            Observações Médicas / Diagnóstico
                        </label>
                        <div class="textarea-wrapper">
                            <textarea
                                id="observacoes"
                                name="observacoes"
                                rows="6"
                                class="form-textarea"
                                placeholder="Descreva o diagnóstico, evolução, observações médicas e conduta...">{{ old('observacoes', $consulta->observacoes ?? '') }}</textarea>
                            <div class="textarea-counter">
                                <i class="bi bi-text-paragraph"></i>
                                <span id="observacoesCounter">0</span> caracteres
                            </div>
                        </div>
                        <div class="input-helper">
                            <i class="bi bi-info-circle"></i>
                            Inclua diagnóstico principal, observações clínicas relevantes e evolução do caso
                        </div>
                        @error('observacoes')
                        <div class="input-error">
                            <i class="bi bi-exclamation-circle"></i>
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>

                {{-- EXAMES SOLICITADOS --}}
                <div class="form-section">
                    <div class="form-section-header">
                        <div class="section-icon">
                            <i class="bi bi-clipboard2-check"></i>
                        </div>
                        <div>
                            <h3>Exames Solicitados</h3>
                            <p>Selecione os exames necessários para o diagnóstico</p>
                        </div>
                    </div>

                    <div class="input-wrapper">
                        <label class="input-label">
                            <i class="bi bi-search"></i>
                            Pesquisar Exames
                        </label>
                        <input type="text" id="filtroExames" class="input-filtro" placeholder="Pesquisar exame...">

                        <div id="listaExames" class="checkbox-list">
                            @php
                            $tiposExame = [
                            'Análises Clínicas',
                            'Anatomia Patológica',
                            'Biologia Molecular',
                            'Endoscopia',
                            'Eletrofisiologia',
                            'Imagem',
                            'Medicina Nuclear',
                            'Procedimentos Invasivos',
                            'Testes Alérgicos',
                            'Testes Funcionais',
                            'Outros'
                            ];

                            $exames = [
                            'Hemograma Completo','Raio X', 'Glicemia de Jejum', 'Colesterol Total e Frações', 'Triglicerídeos',
                            'Creatinina', 'Ureia', 'Ácido Úrico', 'TGO (AST)', 'TGP (ALT)', 'Bilirrubinas',
                            'Electroforese de Proteínas', 'Proteína C-Reativa (PCR)', 'VHS', 'Urinalise',
                            'Eletrocardiograma (ECG)', 'Radiografia de Tórax', 'Ultrassom Abdominal',
                            'Ultrassom Ginecológico', 'Ultrassom Obstétrico', 'Mamografia',
                            'Citologia Oncótica (Papanicolau)', 'Teste Rápido HIV', 'Teste Rápido Sífilis',
                            'Teste Rápido Hepatite B e C', 'Teste de Gravidez', 'Coagulograma', 'TP/INR',
                            'TSH', 'T4 Livre', 'FT3', 'Anticorpos Anti-TPO', 'HbA1c', 'Microalbuminúria',
                            'Cultura de Urina', 'Cultura de Fezes', 'Parasitológico de Fezes', 'Exame de Fezes',
                            'Exame de Sangue Oculto nas Fezes', 'Beta-HCG', 'FSH', 'LH', 'Prolactina',
                            'Testosterona', 'Estradiol', 'Progesterona', 'PSA Total', 'Vitaminas (B12, D)',
                            'Ferro Sérico', 'Ferritina', 'Transferrina', 'Ácido Fólico', 'Calcium Iônico',
                            'Magnésio', 'Fósforo', 'Sódio', 'Potássio', 'Cloro', 'Bicarbonato',
                            'Gasometria Arterial', 'Gasometria Venosa', 'Ecocardiograma', 'Doppler Venoso',
                            'Doppler Arterial', 'Endoscopia Digestiva Alta', 'Colonoscopia', 'Colposcopia',
                            'Biopsia de Pele', 'Biopsia de Mama', 'Biopsia de Colo Uterino', 'Teste de Esforço',
                            'Holter 24h', 'Mapa 24h', 'Polissonografia', 'Teste de Função Pulmonar',
                            'Teste de Audição', 'Teste de Visão', 'Teste de Glicose Tolerância Oral (OGTT)',
                            'Teste de Estresse', 'Teste de Esforço Cardíaco', 'Teste de Função Hepática',
                            'Teste de Função Renal', 'Teste de Função Tireoidiana', 'Teste de Função Adrenal',
                            'Teste de Função Gonadal', 'Teste de Função Pancreática', 'Tomografia Computadorizada (TC)',
                            'Ressonância Magnética (RM)', 'Cintilografia (Miocárdica, Óssea, Pulmonar)', 'Densitometria Óssea',
                            'Marcadores Tumorais (CEA, CA-125, PSA, AFP)', 'Anticorpos Antinucleares (FAN)', 'Tempo de Tromboplastina Parcial Ativado (TTPA)',
                            'Tempo de Sangria', 'Reticulócitos', 'Desidrogenase Lática (DHL)', 'Amilase',
                            'Lipase', 'Imunoglobulinas (IgA, IgG, IgM)', 'Fator Reumatoide', 'Proteína S',
                            'Proteína C', 'Eletroencefalograma (EEG)', 'Cateterismo Cardíaco', 'Broncoscopia',
                            'Uretrocistoscopia', 'Teste Alérgico (Prick Test)', 'Eletroneuromiografia (ENMG)', 'Mapeamento de Retina',
                            'Tonometria', 'Análise de Líquor', 'Cultura de Secreções (Vaginal, Uretral, Ferida)', 'PCR (Reação em Cadeia da Polimerase)',
                            'Teste Genético', 'Dosagem Sérica de Medicamentos (ex: Lítio, Fenobarbital)', 'Biópsia Hepática', 'Outros'
                            ];
                            @endphp

                            @foreach($exames as $exame)
                            <div class="exame-item" data-exame="{{ $exame }}">
                                <label class="checkbox-item">
                                    <input type="checkbox" name="exames_solicitados[]" value="{{ $exame }}"
                                        {{ is_array(old('exames_solicitados')) && in_array($exame, old('exames_solicitados')) ? 'checked' : '' }}
                                        onchange="toggleExameDetails('{{ $exame }}', this.checked)">
                                    {{ $exame }}
                                </label>

                                <div class="exame-details" id="exame-details-{{ str_replace(' ', '-', $exame) }}" style="display: none;">
                                    <div class="exame-detail-row">
                                        <label for="tipo-exame-{{ str_replace(' ', '-', $exame) }}">
                                            <i class="bi bi-tags"></i>
                                            Tipo de Exame:
                                        </label>
                                        <select name="exame_tipos[{{ $exame }}]" id="tipo-exame-{{ str_replace(' ', '-', $exame) }}" class="exame-select">
                                            <option value="">Selecione o tipo</option>
                                            @foreach($tiposExame as $tipo)
                                            <option value="{{ $tipo }}" {{ old('exame_tipos.'.$exame) == $tipo ? 'selected' : '' }}>
                                                {{ $tipo }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @error('exames_solicitados')
                        <div class="input-error">
                            <i class="bi bi-exclamation-circle"></i>
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    {{-- DESCRIÇÃO DOS EXAMES --}}
                    <div class="input-wrapper">
                        <label class="input-label">
                            <i class="bi bi-file-text-fill"></i>
                            Descrição/Justificativa dos Exames
                        </label>
                        <div class="textarea-wrapper">
                            <textarea
                                id="descExame"
                                name="descExame"
                                rows="6"
                                class="form-textarea"
                                placeholder="Descreva a justificativa clínica, indicações, suspeitas diagnósticas ou observações relevantes para os exames solicitados...">{{ old('descExame', $consulta->descExame ?? '') }}</textarea>
                            <div class="textarea-counter">
                                <i class="bi bi-text-paragraph"></i>
                                <span id="descExameCounter">0</span> caracteres
                            </div>
                        </div>
                        <div class="input-helper">
                            <i class="bi bi-info-circle"></i>
                            Campo para registrar informações complementares sobre os exames solicitados
                        </div>
                        @error('descExame')
                        <div class="input-error">
                            <i class="bi bi-exclamation-circle"></i>
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>

                {{-- MEDICAMENTOS PRESCRITOS --}}
                <div class="form-section">
                    <div class="form-section-header">
                        <div class="section-icon">
                            <i class="bi bi-capsule-pill"></i>
                        </div>
                        <div>
                            <h3>Medicamentos Prescritos</h3>
                            <p>Selecione e configure os medicamentos para prescrição</p>
                        </div>
                    </div>

                    <div class="input-wrapper">
                        <label class="input-label">
                            <i class="bi bi-search"></i>
                            Pesquisar Medicamentos
                        </label>
                        <input type="text" id="filtroMedicamentos" class="input-filtro" placeholder="Pesquisar medicamento...">

                        <div id="listaMedicamentos" class="checkbox-list">
                            @php
                            $tiposMedicamento = [
                            'Analgésico',
                            'Anestésico',
                            'Antiagregante Plaquetário',
                            'Anticoncepcional',
                            'Anticonvulsivante',
                            'Antidiabético',
                            'Antidiarreico',
                            'Antiemético',
                            'Antiespasmódico',
                            'Antifúngico',
                            'Antihistamínico',
                            'Anti-hipertensivo',
                            'Anti-inflamatório',
                            'Antimigranoso',
                            'Antiparasitário',
                            'Antiparkinsoniano',
                            'Antipsicótico',
                            'Antiviral',
                            'Betabloqueador',
                            'Bloqueador dos Canais de Cálcio',
                            'Broncodilatador',
                            'BRA (Bloqueador do Receptor da Angiotensina)',
                            'Corticosteroide',
                            'Corticoide Inalatório',
                            'Diurético',
                            'Estabilizador de Humor',
                            'Expectorante',
                            'Fitoterápico',
                            'Hormônio Tireoidiano',
                            'IECA (Inibidor da ECA)',
                            'Imunossupressor',
                            'Insulina',
                            'Laxante',
                            'Mucolítico',
                            'Protetor Gástrico',
                            'Relaxante Muscular',
                            'Solução para Hidratação',
                            'Soro Antiveneno',
                            'Statinas (para controle de colesterol)',
                            'Suplemento',
                            'Terapia de Reposição Hormonal',
                            'Tópico Dermatológico',
                            'Vacina',
                            'Vasodilatador',
                            'Vitamina',
                            'Outros'
                            ];

                            $medicamentos = [
                                'Aciclovir', 'Acetilcisteína', 'Ácido Valproico', 'Ácido Acetilsalicílico (AAS)', 'Alprazolam', 'Amitriptilina', 'Amoxicilina', 'Anlodipino',
                                'Atenolol', 'Atorvastatina', 'Azatioprina', 'Azitromicina', 'Beclometasona', 'Biperideno', 'Budesonida', 'Buspirona',
                                'Buscopam (Butilbrometo de Escopolamina)', 'Bupivacaína', 'Carbamazepina', 'Carvedilol', 'Captopril', 'Cetirizina', 'Cetoprofeno',
                                'Cetoconazol', 'Ciclobenzaprina', 'Ciclosporina', 'Ciprofloxacino', 'Citalopram', 'Clindamicina', 'Clonazepam', 'Clopidogrel',
                                'Clobetasol', 'Codeína', 'Coenzima Q10', 'Colecalciferol (Vitamina D)', 'Complexo B', 'Desloratadina', 'Desmopressina', 'Dexametasona',
                                'Diazepam', 'Diclofenaco', 'Digoxina', 'Dimeticona', 'Dipirona', 'Doxiciclina', 'Domperidona', 'Enalapril',
                                'Escitalopram', 'Esomeprazol', 'Espironolactona', 'Fenoterol', 'Fexofenadina', 'Fluconazol', 'Fluoxetina', 'Formoterol',
                                'Furosemida', 'Gabapentina', 'Ginkgo Biloba', 'Ginseng', 'Glibenclamida', 'Gliclazida', 'Glucosamina', 'Hidroclorotiazida',
                                'Hidrocortisona', 'Hioscina', 'Ibuprofeno', 'Insulina Glargina', 'Insulina NPH', 'Itraconazol', 'Lactulose', 'Lidocaína',
                                'Loperamida', 'Loratadina', 'Losartana', 'Luteína', 'Magnésio', 'Metformina', 'Metoclopramida', 'Metotrexato',
                                'Midazolam', 'Mirtazapina', 'Mupirocina', 'Naproxeno', 'Nistatina', 'Nitrofurantoína', 'Nimesulida', 'Olanzapina',
                                'Omeprazol', 'Ondansetrona', 'Oseltamivir (Tamiflu)', 'Pantoprazol', 'Paracetamol', 'Piroxicam', 'Pregabalina', 'Prednisona',
                                'Propranolol', 'Ranitidina', 'Risperidona', 'Rivabactam', 'Rivaroxabana', 'Salbutamol', 'Selegilina', 'Sertralina',
                                'Sildenafil (Viagra)', 'Simeticona', 'Sinvastatina', 'Soro Fisiológico', 'Sulfato Ferroso', 'Topiramato', 'Tramadol', 'Trazodona',
                                'Tiotrópio', 'Vacina BCG', 'Vacina contra COVID-19', 'Vacina contra Gripe', 'Vacina contra Hepatite B', 'Vacina Tríplice Viral', 'Valsartana',
                                'Varfarina', 'Vitamina A', 'Vitamina C', 'Vitamina E', 'Zinco', 'Ômega-3', 'Outros'
                            ];
                            ];
                            @endphp

                            @foreach($medicamentos as $medicamento)
                            <div class="medicamento-item" data-medicamento="{{ $medicamento }}">
                                <label class="checkbox-item">
                                    <input type="checkbox" name="medicamentos_prescritos[]" value="{{ $medicamento }}"
                                        {{ is_array(old('medicamentos_prescritos')) && in_array($medicamento, old('medicamentos_prescritos')) ? 'checked' : '' }}
                                        onchange="toggleMedicamentoDetails('{{ $medicamento }}', this.checked)">
                                    {{ $medicamento }}
                                </label>

                                <div class="medicamento-details" id="medicamento-details-{{ str_replace(' ', '-', $medicamento) }}" style="display: none;">
                                    <div class="medicamento-detail-row">
                                        <label for="tipo-medicamento-{{ str_replace(' ', '-', $medicamento) }}">
                                            <i class="bi bi-tags"></i>
                                            Tipo:
                                        </label>
                                        <select name="medicamento_tipos[{{ $medicamento }}]" id="tipo-medicamento-{{ str_replace(' ', '-', $medicamento) }}" class="medicamento-select">
                                            <option value="">Selecione o tipo</option>
                                            @foreach($tiposMedicamento as $tipo)
                                            <option value="{{ $tipo }}" {{ old('medicamento_tipos.'.$medicamento) == $tipo ? 'selected' : '' }}>
                                                {{ $tipo }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="medicamento-detail-row">
                                        <label for="dosagem-medicamento-{{ str_replace(' ', '-', $medicamento) }}">
                                            <i class="bi bi-droplet"></i>
                                            Dosagem:
                                        </label>
                                        <input
                                            type="text"
                                            name="medicamento_dosagens[{{ $medicamento }}]"
                                            id="dosagem-medicamento-{{ str_replace(' ', '-', $medicamento) }}"
                                            class="medicamento-input"
                                            placeholder="Ex: 500mg"
                                            value="{{ old('medicamento_dosagens.'.$medicamento) }}">
                                    </div>

                                    <div class="medicamento-detail-row">
                                        <label for="frequencia-medicamento-{{ str_replace(' ', '-', $medicamento) }}">
                                            <i class="bi bi-clock"></i>
                                            Frequência:
                                        </label>
                                        <input
                                            type="text"
                                            name="medicamento_frequencias[{{ $medicamento }}]"
                                            id="frequencia-medicamento-{{ str_replace(' ', '-', $medicamento) }}"
                                            class="medicamento-input"
                                            placeholder="Ex: 8/8 horas"
                                            value="{{ old('medicamento_frequencias.'.$medicamento) }}">
                                    </div>

                                    <div class="medicamento-detail-row">
                                        <label for="periodo-medicamento-{{ str_replace(' ', '-', $medicamento) }}">
                                            <i class="bi bi-calendar"></i>
                                            Período:
                                        </label>
                                        <input
                                            type="text"
                                            name="medicamento_periodos[{{ $medicamento }}]"
                                            id="periodo-medicamento-{{ str_replace(' ', '-', $medicamento) }}"
                                            class="medicamento-input"
                                            placeholder="Ex: 5 dias"
                                            value="{{ old('medicamento_periodos.'.$medicamento) }}">
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @error('medicamentos_prescritos')
                        <div class="input-error">
                            <i class="bi bi-exclamation-circle"></i>
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>

                {{-- ORIENTAÇÕES AO PACIENTE --}}
                <div class="form-section">
                    <div class="form-section-header">
                        <div class="section-icon">
                            <i class="bi bi-chat-square-text"></i>
                        </div>
                        <div>
                            <h3>Orientações ao Paciente</h3>
                            <p>Recomendações, cuidados e observações complementares</p>
                        </div>
                    </div>

                    <div class="input-wrapper">
                        <label for="orientacoes" class="input-label">
                            <i class="bi bi-megaphone"></i>
                            Orientações e Recomendações
                        </label>
                        <div class="textarea-wrapper">
                            <textarea
                                id="orientacoes"
                                name="orientacoes"
                                rows="4"
                                class="form-textarea"
                                placeholder="Descreva orientações gerais, cuidados especiais, restrições alimentares, atividades recomendadas ou outras instruções importantes para o paciente...">{{ old('orientacoes', $consulta->orientacoes ?? '') }}</textarea>
                            <div class="textarea-counter">
                                <i class="bi bi-text-paragraph"></i>
                                <span id="orientacoesCounter">0</span> caracteres
                            </div>
                        </div>
                        <div class="input-helper">
                            <i class="bi bi-lightbulb"></i>
                            <span>Inclua informações sobre dieta, repouso, retorno, sinais de alerta ou outras orientações relevantes</span>
                        </div>
                        @error('orientacoes')
                        <div class="input-error">
                            <i class="bi bi-exclamation-circle"></i>
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>

                {{-- CAMPOS HIDDEN PARA GRAVAÇÃO --}}
                <input type="hidden" id="examesSolicitadosHidden" name="examesSolicitados">
                <input type="hidden" id="medicamentosPrescritosHidden" name="medicamentosPrescritos">

                {{-- AÇÕES DO FORMULÁRIO --}}
                <div class="form-actions">
                    <div class="actions-divider"></div>
                    <div class="actions-buttons">
                        <a href="{{ route('medico.prontuario') }}" class="btn-secondary">
                            <i class="bi bi-x-circle"></i>
                            {{ isset($consulta) ? 'Voltar para Fila' : 'Cancelar' }}
                        </a>
                        <button type="button" class="btn-primary" onclick="openConfirmModal()">
                            <span class="btn-shine"></span>
                            <i class="bi bi-check-circle"></i>
                            {{ isset($consulta) ? 'Finalizar Atendimento' : 'Salvar Prontuário' }}
                        </button>
                    </div>
                    <div class="actions-info">
                        <i class="bi bi-shield-check"></i>
                        Todos os dados são criptografados e protegidos conforme as normas de segurança
                    </div>
                </div>
            </form>
        </div>
    </div>
</main>

{{-- MODAL DE CONFIRMAÇÃO --}}
<div id="confirmModal" class="modal-overlay" style="display: none;">
    <div class="modal-backdrop"></div>
    <div class="modal-container">
        <button type="button" class="modal-close-btn" onclick="closeConfirmModal()">
            <i class="bi bi-x"></i>
        </button>
        <div class="modal-content">
            <div class="modal-icon-wrapper">
                <div class="modal-icon-ring"></div>
                <div class="modal-icon">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
            </div>
            <div class="modal-header">
                <h2>Confirmar Finalização</h2>
                <p>Revise as informações antes de confirmar</p>
            </div>
            <div class="modal-body">
                <div class="warning-box">
                    <i class="bi bi-exclamation-octagon-fill"></i>
                    <div>
                        <strong>Atenção</strong>
                        <p>Tem certeza que deseja finalizar este atendimento? Após a confirmação, o paciente será encaminhado e a consulta não poderá mais ser editada por aqui.</p>
                    </div>
                </div>
                <div class="confirmation-checklist">
                    <div class="checklist-item">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Dados do paciente verificados</span>
                    </div>
                    <div class="checklist-item">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Diagnóstico e observações registrados</span>
                    </div>
                    <div class="checklist-item">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Exames e medicamentos revisados</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="modal-btn-cancel" onclick="closeConfirmModal()">
                    <i class="bi bi-arrow-left"></i>
                    Cancelar
                </button>
                <button type="button" class="modal-btn-confirm" onclick="submitForm()">
                    <i class="bi bi-check-lg"></i>
                    Confirmar e Finalizar
                </button>
            </div>
        </div>
    </div>
</div>

{{-- LOADING OVERLAY --}}
<div id="loadingOverlay" class="loading-overlay" style="display: none;">
    <div class="loading-content">
        <div class="loading-spinner">
            <div class="spinner-ring"></div>
            <div class="spinner-ring"></div>
            <div class="spinner-ring"></div>
        </div>
        <h3>Processando...</h3>
        <p>Salvando as informações do prontuário</p>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        initializeForm();
        setupTextareaCounters();

        window.openConfirmModal = function() {
            const modal = document.getElementById('confirmModal');
            if (modal) {
                modal.style.display = 'block';
                setTimeout(() => modal.classList.add('active'), 10);
                document.body.style.overflow = 'hidden';
            }
        }

        window.closeConfirmModal = function() {
            const modal = document.getElementById('confirmModal');
            if (modal) {
                modal.classList.remove('active');
                setTimeout(() => modal.style.display = 'none', 300);
                document.body.style.overflow = '';
            }
        }

        window.submitForm = function() {
            // Mostrar loading
            const loading = document.getElementById('loadingOverlay');
            if (loading) {
                loading.style.display = 'block';
                setTimeout(() => loading.classList.add('active'), 10);
            }

            // Processar dados dos exames
            const examesSelecionados = [];
            document.querySelectorAll('input[name="exames_solicitados[]"]:checked').forEach(ex => {
                examesSelecionados.push(ex.value);
            });
            document.getElementById('examesSolicitadosHidden').value = examesSelecionados.join("\n");

            // Processar dados dos medicamentos
            const medicamentosSelecionados = [];
            document.querySelectorAll('input[name="medicamentos_prescritos[]"]:checked').forEach(md => {
                medicamentosSelecionados.push(md.value);
            });
            document.getElementById('medicamentosPrescritosHidden').value = medicamentosSelecionados.join("\n");

            // Enviar formulário
            setTimeout(() => {
                document.getElementById('prontuarioForm').submit();
            }, 1000);
        }

        // ===== FUNÇÕES DE DETALHES =====
        window.toggleExameDetails = function(exame, isChecked) {
            const detailsId = 'exame-details-' + exame.replace(/\s+/g, '-');
            const detailsElement = document.getElementById(detailsId);

            if (detailsElement) {
                if (isChecked) {
                    detailsElement.style.display = 'block';
                    // Focar no primeiro campo quando abrir
                    const firstInput = detailsElement.querySelector('select, input');
                    if (firstInput) {
                        setTimeout(() => firstInput.focus(), 300);
                    }
                } else {
                    detailsElement.style.display = 'none';
                    // Limpar campos quando desmarcar (opcional)
                    detailsElement.querySelectorAll('input, select').forEach(field => {
                        if (field.type !== 'checkbox') {
                            field.value = '';
                        }
                    });
                }
            }
        }

        window.toggleMedicamentoDetails = function(medicamento, isChecked) {
            const detailsId = 'medicamento-details-' + medicamento.replace(/\s+/g, '-');
            const detailsElement = document.getElementById(detailsId);

            if (detailsElement) {
                if (isChecked) {
                    detailsElement.style.display = 'block';
                    // Focar no primeiro campo quando abrir
                    const firstInput = detailsElement.querySelector('select, input');
                    if (firstInput) {
                        setTimeout(() => firstInput.focus(), 300);
                    }
                } else {
                    detailsElement.style.display = 'none';
                    // Limpar campos quando desmarcar (opcional)
                    detailsElement.querySelectorAll('input, select').forEach(field => {
                        if (field.type !== 'checkbox') {
                            field.value = '';
                        }
                    });
                }
            }
        }

        function filterItems(inputId) {
            const searchTerm = document.getElementById(inputId).value.toLowerCase();
            const isExames = inputId === 'filtroExames';
            const containerId = isExames ? 'listaExames' : 'listaMedicamentos';

            const items = document.querySelectorAll(`#${containerId} .${isExames ? 'exame-item' : 'medicamento-item'}`);

            items.forEach(item => {
                const text = item.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        // ===== CONTADORES =====
        function setupTextareaCounters() {
            const textareas = {
                'observacoes': 'observacoesCounter',
                'descExame': 'descExameCounter',
                'orientacoes': 'orientacoesCounter'
            };

            Object.keys(textareas).forEach(textareaId => {
                const textarea = document.getElementById(textareaId);
                const counter = document.getElementById(textareas[textareaId]);

                if (textarea && counter) {
                    // Atualizar contador inicial
                    counter.textContent = textarea.value.length;

                    // Atualizar contador durante a digitação
                    textarea.addEventListener('input', function() {
                        counter.textContent = this.value.length;
                    });
                }
            });
        }

        // ===== TRIAGEM EXPANDIR/RECOLHER =====
        window.toggleTriagem = function() {
            const container = document.getElementById('triagemContainer');
            const button = document.querySelector('.btn-expand-triagem');

            if (container && button) {
                container.classList.toggle('expanded');
                const icon = button.querySelector('i');
                const text = button.querySelector('span');

                if (container.classList.contains('expanded')) {
                    icon.className = 'bi bi-arrows-collapse';
                    text.textContent = 'Recolher';
                } else {
                    icon.className = 'bi bi-arrows-expand';
                    text.textContent = 'Expandir';
                }
            }
        }

        function initializeForm() {
            // Configurar eventos de busca
            const filtroExames = document.getElementById('filtroExames');
            if (filtroExames) {
                filtroExames.addEventListener('input', function() {
                    filterItems('filtroExames');
                });
            }

            const filtroMedicamentos = document.getElementById('filtroMedicamentos');
            if (filtroMedicamentos) {
                filtroMedicamentos.addEventListener('input', function() {
                    filterItems('filtroMedicamentos');
                });
            }

            // Inicializar detalhes de itens já marcados
            document.querySelectorAll('input[name="exames_solicitados[]"]:checked').forEach(checkbox => {
                toggleExameDetails(checkbox.value, true);
            });

            document.querySelectorAll('input[name="medicamentos_prescritos[]"]:checked').forEach(checkbox => {
                toggleMedicamentoDetails(checkbox.value, true);
            });

            // Event listeners para fechar modal
            document.addEventListener('click', function(event) {
                const modal = document.getElementById('confirmModal');
                if (modal && event.target === modal.querySelector('.modal-backdrop')) {
                    closeConfirmModal();
                }
            });

            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    closeConfirmModal();
                }
            });
        }

        console.log('✅ Sistema de Prontuário carregado com sucesso!');
    });
</script>
@endsection