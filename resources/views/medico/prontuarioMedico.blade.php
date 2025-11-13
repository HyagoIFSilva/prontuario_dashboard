@extends('medico.templates.medicoTemplate')

@section('title', 'Fila de Atendimento')

@section('content')

<link rel="stylesheet" href="{{ asset('css/medico/MedicoProntuario.css') }}">

<main class="main-dashboard">
    <div class="enfermeiro-container">
        
        <!-- Header -->
        <div class="enfermeiro-header">
            <h1>
                <i class="bi bi-journal-medical"></i> 
                    Gestão de Consultas
            </h1>
        </div>

        <!-- Search Bar -->
        <div class="search-filters">
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" 
                       id="searchInput" 
                       placeholder="Pesquisar histórico por nome, CPF..." 
                       onkeyup="filterPatients()">
            </div>
        </div>      

        <!-- Abas para separar pacientes -->
        <div class="tabs-container">
            <div class="tabs">
                <button class="tab-btn active" onclick="switchTab('fila')">
                    <i class="bi bi-clock-history"></i> Pacientes na Fila
                    <span class="badge">{{ $consultas_na_fila->count() }}</span>
                </button>
                <button class="tab-btn" onclick="switchTab('atendidos')">
                    <i class="bi bi-check-circle"></i> Pacientes Atendidos
                    <span class="badge">{{ $consultas_finalizadas->count() }}</span>
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
                                <th>Risco</th>
                                <th>Paciente</th>
                                <th>Hora Chegada</th>
                                <th>Queixa Principal</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($consultas_na_fila as $consulta)
                                @if ($consulta->paciente)
                                    <tr>
                                        <td>
                                            <span class="status-badge status-{{ $consulta->classificacao_risco }}">
                                                {{ ucfirst($consulta->classificacao_risco) }}
                                            </span>
                                        </td>
                                        <td>{{ $consulta->paciente->nomePaciente }}</td>
                                        <td>
                                            {{-- CORREÇÃO: Formato correto para exibir data e hora --}}
                                            {{ $consulta->dataConsulta ? \Carbon\Carbon::parse($consulta->dataConsulta)->format('d/m/Y H:i') : 'N/A' }}
                                        </td>
                                        <td class="queixa-principal-text" title="{{ $consulta->queixa_principal }}">
                                            {{ $consulta->queixa_principal }}
                                        </td>
                                        <td class="actions">
                                            <a href="{{ route('medico.prontuario.edit', $consulta->idConsultaPK) }}"
                                               class="btn-action btn-atender"
                                               title="Atender Paciente">
                                               <i class="bi bi-person-plus-fill"></i>
                                            </a>
                                            <a href="{{ route('medico.visualizarProntuario', $consulta->paciente->idPaciente) }}"
                                               class="btn-action btn-view"
                                               title="Ver Histórico">
                                               <i class="bi bi-clipboard2-pulse"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @else
                                    <tr class="consulta-invalida">
                                        <td>
                                            @if($consulta->classificacao_risco)
                                                <span class="status-badge status-{{ $consulta->classificacao_risco }}">
                                                    {{ ucfirst($consulta->classificacao_risco) }}
                                                </span>
                                            @else
                                                <span>N/A</span>
                                            @endif
                                        </td>
                                        <td colspan="3">
                                            Consulta (ID: {{ $consulta->idConsultaPK }}) com paciente inválido ou não encontrado.
                                        </td>
                                        <td class="actions">-</td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="5" class="no-enfermeiros">
                                        Nenhum paciente aguardando consulta no momento.
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
                                <th>Paciente</th>
                                <th>Data da Consulta</th>
                                <th>Médico</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($consultas_finalizadas as $consulta)
                                @if ($consulta->paciente)
                                    <tr>
                                        <td>{{ $consulta->paciente->nomePaciente }}</td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($consulta->dataConsulta)->format('d/m/Y H:i') }}
                                        </td>
                                        <td>{{ $consulta->nomeMedico ?? 'N/A' }}</td>
                                        <td>
                                            <span class="status-badge status-verde">
                                                Finalizado
                                            </span>
                                        </td>
                                        <td class="actions">
                                            <a href="{{ route('medico.visualizarProntuario', $consulta->paciente->idPaciente) }}"
                                               class="btn-action btn-view"
                                               title="Ver Detalhes">
                                               <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @else
                                    <tr class="consulta-invalida">
                                        <td colspan="5">
                                            Consulta (ID: {{ $consulta->idConsultaPK }}) com paciente inválido ou não encontrado.
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="5" class="no-enfermeiros">
                                        Nenhum paciente atendido recentemente.
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
    function filterPatients() {
        const searchInput = document.getElementById('searchInput').value.toLowerCase();
        const activeTab = document.querySelector('.tab-pane.active');
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

    // Função para alternar entre abas
    function switchTab(tabName) {
        // Remove a classe active de todas as abas e botões
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.remove('active'));
        
        // Adiciona a classe active na aba e botão selecionados
        document.querySelector(`[onclick="switchTab('${tabName}')"]`).classList.add('active');
        document.getElementById(`${tabName}-tab`).classList.add('active');
    }
</script>

@endsection