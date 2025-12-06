@extends('unidade.templates.unidadeTemplate')

@section('title', 'Gerenciamento de Médicos')

@section('content')
<link rel="stylesheet" href="{{ asset('css/unidade/manutencaoMedicos.css') }}">

@php $unidade = auth()->guard('unidade')->user(); @endphp

<main class="main-dashboard">
    <div class="medico-container">

        <div class="metrics-dashboard">
            <div class="metric-card total">
                <div class="metric-icon">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="metric-info">
                    <span class="metric-label">TOTAL DE MÉDICOS</span>
                    <span class="metric-value">{{ $totalMedicos ?? 0 }}</span>
                </div>
            </div>

            <div class="metric-card active">
                <div class="metric-icon">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div class="metric-info">
                    <span class="metric-label">ATIVOS</span>
                    <span class="metric-value">{{ $medicos->where('usuario.statusAtivoUsuario', 1)->count() }}</span>
                </div>
            </div>

            <div class="metric-card inactive">
                <div class="metric-icon">
                    <i class="bi bi-x-circle-fill"></i>
                </div>
                <div class="metric-info">
                    <span class="metric-label">INATIVOS</span>
                    <span class="metric-value">{{ $medicos->where('usuario.statusAtivoUsuario', 0)->count() }}</span>
                </div>
            </div>

        </div>


        <div class="medico-header">
            <div class="header-title">
                <h1><i class="bi bi-person-badge"></i> Gerenciamento de Médicos</h1>
                <span class="subtitle">Gerencie todos os médicos da unidade</span>
            </div>
            
            <div class="header-actions">
                <a href="{{ route('unidade.medicos.create') }}" class="btn-add-medico">
                    <i class="bi bi-plus-circle"></i> Cadastrar Médico
                </a>
            </div>
        </div>


        <div class="search-filters">
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" id="searchInput" placeholder="Pesquisar por nome, CRM ou email..." onkeyup="filterMedicos()">
            </div>
            
            <div class="custom-select" id="customStatus">
                <div class="selected">
                    <i class="bi bi-filter"></i> Status
                </div>
                <div class="options">
                    <div data-value="">Todos</div>
                    <div data-value="ativo">Ativo</div>
                    <div data-value="inativo">Inativo</div>
                </div>
            </div>
            <input type="hidden" id="filterStatus" value="">

            <div class="view-toggle">
                <button class="view-btn active" data-view="list" onclick="changeView('list')" title="Visualização em Lista">
                    <i class="bi bi-list-ul"></i>
                </button>
                <button class="view-btn" data-view="grid" onclick="changeView('grid')" title="Visualização em Cards">
                    <i class="bi bi-grid-3x3-gap"></i>
                </button>
            </div>

            <button class="btn-refresh" onclick="location.reload()" title="Atualizar">
                <i class="bi bi-arrow-clockwise"></i>
            </button>
        </div>


        <div class="box-table" id="listView">
            <table>
                <thead>
                    <tr>
                        <th onclick="sortTable('nome')" style="cursor: pointer;">
                            NOME MÉDICO <i class="bi bi-arrow-down-up sort-icon"></i>
                        </th>
                        <th>CRM</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th style="text-align: center;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($medicos as $medico)
                    <tr data-status="{{ optional($medico->usuario)->statusAtivoUsuario == 1 ? 'ativo' : 'inativo' }}" 
                        class="{{ $medico->created_at >= now()->subDays(7) ? 'new-entry' : '' }}">
                        <td>
                            <div class="medico-name-cell">
                                <div class="medico-avatar">
                                    {{ substr($medico->nomeMedico, 0, 2) }}
                                </div>
                                <div class="medico-details">
                                    <span class="name">{{ $medico->nomeMedico }}</span>
                                    @if($medico->created_at >= now()->subDays(7))
                                        <span class="badge-new">Novo</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="crm-badge">{{ $medico->crmMedico }}</span>
                        </td>
                        <td>{{ optional($medico->usuario)->emailUsuario ?? 'Sem email' }}</td>
                        <td>
                            @if(optional($medico->usuario)->statusAtivoUsuario == 1)
                                <span class="status-badge status-ativo">
                                    <i class="bi bi-circle-fill"></i> Ativo
                                </span>
                            @else
                                <span class="status-badge status-inativo">
                                    <i class="bi bi-circle-fill"></i> Inativo
                                </span>
                            @endif
                        </td>
                        <td class="actions">
                            <button onclick="quickView({{ $medico->idMedicoPK }}, event)" class="btn-action btn-view" title="Visualizar Rápido">
                                <i class="bi bi-eye"></i>
                            </button>
                            
                            <a href="{{ route('unidade.medicos.edit', $medico->idMedicoPK) }}" class="btn-action btn-edit" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>

                            @if($medico->usuario)
                                <button onclick="openStatusModal('{{ $medico->idMedicoPK }}', '{{ $medico->nomeMedico }}', {{ optional($medico->usuario)->statusAtivoUsuario }}, event)" 
                                        class="btn-action btn-toggle" 
                                        title="{{ optional($medico->usuario)->statusAtivoUsuario == 1 ? 'Desativar' : 'Ativar' }}">
                                    @if(optional($medico->usuario)->statusAtivoUsuario == 1)
                                        <i class="bi bi-toggle-on text-success"></i>
                                    @else
                                        <i class="bi bi-toggle-off text-danger"></i>
                                    @endif
                                </button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    
                    @if($medicos->isEmpty())
                        <tr data-status="empty-list">
                            <td colspan="5" class="no-medicos">
                                <i class="bi bi-inbox"></i>
                                <p>Nenhum médico cadastrado.</p>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>


        <div class="grid-view" id="gridView" style="display: none;">
            @foreach ($medicos as $medico)
            <div class="medico-card" data-status="{{ optional($medico->usuario)->statusAtivoUsuario == 1 ? 'ativo' : 'inativo' }}">
                <div class="card-header">
                    @if($medico->created_at >= now()->subDays(7))
                        <span class="badge-new">Novo</span>
                    @endif
                </div>
                
                <div class="card-avatar">
                    {{ substr($medico->nomeMedico, 0, 2) }}
                </div>
                
                <h3>{{ $medico->nomeMedico }}</h3>
                <p class="card-crm">CRM: {{ $medico->crmMedico }}</p>
                <p class="card-email">{{ optional($medico->usuario)->emailUsuario ?? 'Sem email' }}</p>
                
                <div class="card-status">
                    @if(optional($medico->usuario)->statusAtivoUsuario == 1)
                        <span class="status-badge status-ativo">
                            <i class="bi bi-circle-fill"></i> Ativo
                        </span>
                    @else
                        <span class="status-badge status-inativo">
                            <i class="bi bi-circle-fill"></i> Inativo
                        </span>
                    @endif
                </div>
                
                <div class="card-actions">
                    <button onclick="quickView({{ $medico->idMedicoPK }}, event)" class="btn-action btn-view" title="Visualizar Rápido">
                        <i class="bi bi-eye"></i>
                    </button>
                    <a href="{{ route('unidade.medicos.edit', $medico->idMedicoPK) }}" class="btn-action btn-edit" title="Editar">
                        <i class="bi bi-pencil"></i>
                    </a>
                    @if($medico->usuario)
                        <button onclick="openStatusModal('{{ $medico->idMedicoPK }}', '{{ $medico->nomeMedico }}', {{ optional($medico->usuario)->statusAtivoUsuario }}, event)" class="btn-action btn-toggle" title="{{ optional($medico->usuario)->statusAtivoUsuario == 1 ? 'Desativar' : 'Ativar' }}">
                            @if(optional($medico->usuario)->statusAtivoUsuario == 1)
                                <i class="bi bi-toggle-on text-success"></i>
                            @else
                                <i class="bi bi-toggle-off text-danger"></i>
                            @endif
                        </button>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        <div class="pagination-container">
            @if(method_exists($medicos, 'links'))
                {{ $medicos->links() }}
            @endif
        </div>
    </div>
