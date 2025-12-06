@extends('enfermeiro.templates.enfermeiroTemplate') 

@section('title', 'Prontuário de ' . ($paciente->nomePaciente ?? 'Paciente'))

@section('content')

<link rel="stylesheet" href="{{ asset('css/enfermeiro/visualizarProntuarioEnfermeiro.css') }}">

<main class="main-dashboard">
    <div class="prontuario-wrapper">
        
        <div class="page-header">
            <div class="header-left">
                <div class="header-icon">
                    <i class="bi bi-person-badge-fill"></i>
                </div>
                <div class="header-text">
                    <h1>Prontuário de Enfermagem</h1>
                    <p>Visualização completa do histórico de anotações</p>
                </div>
            </div>
            
            <div class="header-actions">
                <a href="{{ route('enfermeiro.anotacao.create', $paciente->idPaciente) }}" class="btn-add-anotacao">
                    <i class="bi bi-plus-circle-fill"></i>
                    <span>Nova Anotação</span>
                </a>
            </div>
        </div>

        <div class="patient-card">
            <div class="patient-card-header">
                <div class="patient-avatar">
                    <i class="bi bi-person-circle"></i>
                </div>
                <div class="patient-info-main">
                    <h2>{{ $paciente->nomePaciente ?? 'Paciente Não Encontrado' }}</h2>
                    <div class="patient-badges">
                        <span class="badge badge-active">
                            <i class="bi bi-check-circle-fill"></i> Ativo
                        </span>
                        <span class="badge badge-prontuario">
                            <i class="bi bi-file-medical"></i> Prontuário Nº {{ $paciente->idPaciente ?? '00000' }}
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="patient-card-body">
                <div class="info-row">
                    <div class="info-col">
                        <div class="info-label">
                            <i class="bi bi-credit-card-2-front"></i>
                            <span>CPF</span>
                        </div>
                        <div class="info-value">{{ $paciente->cpfPaciente ?? 'N/A' }}</div>
                    </div>
                    
                    <div class="info-col">
                        <div class="info-label">
                            <i class="bi bi-calendar-event"></i>
                            <span>Data de Nascimento</span>
                        </div>
                        <div class="info-value">
                            {{ $paciente->dataNascPaciente ? \Carbon\Carbon::parse($paciente->dataNascPaciente)->format('d/m/Y') : 'N/A' }}
                        </div>
                    </div>
                    
                    <div class="info-col">
                        <div class="info-label">
                            <i class="bi bi-hourglass-split"></i>
                            <span>Idade</span>
                        </div>
                        <div class="info-value">
                            {{ isset($paciente->dataNascPaciente) ? \Carbon\Carbon::parse($paciente->dataNascPaciente)->age : 'N/A' }} anos
                        </div>
                    </div>
                    
                    <div class="info-col">
                        <div class="info-label">
                            <i class="bi bi-clipboard2-data"></i>
                            <span>Total de Anotações</span>
                        </div>
                        <div class="info-value highlight">
                            {{ $anotacoes->count() ?? 0 }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="history-section">
            <div class="history-header">
                <h2>
                    <i class="bi bi-clock-history"></i>
                    Histórico de Anotações de Enfermagem
                </h2>
                <span class="consultas-count">
                    {{ $anotacoes->count() }} {{ $anotacoes->count() === 1 ? 'registro' : 'registros' }}
                </span>
            </div>

            @forelse ($anotacoes ?? [] as $anotacao)
                <div class="consulta-item">
                    <div class="timeline-marker">
                        <div class="timeline-dot"></div>
                        @if(!$loop->last)
                        <div class="timeline-line"></div>
                        @endif
                    </div>

                    <div class="consulta-content">
                        <div class="consulta-top">
                            <div class="consulta-date-badge">
                                <i class="bi bi-calendar3"></i>
                                <strong>{{ \Carbon\Carbon::parse($anotacao->data_hora ?? $anotacao->created_at)->format('d/m/Y H:i') }}</strong>
                            </div>
                            <div class="consulta-menu">
                                {{-- Menu dropdown, se necessário --}}
                            </div>
                        </div>

                        <div class="consulta-professional">
                            <div class="professional-avatar">
                                <i class="bi bi-person-badge"></i>
                            </div>
                            <div class="professional-info">
                                <strong>Enf. {{ $anotacao->enfermeiro->nomeEnfermeiro ?? 'Enfermeiro(a) Desconhecido' }}</strong>
                                <span>Coren: {{ $anotacao->enfermeiro->corenEnfermeiro ?? 'N/A' }}</span>
                            </div>
                        </div>

                        @if($anotacao->unidade_atendimento)
                        <div class="consulta-location">
                            <i class="bi bi-geo-alt-fill"></i>
                            <span>{{ $anotacao->unidadeAtendimento->nomeUnidade ?? 'Unidade Não Informada' }}</span>
                        </div>
                        @endif
                        
                        @if ($anotacao->temperatura || $anotacao->pressao_arterial || $anotacao->frequencia_cardiaca)
                        <div class="sinais-vitais-block">
                            <strong>Sinais Vitais:</strong>
                            @if ($anotacao->temperatura) <span><i class="bi bi-thermometer-half"></i> Temp: {{ $anotacao->temperatura }}°C</span> @endif
                            @if ($anotacao->pressao_arterial) <span><i class="bi bi-heart-pulse-fill"></i> PA: {{ $anotacao->pressao_arterial }}</span> @endif
                            @if ($anotacao->frequencia_cardiaca) <span><i class="bi bi-activity"></i> FC: {{ $anotacao->frequencia_cardiaca }} bpm</span> @endif
                        </div>
                        @endif

                        <div class="consulta-details">
                            @if($anotacao->alergias)
                            <div class="detail-block">
                                <div class="detail-header">
                                    <i class="bi bi-bug-fill"></i>
                                    <span>Alergias Identificadas</span>
                                </div>
                                <div class="detail-content">
                                    {{ $anotacao->alergias }}
                                </div>
                            </div>
                            @endif
                            
                            @if($anotacao->medicacoes_procedimentos)
                            <div class="detail-block">
                                <div class="detail-header">
                                    <i class="bi bi-bandages-fill"></i>
                                    <span>Medicações e/ou Procedimentos</span>
                                </div>
                                <div class="detail-content pre-formatted">
                                    {{ $anotacao->medicacoes_procedimentos }}
                                </div>
                            </div>
                            @endif
                            
                            @if($anotacao->descricao)
                            <div class="detail-block">
                                <div class="detail-header">
                                    <i class="bi bi-file-text-fill"></i>
                                    <span>Descrição da Anotação/Evolução</span>
                                </div>
                                <div class="detail-content pre-formatted">
                                    {{ $anotacao->descricao }}
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="consulta-footer">
                            <small>
                                <i class="bi bi-clock"></i>
                                Registrado em {{ \Carbon\Carbon::parse($anotacao->created_at)->format('d/m/Y \à\s H:i') }}
                                @if($anotacao->updated_at != $anotacao->created_at)
                                    • Atualizado em {{ \Carbon\Carbon::parse($anotacao->updated_at)->format('d/m/Y \à\s H:i') }}
                                @endif
                            </small>
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="bi bi-inbox"></i>
                    </div>
                    <h3>Nenhuma anotação registrada</h3>
                    <p>Este paciente ainda não possui anotações de enfermagem no sistema.</p>
                    <a href="{{ route('enfermeiro.anotacao.create', $paciente->idPaciente) }}" class="btn-empty-action">
                        <i class="bi bi-plus-circle-fill"></i>
                        Registrar Primeira Anotação
                    </a>
                </div>
            @endforelse
        </div>

        <div class="page-footer">
            <a href="{{ route('enfermeiro.prontuario') }}" class="btn-voltar">
                <i class="bi bi-arrow-left-circle"></i>
                <span>Voltar para Lista de Pacientes</span>
            </a>
        </div>
    </div>
</main>
@endsection