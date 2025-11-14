@extends('medico.templates.medicoTemplate')

@section('title', isset($consulta) ? 'Finalizar Atendimento' : 'Cadastrar Consulta')

@section('content')
<link rel="stylesheet" href="{{ asset('css/medico/cadastrarProntuario.css') }}">

<main class="main-dashboard">
    <div class="cadastrar-container">
        <div class="cadastrar-header">
            @if (isset($consulta))
            <i class="bi bi-journal-check icon"></i>
            <h1>Finalizar Atendimento</h1>
            @else
            <i class="bi bi-journal-medical icon"></i>
            <h1>Cadastrar Consulta ao Prontuário</h1>
            @endif
        </div>

        {{-- Informações do Paciente e Médico --}}
        <div class="paciente-info">
            <h3><i class="bi bi-person-fill"></i> Informações do Atendimento</h3>
            <div class="info-grid">
                <div class="info-item">
                    <strong>Paciente:</strong>
                    <span>{{ $paciente->nomePaciente }}</span>
                </div>
                <div class="info-item">
                    <strong>CPF:</strong>
                    <span>{{ $paciente->cpfPaciente }}</span>
                </div>
                <div class="info-item">
                    <strong>Data de Nascimento:</strong>
                    <span>{{ \Carbon\Carbon::parse($paciente->dataNascPaciente)->format('d/m/Y') }}</span>
                </div>
                <div class="info-item">
                    <strong>Médico:</strong>
                    <span>{{ $medico->nomeMedico }}</span>
                </div>
                <div class="info-item">
                    <strong>CRM:</strong>
                    <span>{{ $medico->crmMedico }}</span>
                </div>
                <div class="info-item">
                    <strong>Especialidade:</strong>
                    <span>{{ $medico->especialidadeMedico }}</span>
                </div>
                <div class="info-item">
                    <strong>Data e Hora da Consulta:</strong>
                    <span>{{ isset($consulta) && $consulta->dataConsulta ? \Carbon\Carbon::parse($consulta->dataConsulta)->format('d/m/Y H:i') : \Carbon\Carbon::now()->format('d/m/Y H:i') }}</span>
                </div>
                <div class="info-item">
                    <strong>Unidade de Atendimento:</strong>
                    <span>{{ $unidadeMedico->nomeUnidade ?? 'Não especificada' }}</span>
                </div>
                @if (isset($consulta) && $consulta->classificacao_risco)
                <div class="info-item">
                    <strong>Classificação de Risco:</strong>
                    <span class="status-badge status-{{ $consulta->classificacao_risco }}" style="display:inline-block; margin-top: 5px;">
                        {{ ucfirst($consulta->classificacao_risco) }}
                    </span>
                </div>
                @endif
            </div>
        </div>

        {{-- Anotações da Enfermagem --}}
        @if (isset($consulta) && $anotacoesEnfermagem && $anotacoesEnfermagem->isNotEmpty())
        <div class="anotacoes-enfermagem-container">
            <h3><i class="bi bi-heart-pulse-fill"></i> Triagem da Enfermagem</h3>
            @foreach ($anotacoesEnfermagem as $anotacao)
            <div class="anotacao-item">
                <div class="anotacao-header">
                    <span>Registrado em: <strong>{{ \Carbon\Carbon::parse($anotacao->data_hora)->format('d/m/Y H:i') }}</strong></span>
                    <span>Por: <strong>{{ $anotacao->enfermeiro->nomeEnfermeiro ?? 'Enfermeiro(a)' }}</strong></span>
                </div>
                <div class="anotacao-body">
                    @if ($anotacao->descricao)
                    <p><strong>Descrição:</strong> {{ $anotacao->descricao }}</p>
                    @endif
                    @if ($anotacao->alergias)
                    <p><strong>Alergias:</strong> {{ $anotacao->alergias }}</p>
                    @endif
                    @if ($anotacao->medicacoes_ministradas)
                    <p><strong>Medicações/Procedimentos:</strong> {{ $anotacao->medicacoes_ministradas }}</p>
                    @endif

                    <div class="sinais-vitais-grid">
                        @if($anotacao->pressao_arterial) <div class="sinal-vital-item"><strong>PA:</strong> {{ $anotacao->pressao_arterial }} mmHg</div> @endif
                        @if($anotacao->temperatura) <div class="sinal-vital-item"><strong>Temp:</strong> {{ $anotacao->temperatura }} °C</div> @endif
                        @if($anotacao->frequencia_cardiaca) <div class="sinal-vital-item"><strong>FC:</strong> {{ $anotacao->frequencia_cardiaca }} bpm</div> @endif
                        @if($anotacao->frequencia_respiratoria) <div class="sinal-vital-item"><strong>FR:</strong> {{ $anotacao->frequencia_respiratoria }} rpm</div> @endif
                        @if($anotacao->saturacao) <div class="sinal-vital-item"><strong>SpO₂:</strong> {{ $anotacao->saturacao }} %</div> @endif
                        @if($anotacao->dor !== null) <div class="sinal-vital-item"><strong>Dor:</strong> {{ $anotacao->dor }}/10</div> @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        {{-- FORMULÁRIO DE CONSULTA --}}
        <form action="{{ isset($consulta) ? route('medico.prontuario.update', $consulta->idConsultaPK) : route('medico.prontuario.store', $paciente->idPaciente) }}" method="POST" id="prontuarioForm">
            @csrf
            @if (isset($consulta))
            @method('PUT')
            @endif

            <div class="form-section-title">Dados da Consulta Médica</div>

            {{-- Campo Data e Hora --}}
            <div class="input-group">
                <label for="data_hora">
                    <i class="bi bi-calendar-check"></i> Data e Hora do Registro
                </label>
                <input
                    type="datetime-local"
                    id="dataConsulta"
                    name="dataConsulta"
                    value="{{ old('dataConsulta', \Carbon\Carbon::now()->format('Y-m-d\TH:i')) }}"
                    required
                    class="input-datetime">
                @error('data_hora')
                <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="input-group">
                <label for="observacoes">
                    <i class="bi bi-file-text"></i> Observações Médicas / Diagnóstico
                </label>
                <textarea
                    id="observacoes"
                    name="observacoes"
                    rows="4"
                    placeholder="Descreva o diagnóstico, evolução e observações médicas...">{{ old('observacoes', $consulta->observacoes ?? '') }}</textarea>
                @error('observacoes')
                <span class="error">{{ $message }}</span>
                @enderror
            </div>

            {{-- EXAMES COM CHECKBOX E TIPO + CAMPO DE DESCRIÇÃO --}}
            <div class="input-group">
                <label>
                    <i class="bi bi-clipboard2-pulse"></i> Exames Solicitados
                </label>
                <input type="text" id="filtroExames" class="input-filtro" placeholder="Pesquisar exame...">
                <div id="listaExames" class="checkbox-list">
                    @php
                    $tiposExame = [
                    'Análises Clínicas', 'Imagem', 'Endoscopia', 'Biopsia', 'Testes Funcionais', 'Outros'
                    ];

                    $exames = [
                    'Hemograma Completo', 'Glicemia de Jejum', 'Colesterol Total e Frações', 'Triglicerídeos',
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
                    'Teste de Função Gonadal', 'Teste de Função Pancreática', 'Outros'
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
                                <label for="tipo-exame-{{ str_replace(' ', '-', $exame) }}">Tipo de Exame:</label>
                                <select name="exame_tipos[{{ $exame }}]" id="tipo-exame-{{ str_replace(' ', '-', $exame) }}" class="exame-select">
                                    @foreach($tiposExame as $tipo)
                                    <option value="{{ $tipo }}">{{ $tipo }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @error('exames_solicitados')
                <span class="error">{{ $message }}</span>
                @enderror
            </div>

            {{-- NOVO CAMPO: DESCRIÇÃO DOS EXAMES (descExame) --}}
            <div class="input-group">
                <label for="descExame">
                    <i class="bi bi-file-text-fill"></i> Descrição/Justificativa dos Exames
                </label>
                <textarea
                    id="descExame"
                    name="descExame"
                    rows="6"
                    placeholder="Descreva a justificativa clínica, indicações, suspeitas diagnósticas ou observações relevantes para os exames solicitados...">{{ old('descExame', $consulta->descExame ?? '') }}</textarea>
                <small class="input-helper">
                    <i class="bi bi-info-circle"></i>
                    Campo para registrar informações complementares sobre os exames solicitados
                </small>
                @error('descExame')
                <span class="error">{{ $message }}</span>
                @enderror
            </div>

            {{-- MEDICAMENTOS COM CHECKBOX E DETALHES --}}
            <div class="input-group">
                <label>
                    <i class="bi bi-capsule-pill"></i> Medicamentos Prescritos
                </label>

                <input type="text" id="filtroMedicamentos" class="input-filtro" placeholder="Pesquisar medicamento...">

                <div id="listaMedicamentos" class="checkbox-list">
                    @php
                    $tiposMedicamento = [
                    'Analgésico', 'Anti-inflamatório', 'Antibiótico', 'Antiviral', 'Antifúngico',
                    'Antihistamínico', 'Broncodilatador', 'Corticosteroide', 'Anti-hipertensivo',
                    'Diurético', 'Antidiabético', 'Anticonvulsivante', 'Antidepressivo', 'Ansiolítico',
                    'Vitamina', 'Suplemento', 'Outros'
                    ];

                    $medicamentos = [
                    'Paracetamol', 'Dipirona', 'Ibuprofeno', 'Amoxicilina', 'Azitromicina',
                    'Cefalexina', 'Ciprofloxacino', 'Omeprazol', 'Pantoprazol', 'Ranitidina',
                    'Metoclopramida', 'Bromoprida', 'Domperidona', 'Diclofenaco', 'Nimesulida',
                    'Prednisona', 'Dexametasona', 'Loratadina', 'Desloratadina', 'Cetirizina',
                    'Captopril', 'Losartana', 'Enalapril', 'Sinvastatina', 'Atorvastatina',
                    'Metformina', 'Glibenclamida', 'Levotiroxina', 'Sulfato Ferroso',
                    'Vitamina C', 'Complexo B', 'Soro Fisiológico', 'Glicose 5%', 'Ringer Lactato',
                    'Outros'
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
                                <label for="tipo-medicamento-{{ str_replace(' ', '-', $medicamento) }}">Tipo:</label>
                                <select name="medicamento_tipos[{{ $medicamento }}]" id="tipo-medicamento-{{ str_replace(' ', '-', $medicamento) }}" class="medicamento-select">
                                    @foreach($tiposMedicamento as $tipo)
                                    <option value="{{ $tipo }}">{{ $tipo }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="medicamento-detail-row">
                                <label for="dosagem-medicamento-{{ str_replace(' ', '-', $medicamento) }}">Dosagem:</label>
                                <input type="text" name="medicamento_dosagens[{{ $medicamento }}]" id="dosagem-medicamento-{{ str_replace(' ', '-', $medicamento) }}" class="medicamento-input" placeholder="Ex: 500mg">
                            </div>

                            <div class="medicamento-detail-row">
                                <label for="frequencia-medicamento-{{ str_replace(' ', '-', $medicamento) }}">Frequência:</label>
                                <input type="text" name="medicamento_frequencias[{{ $medicamento }}]" id="frequencia-medicamento-{{ str_replace(' ', '-', $medicamento) }}" class="medicamento-input" placeholder="Ex: 8/8 horas">
                            </div>

                            <div class="medicamento-detail-row">
                                <label for="periodo-medicamento-{{ str_replace(' ', '-', $medicamento) }}">Período:</label>
                                <input type="text" name="medicamento_periodos[{{ $medicamento }}]" id="periodo-medicamento-{{ str_replace(' ', '-', $medicamento) }}" class="medicamento-input" placeholder="Ex: 5 dias">
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @error('medicamentos_prescritos')
                <span class="error">{{ $message }}</span>
                @enderror
            </div>
            <!-- Hidden para gravar na tbConsulta -->
            <input type="hidden" id="examesSolicitadosHidden" name="examesSolicitados">
            <input type="hidden" id="medicamentosPrescritosHidden" name="medicamentosPrescritos">


            {{-- BOTÕES DE AÇÃO --}}
            <div class="button-group">
                <a href="{{ route('medico.prontuario') }}" class="btn-cancelar">
                    <i class="bi bi-x-circle"></i> {{ isset($consulta) ? 'Voltar para Fila' : 'Cancelar' }}
                </a>

                {{-- BOTÃO ALTERADO PARA ABRIR O MODAL DE CONFIRMAÇÃO --}}
                <button type="button" class="save-button" onclick="openConfirmModal()">
                    <i class="bi bi-check-circle"></i> {{ isset($consulta) ? 'Finalizar Atendimento' : 'Salvar Prontuário' }}
                </button>
            </div>
        </form>
    </div>
</main>

{{-- MODAL DE CONFIRMAÇÃO --}}
<div id="confirmModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <h2>Confirmar Finalização</h2>
        </div>
        <div class="modal-body">
            <p>Tem certeza que deseja finalizar este atendimento? Após a confirmação, o paciente será encaminhado e a consulta não poderá mais ser editada por aqui.</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-cancel-modal" onclick="closeConfirmModal()">Cancelar</button>
            <button type="button" class="btn-confirm-modal" onclick="submitForm()">Confirmar e Finalizar</button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ===== FUNÇÕES DO MODAL DE CONFIRMAÇÃO =====
        window.openConfirmModal = function() {
            const modal = document.getElementById('confirmModal');
            if (modal) {
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        }

        window.closeConfirmModal = function() {
            const modal = document.getElementById('confirmModal');
            if (modal) {
                modal.classList.remove('active');
                document.body.style.overflow = '';
            }
        }

        window.submitForm = function() {

            // Montar EXAMES
            const examesSelecionados = [];
            document.querySelectorAll('input[name="exames_solicitados[]"]:checked')
                .forEach(ex => examesSelecionados.push(ex.value));

            document.getElementById('examesSolicitadosHidden').value =
                examesSelecionados.join("\n");


            // Montar MEDICAMENTOS
            const medicamentosSelecionados = [];
            document.querySelectorAll('input[name="medicamentos_prescritos[]"]:checked')
                .forEach(md => medicamentosSelecionados.push(md.value));

            document.getElementById('medicamentosPrescritosHidden').value =
                medicamentosSelecionados.join("\n");


            // Enviar normalmente
            closeConfirmModal();
            document.getElementById('prontuarioForm').submit();
        }


        // ===== FUNÇÕES PARA DETALHES DE EXAMES E MEDICAMENTOS =====
        window.toggleExameDetails = function(exame, isChecked) {
            const detailsId = 'exame-details-' + exame.replace(/\s+/g, '-');
            const detailsElement = document.getElementById(detailsId);

            if (detailsElement) {
                detailsElement.style.display = isChecked ? 'block' : 'none';
            }
        }

        window.toggleMedicamentoDetails = function(medicamento, isChecked) {
            const detailsId = 'medicamento-details-' + medicamento.replace(/\s+/g, '-');
            const detailsElement = document.getElementById(detailsId);

            if (detailsElement) {
                detailsElement.style.display = isChecked ? 'block' : 'none';
            }
        }

        // ===== FILTROS =====
        const filtroMedicamentos = document.getElementById('filtroMedicamentos');
        if (filtroMedicamentos) {
            filtroMedicamentos.addEventListener('input', function() {
                const termo = this.value.toLowerCase();
                document.querySelectorAll('#listaMedicamentos .medicamento-item').forEach(item => {
                    const texto = item.textContent.toLowerCase();
                    item.style.display = texto.includes(termo) ? '' : 'none';
                });
            });
        }

        const filtroExames = document.getElementById('filtroExames');
        if (filtroExames) {
            filtroExames.addEventListener('input', function() {
                const termo = this.value.toLowerCase();
                document.querySelectorAll('#listaExames .exame-item').forEach(item => {
                    const texto = item.textContent.toLowerCase();
                    item.style.display = texto.includes(termo) ? '' : 'none';
                });
            });
        }

        // Inicializa detalhes de exames e medicamentos marcados (se houver)
        const examesMarcados = document.querySelectorAll('input[name="exames_solicitados[]"]:checked');
        examesMarcados.forEach(checkbox => {
            toggleExameDetails(checkbox.value, true);
        });

        const medicamentosMarcados = document.querySelectorAll('input[name="medicamentos_prescritos[]"]:checked');
        medicamentosMarcados.forEach(checkbox => {
            toggleMedicamentoDetails(checkbox.value, true);
        });

        // Event listeners para fechar modal
        document.addEventListener('click', function(event) {
            const modal = document.getElementById('confirmModal');
            if (modal && event.target === modal) {
                closeConfirmModal();
            }
        });

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeConfirmModal();
            }
        });

        console.log('✅ Sistema de Prontuário carregado com sucesso!');
    });
</script>
@endsection