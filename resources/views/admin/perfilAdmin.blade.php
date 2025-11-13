@extends('admin.templates.admTemplate')

@section('content')

<link rel="stylesheet" href="{{ asset('css/admin/perfilAdmin.css') }}">
{{-- ========================================================= --}}
{{-- --- ADICIONADO: Link para o CSS que contém os modais --- --}}
<link rel="stylesheet" href="{{ asset('css/admin/AdmSeguranca.css') }}">
{{-- ========================================================= --}}


@php $admin = auth()->guard('admin')->user(); @endphp

<main class="main-dashboard">
    <div class="cadastrar-container">
        <div class="cadastrar-header">
            <i class="bi bi-person-circle icon"></i>
            <h1>Perfil do Administrador</h1>
        </div>

        <form action="{{ route('admin.perfil.update') }}" method="POST" enctype="multipart/form-data" id="profileForm">
            @csrf

            <div class="foto-upload-container">
                <label for="foto" class="foto-upload-label">
                    <div class="box-foto">
                        <img id="preview-img"
                            src="{{ $admin->foto ? asset('storage/fotos/' . $admin->foto) : asset('img/usuario-de-perfil.png') }}"
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
                <input type="text" name="nomeAdmin" id="nomeAdmin" value="{{ $admin->nomeAdmin }}" required>
            </div>

            <div class="input-group">
                <input type="email" name="emailAdmin" id="emailAdmin" value="{{ $admin->emailAdmin }}" required>
            </div>

            <div class="button-group">
                <a href="{{ route('admin.seguranca') }}" class="btn-trocar-senha">Trocar Senha</a>
                
                <button type="button" id="openConfirmationModal" class="save-button">Salvar Alterações</button>
            </div>
        </form>
    </div>
</main>

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

{{-- MODAL DE SUCESSO --}}
@if(session('success'))
<div id="successModal" class="modal-overlay show">
    <div class="modal-box">
        <i class="bi bi-check-circle-fill modal-icon icon-success"></i>
        <h2>Perfil Atualizado!</h2>
        <p>{{ session('success') }}</p>
        <div class="modal-buttons">
            <button type="button" class="modal-btn modal-btn-confirm" onclick="window.location.href = '{{ route('admin.dashboard') }}'">Fechar</button>
        </div>
    </div>
</div>
@endif

{{-- MODAL DE ERRO --}}
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

    const form = document.getElementById('profileForm');
    const openButton = document.getElementById('openConfirmationModal');
    const confirmButton = document.getElementById('confirmSaveButton');

    function showModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) modal.classList.add('show');
    }
    function hideModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) modal.classList.remove('show');
    }

    if(openButton) {
        openButton.addEventListener('click', () => {
            if (form.checkValidity()) {
                showModal('confirmationModal');
            } else {
                form.reportValidity();
            }
        });
    }

    if(confirmButton) {
        confirmButton.addEventListener('click', () => {
            hideModal('confirmationModal');
            form.submit();
        });
    }

    window.addEventListener('click', (event) => {
        if (event.target.classList.contains('modal-overlay')) {
            event.target.classList.remove('show');
            
            if(event.target.id === 'successModal') {
                 window.location.href = '{{ route('admin.dashboard') }}';
            }
        }
    });
</script>
@endsection