@extends('admin.templates.admTemplate')

@section('title', 'Editar Paciente')

@section('content')

@php $admin = auth()->guard('admin')->user(); @endphp

<link rel="stylesheet" href="{{ asset('css/admin/editarPaciente.css') }}">

<main class="main-dashboard">
    <div class="cadastrar-container">
        <div class="cadastrar-header">
            <i class="bi bi-pencil-square icon"></i>
            <h1>Editar Paciente</h1>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger" style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                <strong>Erros encontrados:</strong>
                <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.pacientes.update', $paciente->idPaciente) }}" method="POST" id="editPacienteForm">
            @csrf
            @method('PUT')

            <div class="form-section-title">Dados do Paciente</div>

            <div class="input-group">
                <label for="nomePaciente">Nome Completo</label>
                <input type="text" 
                       id="nomePaciente" 
                       name="nomePaciente" 
                       value="{{ old('nomePaciente', $paciente->nomePaciente) }}" 
                       required>
            </div>

            <div class="input-group">
                <label for="cpfPaciente">CPF</label>
                <input type="text" 
                       id="cpfPaciente" 
                       name="cpfPaciente" 
                       value="{{ old('cpfPaciente', $paciente->cpfPaciente) }}" 
                       maxlength="11"
                       required>
            </div>

            <div class="input-group">
                <label for="dataNascPaciente">Data de Nascimento</label>
                <input type="date" 
                       id="dataNascPaciente" 
                       name="dataNascPaciente" 
                       value="{{ old('dataNascPaciente', $paciente->dataNascPaciente ? $paciente->dataNascPaciente->format('Y-m-d') : '') }}" 
                       required>
            </div>

            <div class="split-group">
                <div class="input-group">
                    <label for="generoPaciente">Gênero</label>
                    <select id="generoPaciente" name="generoPaciente" required>
                        <option value="">Selecione</option>
                        <option value="Masculino" {{ old('generoPaciente', $paciente->generoPaciente) == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                        <option value="Feminino" {{ old('generoPaciente', $paciente->generoPaciente) == 'Feminino' ? 'selected' : '' }}>Feminino</option>
                        <option value="Outro" {{ old('generoPaciente', $paciente->generoPaciente) == 'Outro' ? 'selected' : '' }}>Outro</option>
                    </select>
                </div>

                <div class="input-group">
                    <label for="cartaoSusPaciente">Cartão SUS</label>
                    <input type="text" 
                           id="cartaoSusPaciente" 
                           name="cartaoSusPaciente" 
                           value="{{ old('cartaoSusPaciente', $paciente->cartaoSusPaciente) }}"
                           maxlength="20">
                </div>
            </div>

            <div class="input-group">
                <label for="statusPaciente">Status</label>
                <select id="statusPaciente" name="statusPaciente" required>
                    <option value="1" {{ old('statusPaciente', $paciente->statusPaciente) == 1 ? 'selected' : '' }}>Ativo</option>
                    <option value="0" {{ old('statusPaciente', $paciente->statusPaciente) == 0 ? 'selected' : '' }}>Inativo</option>
                </select>
            </div>

            <button type="button" onclick="openEditModal()" class="save-button">Salvar Alterações</button>
        </form>
    </div>
</main>

{{-- MODAL CONFIRMAÇÃO --}}
<div id="editPacienteModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <i class="bi bi-pencil-square"></i>
            <h2>Confirmar Edição</h2>
        </div>
        <p>Deseja realmente salvar as alterações feitas neste(a) paciente?</p>
        <div class="modal-buttons">
            <button type="button" onclick="closeEditModal()" class="btn-cancelar">Cancelar</button>
            <button type="button" onclick="submitEditForm()" class="btn-excluir">Sim, salvar</button>
        </div>
    </div>
</div>

<script>
    function openEditModal() {
        console.log('Abrindo modal');
        document.getElementById('editPacienteModal').style.display = 'flex';
    }

    function closeEditModal() {
        console.log('Fechando modal');
        document.getElementById('editPacienteModal').style.display = 'none';
    }

    function submitEditForm() {
        console.log('Submetendo formulário');
        const form = document.getElementById('editPacienteForm');
        console.log('Action:', form.action);
        console.log('Dados:', new FormData(form));
        form.submit();
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('editPacienteModal')?.addEventListener('click', e => {
            if (e.target.id === 'editPacienteModal') closeEditModal();
        });
    });
</script>
@endsection