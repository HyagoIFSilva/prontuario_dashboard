@extends('medico.templates.medicoTemplate')

@section('title', 'Dashboard - Prontuário+')

@section('content')

<link rel="stylesheet" href="{{ asset('css/medico/dashboardMedico.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="main-dashboard">
    <div class="overview-container">

        {{-- HEADER --}}
        <div class="dashboard-header fade-in" style="animation-delay: 0s;">
            <div class="header-content">
                <div class="header-left">
                    <h1>Dashboard do Médico</h1>
                    <p class="header-subtitle">Visão geral e estatísticas em tempo real</p>
                </div>
                <div class="header-right">
                    <div class="date-badge">
                        <i class="bi bi-calendar3"></i>
                        <span>{{ \Carbon\Carbon::now()->locale('pt_BR')->isoFormat('DD [de] MMMM [de] YYYY') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- BANNER DE BOAS-VINDAS --}}
        <div class="welcome-banner zoom-in-banner" style="animation-delay: 0.1s;">
            <div class="banner-decoration"></div>
            <div class="banner-content">
                <div class="banner-left">
                    <div class="banner-logo-container">
                        <img src="{{ asset('img/medico-logo2.png') }}" alt="Logo Prontuário+" class="banner-logo">
                    </div>
                </div>
                <div class="banner-center">
                    <h2>Bem-vindo(a), <span class="doctor-name">Dr(a). {{ $nome ?? 'Médico(a)' }}</span></h2>
                    <p><i class="bi bi-heart-pulse"></i>O Prontuário+ fica feliz com a sua presença e dedicação à saúde.</p>
                </div>
                <div class="banner-right">
                    <img src="{{ asset('img/funcionarios.png') }}" alt="Ilustração de Médicos" class="funcionarios-image">
                </div>
            </div>
        </div>
        
        <div class="content-wrapper">
            <div class="metrics">
                <div class="metric-card slide-up" style="animation-delay: 0.2s;">
                    <div class="metric-icon">
                        <img src="{{ asset('img/icon-pessoa.png') }}" alt="Ícone Pacientes" class="icon-metric-img">
                    </div>
                    <div class="metric-content">
                        <span class="metric-label">Pacientes ativos</span>
                        <strong class="metric-value">{{ $patientsCount ?? 0 }}</strong>
                    </div>
                </div>

                <div class="metric-card slide-up" style="animation-delay: 0.3s;">
                    <div class="metric-icon">
                        <img src="{{ asset('img/icon-prontuario.png') }}" alt="Ícone Prontuários" class="icon-metric-img">
                    </div>
                    <div class="metric-content">
                        <span class="metric-label">Total de Prontuários</span>
                        <strong class="metric-value">{{ $prontuariosCount ?? 0 }}</strong>
                    </div>
                </div>

                <div class="metric-card metric-card-highlight slide-up" style="animation-delay: 0.35s;">
                    <div class="metric-icon">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                    <div class="metric-content">
                        <span class="metric-label">Consultas Hoje</span>
                        <strong class="metric-value">{{ $consultasHoje ?? 0 }}</strong>
                    </div>
                </div>

                <div class="metric-card metric-card-info slide-up" style="animation-delay: 0.4s;">
                    <div class="metric-icon">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <div class="metric-content">
                        <span class="metric-label">Média Diária (30d)</span>
                        <strong class="metric-value">{{ $mediaDiaria ?? 0 }}</strong>
                    </div>
                </div>
            </div>

        </div>

        <div class="quick-actions-section slide-up" style="animation-delay: 0.5s;">
            <div class="section-header">
                <div class="header-title-group">
                    <h2>
                        <i class="bi bi-lightning-charge"></i> 
                        Ações Rápidas
                    </h2>
                    <p>Acesso direto às funcionalidades principais</p>
                </div>
            </div>

            <div class="quick-actions-grid">
                {{-- ATALHO PARA CONSULTAS --}}
                <a href="{{ route('medico.prontuario') }}" class="quick-action-card">
                    <div class="action-icon">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                    <div class="action-content">
                        <h4>Consultas</h4>
                        <p>Gerenciar atendimentos</p>
                        <span class="action-badge">{{ $consultasHoje ?? 0 }} hoje</span>
                    </div>
                    <div class="action-arrow">
                        <i class="bi bi-arrow-right"></i>
                    </div>
                </a>
            </div>
        </div>

        {{-- SEÇÃO DE CONSULTAS EM ABERTO --}}
        @if(isset($consultasEmAberto) && $consultasEmAberto->count() > 0)
        <div class="quick-actions-section slide-up" style="animation-delay: 0.6s;">
            <div class="section-header">
                <div class="header-title-group">
                    <h2>
                        <i class="bi bi-clock-history"></i> 
                        Consultas em Aberto
                        <span class="badge-count">{{ $totalConsultasEmAberto }}</span>
                    </h2>
                    <p>Pacientes aguardando seu atendimento</p>
                </div>
                <a href="{{ route('medico.consultas.index') }}" class="btn-view-all">
                    <span>Ver todas</span>
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <div class="consultas-grid">
                @foreach($consultasEmAberto as $consulta)
                <div class="consulta-card">
                    <div class="consulta-header">
                        <div class="patient-info">
                            <div class="patient-avatar">
                                @if($consulta->paciente->fotoPaciente)
                                    <img src="{{ asset('storage/' . $consulta->paciente->fotoPaciente) }}" alt="Foto">
                                @else
                                    <i class="bi bi-person-fill"></i>
                                @endif
                            </div>
                            <div class="patient-details">
                                <h4>{{ $consulta->paciente->nomePaciente }}</h4>
                                <span class="patient-id">Cartão SUS: {{ $consulta->paciente->cartaoSusPaciente }}</span>
                            </div>
                        </div>
                        <div class="risk-badge risk-{{ $consulta->classificacao_risco ?? 'verde' }}">
                            @php
                                $riscoLabels = [
                                    'vermelho' => 'Emergência',
                                    'laranja' => 'Urgência',
                                    'amarelo' => 'Pouco Urgente',
                                    'verde' => 'Não Urgente',
                                    'azul' => 'Eletivo'
                                ];
                            @endphp
                            {{ $riscoLabels[$consulta->classificacao_risco ?? 'verde'] }}
                        </div>
                    </div>
                    
                    <div class="consulta-body">
                        <div class="info-row">
                            <i class="bi bi-clipboard-pulse"></i>
                            <span><strong>Queixa:</strong> {{ Str::limit($consulta->queixa_principal ?? 'Não informada', 60) }}</span>
                        </div>
                        <div class="info-row">
                            <i class="bi bi-hospital"></i>
                            <span><strong>Unidade:</strong> {{ $consulta->unidade->nomeUnidade ?? 'N/A' }}</span>
                        </div>
                        <div class="info-row">
                            <i class="bi bi-clock"></i>
                            <span><strong>Chegada:</strong> {{ $consulta->dataConsulta->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>

                    <div class="consulta-footer">
                        <a href="{{ route('medico.consultas.show', $consulta->idConsultaPK) }}" class="btn-atender">
                            <i class="bi bi-stethoscope"></i>
                            <span>Iniciar Atendimento</span>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- SEÇÃO DE GRÁFICOS APRIMORADOS --}}
        <div class="charts-section">
            <div class="section-header fade-in" style="animation-delay: 0.7s;">
                <h2><i class="bi bi-bar-chart-fill"></i> Estatísticas e Análises</h2>
                <p>Acompanhe seus atendimentos em tempo real</p>
            </div>

            <div class="charts-grid">
        
                {{-- GRÁFICO DE BARRAS --}}
                <div class="chart-card chart-card-large slide-up" style="animation-delay: 0.8s;">
                    <div class="chart-header">
                        <div class="chart-title">
                            <i class="bi bi-calendar-week"></i>
                            <h3>Atendimentos por Mês</h3>
                        </div>
                        <div class="chart-actions">
                            <button class="chart-action-btn" title="Exportar dados">
                                <i class="bi bi-download"></i>
                            </button>
                            <button class="chart-action-btn" title="Configurações">
                                <i class="bi bi-gear"></i>
                            </button>
                        </div>
                    </div>
                    <div class="chart-body">
                        <canvas id="graficoBarras"></canvas>
                    </div>
                    <div class="chart-footer">
                        <span class="chart-info">
                            <i class="bi bi-info-circle"></i>
                            Distribuição mensal de consultas realizadas no ano atual
                        </span>
                    </div>
                </div>

                {{-- GRÁFICO DE LINHA --}}
                <div class="chart-card chart-card-large slide-up" style="animation-delay: 0.9s;">
                    <div class="chart-header">
                        <div class="chart-title">
                            <i class="bi bi-graph-up-arrow"></i>
                            <h3>Evolução de Atendimentos</h3>
                        </div>
                        <div class="chart-actions">
                            <button class="chart-action-btn" title="Exportar dados">
                                <i class="bi bi-download"></i>
                            </button>
                            <button class="chart-action-btn" title="Configurações">
                                <i class="bi bi-gear"></i>
                            </button>
                        </div>
                    </div>
                    <div class="chart-body">
                        <canvas id="graficoLinha"></canvas>
                    </div>
                    <div class="chart-footer">
                        <span class="chart-info">
                            <i class="bi bi-info-circle"></i>
                            Tendência de crescimento nos últimos 12 meses
                        </span>
                    </div>
                </div>

                {{-- GRÁFICO DE CLASSIFICAÇÃO DE RISCO --}}
                <div class="chart-card chart-card-small slide-up" style="animation-delay: 1s;">
                    <div class="chart-header">
                        <div class="chart-title">
                            <i class="bi bi-shield-fill-exclamation"></i>
                            <h3>Classificação de Risco</h3>
                        </div>
                    </div>
                    <div class="chart-body chart-body-donut">
                        <canvas id="graficoRisco"></canvas>
                    </div>
                    <div class="chart-footer">
                        <div class="legend-row">
                            @if(isset($atendimentosPorRisco) && count($atendimentosPorRisco) > 0)
                                @foreach($atendimentosPorRisco as $item)
                                    <div class="legend-item">
                                        <span class="legend-color" style="background: {{ $item->classificacao_risco == 'vermelho' ? '#DC2626' : ($item->classificacao_risco == 'laranja' ? '#F97316' : ($item->classificacao_risco == 'amarelo' ? '#FBBF24' : ($item->classificacao_risco == 'verde' ? '#10B981' : '#3B82F6'))) }};"></span>
                                        <span>{{ $item->label }} ({{ $item->total }})</span>
                                    </div>
                                @endforeach
                            @else
                                <div class="legend-item">
                                    <span class="legend-color" style="background: #ccc;"></span>
                                    <span>Nenhum dado disponível</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    Chart.defaults.font.family = 'Montserrat, sans-serif';
    Chart.defaults.color = '#6B7280';
    const primaryColor = '#8c1007';
    const secondaryColor = '#a33e38';
    
    const atendimentosData = @json($atendimentosPorMes ?? []);
    const evolucaoData = @json($evolucaoAtendimentos ?? []);
    const tiposAtendimentoData = @json($tiposAtendimento ?? []); 
    const riscoData = @json($atendimentosPorRisco ?? []); 

    // GRÁFICO DE BARRAS APRIMORADO
    if (document.getElementById('graficoBarras')) {
        const labelsBarras = Object.keys(atendimentosData);
        const dataBarras = Object.values(atendimentosData);

        const ctxBarras = document.getElementById('graficoBarras').getContext('2d');
        new Chart(ctxBarras, {
            type: 'bar',
            data: {
                labels: labelsBarras,
                datasets: [{
                    label: 'Atendimentos',
                    data: dataBarras,
                    backgroundColor: function(context) {
                        const chart = context.chart;
                        const {ctx, chartArea} = chart;
                        if (!chartArea) return primaryColor;
                        
                        const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                        gradient.addColorStop(0, 'rgba(140, 16, 7, 0.5)');
                        gradient.addColorStop(1, 'rgba(140, 16, 7, 1)');
                        return gradient;
                    },
                    borderColor: primaryColor,
                    borderWidth: 2,
                    borderRadius: 10,
                    hoverBackgroundColor: primaryColor,
                    barThickness: 'flex',
                    maxBarThickness: 60,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(17, 24, 39, 0.95)',
                        padding: 16,
                        titleColor: '#fff',
                        titleFont: { size: 14, weight: 'bold' },
                        bodyColor: '#fff',
                        bodyFont: { size: 13 },
                        borderColor: primaryColor,
                        borderWidth: 2,
                        displayColors: false,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                return '🩺 ' + context.parsed.y + ' atendimento(s)';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            stepSize: 5,
                            font: { size: 12, weight: '500' },
                            color: '#6B7280'
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { size: 12, weight: '600' },
                            color: '#374151'
                        }
                    }
                },
                animation: {
                    duration: 1500,
                    easing: 'easeInOutQuart'
                }
            }
        });
    }

    // GRÁFICO DE LINHA APRIMORADO
    if (document.getElementById('graficoLinha')) {
        const labelsLinha = evolucaoData.map(item => item.label);
        const dataLinha = evolucaoData.map(item => item.total);

        const ctxLinha = document.getElementById('graficoLinha').getContext('2d');
        new Chart(ctxLinha, {
            type: 'line',
            data: {
                labels: labelsLinha,
                datasets: [{
                    label: 'Total de Atendimentos',
                    data: dataLinha,
                    borderColor: primaryColor,
                    backgroundColor: function(context) {
                        const chart = context.chart;
                        const {ctx, chartArea} = chart;
                        if (!chartArea) return 'rgba(140, 16, 7, 0.1)';
                        
                        const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                        gradient.addColorStop(0, 'rgba(140, 16, 7, 0)');
                        gradient.addColorStop(1, 'rgba(140, 16, 7, 0.3)');
                        return gradient;
                    },
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointRadius: 6,
                    pointHoverRadius: 9,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: primaryColor,
                    pointBorderWidth: 3,
                    pointHoverBackgroundColor: primaryColor,
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 3,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(17, 24, 39, 0.95)',
                        padding: 16,
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 13 },
                        borderColor: primaryColor,
                        borderWidth: 2,
                        cornerRadius: 8,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return '📊 ' + context.parsed.y + ' atendimentos';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            font: { size: 12, weight: '500' },
                            color: '#6B7280'
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { size: 12, weight: '600' },
                            color: '#374151'
                        }
                    }
                },
                animation: {
                    duration: 2000,
                    easing: 'easeInOutQuart'
                }
            }
        });
    }

    // GRÁFICO DE CLASSIFICAÇÃO DE RISCO
    if (document.getElementById('graficoRisco')) {
        const labelsRisco = riscoData.map(item => item.label);
        const dataRisco = riscoData.map(item => item.total);
        const coresRisco = riscoData.map(item => {
            const cores = {
                'vermelho': '#DC2626',
                'laranja': '#F97316',
                'amarelo': '#FBBF24',
                'verde': '#10B981',
                'azul': '#3B82F6'
            };
            return cores[item.classificacao_risco] || '#9CA3AF';
        });

        const ctxRisco = document.getElementById('graficoRisco').getContext('2d');
        new Chart(ctxRisco, {
            type: 'doughnut',
            data: {
                labels: labelsRisco,
                datasets: [{
                    data: dataRisco,
                    backgroundColor: coresRisco,
                    borderWidth: 4,
                    borderColor: '#fff',
                    hoverOffset: 15,
                    hoverBorderWidth: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutout: '65%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(17, 24, 39, 0.95)',
                        padding: 16,
                        borderColor: '#fff',
                        borderWidth: 2,
                        cornerRadius: 8,
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 13 },
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed / total) * 100).toFixed(1);
                                return context.label + ': ' + percentage + '% (' + context.parsed + ')';
                            }
                        }
                    }
                },
                animation: {
                    animateRotate: true,
                    animateScale: true,
                    duration: 1500
                }
            }
        });
    }

    // GRÁFICO DONUT (TIPOS DE ATENDIMENTO)
    if (document.getElementById('graficoDonut')) {
        const labelsDonut = tiposAtendimentoData.map(item => item.label);
        const dataDonut = tiposAtendimentoData.map(item => item.total);
        
        const ctxDonut = document.getElementById('graficoDonut').getContext('2d');
        new Chart(ctxDonut, {
            type: 'doughnut',
            data: {
                labels: labelsDonut,
                datasets: [{
                    data: dataDonut,
                    backgroundColor: [primaryColor, secondaryColor, '#d0655d', '#c24545'],
                    borderWidth: 4,
                    borderColor: '#fff',
                    hoverOffset: 15,
                    hoverBorderWidth: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutout: '65%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(17, 24, 39, 0.95)',
                        padding: 16,
                        borderColor: primaryColor,
                        borderWidth: 2,
                        cornerRadius: 8,
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 13 },
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed / total) * 100).toFixed(1);
                                return context.label + ': ' + percentage + '% (' + context.parsed + ')';
                            }
                        }
                    }
                },
                animation: {
                    animateRotate: true,
                    animateScale: true,
                    duration: 1500
                }
            }
        });
    }

    // ANIMAÇÕES DOS ELEMENTOS
    document.addEventListener('DOMContentLoaded', function() {
        const animatedElements = document.querySelectorAll('.slide-up, .fade-in, .zoom-in-banner');
        
        animatedElements.forEach(element => {
            const delayString = element.style.animationDelay || '0s';
            const delay = parseFloat(delayString) * 1000;
            
            setTimeout(() => {
                element.style.animationName = element.classList.contains('slide-up') ? 'fadeInSlideUp' : 
                                             element.classList.contains('fade-in') ? 'fadeIn' : 
                                             'zoomIn'; 
                
                element.style.animationDuration = '0.8s'; 
                element.style.animationTimingFunction = 'ease-out'; 
                element.style.animationFillMode = 'forwards';
                element.style.animationDelay = '0s';
            }, delay);
        });

        // Ajustar gráficos no mobile
        window.addEventListener('resize', function() {
            if (window.innerWidth < 768) {
                document.querySelectorAll('.chart-body').forEach(chartBody => {
                    chartBody.style.height = '250px';
                });
                document.querySelectorAll('.chart-body-donut').forEach(chartBody => {
                    chartBody.style.height = '280px';
                });
            }
        });
    });
</script>

@endsection