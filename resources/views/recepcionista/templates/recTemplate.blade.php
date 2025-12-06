@php
    $r = auth()->guard('recepcionista')->user();
@endphp


<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Recepcionista | Prontuário+</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/recepcionista/acolhimento.css') }}">

    @yield('styles')
</head>

<body>
    <div class="sidebar-recepcionista d-flex flex-column">
        <div class="logo-recepcionista-container">
           <a href="{{ route('recepcionista.dashboard') }}">
                <img src="{{ asset('img/recepcionista-logo2.png') }}" class="logo-recepcionista"
                alt="Logo Recepcionista(a)">
        </div>

        <ul class="nav-recepcionista nav-pills flex-column mb-auto">
            <li class="nav-item">
                <a href="{{ route('recepcionista.dashboard') }}" class="nav-link-recepcionista active">
                    <i class="bi bi-person-plus-fill"></i>
                    <span class="nav-link-text-recepcionista">Iniciar Atendimento</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('recepcionista.perfil') }}" class="nav-link-recepcionista">
                    <i class="bi bi-folder2-open"></i>
                    <span class="nav-link-text-recepcionista">Perfil</span>
                </a>
            </li>
        </ul>

        <div class="logout-container-recepcionista">
            <form action="{{ route('recepcionista.logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn logout-btn-recepcionista">
                    <i class="bi bi-box-arrow-left"></i>
                    <span class="logout-text-recepcionista">Sair</span>
                </button>
            </form>
        </div>
    </div>

    <header class="navbar-recepcionista">
        <div class="user-info-recepcionista">
            <a href="{{ route('recepcionista.perfil') }}" class="user-info"
                style="text-decoration: none; color: inherit;">
                <span>{{ Auth::user()->name ?? 'Recepcionista' }}</span>
                <img id="sidebar-foto"
                    src="{{ $r->foto ? asset('storage/' . $r->foto) : asset('img/usuario-de-perfil.png') }}"
                    class="logo-recepcionista" alt="Foto Recepcionista">
            </a>
        </div>
    </header>

    <main class="main-content">
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>

</html>