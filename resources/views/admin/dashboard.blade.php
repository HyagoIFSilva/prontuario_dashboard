@extends('admin.templates.admTemplate')

@section('title', 'Dashboard - Painel Administrativo')

@section('content')

<link rel="stylesheet" href="{{ asset('css/admin/dashboardAdm.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

@php
$admin = auth()->guard('admin')->user();
@endphp

<div class="overview-container">
    <div class="dashboard-header fade-in">
        <div class="header-content">
            <div class="header-left">
                <h1>Dashboard do Ministério da Saúde</h1>
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
                    <img src="{{ asset('img/adm-logo2.png') }}" alt="Logo Prontuário+" class="banner-logo">
                </div>
            </div>
            <div class="banner-center">
                <h2>Bem-vindo(a), <span class="name">{{ $nomeAdmin ?? 'Administrador' }}</span></h2>
                <p><i class="bi bi-heart-pulse"></i>O Prontuário+ fica feliz com a sua presença e dedicação à saúde.</p>
            </div>
            <div class="banner-right">
                <img src="{{ asset('img/ministerio.png') }}" alt="Ilustração Ministério" class="funcionarios-image">
            </div>
        </div>
    </div>

    <div class="metrics">
        <div class="metric-card slide-up" style="animation-delay: 0.6s;">
            <div class="metric-icon red">
                <i class="bi bi-person-hearts"></i>
            </div>
            <div class="metric-content">
                <span class="metric-label">Médicos cadastrados</span>
                <strong class="metric-value">{{ number_format($medicosCount, 0, ',', '.') }}</strong>
            </div>
        </div>

        <div class="metric-card slide-up" style="animation-delay: 0.8s;">
            <div class="metric-icon purple">
                <i class="bi bi-person-check"></i>
            </div>
            <div class="metric-content">
                <span class="metric-label">Pacientes cadastrados</span>
                <strong class="metric-value">{{ number_format($patientsCount, 0, ',', '.') }}</strong>
            </div>
        </div>

        <div class="metric-card slide-up" style="animation-delay: 1.0s;">
            <div class="metric-icon green">
                <i class="bi bi-heart-pulse-fill"></i>
            </div>
            <div class="metric-content">
                <span class="metric-label">Enfermeiros cadastrados</span>
                <strong class="metric-value">{{ number_format($nursesCount, 0, ',', '.') }}</strong>
            </div>
        </div>

        <div class="metric-card slide-up" style="animation-delay: 1.2s;">
            <div class="metric-icon orange">
                <i class="bi bi-hospital"></i>
            </div>
            <div class="metric-content">
                <span class="metric-label">Unidades Cadastradas</span>
                <strong class="metric-value">{{ number_format($unidadesCount, 0, ',', '.') }}</strong>
            </div>
        </div>
    </div>

    <div class="content-wrapper">
        <div class="charts">
            <div class="chart-container slide-up" style="animation-delay: 1.4s;">
                <canvas id="graficoEspecialidades"></canvas>
            </div>

            <div class="chart-container slide-up" style="animation-delay: 1.6s;">
                <canvas id="graficoComparacao"></canvas>
            </div>

            <div class="chart-container slide-up" style="animation-delay: 1.8s;">
                <canvas id="graficoFaixaEtaria"></canvas>
            </div>

            <div class="chart-container slide-up" style="animation-delay: 2.0s;">
                <canvas id="graficoUnidadesPorRegiao"></canvas>
            </div>

            <div class="chart-container slide-up" style="animation-delay: 2.2s;">
                <canvas id="graficoTopEstados"></canvas>
            </div>
        </div>

        <div class="mapa-brasil-container slide-up" style="animation-delay: 2.4s;">
            <h3 class="mapa-title">
                <i class="bi bi-geo-alt-fill"></i>
                Distribuição de Unidades por Região
            </h3>
            <div class="mapa-content">
                <svg viewBox="0 0 800 600" class="brasil-svg">
              
                    <g class="regiao-grupo" data-regiao="Norte" data-valor="{{ $unidadesPorRegiao['Norte'] ?? 0 }}">
                        <path d="M 250 80 L 350 80 L 380 150 L 350 180 L 280 180 L 250 150 Z" 
                              class="regiao-path norte" fill="#0618b9"/>
                        <text x="315" y="140" class="regiao-text">Norte</text>
                        <text x="315" y="160" class="regiao-valor">{{ $unidadesPorRegiao['Norte'] ?? 0 }}</text>
                    </g>

                    <g class="regiao-grupo" data-regiao="Nordeste" data-valor="{{ $unidadesPorRegiao['Nordeste'] ?? 0 }}">
                        <path d="M 380 150 L 520 130 L 550 200 L 520 250 L 450 240 L 380 220 L 350 180 Z" 
                              class="regiao-path nordeste" fill="#0a27d6"/>
                        <text x="450" y="190" class="regiao-text">Nordeste</text>
                        <text x="450" y="210" class="regiao-valor">{{ $unidadesPorRegiao['Nordeste'] ?? 0 }}</text>
                    </g>

                    <g class="regiao-grupo" data-regiao="Centro-Oeste" data-valor="{{ $unidadesPorRegiao['Centro-Oeste'] ?? 0 }}">
                        <path d="M 280 180 L 350 180 L 380 220 L 370 300 L 320 320 L 270 300 Z" 
                              class="regiao-path centro-oeste" fill="#1245fa"/>
                        <text x="325" y="250" class="regiao-text">Centro-Oeste</text>
                        <text x="325" y="270" class="regiao-valor">{{ $unidadesPorRegiao['Centro-Oeste'] ?? 0 }}</text>
                    </g>

                    <g class="regiao-grupo" data-regiao="Sudeste" data-valor="{{ $unidadesPorRegiao['Sudeste'] ?? 0 }}">
                        <path d="M 320 320 L 370 300 L 450 340 L 430 420 L 380 440 L 320 420 Z" 
                              class="regiao-path sudeste" fill="#1555ff"/>
                        <text x="380" y="370" class="regiao-text">Sudeste</text>
                        <text x="380" y="390" class="regiao-valor">{{ $unidadesPorRegiao['Sudeste'] ?? 0 }}</text>
                    </g>

                    <g class="regiao-grupo" data-regiao="Sul" data-valor="{{ $unidadesPorRegiao['Sul'] ?? 0 }}">
                        <path d="M 270 420 L 320 420 L 380 440 L 370 500 L 320 520 L 270 500 Z" 
                              class="regiao-path sul" fill="#4554db"/>
                        <text x="320" y="470" class="regiao-text">Sul</text>
                        <text x="320" y="490" class="regiao-valor">{{ $unidadesPorRegiao['Sul'] ?? 0 }}</text>
                    </g>
                </svg>

                <div class="mapa-legenda">
                    <div class="legenda-item">
                        <span class="legenda-cor" style="background: #0618b9;"></span>
                        <span>Norte: {{ $unidadesPorRegiao['Norte'] ?? 0 }} unidades</span>
                    </div>
                    <div class="legenda-item">
                        <span class="legenda-cor" style="background: #0a27d6;"></span>
                        <span>Nordeste: {{ $unidadesPorRegiao['Nordeste'] ?? 0 }} unidades</span>
                    </div>
                    <div class="legenda-item">
                        <span class="legenda-cor" style="background: #1245fa;"></span>
                        <span>Centro-Oeste: {{ $unidadesPorRegiao['Centro-Oeste'] ?? 0 }} unidades</span>
                    </div>
                    <div class="legenda-item">
                        <span class="legenda-cor" style="background: #1555ff;"></span>
                        <span>Sudeste: {{ $unidadesPorRegiao['Sudeste'] ?? 0 }} unidades</span>
                    </div>
                    <div class="legenda-item">
                        <span class="legenda-cor" style="background: #4554db;"></span>
                        <span>Sul: {{ $unidadesPorRegiao['Sul'] ?? 0 }} unidades</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="info-cards-container">
            <div class="info-card slide-up" style="animation-delay: 2.6s;">
                <h3>Distribuição por Gênero</h3>
                <div class="donut-chart">
                    <canvas id="graficoDonutGenero"></canvas>
                </div>
            </div>

            <div class="info-card slide-up" style="animation-delay: 2.8s;">
                <div class="container-card-idosos">
                    <img class="img-pessoas" src="{{ asset('img/icon-pessoas.png') }}" alt="Ícone de idoso">
                    <h3 class="h3-pessoas">{{ $percentualIdosos }}% IDOSOS</h3>
                </div>
                <img class="img-logo-prontuario" src="{{ asset('img/adm-logo1.png') }}">
            </div>

            <div class="info-card slide-up" style="animation-delay: 3.0s;">
                <h3>UBS Cadastradas</h3>
                <div class="container-ubs">
                    <strong>{{ $unidadesCount }}</strong>
                    <img class="mapa" src="{{ asset('img/icon-mapa.png') }}" alt="Mapa do Brasil">
                </div>
            </div>

            <div class="info-card-user slide-up" style="animation-delay: 3.2s;">
                <img class="dez-pessoas" src="{{ asset('img/icon-dezpessoas.png') }}" alt="Pessoas">
                <h3>A Cada 10 Usuários...</h3>
                <ul>
                    <li>{{ round($dadosGenero['percentualMulheres']/10) }} são mulheres</li>
                    <li>{{ round($dadosGenero['percentualHomens']/10) }} são homens</li>
                    <li>{{ round($percentualIdosos/10) }} são idosos (60+)</li>
                </ul>
                <img src="{{ asset('img/logo-sus.png') }}" alt="SUS">
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const primaryBlue = '#0618b9';
const primaryBlueDark = '#04108a';
const colorPalette = ['#0618b9', '#0a27d6', '#1245fa', '#1555ff', '#4554db', '#00cc66', '#ff0066', '#ff9900', '#9933ff'];

