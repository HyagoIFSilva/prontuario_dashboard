@extends('admin.templates.admTemplate')

@section('content')
@php
use Carbon\Carbon;
@endphp

<link rel="stylesheet" href="{{ asset('css/admin/pacientes.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

@php $admin = auth()->guard('admin')->user(); @endphp


<main class="main-dashboard">
    <div class="patients-container">
        <div class="patients-header">
            <h1><i class="bi bi-people-fill"></i> Gerenciamento de Pacientes</h1>
      
            <a href="{{ route('admin.pacientes.create') }}" class="btn-add-paciente">
                <i class="bi bi-plus-circle"></i> Cadastrar Paciente
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success" style="margin-bottom: 20px;">
                {{ session('success') }}
            </div>
        @endif

        {{-- BARRA DE PESQUISA E FILTROS (Com base no exemplo do enfermeiro) --}}
        <div class="search-filters">
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" id="searchInput" placeholder="Pesquisar por nome, CPF ou SUS..." onkeyup="filterPacientes()">
            </div>
            
            {{-- FILTRO DE STATUS (Custom Select) --}}
            <div class="custom-select" id="customStatus">
                <div class="selected">Status</div>
                <div class="options">
                    <div data-value="">Todos</div>
                    <div data-value="ativo">Ativo</div>
                    <div data-value="inativo">Inativo</div>
                </div>
            </div>
            <input type="hidden" id="filterStatus" value="">
        </div>
        
        <div class="table-wrapper">
            <table class="patients-table">
                <thead>
                    <tr>
                        <th>NOME</th>
                        <th>CPF</th>
                        <th>IDADE</th>
                        <th>CARTÃO SUS</th>
                        <th>STATUS</th>
                        <th>AÇÕES</th>
                    </tr>
                </thead>
           <tbody>
    @forelse($pacientes as $paciente)
        @php
            $idade = $paciente->dataNascPaciente ? Carbon::parse($paciente->dataNascPaciente)->age : 'N/A';
            // statusPaciente é boolean: true = ativo, false = inativo
            $statusAtivo = $paciente->statusPaciente;
            $statusTexto = $statusAtivo ? 'ativo' : 'inativo';
            $nomeEscapado = json_encode($paciente->nomePaciente);
        @endphp
        <tr data-status="{{ $statusTexto }}" 
            data-name="{{ $paciente->nomePaciente }}" 
            data-cpf="{{ $paciente->cpfPaciente }}" 
            data-sus="{{ $paciente->cartaoSusPaciente ?? '' }}">
            <td>{{ $paciente->nomePaciente }}</td>
            <td>{{ $paciente->cpfPaciente }}</td>
            <td>{{ $idade }} anos</td>
            <td>{{ $paciente->cartaoSusPaciente ?? 'N/A' }}</td>
            <td>
                <span class="status-badge status-{{ $statusTexto }}">
                    {{ ucfirst($statusTexto) }}
                </span>
            </td>
            <td>
                <div class="action-buttons">
                    {{-- Botão de Edição --}}
                    <a href="{{ route('admin.pacientes.edit', $paciente->idPaciente) }}" 
                        class="btn-action btn-edit" 
                        title="Editar">
                        <i class="bi bi-pencil"></i>
                    </a>
                    
                    {{-- Botão de Alterar Status --}}
                    <a href="#"
                        onclick="openStatusPacienteModal('{{ $paciente->idPaciente }}', {{ $nomeEscapado }}, '{{ $statusTexto }}')"
                        class="btn-action" 
                        title="{{ $statusAtivo ? 'Desativar' : 'Ativar' }}">
                        @if($statusAtivo)
                            <i class="bi bi-slash-circle text-danger"></i>
                        @else
                            <i class="bi bi-check-circle text-success"></i>
                        @endif
                    </a>

                    {{-- REMOVEMOS O BOTÃO DE EXCLUIR DEFINITIVAMENTE --}}
                </div>
            </td>
        </tr>
    @empty
        <tr data-status="empty-list">
            <td colspan="6" class="no-patients">Nenhum paciente encontrado.</td>
        </tr>
    @endforelse
</tbody>
            </table>
        </div>

    </div>
</main>

{{-- MODAL DE EXCLUSÃO (Soft Delete) FOI REMOVIDO --}}

{{-- MODAL DE ALTERAÇÃO DE STATUS (EXISTENTE) --}}
<div id="statusPacienteModal" class="modal-overlay modal-confirm-edit">
    <div class="modal-content">
        <div class="modal-header">
            <i class="bi bi-toggle-on" style="color: #0618b9;"></i>
            <h2>Alterar Status</h2>
        </div>
        
        <p>Tem certeza que deseja <span id="statusAction"></span> o(a) paciente <span id="statusPacienteNome"></span>?</p>

        <form id="statusPacienteForm" method="POST">
            @csrf
            <div class="modal-buttons">
                <button type="button" onclick="closeStatusPacienteModal()" class="btn-cancelar">Cancelar</button>
                <button type="submit" class="btn-excluir">Sim, <span id="confirmStatusText"></span></button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL DE SUCESSO UNIFICADO (EXISTENTE) --}}
<div id="statusSuccessModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <i class="bi bi-check-circle-fill"></i>
            <h2>Sucesso!</h2>
        </div>
        
        <p id="successMessage"></p>

        <div class="modal-buttons">
            <button type="button" onclick="closeSuccessModal()" class="btn-excluir">Fechar</button>
        </div>
    </div>
