@extends('enfermeiro.templates.enfermeiroTemplate')

@section('title', 'Registrar Triagem')

@section('content')

<body>
    {{-- O CSS está sendo referenciado aqui e deve estar na pasta 'public/css/enfermeiro/cadastrarProntuario.css' --}}
    <link rel="stylesheet" href="{{ asset('css/enfermeiro/cadastrarProntuario.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <main class="main-dashboard">
        <div class="cadastrar-container">
            <div class="cadastrar-header">
                <i class="bi bi-journal-plus icon"></i>
                <h1>Realizar Triagem e Anotação</h1>
            </div>

            <div class="paciente-info">
                <h3><i class="bi bi-person-fill"></i> Informações do Atendimento</h3>

                <div class="info-grid">
                    {{-- Variável $paciente (Passada pelo Controller) --}}
                    <div class="info-item">
                        <strong>Paciente:</strong>
                        <span>{{ $paciente->nomePaciente }}</span>
                    </div>
                    <div class="info-item">
                        <strong>CPF:</strong>
                        <span>{{ $paciente->cpfPaciente }}</span>
                    </div>
                    <div class="info-item-nasc">
                        <strong>Data de Nascimento:</strong>
                        <span>{{ \Carbon\Carbon::parse($paciente->dataNascPaciente)->format('d/m/Y') }}</span>
                    </div>

                    {{-- Variável $enfermeiro (Passada pelo Controller) --}}
                    <div class="info-item">
                        <strong>Enfermeiro(a) Responsável:</strong>
                        <span>{{ $enfermeiro->nomeEnfermeiro ?? (Auth::user()->name ?? 'N/A') }}</span>
                    </div>
                    <div class="info-item">
                        <strong>COREN:</strong>
                        <span>{{ $enfermeiro->corenEnfermeiro ?? 'N/A' }}</span>
                    </div>
                    {{-- NOVO: Exibir a unidade de trabalho do enfermeiro --}}
                    <div class="info-item">
                        <strong>Unidade de Trabalho:</strong>
                        <span>{{ $unidadeEnfermeiro->nomeUnidade ?? 'Não especificada' }}</span>
                    </div>
                    <div class="info-item">
                        <strong>Função:</strong>
                        <span>Enfermeiro(a)</span>
                    </div>

                    {{-- Variável $consulta (Passada pelo Controller) --}}
                    <div class="info-item queixa-principal">
                        <strong>Queixa Principal (Registrada na Recepção):</strong>
                        <span>"{{ $consulta->queixa_principal }}"</span>
                    </div>

                </div>
            </div>

            {{-- ============================================= --}}
            {{-- --- ADICIONADO: Bloco de Erros de Validação --- --}}
            {{-- ============================================= --}}
            @if ($errors->any())
            <div class="alert alert-danger" style="margin: 20px 36px; padding: 15px; background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; border-radius: var(--radius-md);">
                <strong><i class="bi bi-exclamation-triangle-fill"></i> Ops! Algo deu errado:</strong>
                <ul style="margin-top: 10px; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            {{-- ============================================= --}}

            {{-- Rota de POST: enfermeiro.anotacao.store (Definida no web.php) --}}
            <form action="{{ route('enfermeiro.anotacao.store', $paciente->idPaciente) }}" method="POST">
                @csrf

                {{-- ID da Consulta (obrigatório para o controller) --}}
                <input type="hidden" name="idConsulta" value="{{ $consulta->idConsultaPK }}">

                {{-- Tipo de Registro (obrigatório para o controller) --}}
                <input type="hidden" name="tipo_registro" value="Anotação de Triagem">

                <div class="form-section-title">Classificação de Risco (Obrigatório)</div>
                <div class="input-group">
                    <div id="botoes_risco">
                        <input type="radio" name="classificacao_risco" id="risco_vermelho" value="vermelho" class="btn-check" required>
                        <label class="btn btn-vermelho" for="risco_vermelho">VERMELHO (Emergência)</label>

                        <input type="radio" name="classificacao_risco" id="risco_laranja" value="laranja" class="btn-check">
                        <label class="btn btn-laranja" for="risco_laranja">LARANJA (Muito Urgente)</label>

                        <input type="radio" name="classificacao_risco" id="risco_amarelo" value="amarelo" class="btn-check">
                        <label class="btn btn-amarelo" for="risco_amarelo">AMARELO (Urgente)</label>

                        <input type="radio" name="classificacao_risco" id="risco_verde" value="verde" class="btn-check">
                        <label class="btn btn-verde" for="risco_verde">VERDE (Pouco Urgente)</label>

                        <input type="radio" name="classificacao_risco" id="risco_azul" value="azul" class="btn-check">
                        <label class="btn btn-azul" for="risco_azul">AZUL (Não Urgente)</label>
                    </div>
                </div>

                <div class="form-section-title">Dados da Anotação</div>

                {{-- Campo Data e Hora --}}
                <div class="input-group">
                    <label for="data_hora">
                        <i class="bi bi-calendar-check"></i> Data e Hora do Registro
                    </label>
                    <input
                        type="datetime-local"
                        id="data_hora"
                        name="data_hora"
                        value="{{ old('data_hora', \Carbon\Carbon::now()->format('Y-m-d\TH:i')) }}"
                        required
                        class="input-datetime">
                    @error('data_hora')
                    <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-section-title">Sinais Vitais (Opcional)</div>

                {{-- Linha 1 de Sinais Vitais em grid de 3 colunas --}}
                <div class="input-group-row-3">
                    <div class="input-group">
                        <label for="pressao_arterial">
                            <i class="bi bi-heart-pulse"></i> Pressão Arterial (mmHg)
                        </label>
                        <input type="text" id="pressao_arterial" name="pressao_arterial" value="{{ old('pressao_arterial') }}" placeholder="Ex: 120/80">
                    </div>
                    <div class="input-group">
                        <label for="temperatura">
                            <i class="bi bi-thermometer-half"></i> Temperatura (°C)
                        </label>
                        <input type="text" id="temperatura" name="temperatura" value="{{ old('temperatura') }}" placeholder="Ex: 36.5">
                    </div>
                    <div class="input-group">
                        <label for="frequencia_cardiaca">
                            <i class="bi bi-activity"></i> Freq. Cardíaca (bpm)
                        </label>
                        <input type="text" id="frequencia_cardiaca" name="frequencia_cardiaca" value="{{ old('frequencia_cardiaca') }}" placeholder="Ex: 75">
                    </div>
                </div>

                {{-- Linha 2 de Sinais Vitais em grid de 3 colunas --}}
                <div class="input-group-row-3">
                    <div class="input-group">
                        <label for="frequencia_respiratoria">
                            <i class="bi bi-lungs"></i> Freq. Respiratória (rpm)
                        </label>
                        <input type="text" id="frequencia_respiratoria" name="frequencia_respiratoria" value="{{ old('frequencia_respiratoria') }}" placeholder="Ex: 18">
                    </div>
                    <div class="input-group">
                        <label for="saturacao">
                            <i class="bi bi-droplet-half"></i> Saturação (SpO₂)
                        </label>
                        <input type="text" id="saturacao" name="saturacao" value="{{ old('saturacao') }}" placeholder="Ex: 98%">
                    </div>
                    <div class="input-group">
                        <label for="dor">
                            <i class="bi bi-bandaid"></i> Escala de Dor (0-10)
                        </label>
                        <input type="number" id="dor" name="dor" value="{{ old('dor') }}" min="0" max="10" placeholder="Ex: 5">
                    </div>
                </div>

                <div class="form-section-title">Detalhes da Ocorrência</div>

                {{-- Alergias com Tipo e Severidade --}}
                <div class="input-group">
                    <label>
                        <i class="bi bi-exclamation-triangle-fill"></i> Alergias Identificadas
                    </label>
                    <input type="text" id="filtroAlergias" class="input-filtro" placeholder="Pesquisar alergia...">

                    <div id="listaAlergias" class="checkbox-list">
                        @php
                
                        $alergias = [
                            'Ácaros', 'Acetato de cobre', 'Adesivos e colas', 'Algodão', 'Alho', 
                            'Alergia ao calor', 'Alergia ao frio', 'Alergia ao sol (fotossensibilidade)', 
                            'Alergia ao exercício físico', 'Alergia à água (urticária aquagênica)', 
                            'Alergia à pressão na pele (dermografismo)', 'Alergia à vibração', 'Alface', 
                            'Alergia ao látex', 'Alergia ao níquel', 'Alergia ao ouro', 'Alergia à prata', 
                            'Alergia ao cobalto', 'Alergia ao cromo', 'Alergia ao mercúrio', 
                            'Alergia à formaldeído', 'Alergia a inseticidas', 'Alergia a perfumes', 
                            'Alergia a conservantes', 'Alergia a corantes', 'Alergia a produtos de limpeza', 
                            'Alergia a cloro', 'Alergia a poeira', 'Alergia a mofo/bolor', 'Alergia a fungos', 
                            'Alergia a penas de pássaro', 'Alergia a pelo de animal (cão, gato, etc.)', 
                            'Alergia a pólen', 'Alergia a picada de inseto (abelha, vespa, etc.)', 
                            'Alergia a veneno de aranha/cobra', 'Alergia a anestésicos', 'Alergia a antibióticos', 
                            'Alergia a anti-inflamatórios (AINEs)', 'Alergia a aspirina', 'Alergia a dipirona', 
                            'Alergia a ibuprofeno', 'Alergia a penicilina', 'Alergia a amoxicilina', 
                            'Alergia a cefalexina', 'Alergia a azitromicina', 'Alergia a sulfonamidas (sulfa)', 
                            'Alergia a tetraciclina', 'Alergia a quinolonas', 'Alergia a codeína', 
                            'Alergia a morfina', 'Alergia a meios de contraste iodados', 'Alergia a insulina', 
                            'Alergia a heparina', 'Alergia a vacinas', 'Alergia a anticoncepcionais', 
                            'Alergia a corticoides', 'Alergia a quimioterápicos', 'Alergia a iodo', 
                            'Alergia a glúten (Doença Celíaca)', 'Alergia a lactose (Intolerância)', 
                            'Alergia a proteína do leite de vaca (caseína)', 'Alergia a ovos', 'Alergia a soja', 
                            'Alergia a amendoim', 'Alergia a castanhas (castanha-do-pará, nozes, amêndoas, etc.)', 
                            'Alergia a sementes (gergelim, girassol)', 'Alergia a mariscos (camarão, lagosta, etc.)', 
                            'Alergia a peixes (salmão, bacalhau, etc.)', 'Alergia a frutas cítricas (laranja, limão, etc.)', 
                            'Alergia a frutas (maçã, pêra, pêssego, morango, kiwi, etc.)', 
                            'Alergia a vegetais (tomate, cenoura, cebola, etc.)', 
                            'Alergia a leguminosas (feijão, lentilha, grão-de-bico)', 'Alergia a carnes (boi, frango, porco)', 
                            'Alergia a milho', 'Alergia a arroz', 'Alergia a aveia', 'Alergia a cevada', 'Alergia a centeio', 
                            'Alergia a chocolate', 'Alergia a café', 'Alergia a chá', 'Alergia a cogumelos', 
                            'Alergia a corantes alimentares (tartrazina, etc.)', 
                            'Alergia a conservantes alimentares (benzoatos, nitritos, sulfitos)', 
                            'Alergia a glutamato monossódico', 'Alergia a edulcorantes (aspartame, sacarina, etc.)', 
                            'Alergia a picada de mosquito', 'Alergia a picada de formiga', 'Alergia a picada de abelha', 
                            'Alergia a picada de vespa', 'Alergia a veneno de aranha', 'Alergia a veneno de cobra', 
                            'Alergia a lã', 'Alergia a seda', 'Alergia a esmalte de unha', 'Alergia a tintas de cabelo', 
                            'Alergia a loções e cremes', 'Alergia a desodorantes', 'Alergia a sabonetes', 
                            'Alergia a detergentes', 'Alergia a amônia', 'Alergia a alvejantes', 
                            'Alergia a fumaça de cigarro', 'Alergia a poluição do ar', 'Alergia a neoprene', 
                            'Alergia a resinas', 'Alergia a borracha', 'Alternaria (fungo)', 'Bacalhau', 'Beterraba', 
                            'Bolor (Mofo)', 'Brócolis', 'Camarão', 'Carne de boi', 'Carneiro', 'Castanha de caju', 
                            'Castanha-do-pará', 'Cebola', 'Cefalexina', 'Cenoura', 'Chocolate', 'Cobalto', 'Coco', 
                            'Cogumelos (Champignon)', 'Corticoide', 'Cromo', 'Dipirona', 'Diclofenaco', 'Espinafre', 
                            'Feijão', 'Fragrâncias (Cosméticos)', 'Frango', 'Frutos do mar', 'Fungos', 'Glúten', 
                            'Glutamato monossódico', 'Grão-de-bico', 'Ibuprofeno', 'Insulina', 'Iodo', 'Kiwi', 
                            'Lactose', 'Lagosta', 'Látex', 'Lentilha', 'Lula', 'Maçã', 'Mariscos', 'Melancia', 
                            'Melão', 'Mexilhão', 'Milho', 'Morango', 'Níquel', 'Nozes', 'Ostras', 'Ovos', 'Pêra', 
                            'Pêssego', 'Pelo de animal', 'Penicilina', 'Pólen', 'Polvo', 'Porco', 
                            'Proteína do leite de vaca', 'Salmão', 'Semente de gergelim', 'Siri', 'Soja', 
                            'Sulfonamidas (Sulfa)', 'Tomate', 'Vespas', 'Outros'
                        ];

                        $tiposAlergia = [
                            'Alimentar',
                            'Contato',
                            'Cutânea',
                            'Física',
                            'Fúngica (Fungos/Mofo)',
                            'Idiopática (Causa Desconhecida)',
                            'Insetos (Picadas/Mordidas)',
                            'Medicamentosa',
                            'Ocupacional',
                            'Ocular',
                            'Outra',
                            'Respiratória',
                            'Veneno Animal'
                        ];

                        $severidadesAlergia = [
                        'Baixa', 'Média', 'Alta'
                        ];
                        @endphp
                        @foreach($alergias as $alergia)
                        <div class="alergia-item" data-alergia="{{ $alergia }}">
                            <label class="checkbox-item">
                                <input type="checkbox" name="alergias[]" value="{{ $alergia }}"
                                    {{ is_array(old('alergias')) && in_array($alergia, old('alergias')) ? 'checked' : '' }}
                                    onchange="toggleAlergiaDetails('{{ $alergia }}', this.checked)">
                                {{ $alergia }}
                            </label>

                            <div class="alergia-details" id="alergia-details-{{ str_replace(' ', '-', $alergia) }}" style="display: none;">
                                <div class="alergia-detail-row">
                                    <label for="tipo-{{ str_replace(' ', '-', $alergia) }}">Tipo:</label>
                                    <select name="alergia_tipos[{{ $alergia }}]" id="tipo-{{ str_replace(' ', '-', $alergia) }}" class="alergia-select">
                                        @foreach($tiposAlergia as $tipo)
                                        <option value="{{ $tipo }}" {{ (old('alergia_tipos')[$alergia] ?? null) == $tipo ? 'selected' : '' }}>{{ $tipo }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="alergia-detail-row">
                                    <label for="severidade-{{ str_replace(' ', '-', $alergia) }}">Severidade:</label>
                                    <select name="alergia_severidades[{{ $alergia }}]" id="severidade-{{ str_replace(' ', '-', $alergia) }}" class="alergia-select">
                                        @foreach($severidadesAlergia as $severidade)
                                        <option value="{{ $severidade }}" {{ (old('alergia_severidades')[$alergia] ?? null) == $severidade ? 'selected' : '' }}>{{ $severidade }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @error('alergias')
                    <span class="error" style="color: var(--error-red); font-size: 0.85rem; font-weight: 600; margin-top: 6px;">{{ $message }}</span>
                    @enderror
                </div>



                {{-- Campo Descrição/Evolução --}}
                <div class="input-group">
                    <label for="descricao">
                        <i class="bi bi-file-text"></i> Descrição da Anotação / Evolução *
                    </label>
                    <textarea
                        id="descricao"
                        name="descricao"
                        rows="8"
                        placeholder="Descreva detalhadamente a ocorrência, evolução do paciente e quaisquer observações relevantes."
                        required>{{ old('descricao') }}</textarea>
                    @error('descricao')
                    <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- CAMPO DE SELEÇÃO DE UNIDADE AJUSTADO E PRÉ-SELECIONADO --}}
                <div class="input-group">
                    <label for="unidade_atendimento">
                        <i class="bi bi-hospital"></i> Unidade de Atendimento *
                    </label>
                    <select id="unidade_atendimento" name="unidade_atendimento" class="input-select" required>
                        <option value="" disabled>Selecione a unidade</option>
                        {{-- Percorre a coleção de unidades e cria uma opção para cada uma --}}
                        @foreach($unidades as $unidade)
                        <option value="{{ $unidade->idUnidadePK }}"
                            {{ 
                                (old('unidade_atendimento') == $unidade->idUnidadePK) || 
                                (isset($unidadeEnfermeiro) && $unidade->idUnidadePK == $unidadeEnfermeiro->idUnidadePK) 
                                ? 'selected' : '' 
                            }}>
                            {{ $unidade->nomeUnidade }}
                        </option>
                        @endforeach
                    </select>
                    @error('unidade_atendimento')
                    <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="button-group">
                    {{-- Rota de Cancelar: enfermeiro.prontuario.index (Definida no web.php) --}}
                    <a href="{{ route('enfermeiro.prontuario') }}" class="btn-cancelar">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </a>
                    <button type="submit" class="save-button">
                        <i class="bi bi-check-circle"></i> Salvar Triagem e Encaminhar
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>

<script>
    // Filtro de pesquisa para Alergias
    document.getElementById('filtroAlergias').addEventListener('input', function() {
        const termo = this.value.toLowerCase();
        document.querySelectorAll('#listaAlergias .alergia-item').forEach(item => {
            const texto = item.textContent.toLowerCase();
            item.style.display = texto.includes(termo) ? '' : 'none';
        });
    });

    // Função para mostrar/ocultar detalhes da alergia
    function toggleAlergiaDetails(alergia, isChecked) {
        const detailsId = 'alergia-details-' + alergia.replace(/\s+/g, '-');
        const detailsElement = document.getElementById(detailsId);

        if (detailsElement) {
            detailsElement.style.display = isChecked ? 'block' : 'none';
        }
    }

    // Inicializar detalhes das alergias marcadas (se houver) ao carregar a página
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('input[name="alergias[]"]');

        checkboxes.forEach(function(checkbox) {
            if (checkbox.checked) {
                const alergia = checkbox.value;
                toggleAlergiaDetails(alergia, true);
            }
        });
    });
</script>
@endsection