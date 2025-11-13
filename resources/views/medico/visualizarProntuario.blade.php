@extends('medico.templates.medicoTemplate')

@section('title', 'Visualizar Prontuário')

@section('content')

<link rel="stylesheet" href="{{ asset('css/medico/visualizarProntuario.css') }}">

<main class="main-dashboard">
    <div class="prontuario-wrapper">

        {{-- ===== HEADER PRINCIPAL COM ANIMAÇÕES ===== --}}
        <div class="page-header">
            <div class="header-background-pattern"></div>
            <div class="header-left">
                <div class="header-icon-wrapper">
                    <div class="icon-pulse"></div>
                    <i class="bi bi-folder2-open"></i>
                </div>
                <div class="header-text">
                    <h1>Prontuário Médico Eletrônico</h1>
                    <p>Visualização completa do histórico de atendimentos</p>
                    <div class="header-breadcrumb">
                        <span><i class="bi bi-house-door"></i> Início</span>
                        <i class="bi bi-chevron-right"></i>
                        <span><i class="bi bi-people"></i> Pacientes</span>
                        <i class="bi bi-chevron-right"></i>
                        <span class="active">Prontuário</span>
                    </div>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('medico.cadastrarProntuario', $paciente->idPaciente) }}" class="btn-add-consulta">
                    <div class="btn-icon-circle">
                        <i class="bi bi-plus-lg"></i>
                    </div>
                    <span>Nova Consulta</span>
                </a>
            </div>
        </div>

        {{-- ===== CARD DO PACIENTE COM DESIGN PREMIUM ===== --}}
        <div class="patient-card">
            <div class="patient-card-glow"></div>
            <div class="patient-card-header">
                <div class="patient-left-section">
                    <div class="patient-avatar-container">
                        <div class="avatar-ring"></div>
                        <div class="patient-avatar">
                            <i class="bi bi-person-circle"></i>
                        </div>
                        <div class="status-indicator"></div>
                    </div>
                    <div class="patient-info-main">
                        <div class="patient-name-group">
                            <h2>{{ $paciente->nomePaciente }}</h2>
                            <button class="btn-favorite" title="Marcar como favorito">
                                <i class="bi bi-star"></i>
                            </button>
                        </div>
                        <div class="patient-badges">
                            <span class="badge badge-active">
                                <i class="bi bi-check-circle-fill"></i> Ativo
                            </span>
                            @if($prontuario)
                            <span class="badge badge-prontuario">
                                <i class="bi bi-file-medical"></i> Prontuário Nº {{ str_pad($prontuario->idProntuarioPK, 6, '0', STR_PAD_LEFT) }}
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="patient-quick-actions">
                    <button class="quick-action-btn" title="Compartilhar">
                        <i class="bi bi-share"></i>
                    </button>
                    <button class="quick-action-btn" title="Imprimir">
                        <i class="bi bi-printer"></i>
                    </button>
                    <button class="quick-action-btn" title="Exportar">
                        <i class="bi bi-download"></i>
                    </button>
                </div>
            </div>

            <div class="patient-card-body">
                <div class="info-grid">
                    <div class="info-card">
                        <div class="info-icon">
                            <i class="bi bi-credit-card-2-front"></i>
                        </div>
                        <div class="info-content">
                            <span class="info-label">CPF</span>
                            <span class="info-value">{{ $paciente->cpfPaciente }}</span>
                        </div>
                    </div>

                    <div class="info-card">
                        <div class="info-icon">
                            <i class="bi bi-calendar-event"></i>
                        </div>
                        <div class="info-content">
                            <span class="info-label">Data de Nascimento</span>
                            <span class="info-value">
                                {{ \Carbon\Carbon::parse($paciente->dataNascPaciente)->format('d/m/Y') }}
                            </span>
                        </div>
                    </div>

                    <div class="info-card">
                        <div class="info-icon">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                        <div class="info-content">
                            <span class="info-label">Idade</span>
                            <span class="info-value highlight">
                                {{ \Carbon\Carbon::parse($paciente->dataNascPaciente)->age }} anos
                            </span>
                        </div>
                    </div>

                    @if($prontuario)
                    <div class="info-card">
                        <div class="info-icon">
                            <i class="bi bi-folder-plus"></i>
                        </div>
                        <div class="info-content">
                            <span class="info-label">Prontuário Aberto</span>
                            <span class="info-value">
                                {{ \Carbon\Carbon::parse($prontuario->dataAbertura)->format('d/m/Y') }}
                            </span>
                        </div>
                    </div>

                    <div class="info-card featured">
                        <div class="info-icon">
                            <i class="bi bi-clipboard2-data"></i>
                        </div>
                        <div class="info-content">
                            <span class="info-label">Total de Consultas</span>
                            <span class="info-value highlight-red">
                                {{ $consultas->count() }}
                            </span>
                        </div>
                        <div class="card-shine"></div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ===== HISTÓRICO DE CONSULTAS COM FILTROS E ESTATÍSTICAS ===== --}}
        <div class="history-section">
            <div class="history-header-container">
                <div class="history-header">
                    <div class="history-title-group">
                        <div class="history-icon">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div>
                            <h2>Histórico de Atendimentos</h2>
                            <p class="history-subtitle">Registro cronológico de todas as consultas realizadas</p>
                        </div>
                    </div>
                    <div class="history-stats">
                        <div class="stat-item">
                            <span class="stat-value">{{ $consultas->count() }}</span>
                            <span class="stat-label">{{ $consultas->count() === 1 ? 'Consulta' : 'Consultas' }}</span>
                        </div>
                    </div>
                </div>

                @if($consultas->isNotEmpty())
                <div class="history-filters">
                    <div class="filter-group">
                        <button class="filter-btn active" data-filter="all">
                            <i class="bi bi-grid"></i>
                            Todas
                        </button>
                        <button class="filter-btn" data-filter="recent">
                            <i class="bi bi-clock"></i>
                            Recentes
                        </button>
                    </div>
                    <div class="search-group">
                        <div class="search-input-wrapper">
                            <i class="bi bi-search"></i>
                            <input type="text" placeholder="Pesquisar no histórico..." class="search-input">
                        </div>
                    </div>
                </div>
                @endif
            </div>

            @if($consultas->isEmpty())
            <div class="empty-state">
                <div class="empty-animation">
                    <div class="empty-icon-wrapper">
                        <i class="bi bi-inbox"></i>
                    </div>
                </div>
                <h3>Nenhuma consulta registrada</h3>
                <p>Este paciente ainda não possui atendimentos registrados no sistema.</p>
                <a href="{{ route('medico.cadastrarProntuario', $paciente->idPaciente) }}" class="btn-empty-action">
                    <i class="bi bi-plus-circle-fill"></i>
                    Registrar Primeira Consulta
                </a>
            </div>
            @else
            <div class="consultas-list">
                @foreach($consultas as $consulta)
                <div class="consulta-item" data-consulta-id="{{ $consulta->idConsultaPK }}">
                    <div class="timeline-marker">
                        <div class="timeline-dot">
                            <div class="dot-inner"></div>
                        </div>
                        @unless($loop->last)
                        <div class="timeline-line"></div>
                        @endunless
                    </div>

                    <div class="consulta-content">
                        <div class="consulta-header">
                            <div class="consulta-header-left">
                                <div class="consulta-date-badge">
                                    <i class="bi bi-calendar3"></i>
                                    <strong>{{ \Carbon\Carbon::parse($consulta->dataConsulta)->format('d/m/Y') }}</strong>
                                    <span class="date-weekday">{{ \Carbon\Carbon::parse($consulta->dataConsulta)->locale('pt_BR')->isoFormat('dddd') }}</span>
                                </div>
                                <div class="consulta-tags">
                                    @if($consulta->medicamentos->isNotEmpty())
                                    <span class="tag tag-prescription">
                                        <i class="bi bi-capsule"></i>
                                        Receita
                                    </span>
                                    @endif
                                    @if($consulta->exames->isNotEmpty())
                                    <span class="tag tag-exams">
                                        <i class="bi bi-clipboard-pulse"></i>
                                        Exames
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="consulta-menu">
                                <button class="btn-menu-toggle">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a href="{{ route('medico.prontuario.edit', $consulta->idConsultaPK) }}" class="dropdown-item">
                                        <i class="bi bi-pencil-square"></i>
                                        <span>Editar</span>
                                    </a>
                                    <button type="button" class="dropdown-item" onclick="openPdfModal({{ $consulta->idConsultaPK }})">
                                        <i class="bi bi-file-earmark-pdf-fill"></i>
                                        <span>Gerar PDFs</span>
                                    </button>
                                    <div class="dropdown-divider"></div>
                                    <form action="{{ route('medico.prontuario.destroy', $consulta->idConsultaPK) }}" method="POST" onsubmit="return confirm('Deseja realmente excluir esta consulta?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-trash3"></i>
                                            <span>Excluir</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="consulta-professional">
                            <div class="professional-avatar">
                                <i class="bi bi-person-badge"></i>
                            </div>
                            <div class="professional-info">
                                <strong>Dr(a). {{ $consulta->nomeMedico }}</strong>
                                <span>CRM: {{ $consulta->crmMedico }}</span>
                            </div>
                        </div>

                        @if($consulta->unidade)
                        <div class="consulta-location">
                            <i class="bi bi-geo-alt-fill"></i>
                            <span>{{ $consulta->unidade }}</span>
                        </div>
                        @endif

                        <div class="consulta-details">
                            {{-- Observações --}}
                            <div class="detail-block">
                                <div class="detail-header">
                                    <div class="detail-icon-wrapper">
                                        <i class="bi bi-file-text-fill"></i>
                                    </div>
                                    <span>Observações Médicas</span>
                                    @if($consulta->observacoes)
                                    <button class="btn-expand" title="Expandir">
                                        <i class="bi bi-arrows-angle-expand"></i>
                                    </button>
                                    @endif
                                </div>
                                @php
                                $observacoes = $consulta->observacoes ? $consulta->observacoes : 'Nenhuma observação registrada';
                                $observacoesClass = $consulta->observacoes ? '' : 'empty';
                                @endphp
                                <div class="detail-content pre-formatted {{ $observacoesClass }}">
                                    {{ $observacoes }}
                                </div>
                            </div>

                            {{-- Exames Solicitados --}}
                            <div class="detail-block">
                                <div class="detail-header">
                                    <div class="detail-icon-wrapper">
                                        <i class="bi bi-clipboard2-pulse-fill"></i>
                                    </div>
                                    <span>Exames Solicitados</span>
                                    @if($consulta->exames->isNotEmpty())
                                    <button class="btn-expand" title="Expandir">
                                        <i class="bi bi-arrows-angle-expand"></i>
                                    </button>
                                    @endif
                                </div>
                                @php
                                $examesList = [];
                                foreach ($consulta->exames as $exame) {
                                    $exameInfo = $exame->nomeExame;
                                    if ($exame->tipoExame) {
                                        $exameInfo .= " (" . $exame->tipoExame . ")";
                                    }
                                    $examesList[] = $exameInfo;
                                }
                                $examesText = !empty($examesList) ? implode("\n", $examesList) : 'Nenhum exame solicitado';
                                $examesClass = !empty($examesList) ? '' : 'empty';
                                @endphp
                                <div class="detail-content pre-formatted {{ $examesClass }}">
                                    {{ $examesText }}
                                </div>
                            </div>

                            {{-- Medicamentos Prescritos --}}
                            <div class="detail-block prescription-block">
                                <div class="detail-header">
                                    <div class="detail-icon-wrapper">
                                        <i class="bi bi-capsule-pill"></i>
                                    </div>
                                    <span>Medicamentos Prescritos</span>
                                    @if($consulta->medicamentos->isNotEmpty())
                                    <button class="btn-expand" title="Expandir">
                                        <i class="bi bi-arrows-angle-expand"></i>
                                    </button>
                                    @endif
                                </div>
                                @php
                                $medicamentosList = [];
                                foreach ($consulta->medicamentos as $medicamento) {
                                    $medInfo = $medicamento->nomeMedicamento;
                                    if ($medicamento->dosagemMedicamento) {
                                        $medInfo .= " - " . $medicamento->dosagemMedicamento;
                                    }
                                    if ($medicamento->frequenciaMedicamento) {
                                        $medInfo .= " (" . $medicamento->frequenciaMedicamento . ")";
                                    }
                                    if ($medicamento->periodoMedicamento) {
                                        $medInfo .= " por " . $medicamento->periodoMedicamento;
                                    }
                                    $medicamentosList[] = $medInfo;
                                }
                                $medicamentosText = !empty($medicamentosList) ? implode("\n", $medicamentosList) : 'Nenhum medicamento prescrito';
                                $medicamentosClass = !empty($medicamentosList) ? '' : 'empty';
                                @endphp
                                <div class="detail-content pre-formatted {{ $medicamentosClass }}">
                                    {{ $medicamentosText }}
                                </div>
                            </div>
                        </div>

                        <div class="consulta-footer">
                            <div class="footer-info">
                                <i class="bi bi-clock"></i>
                                <small>
                                    Registrado em {{ \Carbon\Carbon::parse($consulta->created_at)->format('d/m/Y \à\s H:i') }}
                                    @if($consulta->updated_at != $consulta->created_at)
                                    • Atualizado em {{ \Carbon\Carbon::parse($consulta->updated_at)->format('d/m/Y \à\s H:i') }}
                                    @endif
                                </small>
                            </div>
                            <button class="btn-collapse-consulta">
                                <i class="bi bi-chevron-up"></i>
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        <div class="page-footer">
            <a href="{{ route('medico.prontuario') }}" class="btn-voltar">
                <i class="bi bi-arrow-left-circle"></i>
                <span>Voltar para Lista de Pacientes</span>
            </a>
        </div>
    </div>
