@extends('admin.templates.admTemplate')

@section('title', 'Cadastrar Nova Unidade')

@section('content')

    @php $admin = auth()->guard('admin')->user(); @endphp

    <link rel="stylesheet" href="{{ asset('css/admin/cadastroUnidade.css') }}">

    <main class="main-dashboard">
        <div class="cadastrar-container">
            <div class="cadastrar-header">
                <i class="bi bi-hospital-fill icon"></i>
                <h1>Cadastrar Nova Unidade</h1>
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

            <form action="{{ route('admin.unidades.store') }}" method="POST">
                @csrf

                <div class="form-section-title">Informações da Unidade</div>

                <div class="input-group">
                    <label for="nomeUnidade">Nome da Unidade</label>
                    <input type="text" name="nomeUnidade" id="nomeUnidade" value="{{ old('nomeUnidade') }}"
                        placeholder="Ex: Hospital Municipal Central" required>
                </div>

                <div class="split-group">
                    <div class="input-group">
                        <label for="tipoUnidade">Tipo de Unidade</label>
                        <select name="tipoUnidade" id="tipoUnidade" class="input-select" required>
                            <option value="" disabled {{ old('tipoUnidade') ? '' : 'selected' }}>Selecione um tipo...</option>
                            <option value="Hospital" {{ old('tipoUnidade') == 'Hospital' ? 'selected' : '' }}>Hospital</option>
                            <option value="Clínica" {{ old('tipoUnidade') == 'Clínica' ? 'selected' : '' }}>Clínica</option>
                            <option value="UBS (Unidade Básica de Saúde)" {{ old('tipoUnidade') == 'UBS (Unidade Básica de Saúde)' ? 'selected' : '' }}>UBS (Unidade Básica de Saúde)</option>
                            <option value="UPA (Unidade de Pronto Atendimento)" {{ old('tipoUnidade') == 'UPA (Unidade de Pronto Atendimento)' ? 'selected' : '' }}>UPA (Unidade de Pronto Atendimento)</option>
                            <option value="Posto de Saúde" {{ old('tipoUnidade') == 'Posto de Saúde' ? 'selected' : '' }}>Posto de Saúde</option>
                            <option value="Laboratório" {{ old('tipoUnidade') == 'Laboratório' ? 'selected' : '' }}>Laboratório</option>
                            <option value="Outro" {{ old('tipoUnidade') == 'Outro' ? 'selected' : '' }}>Outro</option>
                        </select>
                    </div>

                    <div class="input-group">
                        <label for="cnpjUnidade" class="form-label">CNPJ</label>
                        <input type="text" name="cnpjUnidade" id="cnpjUnidade" value="{{ old('cnpjUnidade') }}"
                            placeholder="XX.XXX.XXX/0001-XX" maxlength="18">
                    </div>

                    <div class="input-group">
                        <label for="emailUnidade" class="form-label">E-mail da Unidade</label>
                        <input type="email" name="emailUnidade" id="emailUnidade" value="{{ old('emailUnidade') }}"
                           placeholder="Ex: hospital@gmail.com">
                    </div>

                    <div class="input-group">
                        <label for="telefoneUnidade">Telefone</label>
                        <input type="tel" name="telefoneUnidade" id="telefoneUnidade" value="{{ old('telefoneUnidade') }}"
                            placeholder="(XX) XXXXX-XXXX" maxlength="15">
                    </div>
                </div>

                <hr class="section-separator">
                <div class="form-section-title">Endereço</div>

                <div class="input-group">
                    <label for="cepUnidade">CEP</label>
                    <input type="text" name="cepUnidade" id="cepUnidade" value="{{ old('cepUnidade') }}"
                        placeholder="Digite o CEP e aguarde" maxlength="9">
                </div>

                <div class="input-group">
                    <label for="logradouroUnidade">Logradouro</label>
                    <input type="text" name="logradouroUnidade" id="logradouroUnidade"
                        value="{{ old('logradouroUnidade') }}" placeholder="Preenchido automaticamente">
                </div>

                <div class="split-group">
                    <div class="input-group" style="flex: 1;">
                        <label for="numLogradouroUnidade">Número</label>
                        <input type="text" name="numLogradouroUnidade" id="numLogradouroUnidade"
                            value="{{ old('numLogradouroUnidade') }}" placeholder="Digite o número">
                    </div>
                    <div class="input-group" style="flex: 2;">
                        <label for="bairroUnidade">Bairro</label>
                        <input type="text" name="bairroUnidade" id="bairroUnidade" value="{{ old('bairroUnidade') }}"
                            placeholder="Preenchido automaticamente">
                    </div>
                </div>

                <div class="split-group">
                    <div class="input-group">
                        <label for="cidadeUnidade">Cidade</label>
                        <input type="text" name="cidadeUnidade" id="cidadeUnidade" value="{{ old('cidadeUnidade') }}"
                            placeholder="Preenchido automaticamente">
                    </div>
                    <div class="input-group">
                        <label for="ufUnidade">UF</label>
                        <input type="text" name="ufUnidade" id="ufUnidade" value="{{ old('ufUnidade') }}"
                            placeholder="Preenchido automaticamente" maxlength="2">
                    </div>
                </div>

                <div class="split-group">
                    <div class="input-group">
                        <label for="estadoUnidade">Estado</label>
                        <input type="text" name="estadoUnidade" id="estadoUnidade" value="{{ old('estadoUnidade') }}"
                            placeholder="Preenchido automaticamente">
                    </div>
                    <div class="input-group">
                        <label for="paisUnidade">País</label>
                        <input type="text" name="paisUnidade" id="paisUnidade" value="{{ old('paisUnidade') ?? 'Brasil' }}"
                            placeholder="Brasil" readonly>
                    </div>
                </div>

                <button type="submit" class="save-button">Cadastrar Unidade</button>
            </form>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            
            const cepInput = document.getElementById('cepUnidade');
            const logradouroInput = document.getElementById('logradouroUnidade');
            const bairroInput = document.getElementById('bairroUnidade');
            const cidadeInput = document.getElementById('cidadeUnidade');
            const ufInput = document.getElementById('ufUnidade');
            const estadoInput = document.getElementById('estadoUnidade');
            const paisInput = document.getElementById('paisUnidade');
            const numeroInput = document.getElementById('numLogradouroUnidade');
            const cnpjInput = document.getElementById('cnpjUnidade');
            const telefoneInput = document.getElementById('telefoneUnidade');
            const nomeUnidadeInput = document.getElementById('nomeUnidade');

            const ufParaEstado = {
                'AC': 'Acre', 'AL': 'Alagoas', 'AP': 'Amapá', 'AM': 'Amazonas',
                'BA': 'Bahia', 'CE': 'Ceará', 'DF': 'Distrito Federal', 'ES': 'Espírito Santo',
                'GO': 'Goiás', 'MA': 'Maranhão', 'MT': 'Mato Grosso', 'MS': 'Mato Grosso do Sul',
                'MG': 'Minas Gerais', 'PA': 'Pará', 'PB': 'Paraíba', 'PR': 'Paraná',
                'PE': 'Pernambuco', 'PI': 'Piauí', 'RJ': 'Rio de Janeiro', 'RN': 'Rio Grande do Norte',
                'RS': 'Rio Grande do Sul', 'RO': 'Rondônia', 'RR': 'Roraima', 'SC': 'Santa Catarina',
                'SP': 'São Paulo', 'SE': 'Sergipe', 'TO': 'Tocantins'
            };

            cepInput.addEventListener('blur', function () {
                const cep = this.value.replace(/\D/g, '');

                if (cep.length === 8) {
                    logradouroInput.value = 'Buscando...';
                    bairroInput.value = 'Buscando...';
                    cidadeInput.value = 'Buscando...';
                    ufInput.value = '...';
                    estadoInput.value = 'Buscando...';
                    paisInput.value = 'Buscando...';

                    fetch(`https://viacep.com.br/ws/${cep}/json/`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.erro) {
                                alert('CEP não encontrado.');
                                logradouroInput.value = '';
                                bairroInput.value = '';
                                cidadeInput.value = '';
                                ufInput.value = '';
                                estadoInput.value = '';
                                paisInput.value = '';
                            } else {
                                logradouroInput.value = data.logradouro;
                                bairroInput.value = data.bairro;
                                cidadeInput.value = data.localidade;
                                ufInput.value = data.uf;
                                estadoInput.value = ufParaEstado[data.uf] || '';
                                paisInput.value = 'Brasil';
                                numeroInput.focus();
                            }
                        })
                        .catch(error => {
                            console.error('Erro ao buscar o CEP:', error);
                            alert('Não foi possível buscar o CEP. Verifique a sua conexão.');
                            logradouroInput.value = '';
                            bairroInput.value = '';
                            cidadeInput.value = '';
                            ufInput.value = '';
                            estadoInput.value = '';
                            paisInput.value = '';
                        });
                }
            });

            cnpjInput.addEventListener('input', function (e) {
                let value = e.target.value;
                value = value.replace(/\D/g, ''); 
                value = value.replace(/^(\d{2})(\d)/, '$1.$2');
                value = value.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
                value = value.replace(/\.(\d{3})(\d)/, '.$1/$2');
                value = value.replace(/(\d{4})(\d)/, '$1-$2');
                e.target.value = value;
            });

            telefoneInput.addEventListener('input', function (e) {
                let value = e.target.value;
                value = value.replace(/\D/g, '');
                value = value.replace(/^(\d{2})(\d)/g, '($1) $2');
                value = value.replace(/(\d{5})(\d{4})$/, '$1-$2');
                value = value.replace(/(\d{4})(\d{4})$/, '$1-$2'); 
                e.target.value = value.slice(0, 15);
            });

            nomeUnidadeInput.addEventListener('input', function (e) {
                let value = e.target.value;
                value = value.replace(/[0-9]/g, ''); 
                e.target.value = value;
            });
        });
    </script>

@endsection