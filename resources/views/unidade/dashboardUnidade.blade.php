@extends('unidade.templates.unidadeTemplate')

@section('title', 'Dashboard Unidade')

@section('content')

@php
$unidade = auth()->guard('unidade')->user();
$nomeUnidade = $unidade->nomeUnidade ?? 'Unidade de Saúde';
$medicosCount = $medicosCount ?? 15;
$nursesCount = $nursesCount ?? 22;
$recepcionistasCount = $recepcionistasCount ?? 8;
@endphp

<link rel="stylesheet" href="{{ asset('css/unidade/dashboardUnidade.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="dashboard-container">

    <div class="dashboard-header fade-in" style="animation-delay: 0s;">
        <div class="header-content">
            <div class="header-left">
                <h1>Dashboard da Unidade</h1>
                <p class="header-subtitle">Visão geral e estatísticas em tempo real</p>
            </div>
            <div class="header-right">
                <div class="date-badge" aria-label="Data atual">
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
                <img src="{{ asset('img/unidade-logo2.png') }}" alt="Logo Prontuário+" class="banner-logo" onerror="this.onerror=null; this.src='https://placehold.co/300x100/3C0061/ffffff?text=Logo+Prontuario%2B';">
            </div>
            <div class="banner-center">
                <h2>Bem-vindo(a), <span class="highlight">{{ $nomeUnidade }}</span></h2>
                <p><i class="bi bi-heart-pulse"></i> O Prontuário+ fica feliz com a sua presença e dedicação à saúde.</p>
            </div>
            <div class="banner-right">
                <img src="{{ asset('img/icon-hospital.png') }}" alt="Ícone de Hospital" class="banner-gov-logo" onerror="this.onerror=null; this.src='https://placehold.co/200x200/5C0091/ffffff?text=Icone+Hospital';">
            </div>
        </div>
    </div>

    <div class="metrics-grid">
        <div class="metric-card slide-up" style="animation-delay: 0.2s;">
            <div class="metric-icon red" aria-label="Médicos">
                <i class="bi bi-person-hearts"></i>
            </div>
            <div class="metric-content">
                <span class="metric-label">Médicos Cadastrados</span>
                <strong class="metric-value">{{ $medicosCount }}</strong>
            </div>
        </div>

        <div class="metric-card slide-up" style="animation-delay: 0.3s;">
            <div class="metric-icon green" aria-label="Enfermeiros">
                <i class="bi bi-heart-pulse-fill"></i>
            </div>
            <div class="metric-content">
                <span class="metric-label">Enfermeiros Cadastrados</span>
                <strong class="metric-value">{{ $nursesCount }}</strong>
            </div>
        </div>

        <div class="metric-card slide-up" style="animation-delay: 0.4s;">
            <div class="metric-icon purple" aria-label="Recepcionistas">
                <i class="bi bi-person-check"></i>
            </div>
            <div class="metric-content">
                <span class="metric-label">Recepcionistas Cadastrados</span>
                <strong class="metric-value">{{ $recepcionistasCount }}</strong>
            </div>
        </div>

        <div class="metric-card slide-up" style="animation-delay: 0.5s;">
            <div class="metric-icon orange" aria-label="Total de Profissionais">
                <i class="bi bi-people-fill"></i>
            </div>
            <div class="metric-content">
                <span class="metric-label">Total de Profissionais</span>
                <strong class="metric-value">
                    {{ $medicosCount + $nursesCount + $recepcionistasCount }}
                </strong>
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
                    <button class="chart-options" title="Opções do Gráfico">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                </div>
                <div class="chart-body">
                    <canvas id="graficoEspecialidades" role="img" aria-label="Gráfico de Barras de Médicos por Especialidade"></canvas>
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
                        <h3>Evolução de Consultas</h3>
                    </div>
                    <button class="chart-options" title="Opções do Gráfico">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                </div>
                <div class="chart-body">
                    <canvas id="graficoLinha" role="img" aria-label="Gráfico de Linha de Evolução de Consultas"></canvas>
                </div>
                <div class="chart-footer">
                    <span class="chart-info">
                        <i class="bi bi-info-circle"></i>
                        Evolução mensal de consultas
                    </span>
                </div>
            </div>

            <div class="chart-card chart-card-small slide-up" style="animation-delay: 0.9s;">
                <div class="chart-header">
                    <div class="chart-title">
                        <i class="bi bi-gender-ambiguous"></i>
                        <h3>Distribuição por Gênero</h3>
                    </div>
                    <button class="chart-options" title="Opções do Gráfico">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                </div>
                <div class="chart-body chart-body-donut">
                    <canvas id="graficoDonutGenero" role="img" aria-label="Gráfico Donut de Distribuição por Gênero"></canvas>
                </div>
                <div class="chart-footer">
                    <div class="legend-row" aria-label="Legenda de Gêneros">
                        <div class="legend-item">
                            <span class="legend-color" style="background: #3C0061;"></span>
                            <span>Homens: {{ $dadosGenero['Homens'] ?? 0 }}</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-color" style="background: #DC2626;"></span>
                            <span>Mulheres: {{ $dadosGenero['Mulheres'] ?? 0 }}</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-color" style="background: #FFA500;"></span>
                            <span>Outros: {{ $dadosGenero['Outros'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="chart-card slide-up" style="animation-delay: 1.0s;">
                <div class="chart-header">
                    <div class="chart-title">
                        <i class="bi bi-graph-up"></i>
                        <h3>Média de Consultas por Médico</h3>
                    </div>
                    <button class="chart-options" title="Opções do Gráfico">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                </div>
                <div class="chart-body">
                    <canvas id="graficoConsultasPorMedico" role="img" aria-label="Gráfico de Barras de Média de Consultas por Médico"></canvas>
                </div>
                <div class="chart-footer">
                    <span class="chart-info">
                        <i class="bi bi-info-circle"></i>
                        Média diária no mês atual
                    </span>
                </div>
            </div>

            {{-- GRÁFICO NOVO 1: Produtividade por Especialidade --}}
            <div class="chart-card slide-up" style="animation-delay: 1.1s;">
                <div class="chart-header">
                    <div class="chart-title">
                        <i class="bi bi-lightning-charge"></i>
                        <h3>Produtividade por Especialidade</h3>
                    </div>
                    <button class="chart-options" title="Opções do Gráfico">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                </div>
                <div class="chart-body">
                    <canvas id="graficoProdutividade" role="img" aria-label="Gráfico de Produtividade por Especialidade"></canvas>
                </div>
                <div class="chart-footer">
                    <span class="chart-info">
                        <i class="bi bi-info-circle"></i>
                        Consultas realizadas por especialidade no mês atual
                    </span>
                </div>
            </div>

            {{-- GRÁFICO NOVO 2: Composição da Equipe --}}
            <div class="chart-card chart-card-small slide-up" style="animation-delay: 1.2s;">
                <div class="chart-header">
                    <div class="chart-title">
                        <i class="bi bi-pie-chart-fill"></i>
                        <h3>Composição da Equipe</h3>
                    </div>
                    <button class="chart-options" title="Opções do Gráfico">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                </div>
                <div class="chart-body chart-body-donut">
                    <canvas id="graficoComposicao" role="img" aria-label="Gráfico de Composição da Equipe"></canvas>
                </div>
                <div class="chart-footer">
                    <div class="legend-row" aria-label="Legenda da Equipe">
                        <div class="legend-item">
                            <span class="legend-color" style="background: #8c1007;"></span>
                            <span>Médicos: {{ $composicaoEquipe['totais'][0] }} ({{ $composicaoEquipe['percentuais'][0] ?? 0 }}%)</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-color" style="background: #0a400c';"></span>
                            <span>Enfermeiros: {{ $composicaoEquipe['totais'][1] }} ({{ $composicaoEquipe['percentuais'][1] ?? 0 }}%)</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-color" style="background: #606060;"></span>
                            <span>Recepcionistas: {{ $composicaoEquipe['totais'][2] }} ({{ $composicaoEquipe['percentuais'][2] ?? 0 }}%)</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="info-section">
        <div class="info-card-large slide-up" style="animation-delay: 1.3s;">
            <div class="info-icon">
                <i class="bi bi-info-circle-fill"></i>
            </div>
            <div class="info-content">
                <h3>Sistema de Gestão em Saúde</h3>
                <p>O Prontuário+ é uma plataforma completa para gerenciamento de unidades de saúde, desenvolvida para facilitar o trabalho dos profissionais e melhorar o atendimento aos pacientes.</p>
                <ul>
                    <li aria-label="Funcionalidade: Gestão completa de profissionais"><i class="bi bi-check-circle-fill"></i> Gestão completa de profissionais</li>
                    <li aria-label="Funcionalidade: Prontuários eletrônicos seguros"><i class="bi bi-check-circle-fill"></i> Prontuários eletrônicos seguros</li>
                    <li aria-label="Funcionalidade: Agendamento inteligente"><i class="bi bi-check-circle-fill"></i> Agendamento inteligente</li>
                    <li aria-label="Funcionalidade: Relatórios e estatísticas em tempo real"><i class="bi bi-check-circle-fill"></i> Relatórios e estatísticas em tempo real</li>
                </ul>
            </div>
        </div>

        <div class="info-card-small slide-up" style="animation-delay: 1.4s;">
            <div class="support-content">
                <i class="bi bi-headset"></i>
                <h4>Precisa de Ajuda?</h4>
                <p>Nossa equipe está pronta para atendê-lo</p>
                <a class="support-btn" href="{{ route('unidade.ajuda') ?? '#' }}">
                    <i class="bi bi-chat-dots-fill"></i>
                    Falar com Suporte
                </a>
            </div>
        </div>
    </div>

</div>

<script>
    Chart.defaults.font.family = 'Montserrat, sans-serif';
    Chart.defaults.color = '#6B7280';

    const primaryColor = '#3C0061';
    const secondaryColor = '#DC2626';
    const transparentPrimary = 'rgba(60, 0, 97, 0.8)';
    const transparentSecondary = 'rgba(220, 38, 38, 0.8)';

    const generateColors = (count) => {
        const colors = [];
        const baseColors = [
            '#3C0061', '#DC2626', '#8B5CF6', '#F59E0B', '#10B981', '#6366F1'
        ];
        for (let i = 0; i < count; i++) {
            colors.push(baseColors[i % baseColors.length]);
        }
        return colors;
    };

    // Gráfico: Médicos por Especialidade
    const ctxEspecialidade = document.getElementById('graficoEspecialidades');
    if (ctxEspecialidade) {
        const especialidadesData = @json($medicosPorEspecialidade);
        const labels = especialidadesData.length > 0 ? especialidadesData.map(item => item.especialidadeMedico) : ['Sem Dados'];
        const data = especialidadesData.length > 0 ? especialidadesData.map(item => item.total) : [1];
        const colors = generateColors(labels.length);
        const backgroundColors = colors.map(color => color + 'b3');

        new Chart(ctxEspecialidade, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Médicos',
                    data: data,
                    backgroundColor: backgroundColors,
                    borderColor: colors,
                    borderWidth: 1,
                    borderRadius: 6,
                    hoverBackgroundColor: colors,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#111827',
                        callbacks: {
                            label: context => context.parsed.y + ' médico(s)'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.08)' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    }

    // Gráfico: Evolução de Consultas
    const ctxLinha = document.getElementById('graficoLinha');
    if (ctxLinha) {
        const consultasMensal = @json($consultasMensal);
        const meses = consultasMensal.meses.length > 0 ? consultasMensal.meses : ['N/A'];
        const consultas = consultasMensal.totais.length > 0 ? consultasMensal.totais : [0];

        new Chart(ctxLinha, {
            type: 'line',
            data: {
                labels: meses,
                datasets: [{
                    label: 'Consultas',
                    data: consultas,
                    borderColor: primaryColor,
                    backgroundColor: 'rgba(60, 0, 97, 0.1)',
                    pointBackgroundColor: primaryColor,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#111827',
                        callbacks: {
                            label: context => context.parsed.y + ' consultas realizadas'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.08)' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    }

    // Gráfico: Distribuição por Gênero
    const ctxGenero = document.getElementById('graficoDonutGenero');
    if (ctxGenero) {
        const dadosGenero = @json($dadosGenero);
        const homens = dadosGenero['Homens'] ?? 0;
        const mulheres = dadosGenero['Mulheres'] ?? 0;
        const outros = dadosGenero['Outros'] ?? 0;
        const total = homens + mulheres + outros;
        const chartData = total > 0 ? [homens, mulheres, outros] : [1]; 
        const chartLabels = total > 0 ? ['Homens', 'Mulheres', 'Outros'] : ['Sem Dados'];
        const chartColors = total > 0 ? [primaryColor, secondaryColor, '#FFA500'] : ['#A0AEC0']; 

        new Chart(ctxGenero, {
            type: 'doughnut',
            data: {
                labels: chartLabels,
                datasets: [{
                    data: chartData,
                    backgroundColor: chartColors,
                    borderColor: '#fff',
                    borderWidth: 4,
                    hoverOffset: 12
                }]
            },
            options: {
                cutout: '70%',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#111827',
                        callbacks: {
                            label: context => {
                                if (total === 0) return 'Sem dados de profissionais';
                                const value = context.parsed;
                                const perc = ((value / total) * 100).toFixed(1);
                                return `${context.label}: ${value} (${perc}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    // Gráfico: Consultas por Médico
    const ctxConsultasMedico = document.getElementById('graficoConsultasPorMedico');
    if (ctxConsultasMedico) {
        const consultasPorMedicoData = @json($consultasPorMedico);
        const labels = consultasPorMedicoData.length > 0 ? consultasPorMedicoData.map(item => item.nome) : ['Sem Dados'];
        const data = consultasPorMedicoData.length > 0 ? consultasPorMedicoData.map(item => item.media) : [1];
        const colors = generateColors(labels.length);
        const backgroundColors = colors.map(color => color + 'b3');

        new Chart(ctxConsultasMedico, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Média de Consultas/Dia',
                    data: data,
                    backgroundColor: backgroundColors,
                    borderColor: colors,
                    borderWidth: 1,
                    borderRadius: 6,
                    hoverBackgroundColor: colors,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#111827',
                        callbacks: {
                            label: context => 'Média: ' + context.parsed.x + ' consultas/dia'
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.08)' }
                    },
                    y: {
                        grid: { display: false }
                    }
                }
            }
        });
    }

    const ctxProdutividade = document.getElementById('graficoProdutividade');
    if (ctxProdutividade) {
        const produtividadeData = @json($produtividadeEspecialidade);
        const labels = produtividadeData.map(item => item.especialidade);
        const consultas = produtividadeData.map(item => item.consultas);
        const mediaPorMedico = produtividadeData.map(item => item.mediaPorMedico);

        new Chart(ctxProdutividade, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Total de Consultas',
                        data: consultas,
                        backgroundColor: '#0a400c',
                        borderColor: '#0a400c',
                        borderWidth: 2,
                        borderRadius: 6,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Média por Médico',
                        data: mediaPorMedico,
                        backgroundColor: '#8c1007',
                        borderColor: '#8c1007',
                        borderWidth: 2,
                        borderRadius: 6,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        backgroundColor: '#111827',
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += context.parsed.y;
                                if (context.datasetIndex === 0) {
                                    label += ' consultas';
                                } else {
                                    label += ' consultas/médico';
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Total de Consultas'
                        },
                        grid: { color: 'rgba(0,0,0,0.08)' }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Média por Médico'
                        },
                        grid: {
                            drawOnChartArea: false,
                        }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    }

    const ctxComposicao = document.getElementById('graficoComposicao');
    if (ctxComposicao) {
        const composicaoData = @json($composicaoEquipe);
        const total = composicaoData.totais.reduce((a, b) => a + b, 0);

        new Chart(ctxComposicao, {
            type: 'doughnut',
            data: {
                labels: composicaoData.categorias,
                datasets: [{
                    data: composicaoData.totais,
                    backgroundColor: ['#8c1007', '#0a400c', '#606060'],
                    borderColor: '#fff',
                    borderWidth: 4,
                    hoverOffset: 12
                }]
            },
            options: {
                cutout: '65%',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#111827',
                        callbacks: {
                            label: context => {
                                const value = context.parsed;
                                const perc = ((value / total) * 100).toFixed(1);
                                return `${context.label}: ${value} profissionais (${perc}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    // Animações de Scroll
    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.1
    };

    const observerCallback = (entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const el = entry.target;
                const delay = parseFloat(el.style.animationDelay) * 1000 || 0;
                setTimeout(() => {
                    el.style.opacity = '1';
                    el.style.transform = 'translateY(0) scale(1)';
                }, delay);
                observer.unobserve(el);
            }
        });
    };

    const observer = new IntersectionObserver(observerCallback, observerOptions);

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.slide-up, .fade-in, .zoom-in').forEach(el => {
            observer.observe(el);
        });
    });
</script>

@endsection