</main>

{{-- ===== MODAL DE PDF REDESENHADO ===== --}}
@if($consultas->isNotEmpty())
<div id="pdfOptionsModal" class="modal-overlay-pdf">
    <div class="modal-backdrop"></div>
    <div class="modal-content-pdf">
        <button class="modal-close-btn" onclick="closePdfModal()">
            <i class="bi bi-x-lg"></i>
        </button>

        <div class="modal-header-pdf">
            <div class="modal-icon-container">
                <i class="bi bi-file-earmark-pdf-fill"></i>
            </div>
            <h2>Geração de Documentos</h2>
            <p>Selecione o tipo de documento que deseja gerar em PDF</p>
        </div>

        <div class="modal-body-pdf">
            <div id="examesPdfOption" class="pdf-modal-option" onclick="downloadPdf('exames')">
                <div class="option-background"></div>
                <div class="option-icon">
                    <i class="bi bi-clipboard2-pulse"></i>
                </div>
                <div class="pdf-modal-info">
                    <h3>Pedido de Exames</h3>
                    <p>Documento contendo lista de exames solicitados para o paciente</p>
                </div>
                <span class="pdf-status-badge disponivel">
                    <i class="bi bi-check-circle-fill"></i> Disponível
                </span>
                <div class="option-arrow">
                    <i class="bi bi-arrow-right"></i>
                </div>
            </div>

            <div id="receitaPdfOption" class="pdf-modal-option" onclick="downloadPdf('receita')">
                <div class="option-background"></div>
                <div class="option-icon">
                    <i class="bi bi-prescription2"></i>
                </div>
                <div class="pdf-modal-info">
                    <h3>Receita Médica</h3>
                    <p>Documento com medicamentos prescritos e instruções médicas</p>
                </div>
                <span class="pdf-status-badge disponivel">
                    <i class="bi bi-check-circle-fill"></i> Disponível
                </span>
                <div class="option-arrow">
                    <i class="bi bi-arrow-right"></i>
                </div>
            </div>
        </div>

        <div class="modal-footer-pdf">
            <button type="button" onclick="closePdfModal()" class="btn-modal-cancel">
                <i class="bi bi-x-circle"></i>
                <span>Cancelar</span>
            </button>
        </div>
    </div>
