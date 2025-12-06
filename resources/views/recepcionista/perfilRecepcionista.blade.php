@extends('recepcionista.templates.recTemplate')

@section('content')

<link rel="stylesheet" href="{{ url('/css/recepcionista/perfilRecepcionista.css') }}">

<div class="cadastrar-container">
    <div class="cadastrar-header">
        <i class="bi bi-person-fill"></i>
        <h1>Meu Perfil (Recepcionista)</h1>
    </div>

    <form id="profileForm" action="{{ route('recepcionista.atualizarPerfil') }}" method="POST" enctype="multipart/form-data"> 
        @csrf

        <div class="foto-upload-container">
            @php $recepcionista = auth()->guard('recepcionista')->user(); @endphp 

            <label for="foto" class="foto-upload-label">
                <div class="box-foto">
                    <img id="preview-img"
                         src="{{ $recepcionista->foto ? asset('storage/' . $recepcionista->foto) : asset('img/usuario-de-perfil.png') }}" 
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
            <label for="nomeRecepcionista">Nome:</label>
            <input type="text" name="nomeRecepcionista" id="nomeRecepcionista" value="{{ old('nomeRecepcionista', $recepcionista->nomeRecepcionista) }}" required>
        </div>

        <div class="input-group">
            <label for="emailRecepcionista">E-mail:</label>
            <input type="email" name="emailRecepcionista" id="emailRecepcionista" value="{{ old('emailRecepcionista', $recepcionista->emailRecepcionista) }}" required>
        </div>

        <div class="button-group">
            <a href="{{ route('recepcionista.seguranca') }}" class="btn-trocar-senha">
                <i class="bi bi-key-fill"></i> Trocar Senha
            </a>

            <button type="button" class="save-button" onclick="showConfirmationModal()">
                <i class="bi bi-check-circle"></i> Salvar Alterações
            </button>
        </div>
    </form>
</div>

<!-- Modal de Confirmação -->
<div id="confirmationModal" class="modal-overlay">
    <div class="modal-box">
        <i class="bi bi-exclamation-triangle-fill modal-icon icon-warning"></i>
        <h2>Confirmar Alterações</h2>
        <p>Deseja realmente salvar as alterações no seu perfil?</p>
        <div class="modal-buttons">
            <button type="button" class="modal-btn modal-btn-cancel" onclick="hideConfirmationModal()">Cancelar</button>
            <button type="button" class="modal-btn modal-btn-confirm" onclick="submitProfileForm()">Confirmar</button>
        </div>
    </div>
</div>

<!-- Modal de Sucesso -->
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

<!-- Modal de Erro -->
@if($errors->any())
<div id="errorModal" class="modal-overlay show">
    <div class="modal-box">
        <i class="bi bi-exclamation-triangle-fill modal-icon icon-error"></i>
        <h2>Erro!</h2>
        <p>
            @foreach ($errors->all() as $error)
                {{ $error }}<br>
            @endforeach
        </p>
        <div class="modal-buttons">
            <button type="button" class="modal-btn modal-btn-confirm" onclick="hideErrorModal()">Fechar</button>
        </div>
    </div>
</div>
@endif

<script>
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

    const profileForm = document.getElementById('profileForm');
    const confirmationModal = document.getElementById('confirmationModal');
    const successModal = document.getElementById('successModal');
    const errorModal = document.getElementById('errorModal');

    function showConfirmationModal() {
        if (confirmationModal) confirmationModal.classList.add('show');
    }

    function hideConfirmationModal() {
        if (confirmationModal) confirmationModal.classList.remove('show');
    }

    function submitProfileForm() {
        hideConfirmationModal();
        if (profileForm) profileForm.submit();
    }

    function hideSuccessModal() {
        if (successModal) successModal.classList.remove('show');
    }

    function hideErrorModal() {
        if (errorModal) errorModal.classList.remove('show');
    }

    window.onclick = function(event) {
        if (event.target == confirmationModal) {
            hideConfirmationModal();
        }
        if (event.target == successModal) {
            hideSuccessModal();
        }
        if (event.target == errorModal) {
            hideErrorModal();
        }
    }

    @if(session('success'))
    setTimeout(function() {
        hideSuccessModal();
    }, 5000);
    @endif

    @if($errors->any())
    setTimeout(function() {
        hideErrorModal();
    }, 5000);
    @endif
</script>

@endsection