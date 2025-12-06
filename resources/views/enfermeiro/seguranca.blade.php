@extends('enfermeiro.templates.enfermeiroTemplate')

@section('content')

@php
    if (!isset($enfermeiro)) {
        $usuarioLogado = auth()->guard('enfermeiro')->user(); 
        if ($usuarioLogado) {
            $enfermeiro = \App\Models\Enfermeiro::where('id_usuario', $usuarioLogado->idUsuarioPK)->first();
        }
    }
@endphp

<link rel="stylesheet" href="{{ asset('css/enfermeiro/seguranca.css') }}">

<main class="main-dashboard">
    <div class="security-container">
        <h1><i class="bi bi-shield-lock-fill"></i> Configurações de Segurança</h1>
        <p>Gerencie suas configurações de segurança e privacidade.</p>

        <div class="security-section">
            <h2><i class="bi bi-key-fill"></i> Alterar Senha</h2>

            {{-- Mensagens de erro de validação --}}
            @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $erro)
                    <li>{{ $erro }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form id="securityForm" action="{{ route('enfermeiro.alterarSenha') }}" method="POST" class="security-form">
                @csrf
                
                <div class="form-group">
                    <label for="senha_atual">Senha Atual:</label>
                    <div class="password-wrapper">
                        <input type="password" id="senha_atual" name="senha_atual" required>
                        <i class="bi bi-eye toggle-password" onclick="togglePassword('senha_atual', this)"></i>
                    </div>
                </div>
            
                <div class="form-group">
                    <label for="nova_senha">Nova Senha:</label>
                    <div class="password-wrapper">
                        <input type="password" id="nova_senha" name="nova_senha" required>
                        <i class="bi bi-eye toggle-password" onclick="togglePassword('nova_senha', this)"></i>
                    </div>
                    <small class="form-hint">Mínimo de 8 caracteres.</small>
                </div>

                <div class="form-group">
                    <label for="nova_senha_confirmation">Confirmar Nova Senha:</label>
                    <div class="password-wrapper">
                        <input type="password" id="nova_senha_confirmation" name="nova_senha_confirmation" required>
                        <i class="bi bi-eye toggle-password" onclick="togglePassword('nova_senha_confirmation', this)"></i>
                    </div>
                </div>

                <button type="button" class="btn-primary" onclick="showConfirmationModal()">
                    Alterar Senha
                </button>
            </form>
        </div>
    </div>
</main>


<div id="confirmationModal" class="modal-overlay">
    <div class="modal-box">
        <i class="bi bi-exclamation-triangle-fill modal-icon icon-warning"></i>
        <h2>Alterar Senha</h2>
        <p>Você tem certeza que deseja alterar sua senha?</p>
        <div class="modal-buttons">
            <button type="button" class="modal-btn modal-btn-cancel" onclick="hideConfirmationModal()">Cancelar</button>
            <button type="button" class="modal-btn modal-btn-confirm" onclick="submitSecurityForm()">Confirmar</button>
        </div>
    </div>
</div>

@if(session('success'))
<div id="successModal" class="modal-overlay show">
    <div class="modal-box">
        <i class="bi bi-check-circle-fill modal-icon icon-success"></i>
        <h2>Sucesso!</h2>
        <p>{{ session('success') }}</p>
        <div class="modal-buttons">
            <button type="button" class="modal-btn modal-btn-confirm" onclick="hideSuccessModal()">Fechar</button>
        </div>
    </div>
</div>
@endif


<script>
    const securityForm = document.getElementById('securityForm');
    const confirmationModal = document.getElementById('confirmationModal');
    const successModal = document.getElementById('successModal');

    function togglePassword(inputId, iconElement) {
        const input = document.getElementById(inputId);
        
        if (input.type === 'password') {
            input.type = 'text';
            iconElement.classList.remove('bi-eye');
            iconElement.classList.add('bi-eye-slash');
        } else {
            input.type = 'password';
            iconElement.classList.remove('bi-eye-slash');
            iconElement.classList.add('bi-eye');
        }
    }

    function showConfirmationModal() {
        if (confirmationModal) confirmationModal.classList.add('show');
    }
    
    function hideConfirmationModal() {
        if (confirmationModal) confirmationModal.classList.remove('show');
    }
    
    function submitSecurityForm() {
        hideConfirmationModal();
        if (securityForm) securityForm.submit();
    }

    function hideSuccessModal() {
        if (successModal) {
            successModal.classList.remove('show');
            window.location.href = "{{ route('enfermeiro.perfil') }}";
        }
    }

    window.onclick = function(event) {
        if (event.target == confirmationModal) {
            hideConfirmationModal();
        }
        if (event.target == successModal) {
            hideSuccessModal();
        }
    }
</script>

@endsection