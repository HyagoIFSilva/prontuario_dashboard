@extends('unidade.templates.unidadeTemplate')

@section('title', 'Editar Médico')

@section('content')

<link rel="stylesheet" href="{{ asset('css/unidade/editarMedico.css') }}">

@php $admin = auth()->guard('admin')->user(); @endphp

<main class="main-dashboard">
    <div class="cadastrar-container">
        <div class="cadastrar-header">
            <i class="bi bi-pencil-square icon"></i>
            <h1>Editar Médico</h1>
        </div>

        {{-- Adicionado o ID 'editMedicoForm' ao formulário --}}
        <form action="{{ route('unidade.medicos.update', $medico->idMedicoPK) }}" method="POST" id="editMedicoForm" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-section-title">Dados do(a) Médico(a)</div>

            {{-- Campo Nome do Médico --}}
            <div class="input-group">
                <label for="nomeMedico">Nome</label>
                <input type="text" name="nomeMedico" id="nomeMedico"
                    value="{{ $medico->nomeMedico ?? '' }}"
                    placeholder="Nome do Médico"
                    required>
            </div>

            {{-- Campo CRM (50% da largura) --}}
            <div class="input-group">
                <label for="crmMedico">CRM</label>
                <input type="text" name="crmMedico" id="crmMedico"
                    value="{{ $medico->crmMedico ?? '' }}"
                    placeholder="CRM"
                    required>
            </div>

            {{-- Linha: Gênero e Email (Lado a Lado - SPLIT GROUP) --}}
            <div class="split-group">
                {{-- Campo Gênero (50% da largura) --}}
                <div class="input-group">
                    <label for="genero">Gênero</label>
                    <select name="genero" id="genero" class="custom-select" required>
                        <option value="">Gênero</option>
                        <option value="Masculino" {{ ($medico->genero ?? '') == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                        <option value="Feminino" {{ ($medico->genero ?? '') == 'Feminino' ? 'selected' : '' }}>Feminino</option>
                        <option value="Outro" {{ ($medico->genero ?? '') == 'Outro' ? 'selected' : '' }}>Outro</option>
                    </select>
                </div>

                <div class="input-group">
                    <label for="emailMedico">Email do Médico</label>
                    <input type="email" name="emailMedico" id="emailMedico"
                    value="{{ $medico->usuario->emailUsuario ?? '' }}"
                    placeholder="Email de Acesso"
                    required>
                </div>

            </div>

            <hr class="section-separator">


            <div class="form-section-title">Dados de Acesso (Login)</div>

            {{-- Campo Nome do Usuário --}}
            <div class="input-group">
                <label for="nomeUsuario">Nome do Usuário</label>
                <input type="text" name="nomeUsuario" id="nomeUsuario"
                    value="{{ $medico->usuario->nomeUsuario ?? '' }}"
                    placeholder="Nome de Usuário"
                    required>
            </div>

            {{-- Campo Email do Usuário --}}
            <div class="input-group">
                <label for="emailUsuario">Email do Usuário</label>
                <input type="email" name="emailUsuario" id="emailUsuario"
                    value="{{ $medico->usuario->emailUsuario ?? '' }}"
                    placeholder="Email de Acesso"
                    required>
            </div>

            {{-- O botão agora chama a função JS para abrir o modal --}}
            <button type="button" onclick="openEditModal()" class="save-button">Salvar Alterações</button>
        </form>
    </div>
</main>

{{-- MODAL CONFIRMAÇÃO DE EDIÇÃO --}}
<div id="editMedicoModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <i class="bi bi-pencil-square"></i>
            <h2>Confirmar Edição</h2>
        </div>
        <p>Deseja realmente salvar as alterações feitas neste(a) médico(a)?</p>
        <div class="modal-buttons">
            <button type="button" onclick="closeEditModal()" class="btn-cancelar">Cancelar</button>
            {{-- Este botão submete o formulário principal --}}
            <button type="button" onclick="submitEditForm()" class="btn-excluir">Sim, salvar</button>
        </div>
    </div>
</div>


<script>
function openEditModal() {
    document.getElementById('editMedicoModal').style.display = 'flex';
}

function closeEditModal() {
    document.getElementById('editMedicoModal').style.display = 'none';
}

function submitEditForm() {
    document.getElementById('editMedicoForm').submit();
    closeEditModal(); // Fecha o modal após submeter
}

document.getElementById('editMedicoModal').addEventListener('click', function(e) {
    if(e.target.id === 'editMedicoModal') {
        closeEditModal();
    }
});
</script>

@endsection
