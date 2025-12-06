<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Painel Médico')</title>

    {{-- CSS do Template --}}
    <link rel="stylesheet" href="{{ asset('css/medico/template.css') }}">

    {{-- CSS específico de cada página --}}
    @stack('styles')

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>
<body>
    {{-- Sidebar --}}
    <div class="sidebar-medico">
        <a href="{{ route('medico.dashboard') }}" class="logo-link-medico">
            <img src="{{ asset('img/medico-logo2.png') }}" alt="Logo Prontuário+" class="logo-medico">
        </a>

        <nav class="nav-medico">
            <a href="{{ route('medico.dashboard') }}" class="nav-item-medico">
                <i class="bi bi-house-door-fill"></i>
                <span class="nav-text-medico">Dashboard</span>
            </a>
            <a href="{{ route('medico.prontuario') }}" class="nav-item-medico">
                <i class="bi bi-journal-medical"></i>
                <span class="nav-text-medico">Consulta</span>
            </a>
            <a href="{{ route('medico.ajuda') }}" class="nav-item-medico">
                <i class="bi bi-question-circle-fill"></i>
                <span class="nav-text-medico">Ajuda</span>
            </a>
            <a href="{{ route('medico.perfil') }}" class="nav-item-medico">
                <i class="bi bi-shield-lock-fill"></i>
                <span class="nav-text-medico">Perfil</span>
            </a>

            {{-- Logout --}}
            <a href="{{ route('medico.logout') }}" 
               onclick="event.preventDefault(); document.getElementById('logout-form-medico').submit();"
               class="nav-item-medico">
                <i class="bi bi-power"></i>
                <span class="nav-text-medico">Sair</span>
            </a>
            <form id="logout-form-medico" action="{{ route('medico.logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </nav>
    </div>

    {{-- Header --}}
    <header class="header-medico">
        <a href="{{ route('medico.perfil') }}" class="user-info-medico" style="text-decoration: none; color: inherit;">
            @php 
                // 🔥 CORREÇÃO: Buscar o médico relacionado ao usuário logado
                $usuario = auth()->user();
                $medico = $usuario ? App\Models\Medico::where('id_usuarioFK', $usuario->idUsuarioPK)->first() : null;
            @endphp
            
            @if($medico && $medico->foto)
                <img src="{{ asset('storage/fotos/' . $medico->foto) }}" alt="Foto do Médico">
            @else
                <img src="{{ asset('img/usuario-de-perfil.png') }}" alt="Foto padrão">
            @endif
            
            {{-- 🔥 AGORA VAI MOSTRAR O NOME CORRETO DO BANCO --}}
            <span>{{ $medico->nomeMedico ?? 'Médico' }}</span>
        </a>
    </header>

    {{-- Conteúdo Dinâmico --}}
    <main class="main-content-medico">
        @yield('content')
    </main>

    {{-- Scripts específicos de cada página --}}
    @stack('scripts')

    {{-- Script para sincronizar header e conteúdo com sidebar --}}
    <script>
        const sidebar = document.querySelector('.sidebar-medico');
        const header = document.querySelector('.header-medico');
        const mainContent = document.querySelector('.main-content-medico');

        sidebar.addEventListener('mouseenter', () => {
            header.style.left = '250px';
            mainContent.style.marginLeft = '250px';
        });

        sidebar.addEventListener('mouseleave', () => {
            header.style.left = '100px';
            mainContent.style.marginLeft = '100px';
        });
    </script>
</body>
</html>