</div>

<script>
    // ------------------------------------------
    // LÓGICA DE FILTRAGEM (Baseada no seu exemplo)
    // ------------------------------------------

    function filterPacientes() {
        const searchInput = document.getElementById('searchInput').value.toLowerCase();
        const filterStatus = document.getElementById('filterStatus').value;

        const rows = document.querySelectorAll('.patients-table tbody tr');
        let visibleRowsCount = 0;
        let emptyRow = null;

        rows.forEach(row => {
            if (row.dataset.status === 'empty-list') {
                emptyRow = row;
                row.style.display = 'none';
                return;
            }

            const name = row.dataset.name.toLowerCase();
            const cpf = row.dataset.cpf.toLowerCase();
            const sus = row.dataset.sus.toLowerCase();
            const status = row.dataset.status;

            const matchesSearch = name.includes(searchInput) || cpf.includes(searchInput) || sus.includes(searchInput);
            const matchesStatus = !filterStatus || status === filterStatus;

            if (matchesSearch && matchesStatus) {
                row.style.display = '';
                visibleRowsCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        if (emptyRow) {
            if (visibleRowsCount === 0) {
                emptyRow.style.display = ''; 
            } else {
                emptyRow.style.display = 'none';
            }
        }
    }

    // Inicializa o Custom Select para o filtro de Status
    function initializeCustomSelect(containerId) {
        const customSelect = document.getElementById(containerId);
        const selected = customSelect.querySelector(".selected");
        const options = customSelect.querySelector(".options");
        const hiddenInput = document.getElementById('filterStatus'); // Hardcoded para o ID do filtro

        selected.addEventListener("click", (e) => {
            e.stopPropagation();
            // Fecha outros selects
            document.querySelectorAll(".custom-select.active").forEach(sel => {
                if (sel !== customSelect) sel.classList.remove('active');
            });
            customSelect.classList.toggle('active');
        });

        options.querySelectorAll("div").forEach(option => {
            option.addEventListener("click", () => {
                selected.textContent = option.textContent;
                hiddenInput.value = option.dataset.value;
                customSelect.classList.remove('active');
                filterPacientes(); // Chama a função de filtro
            });
        });

        document.addEventListener("click", () => {
            customSelect.classList.remove('active');
        });
    }

    document.addEventListener("DOMContentLoaded", () => {
        // Inicializa o filtro de status e a escuta da barra de pesquisa
        initializeCustomSelect("customStatus");
        document.getElementById('searchInput').addEventListener('input', filterPacientes);

        // Verifica se há mensagem de sucesso na sessão para abrir o modal
        @if(session('success'))
            openSuccessModal("{{ session('success') }}");
        @endif
    });
    
    // ------------------------------------------
    // LÓGICA DOS MODAIS
    // ------------------------------------------
    
    // Modal de Sucesso
    function openSuccessModal(message) {
        document.getElementById('successMessage').textContent = message;
        document.getElementById('statusSuccessModal').style.display = 'flex';
    }

    function closeSuccessModal() {
        document.getElementById('statusSuccessModal').style.display = 'none';
        window.location.reload(); 
    }
    
    // O modal de exclusão foi removido, então as funções openDeletePacienteModal e closeDeletePacienteModal também devem ser removidas.
    // O código original ainda tem essas funções. A forma correta seria assim:

    /*
    function openDeletePacienteModal(pacienteId, pacienteNome) {
        // Esta função não é mais necessária, pois o botão de exclusão foi removido.
    }

    function closeDeletePacienteModal() {
        // Esta função não é mais necessária.
    }
    */
    
    // Modal de Alteração de Status
    function openStatusPacienteModal(pacienteId, pacienteNome, currentStatus) {
        const modal = document.getElementById('statusPacienteModal');
        const nomeSpan = document.getElementById('statusPacienteNome');
        const actionSpan = document.getElementById('statusAction');
        const confirmText = document.getElementById('confirmStatusText');
        const form = document.getElementById('statusPacienteForm');
        
        // Define a ação baseada no status atual (ativo -> desativar; inativo -> ativar)
        // O botão de "excluir" agora também usará essa lógica para inativar
        const action = currentStatus == 'ativo' ? 'desativar' : 'ativar';
        const confirmAction = currentStatus == 'ativo' ? 'desativar' : 'ativar';
        
        nomeSpan.textContent = pacienteNome;
        actionSpan.textContent = action;
        confirmText.textContent = confirmAction;

        // Rota de alteração de status (toggleStatus)
        const statusRoute = "{{ route('admin.pacientes.toggleStatus', ['paciente' => 'PLACEHOLDER_ID']) }}";
        form.action = statusRoute.replace('PLACEHOLDER_ID', pacienteId);
        
        modal.style.display = 'flex';
    }

    function closeStatusPacienteModal() {
        document.getElementById('statusPacienteModal').style.display = 'none';
    }

    // Fechamento dos modais clicando fora
    ['statusPacienteModal', 'statusSuccessModal'].forEach(id => {
        document.getElementById(id)?.addEventListener('click', function(event) {
            if (event.target.id === id) {
                if (id === 'statusPacienteModal') closeStatusPacienteModal();
                if (id === 'statusSuccessModal') closeSuccessModal();
            }
        });
    });
</script>
@endsection