</div>
@endif

<script>
let currentConsultaId = null;

function openPdfModal(consultaId) {
    currentConsultaId = consultaId;
    const modal = document.getElementById('pdfOptionsModal');
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            menu.classList.remove('show');
        });
    }
}

function closePdfModal() {
    const modal = document.getElementById('pdfOptionsModal');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
    currentConsultaId = null;
}

function downloadPdf(type) {
    if (!currentConsultaId) {
        showNotification('Erro: ID da consulta não encontrado.', 'error');
        return;
    }

    const optionElement = type === 'exames' ? 
        document.getElementById('examesPdfOption') : 
        document.getElementById('receitaPdfOption');
    const statusBadge = optionElement.querySelector('.pdf-status-badge');
    const optionIcon = optionElement.querySelector('.option-icon');

    optionIcon.innerHTML = '<i class="bi bi-hourglass-split"></i>';
    statusBadge.innerHTML = '<i class="bi bi-hourglass-split"></i> Gerando...';
    statusBadge.classList.remove('disponivel');
    statusBadge.classList.add('gerando');

    let url = type === 'exames' ? 
        "{{ route('medico.pdf.exames', '') }}/" + currentConsultaId :
        "{{ route('medico.pdf.receita', '') }}/" + currentConsultaId;

    window.location.href = url;

    setTimeout(() => {
        closePdfModal();
        showNotification(`${type === 'exames' ? 'Pedido de Exames' : 'Receita Médica'} gerado com sucesso!`, 'success');

        setTimeout(() => {
            optionIcon.innerHTML = type === 'exames' ? 
                '<i class="bi bi-clipboard2-pulse"></i>' :
                '<i class="bi bi-prescription2"></i>';
            statusBadge.innerHTML = '<i class="bi bi-check-circle-fill"></i> Disponível';
            statusBadge.classList.remove('gerando');
            statusBadge.classList.add('disponivel');
        }, 2000);
    }, 1000);
}

