@extends('unidade.templates.unidadeTemplate')

@section('title', 'Dashboard Unidade')

@section('content')

@php $unidade = auth()->guard('unidade')->user(); @endphp

<link rel="stylesheet" href="{{ asset('css/unidade/dashboardUnidade.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@php 
    // Variáveis de exemplo (substituídas pelo Controller)
    // $medicosCount = 15;
    // $nursesCount = 20;
    // $recepcionistasCount = 10; 
    // $dadosGenero = ['Homens' => 45, 'Mulheres' => 55];
    // $medicosPorEspecialidade = collect([
    //     (object)['especialidadeMedico' => 'Cardio', 'total' => 12],
    //     (object)['especialidadeMedico' => 'Pediatria', 'total' => 8],
    // ]);
    // $dadosLinha = ['meses' => ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'], 'pacientes' => [45, 52, 61, 58, 70, 78]];
@endphp

<div class="dashboard-container">
    <div class="dashboard-header fade-in" style="animation-delay: 0s;">
        <div class="header-content">
            <div class="header-left">
                <h1>Dashboard da Unidade</h1>
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

    <div class="welcome-banner zoom-in" style="animation-delay: 0.1s;">
        <div class="banner-decoration"></div>
        <div class="banner-content">
            <div class="banner-left">
                <img src="{{ asset('img/unidade-logo2.png') }}" alt="Logo Prontuário+" class="banner-logo">
            </div>
            <div class="banner-center">
                {{-- Usa a variável $nomeUnidade enviada pelo Controller --}}
                <h2>Bem-vindo(a),<span class="highlight">{{ $nomeUnidade ?? 'Usuário' }}</span></h2> 
                <p><i class="bi bi-heart-pulse"></i> O Prontuário+ fica feliz com a sua presença e dedicação à saúde.</p>
            </div>
            <div class="banner-right">
                <img src="{{ asset('img/icon-hospital.png') }}" alt="unidade" class="banner-gov-logo">
            </div>
        </div>
    </div>

    <div class="metrics-grid">
        <div class="metric-card slide-up" style="animation-delay: 0.2s;">
            <div class="metric-icon red">
                <i class="bi bi-person-hearts"></i>
            </div>
            <div class="metric-content">
                <span class="metric-label">Médicos Cadastrados</span>
                <strong class="metric-value">{{ $medicosCount ?? 0 }}</strong>
            </div>
        </div>

        <div class="metric-card slide-up" style="animation-delay: 0.3s;">
            <div class="metric-icon green">
                <i class="bi bi-heart-pulse-fill"></i>
            </div>
            <div class="metric-content">
                <span class="metric-label">Enfermeiros Cadastrados</span>
                <strong class="metric-value">{{ $nursesCount ?? 0 }}</strong>
            </div>
        </div>

        {{-- ============================================= --}}
        {{-- --- CORREÇÃO AQUI --- --}}
        {{-- ============================================= --}}
        <div class="metric-card slide-up" style="animation-delay: 0.4s;">
            <div class="metric-icon purple">
                <i class="bi bi-person-check"></i>
            </div>
            <div class="metric-content">
                <span class="metric-label">Recepcionistas Cadastrados</span>
                {{-- Trocado de $patientsCount para $recepcionistasCount --}}
                <strong class="metric-value">{{ $recepcionistasCount ?? 0 }}</strong>
            </div>
        </div>

        {{-- ============================================= --}}
        {{-- --- CORREÇÃO AQUI --- --}}
        {{-- ============================================= --}}
        <div class="metric-card slide-up" style="animation-delay: 0.5s;">
            <div class="metric-icon orange">
                <i class="bi bi-people-fill"></i>
            </div>
            <div class="metric-content">
                <span class="metric-label">Total de Profissionais</span>
                {{-- Trocado de $patientsCount para $recepcionistasCount --}}
                <strong class="metric-value">{{ ($medicosCount ?? 0) + ($nursesCount ?? 0) + ($recepcionistasCount ?? 0) }}</strong>
            </div>
        </div>
    </div>

    <div class="charts-section">
        <div class="section-header fade-in" style="animation-delay: 0.6s;">
            <h2><i class="bi bi-bar-chart-fill"></i> Estatísticas e Análises</h2>
            <p>Acompanhe os dados da sua unidade em tempo real</p>
        </div>

        <div class="charts-grid">
            <div class="chart-card slide-up" style="animation-delay: 0.7s;">
                <div class="chart-header">
                    <div class="chart-title">
                        <i class="bi bi-clipboard2-pulse"></i>
                        <h3>Médicos por Especialidade</h3>
                    </div>
                    <button class="chart-options" title="Opções">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                </div>
                <div class="chart-body">
                    <canvas id="graficoEspecialidades"></canvas>
                </div>
                <div class="chart-footer">
                    <span class="chart-info">
                        <i class="bi bi-info-circle"></i>
                        Distribuição por área médica
                    </span>
                </div>
            </div>

            <div class="chart-card slide-up" style="animation-delay: 0.8s;">
                <div class="chart-header">
                    <div class="chart-title">
                        <i class="bi bi-graph-up-arrow"></i>
                        <h3>Crescimento de Pacientes</h3>
                    </div>
                    <button class="chart-options" title="Opções">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                </div>
                <div class="chart-body">
                    <canvas id="graficoLinha"></canvas>
                </div>
                <div class="chart-footer">
                    <span class="chart-info">
                        <i class="bi bi-info-circle"></i>
                        Evolução mensal de cadastros
                    </span>
                </div>
            </div>

            <div class="chart-card chart-card-small slide-up" style="animation-delay: 0.9s;">
                <div class="chart-header">
                    <div class="chart-title">
                        <i class="bi bi-gender-ambiguous"></i>
                        <h3>Distribuição por Gênero</h3>
                    </div>
                    <button class="chart-options" title="Opções">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                </div>
                <div class="chart-body chart-body-donut">
                    <canvas id="graficoDonutGenero"></canvas>
                </div>
                <div class="chart-footer">
                    <div class="legend-row">
                        <div class="legend-item">
                            <span class="legend-color" style="background: #3C0061;"></span>
                            <span>Homens: {{ $dadosGenero['Homens'] ?? 0 }}</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-color" style="background: #DC2626;"></span>
                            <span>Mulheres: {{ $dadosGenero['Mulheres'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="info-section">
        <div class="info-card-large slide-up" style="animation-delay: 1.0s;">
            <div class="info-icon">
                <i class="bi bi-info-circle-fill"></i>
            </div>
            <div class="info-content">
                <h3>Sistema de Gestão em Saúde</h3>
                <p>O Prontuário+ é uma plataforma completa para gerenciamento de unidades de saúde, desenvolvida para facilitar o trabalho dos profissionais e melhorar o atendimento aos pacientes.</p>
                <ul>
                    <li><i class="bi bi-check-circle-fill"></i> Gestão completa de profissionais</li>
                    <li><i class="bi bi-check-circle-fill"></i> Prontuários eletrônicos seguros</li>
                    <li><i class="bi bi-check-circle-fill"></i> Agendamento inteligente</li>
                    <li><i class="bi bi-check-circle-fill"></i> Relatórios e estatísticas em tempo real</li>
                </ul>
            </div>
        </div>

        <div class="info-card-small slide-up" style="animation-delay: 1.1s;">
            <div class="support-content">
                <i class="bi bi-headset"></i>
                <h4>Precisa de Ajuda?</h4>
                <p>Nossa equipe está pronta para atendê-lo</p>
                <a class="support-btn" href="{{ route('unidade.ajuda') }}">
                    <i class="bi bi-chat-dots-fill"></i>
                    Falar com Suporte
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    // Configurações e Instanciação dos Gráficos Chart.js
    Chart.defaults.font.family = 'Montserrat, sans-serif';
    Chart.defaults.color = '#6B7280';
    const primaryColor = '#3C0061';
    const secondaryColor = '#DC2626';

    // === Gráfico de Especialidades ===
    const ctxEspecialidade = document.getElementById('graficoEspecialidades');
    if (ctxEspecialidade) {
        // Usa a variável real $medicosPorEspecialidade do controller
        const especialidadesData = @json($medicosPorEspecialidade);
        
        const labels = especialidadesData.length > 0 
            ? especialidadesData.map(item => item.especialidadeMedico)
            : ['Sem Dados'];
        
        const data = especialidadesData.length > 0 
            ? especialidadesData.map(item => item.total)
            : [1]; // Mostra 1 para não ficar vazio

        new Chart(ctxEspecialidade, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Médicos',
                    data: data,
                    backgroundColor: 'rgba(60, 0, 97, 0.8)',
                    borderColor: primaryColor,
                    borderWidth: 2,
                    borderRadius: 8,
                    hoverBackgroundColor: primaryColor,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#111827',
                        padding: 12,
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: primaryColor,
                        borderWidth: 1,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y + ' médico(s)';
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
                        ticks: { stepSize: 5, font: { size: 11 } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 } }
                    }
                }
            }
        });
    }

    // === Gráfico de Crescimento (Linha) ===
    const ctxLinha = document.getElementById('graficoLinha');
    if (ctxLinha) {
        // Usa a variável real $dadosLinha do controller
        const dadosLinha = @json($dadosLinha);
        
        const meses = dadosLinha.meses.length > 0 
            ? dadosLinha.meses 
            : ['N/A'];
        
        const pacientes = dadosLinha.pacientes.length > 0 
            ? dadosLinha.pacientes 
            : [0];

        new Chart(ctxLinha, {
            type: 'line',
            data: {
                labels: meses,
                datasets: [{
                    label: 'Pacientes',
                    data: pacientes,
                    borderColor: primaryColor,
                    backgroundColor: 'rgba(60, 0, 97, 0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: primaryColor,
                    pointBorderWidth: 2,
                    pointHoverBackgroundColor: primaryColor,
                    pointHoverBorderColor: '#fff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#111827',
                        padding: 12,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y + ' pacientes';
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
                        ticks: { font: { size: 11 } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 } }
                    }
                }
            }
        });
    }

    // === Gráfico de Gênero (Donut) ===
    const ctxGenero = document.getElementById('graficoDonutGenero');
    if (ctxGenero) {
        // Usa a variável real $dadosGenero do controller
        const homens = {{ $dadosGenero['Homens'] ?? 0 }};
        const mulheres = {{ $dadosGenero['Mulheres'] ?? 0 }};

        new Chart(ctxGenero, {
            type: 'doughnut',
            data: {
                labels: ['Homens', 'Mulheres'],
                datasets: [{
                    data: [homens, mulheres],
                    backgroundColor: [primaryColor, secondaryColor],
                    borderWidth: 3,
                    borderColor: '#fff',
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutout: '70%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#111827',
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                if (total === 0) return context.label + ': 0 (0%)'; // Evita divisão por zero
                                const percentage = ((context.parsed / total) * 100).toFixed(1);
                                return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    }

</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const animatedElements = document.querySelectorAll('.slide-up, .fade-in, .zoom-in');

        animatedElements.forEach(element => {
            const delay = parseFloat(element.style.animationDelay) * 1000 || 0;

            setTimeout(() => {
                element.style.opacity = '1'; 
                element.style.transform = 'translateY(0) scale(1)';
            }, delay);
        });
    });
</script>

@endsection