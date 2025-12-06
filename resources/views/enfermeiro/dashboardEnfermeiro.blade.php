@extends('enfermeiro.templates.enfermeiroTemplate')

@section('title', 'Dashboard - Prontuário+')

@section('content')

<link rel="stylesheet" href="{{ asset('css/enfermeiro/dashboardEnfermeiro.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="main-dashboard">
    <div class="overview-container">

        <div class="dashboard-header fade-in">
            <div class="header-content">
                <div class="header-left">
                    <h1>Dashboard do Enfermeiro</h1>
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
        
        <div class="welcome-banner zoom-in-banner" style="animation-delay: 0.4s;">
            <div class="banner-decoration"></div>
            <div class="banner-content">
                <div class="banner-left">
                    <div class="banner-logo-container">
                        <img src="{{ asset('img/enfermeiro-logo2.png') }}" alt="Logo Prontuário+" class="banner-logo">
                    </div>
                </div>
                <div class="banner-center">
                    <h2>Bem-vindo(a), <span class="name">{{ $enfermeiro->nomeEnfermeiro ?? 'Enfermeiro(a)' }}</span></h2>
                    <p><i class="bi bi-heart-pulse"></i>O Prontuário+ fica feliz com a sua presença e dedicação à saúde.</p>
                </div>
                <div class="banner-right">
                    <img src="{{ asset('img/enfermeiros.png') }}" alt="Ilustração de Enfermeiros" class="funcionarios-image">
                </div>
            </div>
        </div>

        <div class="metrics">
            <div class="metric-card slide-up" style="animation-delay: 1.0s;">
                <div class="metric-icon green">
                    <i class="bi bi-hospital-fill"></i>
                </div>
                <div class="metric-content">
                    <span class="metric-label">Unidade de Atuação</span>
                    <strong class="metric-value metric-text-small">{{ $unidadeAtuacao ?? 'N/A' }}</strong>
                </div>
            </div>

            <div class="metric-card slide-up" style="animation-delay: 1.2s;">
                <div class="metric-icon orange">
                    <i class="bi bi-person-fill-gear"></i>
                </div>
                <div class="metric-content">
                    <span class="metric-label">Total Pacientes Próprios</span>
                    <strong class="metric-value">{{ $pacientesProprios ?? 0 }}</strong>
                </div>
            </div>

            <div class="metric-card slide-up" style="animation-delay: 1.4s;">
                <div class="metric-icon blue">
                    <i class="bi bi-clipboard2-check-fill"></i>
                </div>
                <div class="metric-content">
                    <span class="metric-label">Triagens Concluídas Hoje</span>
                    <strong class="metric-value">{{ $atendimentosDia ?? 0 }}</strong>
                </div>
            </div>
        </div>

        <div class="charts-section">
            <div class="section-header slide-up" style="animation-delay: 1.6s;">
                <h2><i class="bi bi-bar-chart-fill"></i> Estatísticas e Análises</h2>
                <p>Monitore seus pacientes e atendimentos em tempo real</p>
            </div>
            
            <div class="charts-grid">
                {{-- GRÁFICO DE CLASSIFICAÇÕES DE RISCO --}}
                <div class="chart-container slide-up" style="animation-delay: 1.8s;">
                    <div class="chart-header">
                        <h3><i class="bi bi-activity"></i> Classificações de Risco</h3>
                        <p>Últimos 6 meses</p>
                    </div>
                    <div class="chart-wrapper">
                        <canvas id="graficoClassificacoesRisco"></canvas>
                    </div>
                </div>
                
                {{-- GRÁFICO DE TIPOS DE ALERGIA --}}
                <div class="chart-container slide-up" style="animation-delay: 2.0s;">
                    <div class="chart-header">
                        <h3><i class="bi bi-clipboard-pulse"></i> Tipos de Alergia</h3>
                        <p>Registros mais frequentes</p>
                    </div>
                    <div class="chart-wrapper">
                        <canvas id="graficoVazio"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    // Configurações de cores e tema
    const chartColors = {
        primary: '#0e5f12',
        primaryLight: '#66bb6a',
        primaryDark: '#0a400c',
        secondary: '#2196F3',
        warning: '#FF9800',
        danger: '#F44336',
        info: '#00BCD4',
        success: '#4CAF50',
        grid: 'rgba(0, 0, 0, 0.06)',
        text: '#4b5563'
    };

    // Configurações globais do Chart.js
    Chart.defaults.font.family = 'Montserrat, sans-serif';
    Chart.defaults.color = chartColors.text;
    Chart.defaults.plugins.legend.labels.usePointStyle = true;
    Chart.defaults.plugins.legend.labels.padding = 15;
    Chart.defaults.plugins.tooltip.backgroundColor = 'rgba(0, 0, 0, 0.85)';
    Chart.defaults.plugins.tooltip.padding = 12;
    Chart.defaults.plugins.tooltip.cornerRadius = 8;
    Chart.defaults.plugins.tooltip.titleFont = { size: 13, weight: 'bold' };
    Chart.defaults.plugins.tooltip.bodyFont = { size: 12 };

    // Gráfico de Classificações de Risco (Linhas Suaves)
    const ctxClassificacoes = document.getElementById('graficoClassificacoesRisco').getContext('2d');
    const dadosClassificacoes = @json($dadosClassificacoesRisco);

    new Chart(ctxClassificacoes, {
        type: 'line',
        data: {
            labels: dadosClassificacoes.labels,
            datasets: dadosClassificacoes.datasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                y: { 
                    beginAtZero: true,
                    ticks: { 
                        stepSize: 1,
                        font: { size: 12, weight: '500' },
                        color: chartColors.text,
                        padding: 8
                    },
                    grid: { 
                        color: chartColors.grid,
                        drawBorder: false,
                        lineWidth: 1
                    },
                    border: { display: false }
                },
                x: { 
                    grid: { display: false },
                    ticks: { 
                        font: { size: 12, weight: '500' },
                        color: chartColors.text,
                        padding: 8
                    },
                    border: { display: false }
                }
            },
            plugins: {
                legend: { 
                    display: true,
                    position: 'bottom',
                    labels: { 
                        usePointStyle: true,
                        padding: 15,
                        font: { size: 12, weight: '600' },
                        boxWidth: 10,
                        boxHeight: 10,
                        color: chartColors.text
                    }
                },
                title: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return ' ' + context.dataset.label + ': ' + context.parsed.y + ' registro(s)';
                        }
                    }
                }
            },
            elements: {
                line: {
                    tension: 0.4, // Linhas suaves
                    borderWidth: 3
                },
                point: {
                    radius: 4,
                    hoverRadius: 6,
                    borderWidth: 2,
                    hoverBorderWidth: 3
                }
            }
        }
    });

    const ctxTiposAlergia = document.getElementById('graficoVazio').getContext('2d');
    const dadosTiposAlergia = @json($dadosTiposAlergia);

    new Chart(ctxTiposAlergia, {
        type: 'bar',
        data: {
            labels: dadosTiposAlergia.labels,
            datasets: [{
                label: 'Registros',
                data: dadosTiposAlergia.data,
                backgroundColor: 'rgba(14, 95, 18, 0.85)',
                borderColor: chartColors.primary,
                borderWidth: 0,
                borderRadius: 10,
                barThickness: 35,
                hoverBackgroundColor: chartColors.primaryLight,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'x',
            scales: {
                y: { 
                    beginAtZero: true,
                    ticks: { 
                        stepSize: 1,
                        font: { size: 12, weight: '500' },
                        color: chartColors.text,
                        padding: 8
                    },
                    grid: { 
                        color: chartColors.grid,
                        drawBorder: false,
                        lineWidth: 1
                    },
                    border: { display: false }
                },
                x: { 
                    grid: { display: false },
                    ticks: { 
                        font: { size: 12, weight: '500' },
                        color: chartColors.text,
                        autoSkip: false,
                        maxRotation: 45,
                        minRotation: 0,
                        padding: 8
                    },
                    border: { display: false }
                }
            },
            plugins: {
                legend: { 
                    display: false
                },
                title: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return ' Total: ' + context.parsed.y + ' registro(s)';
                        }
                    }
                }
            }
        }
    });