function showNotification(message, type) {
    const existingNotification = document.querySelector('.notification');
    if (existingNotification) {
        document.body.removeChild(existingNotification);
    }

    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-icon">
            <i class="bi bi-${type === 'success' ? 'check-circle-fill' : 'exclamation-circle-fill'}"></i>
        </div>
        <div class="notification-content">
            <span class="notification-message">${message}</span>
        </div>
        <button class="notification-close" onclick="this.parentElement.remove()">
            <i class="bi bi-x"></i>
        </button>
    `;

    document.body.appendChild(notification);

    setTimeout(() => notification.classList.add('show'), 100);

    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 4000);
}

document.addEventListener('DOMContentLoaded', function() {
    const menuToggles = document.querySelectorAll('.btn-menu-toggle');
    menuToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.stopPropagation();
            const dropdown = this.nextElementSibling;
            
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                if (menu !== dropdown) menu.classList.remove('show');
            });
            
            dropdown.classList.toggle('show');
        });
    });

    document.addEventListener('click', function() {
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            menu.classList.remove('show');
        });
    });

    document.addEventListener('click', function(event) {
        const modal = document.getElementById('pdfOptionsModal');
        if (modal && event.target.classList.contains('modal-backdrop')) {
            closePdfModal();
        }
    });

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') closePdfModal();
    });

    document.querySelectorAll('.btn-expand').forEach(btn => {
        btn.addEventListener('click', function() {
            const detailBlock = this.closest('.detail-block');
            detailBlock.classList.toggle('expanded');
            this.querySelector('i').classList.toggle('bi-arrows-angle-expand');
            this.querySelector('i').classList.toggle('bi-arrows-angle-contract');
        });
    });

    document.querySelectorAll('.btn-collapse-consulta').forEach(btn => {
        btn.addEventListener('click', function() {
            const consulta = this.closest('.consulta-item');
            consulta.classList.toggle('collapsed');
            this.querySelector('i').classList.toggle('bi-chevron-up');
            this.querySelector('i').classList.toggle('bi-chevron-down');
        });
    });

    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            const filter = this.dataset.filter;
            const consultas = document.querySelectorAll('.consulta-item');
            
            consultas.forEach(consulta => {
                consulta.style.display = 'flex';
            });
        });
    });

    const searchInput = document.querySelector('.search-input');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            document.querySelectorAll('.consulta-item').forEach(consulta => {
                const text = consulta.textContent.toLowerCase();
                consulta.style.display = text.includes(searchTerm) ? 'flex' : 'none';
            });
        });
    }

    @if(session('success'))
    showNotification('{{ session('success') }}', 'success');
    @endif

    @if(session('error'))
    showNotification('{{ session('error') }}', 'error');
    @endif
});
</script>

@endsection