@extends('unidade.templates.unidadeTemplate')

@section('content')

<link rel="stylesheet" href="{{ asset('css/unidade/perfilUnidade.css') }}">
{{-- Adiciona o CSS dos modais (o mesmo da tela de segurança) --}}
<link rel="stylesheet" href="{{ asset('css/unidade/UnidadeSeguranca.css') }}">


@php $unidade = auth()->guard('unidade')->user(); @endphp

<main class="main-dashboard">
    <div class="cadastrar-container">
        <div class="cadastrar-header">
            <i class="bi bi-person-circle icon"></i>
            <h1>Perfil da Unidade</h1>
        </div>

        {{-- ============================================= --}}
        {{-- --- FORMULÁRIO ATUALIZADO (com ID) --- --}}
        {{-- ============================================= --}}
        <form action="{{ route('unidade.perfil.update') }}" method="POST" enctype="multipart/form-data" id="profileForm">
            @csrf
            @method('PUT') 

            {{-- Bloco da Foto --}}
            <div class="foto-upload-container">
                <label for="foto" class="foto-upload-label">

                    <div class="box-foto">
                        {{-- ============================================= --}}
                        {{-- --- CORREÇÃO AQUI (Carregamento da Foto) --- --}}
                        {{-- Busca a foto direto da $unidade->foto --}}
                        {{-- ============================================= --}}
                        <img id="preview-img"
                             src="{{ $unidade->foto ? asset('storage/' . $unidade->foto) : asset('img/usuario-de-perfil.png') }}"
                             alt="Foto atual">
                    </div>

                    <div class="overlay">
                        <i class="bi bi-camera"></i>
                        <span>Alterar Foto</span>
                    </div>
                </label>
                <input type="file" id="foto" name="foto" accept="image/*" hidden onchange="previewFoto(event)">
            </div>

            <div class="input-group">
                <input type="text" name="nomeUnidade" id="nomeUnidade" value="{{ $unidade->nomeUnidade }}" required>
            </div>

            <div class="input-group">
                <input type="email" name="emailUnidade" id="emailUnidade" value="{{ $unidade->emailUnidade }}" required>
            </div>

            <div class="button-group">
                <a href="{{ route('unidade.seguranca') }}" class="btn-trocar-senha">Trocar Senha</a>
                {{-- Botão "Salvar" agora abre o modal --}}
                <button type="button" class="save-button" id="openConfirmationModal">Salvar Alterações</button>
            </div>
        </form>

    </div>
</main>

{{-- ============================================ --}}
{{-- --- HTML DOS MODAIS ADICIONADO --- --}}
{{-- ============================================ --}}

{{-- MODAL DE CONFIRMAÇÃO (Salvar Perfil) --}}
<div id="confirmationModal" class="modal-overlay">
    <div class="modal-box">
        <i class="bi bi-exclamation-triangle-fill modal-icon icon-warning"></i>
        <h2>Salvar Alterações</h2>
        <p>Você tem certeza que deseja salvar as alterações no seu perfil?</p>
        <div class="modal-buttons">
            <button type="button" class="modal-btn modal-btn-cancel" onclick="hideModal('confirmationModal')">Cancelar</button>
            <button type="button" class="modal-btn modal-btn-confirm" id="confirmSaveButton">Confirmar</button>
        </div>
    </div>
</div>

{{-- MODAL DE SUCESSO (Substitui o redirect do JS) --}}
@if(session('success'))
<div id="successModal" class="modal-overlay show">
    <div class="modal-box">
        <i class="bi bi-check-circle-fill modal-icon icon-success"></i>
        <h2>Perfil Atualizado!</h2>
        <p>{{ session('success') }}</p>
        <div class="modal-buttons">
            {{-- Redireciona para o dashboard ao fechar --}}
            <button type="button" class="modal-btn modal-btn-confirm" onclick="window.location.href = '{{ route('unidade.dashboard') }}'">Fechar</button>
        </div>
    </div>
</div>
@endif

{{-- MODAL DE ERRO (Substitui o redirect do JS) --}}
@if(session('error') || $errors->any())
<div id="errorModal" class="modal-overlay show">
    <div class="modal-box">
        <i class="bi bi-x-circle-fill modal-icon icon-error"></i>
        <h2>Erro!</h2>
        <p>
            @if(session('error'))
                {{ session('error') }}
            @else
                Ocorreram erros ao salvar:
                <ul style="text-align: left; margin-top: 10px; font-size: 0.9rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
        </p>
        <div class="modal-buttons">
            <button type="button" class="modal-btn modal-btn-confirm" onclick="hideModal('errorModal')">Fechar</button>
        </div>
    </div>
</div>
@endif


{{-- ============================================ --}}
{{-- --- SCRIPT ATUALIZADO (Com Lógica de Modal) --- --}}
{{-- ============================================ --}}
<script>
    // Função de preview (seu código original, mantido)
    function previewFoto(event) {
        const input = event.target;
        const preview = document.getElementById('preview-img');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // --- LÓGICA DOS MODAIS ---

    // Referências aos elementos
    const form = document.getElementById('profileForm');
    const openButton = document.getElementById('openConfirmationModal');
    const confirmButton = document.getElementById('confirmSaveButton');

    // Funções genéricas para mostrar/esconder
    function showModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) modal.classList.add('show');
    }
    function hideModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) modal.classList.remove('show');
    }

    // 1. Liga o botão "Salvar Alterações" para mostrar o modal
    if(openButton) {
        openButton.addEventListener('click', () => {
            if (form.checkValidity()) {
                showModal('confirmationModal');
            } else {
                form.reportValidity();
            }
        });
    }

    // 2. Liga o botão "Confirmar" (dentro do modal) para enviar o formulário
    if(confirmButton) {
        confirmButton.addEventListener('click', () => {
            hideModal('confirmationModal');
            form.submit();
        });
    }

    // 3. Lógica para fechar os modais ao clicar fora
    window.addEventListener('click', (event) => {
        if (event.target.classList.contains('modal-overlay')) {
            event.target.classList.remove('show');
            
            // Se fechou o modal de sucesso, redireciona para o dashboard
            if(event.target.id === 'successModal') {
                 window.location.href = '{{ route('unidade.dashboard') }}';
            }
        }
    });

    // 4. Remove a lógica antiga de 'window.onload'
    /*
    window.onload = function() {
        @if(session('success'))
        setTimeout(function() {
            window.location.href = "{{ route('unidade.dashboard') }}";
        }, 3000);
        @endif

        @if(session('error'))
        setTimeout(function() {
            window.location.href = "{{ route('unidade.perfil') }}";
        }, 3000);
        @endif
    };
    */
</script>
@endsection