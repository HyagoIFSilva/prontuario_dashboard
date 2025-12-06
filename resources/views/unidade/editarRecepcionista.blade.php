@extends('unidade.templates.unidadeTemplate')

@section('title', 'Editar Recepcionista')

@section('content')
<link rel="stylesheet" href="{{ asset('css/unidade/editarRecepcionista.css') }}">

<main class="main-dashboard">
    <div class="cadastrar-container">
        {{-- CORREÇÃO: Título corrigido --}}
        <div class="cadastrar-header">
            <i class="bi bi-pencil-square icon"></i>
            <h1>Editar Recepcionista</h1>
        </div>

        {{-- CORREÇÃO: Rota e nome do formulário corrigidos --}}
        <form id="editarRecepcionistaForm" action="{{ route('unidade.recepcionistas.update', $recepcionista->idRecepcionistaPK) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-section-title">Dados do(a) Recepcionista</div>

            <div class="input-group">
                <label for="nomeRecepcionista">Nome Completo</label>
                <input type="text" name="nomeRecepcionista" id="nomeRecepcionista" value="{{ $recepcionista->nomeRecepcionista }}" required>
            </div>

            <div class="input-group">
                {{-- CORREÇÃO: Nome do campo de email corrigido --}}
                <label for="emailRecepcionista">E-mail</label>
                <input type="email" name="emailRecepcionista" id="emailRecepcionista" value="{{ $recepcionista->emailRecepcionista }}" required>
            </div>

            <button type="button" class="save-button" onclick="openEditModal()">Salvar Alterações</button>
        </form>
    </div>
</main>

{{-- MODAL CONFIRMAÇÃO DE EDIÇÃO --}}
<div id="editEnfermeiroModal" class="modal-overlay">
    <div class="modal-content">
        <i class="bi bi-pencil-square"></i>
        <h2>Confirmar Edição</h2>
        <p>Deseja realmente salvar as alterações feitas neste(a) recepcionista?</p>
        <div class="modal-buttons">
            <button type="button" onclick="closeEditModal()" class="btn-cancelar">Cancelar</button>
            <button type="button" onclick="submitEditForm()" class="btn-excluir">Sim, salvar</button>
        </div>
    </div>
</div>

<script>
function openEditModal() {
    document.getElementById('editEnfermeiroModal').style.display = 'flex';
}

function closeEditModal() {
    document.getElementById('editEnfermeiroModal').style.display = 'none';
}

function submitEditForm() {
    document.getElementById('editarRecepcionistaForm').submit();
}

document.querySelectorAll('.modal-overlay').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if(e.target === modal) modal.style.display = 'none';
    });
});
</script>
@endsection