</script>

@endsection

{{-- CSS ADICIONAL --}}
<style>
/* Melhorias nos gráficos */
.charts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
    gap: 25px;
    margin-top: 30px;
}

.chart-container {
    background: linear-gradient(145deg, #ffffff 0%, #f9fafb 100%);
    border-radius: 16px;
    padding: 25px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid rgba(14, 95, 18, 0.08);
    height: 450px;
    display: flex;
    flex-direction: column;
}

.chart-container:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(14, 95, 18, 0.15);
    border-color: rgba(14, 95, 18, 0.15);
}

.chart-header {
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid rgba(14, 95, 18, 0.1);
}

.chart-header h3 {
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--primary-green);
    margin: 0 0 5px 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.chart-header h3 i {
    font-size: 1.1rem;
}

.chart-header p {
    margin: 0;
    font-size: 0.85rem;
    color: var(--text-secondary);
    font-weight: 500;
}

.chart-wrapper {
    flex: 1;
    position: relative;
    min-height: 0;
}

.chart-wrapper canvas {
    width: 100% !important;
    height: 100% !important;
}

/* Responsividade para gráficos */
@media (max-width: 1400px) {
    .charts-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .charts-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .chart-container {
        height: 380px;
        padding: 20px;
    }
    
    .chart-header h3 {
        font-size: 1.1rem;
    }
}

@media (max-width: 576px) {
    .chart-container {
        height: 350px;
        padding: 15px;
    }
}
</style>