Chart.defaults.font.family = "'Montserrat', sans-serif";

document.querySelectorAll('.regiao-grupo').forEach(grupo => {
    grupo.addEventListener('mouseenter', function() {
        const regiao = this.dataset.regiao;
        const valor = this.dataset.valor;
        this.style.transform = 'scale(1.05)';
        this.style.transformOrigin = 'center';
    });
    
    grupo.addEventListener('mouseleave', function() {
        this.style.transform = 'scale(1)';
    });
});

new Chart(document.getElementById('graficoEspecialidades'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($medicosPorEspecialidade->pluck('especialidadeMedico')) !!},
        datasets: [{
            label: 'Médicos',
            data: {!! json_encode($medicosPorEspecialidade->pluck('total')) !!},
            backgroundColor: primaryBlue,
            borderRadius: 8,
            barThickness: 25
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12
            },
            title: {
                display: true,
                text: 'Top 10 Especialidades Médicas',
                color: primaryBlue,
                font: { size: 16, weight: 'bold' },
                padding: { bottom: 15 }
            }
        },
        scales: {
            x: { beginAtZero: true, grid: { color: '#f0f0f0' } },
            y: { grid: { display: false } }
        }
    }
});

const comparacao = @json($comparacaoProfissionais);
new Chart(document.getElementById('graficoComparacao'), {
    type: 'bar',
    data: {
        labels: comparacao.categorias,
        datasets: [{
            label: 'Total',
            data: comparacao.valores,
            backgroundColor: ['#8c1007', '#0a400c', '#0618b9'],
            borderRadius: 10,
            barThickness: 60
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: { backgroundColor: 'rgba(0, 0, 0, 0.8)', padding: 12 },
            title: {
                display: true,
                text: 'Comparação de Cadastros',
                color: primaryBlue,
                font: { size: 16, weight: 'bold' },
                padding: { bottom: 15 }
            }
        },
        scales: {
            y: { beginAtZero: true, grid: { color: '#f0f0f0' } },
            x: { grid: { display: false } }
        }
    }
});

const faixas = @json($faixasEtarias);
new Chart(document.getElementById('graficoFaixaEtaria'), {
    type: 'polarArea',
    data: {
        labels: Object.keys(faixas),
        datasets: [{
            data: Object.values(faixas),
            backgroundColor: [
                'rgba(6, 24, 185, 0.7)',
                'rgba(10, 39, 214, 0.7)',
                'rgba(18, 69, 250, 0.7)',
                'rgba(21, 85, 255, 0.7)',
                'rgba(69, 84, 219, 0.7)'
            ],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: { padding: 10, font: { size: 11 } }
            },
            title: {
                display: true,
                text: 'Faixa Etária dos Pacientes',
                color: primaryBlue,
                font: { size: 16, weight: 'bold' },
                padding: { bottom: 15 }
            }
        }
    }
});

const unidadesRegiao = @json($unidadesPorRegiao);
new Chart(document.getElementById('graficoUnidadesPorRegiao'), {
    type: 'doughnut',
    data: {
        labels: Object.keys(unidadesRegiao),
        datasets: [{
            data: Object.values(unidadesRegiao),
            backgroundColor: ['#0618b9', '#0a27d6', '#1245fa', '#1555ff', '#4554db'],
            borderWidth: 3,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: { padding: 12, font: { size: 11 } }
            },
            title: {
                display: true,
                text: 'Unidades por Região',
                color: primaryBlue,
                font: { size: 16, weight: 'bold' },
                padding: { bottom: 15 }
            }
        }
    }
});

const topEstados = @json($unidadesPorEstado);
new Chart(document.getElementById('graficoTopEstados'), {
    type: 'bar',
    data: {
        labels: topEstados.map(e => e.ufUnidade),
        datasets: [{
            label: 'Unidades',
            data: topEstados.map(e => e.total),
            backgroundColor: colorPalette,
            borderRadius: 8,
            barThickness: 40
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: { backgroundColor: 'rgba(0, 0, 0, 0.8)', padding: 12 },
            title: {
                display: true,
                text: 'Top 5 Estados com Mais Unidades',
                color: primaryBlue,
                font: { size: 16, weight: 'bold' },
                padding: { bottom: 15 }
            }
        },
        scales: {
            x: { beginAtZero: true, grid: { color: '#f0f0f0' } },
            y: { grid: { display: false } }
        }
    }
});

new Chart(document.getElementById('graficoDonutGenero'), {
    type: 'pie',
    data: {
        labels: ['Homens', 'Mulheres'],
        datasets: [{
            data: [{{ $dadosGenero['Homens'] }}, {{ $dadosGenero['Mulheres'] }}],
            backgroundColor: ['#0000ff', '#8c1007'],
            borderWidth: 3,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: { padding: 12, usePointStyle: true, font: { size: 12 } }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                callbacks: {
                    label: function(context) {
                        const label = context.label || '';
                        const value = context.parsed;
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = ((value / total) * 100).toFixed(1);
                        return `${label}: ${value} (${percentage}%)`;
                    }
                }
            }
        }
    }
});
</script>
@endsection