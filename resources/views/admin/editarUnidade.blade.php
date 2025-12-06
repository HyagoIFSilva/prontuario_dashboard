@extends('admin.templates.admTemplate')

@section('title', 'Editar Unidade')

@section('content')

@php $admin = auth()->guard('admin')->user(); @endphp

{{-- Reutilizando o mesmo CSS para manter a consistência --}}
<link rel="stylesheet" href="{{ asset('css/admin/editarUnidade.css') }}">

<main class="main-dashboard">
    <div class="cadastrar-container">
        <div class="cadastrar-header">
            <i class="bi bi-pencil-square icon"></i>
            <h1>Editar Unidade de Saúde</h1>
        </div>

        @if ($errors->any())
            <div class="alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- O formulário aponta para a rota 'admin.unidades.update' e envia os dados da unidade específica --}}
        <form id="editarUnidadeForm" action="{{ route('admin.unidades.update', $unidade->idUnidadePK) }}" method="POST">
            @csrf
            @method('PUT') {{-- Importante: Informa ao Laravel que esta é uma requisição de atualização --}}

            <div class="form-section-title">Informações da Unidade</div>

            <div class="input-group">
                <label for="nomeUnidade">Nome da Unidade</label>
                {{-- O campo já vem preenchido com o valor atual --}}
                <input type="text" name="nomeUnidade" id="nomeUnidade" value="{{ old('nomeUnidade', $unidade->nomeUnidade) }}" required>
            </div>

            <div class="split-group">
                <div class="input-group">
                    <label for="cnpjUnidade">CNPJ</label>
                    <input type="text" name="cnpjUnidade" id="cnpjUnidade" value="{{ old('cnpjUnidade', $unidade->cnpjUnidade) }}" required>
                </div>
                <div class="input-group">
                    <label for="emailUnidade">E-mail</label>
                    <input type="email" name="emailUnidade" id="emailUnidade" value="{{ old('emailUnidade', $unidade->emailUnidade) }}" required>
                </div>
            </div>
            
            <div class="form-section-title">Detalhes e Contato</div>

            <div class="split-group">
                <div class="input-group">
                    <label for="tipoUnidade">Tipo de Unidade</label>
                    <input type="text" name="tipoUnidade" id="tipoUnidade" value="{{ old('tipoUnidade', $unidade->tipoUnidade) }}" placeholder="Ex: Hospital, UBS">
                </div>

                <div class="input-group">
                    <label for="telefoneUnidade">Telefone</label>
                    <input type="text" name="telefoneUnidade" id="telefoneUnidade" value="{{ old('telefoneUnidade', $unidade->telefoneUnidade) }}">
                </div>
            </div>
            
            <div class="form-section-title">Endereço</div>

            <div class="split-group">
                <div class="input-group">
                    <label for="cepUnidade">CEP</label>
                    <input type="text" name="cepUnidade" id="cepUnidade" value="{{ old('cepUnidade', $unidade->cepUnidade) }}" required>
                </div>
                <div class="input-group">
                    <label for="logradouroUnidade">Logradouro</label>
                    <input type="text" name="logradouroUnidade" id="logradouroUnidade" value="{{ old('logradouroUnidade', $unidade->logradouroUnidade) }}" required>
                </div>
            </div>
            <div class="split-group">
                <div class="input-group">
                    <label for="numLogradouroUnidade">Número</label>
                    <input type="text" name="numLogradouroUnidade" id="numLogradouroUnidade" value="{{ old('numLogradouroUnidade', $unidade->numLogradouroUnidade) }}" required>
                </div>
                <div class="input-group">
                    <label for="bairroUnidade">Bairro</label>
                    <input type="text" name="bairroUnidade" id="bairroUnidade" value="{{ old('bairroUnidade', $unidade->bairroUnidade) }}" required>
                </div>
            </div>
            <div class="split-group">
                <div class="input-group">
                    <label for="cidadeUnidade">Cidade</label>
                    <input type="text" name="cidadeUnidade" id="cidadeUnidade" value="{{ old('cidadeUnidade', $unidade->cidadeUnidade) }}" required>
                </div>
                <div class="input-group">
                    <label for="ufUnidade">UF</label>
                    <input type="text" name="ufUnidade" id="ufUnidade" value="{{ old('ufUnidade', $unidade->ufUnidade) }}" maxlength="2" required>
                </div>
            </div>
            <div class="split-group">
                <div class="input-group">
                    <label for="estadoUnidade">Estado</label>
                    <input type="text" name="estadoUnidade" id="estadoUnidade" value="{{ old('estadoUnidade', $unidade->estadoUnidade) }}" required>
                </div>
                <div class="input-group">
                    <label for="paisUnidade">País</label>
                    <input type="text" name="paisUnidade" id="paisUnidade" value="{{ old('paisUnidade', $unidade->paisUnidade) }}" required>
                </div>
            </div>

            <button type="button" class="save-button" onclick="openEditModal()">Salvar Alterações</button>
        </form>
    </div>
</main>

{{-- MODAL DE CONFIRMAÇÃO (reutilizado da sua outra tela) --}}
<div id="editModal" class="modal-overlay">
    <div class="modal-content">
        <i class="bi bi-pencil-square"></i>
        <h2>Confirmar Edição</h2>
        <p>Deseja realmente salvar as alterações feitas nesta unidade?</p>
        <div class="modal-buttons">
            <button type="button" onclick="closeEditModal()" class="btn-cancelar">Cancelar</button>
            <button type="button" onclick="submitEditForm()" class="btn-excluir">Sim, salvar</button>
        </div>
    </div>
</div>

<script>
    function openEditModal() {
        document.getElementById('editModal').style.display = 'flex';
    }

    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }

    function submitEditForm() {
        document.getElementById('editarUnidadeForm').submit();
    }

    // Fechar modal clicando fora
    document.getElementById('editModal').addEventListener('click', function(e) {
        if (e.target.id === 'editModal') {
            closeEditModal();
        }
    });
</script>

@endsection