</main>

{{-- MODAL DE VISUALIZAÇÃO RÁPIDA --}}
<div id="quickViewModal" class="modal-overlay">
    <div class="modal-content modal-large">
        <button class="modal-close" onclick="closeQuickView()">
            <i class="bi bi-x"></i>
        </button>
        
        <div class="modal-header">
            <i class="bi bi-eye"></i>
            <h2>Visualização Rápida</h2>
        </div>
        
        <div id="quickViewContent" class="quick-view-content">
            <div class="loading-spinner">
                <i class="bi bi-hourglass-split"></i> Carregando...
            </div>
        </div>
    </div>
</div>

{{-- MODAL DE ALTERAÇÃO DE STATUS --}}
<div id="statusMedicoModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <i class="bi bi-toggle-on"></i>
            <h2>Alterar Status</h2>
        </div>
        
        <p>Tem certeza que deseja <span id="statusAction"></span> o(a) médico(a) <strong><span id="statusMedicoNome"></span></strong>?</p>

        <form id="statusMedicoForm" method="POST" action="{{ route('unidade.medicos.toggleStatus', ':id:') }}">
            @csrf
            <div class="modal-buttons">
                <button type="button" onclick="closeStatusModal()" class="btn-cancelar">Cancelar</button>
                <button type="submit" class="btn-excluir">Sim, <span id="confirmStatusText"></span></button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL DE SUCESSO --}}
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

