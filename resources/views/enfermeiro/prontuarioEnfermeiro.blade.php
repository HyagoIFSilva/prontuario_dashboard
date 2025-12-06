@extends('enfermeiro.templates.enfermeiroTemplate')

@section('title', 'Prontuário dos Pacientes')

@section('content')

<link rel="stylesheet" href="{{ asset('css/enfermeiro/prontuario.css') }}">

<main class="main-dashboard">
    <div class="enfermeiro-container">
        
        <!-- Header -->
        <div class="enfermeiro-header">
            <h1>
                <i class="bi bi-journal-medical"></i> 
                Prontuário dos Pacientes
            </h1>
        </div>

        <!-- Search Bar -->
        <div class="search-filters">
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" 
                       id="searchInput" 
                       placeholder="Pesquisar por nome, CPF..." 
                       onkeyup="filterPatients()">
            </div>
        </div>      

        <!-- Abas para separar pacientes -->
        <div class="tabs-container">
            <div class="tabs">
                <button class="tab-btn active" data-tab="fila">
                    <i class="bi bi-clock-history"></i> Pacientes na Fila
                    <span class="badge badge-fila">{{ $pacientes_na_fila->count() }}</span>
                </button>
                <button class="tab-btn" data-tab="atendidos">
                    <i class="bi bi-check-circle"></i> Pacientes Atendidos
                    <span class="badge badge-atendidos">{{ $pacientes_atendidos->count() }}</span>
                </button>
            </div>
        </div>

        <!-- Conteúdo das abas -->
        <div class="tab-content">
            <!-- Aba: Pacientes na Fila -->
            <div id="fila-tab" class="tab-pane active">
                <div class="box-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>CPF</th>
                                <th>Nascimento</th>
                                <th class="status-header">Status</th>
                                <th class="actions-header">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pacientes_na_fila as $paciente)
                                <tr>
                                    <td>{{ $paciente->nomePaciente }}</td>
                                    <td>{{ $paciente->cpfPaciente }}</td>
                                    <td>{{ \Carbon\Carbon::parse($paciente->dataNascPaciente)->format('d/m/Y') }}</td>
                                    <td class="status-cell">
                                        <span class="status-badge status-ativo">
                                            Aguardando Triagem
                                        </span>
                                    </td>
                                    <td class="actions">
                                        <a href="{{ route('enfermeiro.anotacao.create', $paciente->idPaciente) }}"
                                           class="btn-action btn-add-consulta"
                                           title="Realizar Triagem">
                                           <i class="bi bi-file-earmark-text-fill"></i>
                                        </a>
                                        <a href="{{ route('enfermeiro.visualizarProntuario', $paciente->idPaciente) }}"
                                           class="btn-action btn-view"
                                           title="Ver Histórico">
                                           <i class="bi bi-clipboard2-pulse"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="no-enfermeiros">
                                        Nenhum paciente aguardando triagem no momento.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Aba: Pacientes Atendidos -->
            <div id="atendidos-tab" class="tab-pane">
                <div class="box-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>CPF</th>
                                <th>Última Triagem</th>
                                <th>Médico Responsável</th>
                                <th class="actions-header">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pacientes_atendidos as $paciente)
                                @php
                                    // Busca a última anotação de enfermagem deste paciente
                                    $ultimaAnotacao = $paciente->anotacoesEnfermagem->sortByDesc('data_hora')->first();
                                    
                                    // Busca a última consulta do paciente
                                    $ultimaConsulta = $paciente->consultas->sortByDesc('dataConsulta')->first();
                                    $medicoResponsavel = 'N/A';
                                    
                                    if ($ultimaConsulta && $ultimaConsulta->medico) {
                                        $medicoResponsavel = $ultimaConsulta->medico->nomeMedico;
                                    }
                                @endphp
                                <tr>
                                    <td>{{ $paciente->nomePaciente }}</td>
                                    <td>{{ $paciente->cpfPaciente }}</td>
                                    <td>
                                        @if($ultimaAnotacao)
                                            {{ \Carbon\Carbon::parse($ultimaAnotacao->data_hora)->format('d/m/Y H:i') }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>{{ $medicoResponsavel }}</td>
                                    <td class="actions">
                                        <a href="{{ route('enfermeiro.visualizarProntuario', $paciente->idPaciente) }}"
                                           class="btn-action btn-view"
                                           title="Ver Histórico">
                                           <i class="bi bi-eye-fill"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="no-enfermeiros">
                                        Nenhum paciente com atendimento finalizado recentemente.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
// Função para alternar entre abas - VERSÃO CORRIGIDA
function switchTab(tabName) {
    console.log('Tentando mudar para aba:', tabName);
    
    // Remove classe active de todos os botões
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Remove classe active de todos os painéis
    document.querySelectorAll('.tab-pane').forEach(pane => {
        pane.classList.remove('active');
    });
    
    // Adiciona classe active ao botão clicado
    const activeBtn = document.querySelector(`[data-tab="${tabName}"]`);
    if (activeBtn) {
        activeBtn.classList.add('active');
    }
    
    // Adiciona classe active ao painel correspondente
    const activePane = document.getElementById(`${tabName}-tab`);
    if (activePane) {
        activePane.classList.add('active');
    }
    
    // Reaplica o filtro de pesquisa na aba ativa
    filterPatients();
}

// Adiciona event listeners aos botões das abas
document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('.tab-btn');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabName = this.getAttribute('data-tab');
            switchTab(tabName);
        });
    });
});

function filterPatients() {
    const searchInput = document.getElementById('searchInput').value.toLowerCase();
    const activeTab = document.querySelector('.tab-pane.active');
    
    if (!activeTab) return;
    
    const rows = activeTab.querySelectorAll('tbody tr');

    rows.forEach(row => {
        // Ignora a linha de "nenhum paciente encontrado"
        if (row.querySelector('.no-enfermeiros')) {
            row.style.display = searchInput === '' ? '' : 'none';
            return;
        }

        // Pega o texto de todas as células (exceto ações)
        const cells = row.querySelectorAll('td:not(.actions)');
        let textContent = '';
        cells.forEach(cell => {
            textContent += cell.textContent.toLowerCase() + ' ';
        });

        // Mostra ou esconde baseado na busca
        row.style.display = textContent.includes(searchInput) ? '' : 'none';
    });
}

// Função para debug - verificar se os dados estão carregando
document.addEventListener('DOMContentLoaded', function() {
    console.log('Pacientes na fila:', {{ $pacientes_na_fila->count() }});
    console.log('Pacientes atendidos:', {{ $pacientes_atendidos->count() }});
    
    // Verifica o conteúdo das variáveis
    @foreach($pacientes_atendidos as $paciente)
        console.log('Paciente atendido:', '{{ $paciente->nomePaciente }}');
    @endforeach
});
</script>

@endsection