@extends('admin.templates.admTemplate')

@section('content')

{{-- Garante que o CSS está linkado --}}
<link rel="stylesheet" href="{{ asset('css/admin/AdmSeguranca.css') }}">

{{-- CSS Adicional (Para o "Olhinho") --}}
<style>
    .form-group .input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }
    .form-group .input-wrapper input {
        width: 100%;
        padding-right: 45px !important; 
    }
    .form-group .icon-right-pass {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #6B7280; 
        font-size: 1.1rem;
    }
    .form-group .icon-right-pass:hover {
        color: #111827; 
    }
</style>

@php $admin = auth()->guard('admin')->user(); @endphp

<main class="main-dashboard">
    <div class="security-container">
        <h1 class="main-title"><i class="bi bi-shield-lock-fill"></i> Configurações de Segurança</h1>
        <p class="main-subtitle">Gerencie suas configurações de segurança e privacidade</p>

        @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="security-section">
            <h2><i class="bi bi-key-fill"></i> Alterar Senha</h2>

            <form action="{{ route('admin.alterarSenha') }}" method="POST" class="security-form" id="securityForm">
                @csrf
                
                <div class="form-group">
                    <label for="senha_atual">Senha Atual:</label>
                    <div class="input-wrapper">
                        <input type="password" id="senha_atual" name="senha_atual" required>
                        <i id="toggleSenhaAtual" class="bi bi-eye-slash icon-right-pass"></i>
                    </div>
                    @error('senha_atual')
                        <small class="error-message">{{ $message }}</small>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="nova_senha">Nova Senha:</label>
                    <div class="input-wrapper">
                        <input type="password" id="nova_senha" name="nova_senha" required>
                        <i id="toggleNovaSenha" class="bi bi-eye-slash icon-right-pass"></i>
                    </div>
                    <small class="form-hint">Mínimo de 8 caracteres, incluindo números e letras</small>
                    @error('nova_senha')
                        <small class="error-message">{{ $message }}</small>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="nova_senha_confirmation">Confirmar Nova Senha:</label>
                    <div class="input-wrapper">
                        <input type="password" id="nova_senha_confirmation" name="nova_senha_confirmation" required>
                        <i id="toggleNovaSenhaConf" class="bi bi-eye-slash icon-right-pass"></i>
                    </div>
                </div>
                
                <button type="button" id="openConfirmationModal" class="btn-primary">Alterar Senha</button>
            </form>
        </div>

    </div>
</main>

{{-- MODAL DE CONFIRMAÇÃO --}}
<div id="confirmationModal" class="modal-overlay">
    <div class="modal-box">
        <i class="bi bi-exclamation-triangle-fill modal-icon icon-warning"></i>
        <h2>Alterar Senha</h2>
        <p>Você tem certeza que deseja alterar sua senha? Esta ação é irreversível.</p>
        <div class="modal-buttons">
            <button type="button" class="modal-btn modal-btn-cancel" onclick="hideConfirmationModal()">Cancelar</button>
            <button type="button" class="modal-btn modal-btn-confirm" id="confirmChangePassword">Confirmar Alteração</button>
        </div>
    </div>
</div>

{{-- MODAL DE SUCESSO --}}
@if(session('success'))
<div id="successModal" class="modal-overlay show">
    <div class="modal-box">
        <i class="bi bi-check-circle-fill modal-icon icon-success"></i>
        <h2>Senha Alterada!</h2>
        <p>{{ session('success') }}</p>
        <div class="modal-buttons">
            <button type="button" class="modal-btn modal-btn-confirm" 
                    onclick="window.location.href = '{{ route('admin.perfil') }}'">
                Fechar
            </button>
        </div>
    </div>
</div>
@endif


<script>
    const form = document.getElementById('securityForm'); 
    const openConfirmationModalButton = document.getElementById('openConfirmationModal');
    const confirmationModal = document.getElementById('confirmationModal');
    const confirmChangePasswordButton = document.getElementById('confirmChangePassword');
    const successModal = document.getElementById('successModal');

    function redirectToProfile() {
        window.location.href = '{{ route('admin.perfil') }}';
    }

    function showConfirmationModal() {
        if (confirmationModal) confirmationModal.classList.add('show');
    }
    function hideConfirmationModal() {
        if (confirmationModal) confirmationModal.classList.remove('show');
    }
    function hideSuccessModal() {
        if (successModal) {
             successModal.classList.remove('show');
             redirectToProfile(); 
        }
    }

    if (openConfirmationModalButton) {
        openConfirmationModalButton.addEventListener('click', (event) => {
            if (form.checkValidity()) {
                showConfirmationModal();
            } else {
                form.reportValidity();
            }
        });
    }

    if (confirmChangePasswordButton) {
        confirmChangePasswordButton.addEventListener('click', () => {
            hideConfirmationModal();
            form.submit(); 
        });
    }

    window.onclick = function(event) {
        if (event.target == confirmationModal) {
            hideConfirmationModal();
        }
        if (event.target == successModal) {
            hideSuccessModal(); 
        }
    }

    function setupPasswordToggle(inputId, toggleId) {
        const input = document.getElementById(inputId);
        const toggle = document.getElementById(toggleId);
        
        if (input && toggle) {
            toggle.addEventListener('click', () => {
                const isPassword = input.type === 'password';
                input.type = isPassword ? 'text' : 'password';
                toggle.classList.toggle('bi-eye');
                toggle.classList.toggle('bi-eye-slash');
            });
        }
    }

    setupPasswordToggle('senha_atual', 'toggleSenhaAtual');
    setupPasswordToggle('nova_senha', 'toggleNovaSenha');
    setupPasswordToggle('nova_senha_confirmation', 'toggleNovaSenhaConf');
</script>

@endsection