@push('scripts')
<script>
    let currentView = 'list';

    function changeView(view) {
        currentView = view;
        const listView = document.getElementById('listView');
        const gridView = document.getElementById('gridView');
        const buttons = document.querySelectorAll('.view-btn');
        
        buttons.forEach(btn => {
            btn.classList.remove('active');
            if (btn.dataset.view === view) {
                btn.classList.add('active');
            }
        });
        
        if (view === 'list') {
            listView.style.display = 'block';
            gridView.style.display = 'none';
        } else {
            listView.style.display = 'none';
            gridView.style.display = 'grid';
        }
    }

    function quickView(medicoId, event) {
        if (event) event.stopPropagation();
        
        const modal = document.getElementById('quickViewModal');
        const content = document.getElementById('quickViewContent');
        
        modal.style.display = 'flex';
        content.innerHTML = '<div class="loading-spinner"><i class="bi bi-hourglass-spin"></i> Carregando...</div>';
        
        fetch(`/unidade/medicos/${medicoId}/quick-view`)
        .then(response => response.json())
        .then(data => {
            content.innerHTML = `
                <div class="quick-view-grid">
                    <div class="quick-view-header">
                        <div class="quick-view-avatar-large">${data.nome.substring(0, 2).toUpperCase()}</div>
                        <div class="quick-view-info">
                            <h3>${data.nome}</h3>
                            <p><i class="bi bi-clipboard2-pulse"></i> <strong>Especialidade:</strong> ${data.especialidade}</p>
                            <p><i class="bi bi-card-text"></i> <strong>CRM:</strong> ${data.crm}</p>
                            <p><i class="bi bi-envelope"></i> <strong>Email:</strong> ${data.email}</p>
                            <p><i class="bi bi-circle-fill ${data.status == 1 ? 'text-success' : 'text-danger'}"></i> <strong>Status:</strong> ${data.status == 1 ? 'Ativo' : 'Inativo'}</p>
                        </div>
                    </div>
                    <div class="quick-view-actions">
                        <a href="/unidade/medicos/${medicoId}/editar" class="btn-quick-edit">
                            <i class="bi bi-pencil"></i> Editar Perfil Completo
                        </a>
                    </div>
                </div>
            `;
        })
        .catch(error => {
            console.error('Erro:', error);
            content.innerHTML = '<p>Erro ao carregar informações do médico.</p>';
        });
    }

    function closeQuickView() {
        document.getElementById('quickViewModal').style.display = 'none';
    }

    function openStatusModal(medicoId, medicoNome, currentStatus, event) {
        if (event) event.stopPropagation();
        
        const modal = document.getElementById('statusMedicoModal');
        const form = document.getElementById('statusMedicoForm');
        const action = currentStatus == 1 ? 'desativar' : 'ativar';
        
        document.getElementById('statusMedicoNome').textContent = medicoNome;
        document.getElementById('statusAction').textContent = action;
        document.getElementById('confirmStatusText').textContent = action;
        
        form.action = form.action.replace(':id:', medicoId);
        modal.style.display = 'flex';
    }

    function closeStatusModal() {
        document.getElementById('statusMedicoModal').style.display = 'none';
    }

    let sortDirection = {};
    function sortTable(column) {
        const table = document.querySelector('#listView table');
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr:not([data-status="empty-list"])'));
        
        sortDirection[column] = sortDirection[column] === 'asc' ? 'desc' : 'asc';
        
        rows.sort((a, b) => {
            let aValue, bValue;
            
            if (column === 'nome') {
                aValue = a.querySelector('.medico-name-cell .name').textContent.trim();
                bValue = b.querySelector('.medico-name-cell .name').textContent.trim();
            } else if (column === 'crm') {
                aValue = a.children[1].textContent.trim();
                bValue = b.children[1].textContent.trim();
            } else if (column === 'email') {
                aValue = a.children[2].textContent.trim();
                bValue = b.children[2].textContent.trim();
            } else if (column === 'status') {
                aValue = a.dataset.status;
                bValue = b.dataset.status;
            }
            
            if (sortDirection[column] === 'asc') {
                return aValue.localeCompare(bValue);
            } else {
                return bValue.localeCompare(aValue);
            }
        });
        
        rows.forEach(row => tbody.appendChild(row));
    }

    function filterMedicos() {
        const searchInput = document.getElementById('searchInput').value.toLowerCase();
        const filterStatus = document.getElementById('filterStatus').value;
        
        document.querySelectorAll('#listView tbody tr').forEach(row => {
            if (row.dataset.status === 'empty-list') return;
            
            const name = row.querySelector('.medico-name-cell .name')?.textContent.toLowerCase() || '';
            const crm = row.querySelector('.crm-badge')?.textContent.toLowerCase() || '';
            const email = row.children[2]?.textContent.toLowerCase() || '';
            const status = row.dataset.status;
            
            const matchesSearch = name.includes(searchInput) || crm.includes(searchInput) || email.includes(searchInput);
            const matchesStatus = !filterStatus || status === filterStatus;
            
            row.style.display = (matchesSearch && matchesStatus) ? '' : 'none';
        });
        
        document.querySelectorAll('.medico-card').forEach(card => {
            const name = card.querySelector('h3')?.textContent.toLowerCase() || '';
            const crm = card.querySelector('.card-crm')?.textContent.toLowerCase() || '';
            const email = card.querySelector('.card-email')?.textContent.toLowerCase() || '';
            const status = card.dataset.status;
            
            const matchesSearch = name.includes(searchInput) || crm.includes(searchInput) || email.includes(searchInput);
            const matchesStatus = !filterStatus || status === filterStatus;
            
            card.style.display = (matchesSearch && matchesStatus) ? '' : 'none';
        });
    }

    function openSuccessModal(message) {
        document.getElementById('successMessage').textContent = message;
        document.getElementById('statusSuccessModal').style.display = 'flex';
    }

    function closeSuccessModal() {
        document.getElementById('statusSuccessModal').style.display = 'none';
        window.location.reload();
    }

    function initializeCustomSelect(containerId) {
        const customSelect = document.getElementById(containerId);
        const selected = customSelect.querySelector(".selected");
        const options = customSelect.querySelector(".options");
        const hiddenInput = document.getElementById(containerId.replace('custom', 'filter'));

        selected.addEventListener("click", (e) => {
            e.stopPropagation();
            document.querySelectorAll(".custom-select .options").forEach(opt => {
                if (opt !== options) opt.parentElement.classList.remove('active');
            });
            customSelect.classList.toggle('active');
        });

        options.querySelectorAll("div").forEach(option => {
            option.addEventListener("click", () => {
                const icon = selected.querySelector('i').outerHTML;
                selected.innerHTML = `${icon} ${option.textContent}`;
                hiddenInput.value = option.dataset.value;
                customSelect.classList.remove('active');
                filterMedicos();
            });
        });

        document.addEventListener("click", () => {
            customSelect.classList.remove('active');
        });
    }

    document.addEventListener("DOMContentLoaded", () => {
        initializeCustomSelect("customStatus");
        
        @if(session('success'))
            openSuccessModal("{{ session('success') }}");
        @endif
    });

    document.getElementById('quickViewModal').addEventListener('click', (e) => {
        if (e.target.id === 'quickViewModal') closeQuickView();
    });

    document.getElementById('statusMedicoModal').addEventListener('click', (e) => {
        if (e.target.id === 'statusMedicoModal') closeStatusModal();
    });

    document.getElementById('statusSuccessModal').addEventListener('click', (e) => {
        if (e.target.id === 'statusSuccessModal') closeSuccessModal();
    });

    function exportToExcel() {
        alert('Exportando para Excel...\n\nImplementar no backend:\n- Rota GET /unidade/medicos/export\n- Usar Laravel Excel (maatwebsite/excel)\n- Retornar arquivo .xlsx com filtros aplicados');
    }
</script>
@endpush
@endsection