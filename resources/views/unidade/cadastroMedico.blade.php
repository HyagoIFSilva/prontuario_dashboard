<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <!-- Token CSRF essencial para o fetch na rota Laravel -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Prontuário+ | Cadastro Médico</title>

    <link rel="stylesheet" href="{{ url('/css/unidade/cadastroMedico.css') }}">
    <link rel="shortcut icon" href="{{url('img/logo-azul.png')}}" type="image/x-icon" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body>
    <main class="main-container">
        <!-- Lado Esquerdo com Ilustração -->
        <div class="left-side">
            <img src="{{ asset('img/unidade-logo2.png') }}" alt="Logo ilustrativa">
        </div>

        <!-- Lado Direito com Formulário -->
        <div class="right-side">
            <div class="login-content fade-in">
                <h2>Cadastro de Médico</h2>
                
                <!-- Caixa de Mensagens (Erros/Sucesso) -->
                <div id="form-messages" class="alert-error" style="display: none;"></div>
                
                <form id="cadastroMedicoForm">
                    @csrf
                    
                    <!-- Campo Nome do Médico -->
                    <div class="input-group">
                        <label for="nomeMedico">Nome completo</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-user-doctor icon-left"></i>
                            <input type="text" id="nomeMedico" name="nomeMedico" required placeholder="Nome completo do médico" />
                        </div>
                    </div>

                    <!-- Campo CRM -->
                    <div class="input-group">
                        <label for="crmMedico">CRM</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-id-card icon-left"></i>
                            <input type="text" id="crmMedico" name="crmMedico" required maxlength="9" placeholder="Ex: 123456/SP" />
                        </div>
                    </div>

                    <!-- Campo Email -->
                    <div class="input-group">
                        <label for="emailUsuario">E-mail</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-envelope icon-left"></i>
                            <input type="email" id="emailUsuario" name="emailUsuario" required placeholder="email@exemplo.com" />
                        </div>
                    </div>
                    
                    <!-- Campo Gênero (Reintroduzido) -->
                    <div class="input-group">
                        <label for="genero">Gênero</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-venus-mars icon-left"></i>
                            <select id="genero" name="genero" class="input-select">
                                <option value="" disabled selected>Selecione o Gênero (Opcional)</option>
                                <option value="Masculino">Masculino</option>
                                <option value="Feminino">Feminino</option>
                                <option value="Outro">Outro</option>
                            </select>
                        </div>
                    </div>

                    <!-- Campo Especialidade (Select) -->
                    <div class="input-group">
                        <label for="especialidadeMedico">Especialidade</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-stethoscope icon-left"></i>
                            <select id="especialidadeMedico" name="especialidadeMedico" class="input-select" required>
                                <option value="" disabled selected>Selecione a especialidade...</option>
                                <option value="Clínico Geral">Clínico Geral</option>
                                <option value="Cardiologia">Cardiologia</option>
                                <option value="Dermatologia">Dermatologia</option>
                                <option value="Ginecologia e Obstetrícia">Ginecologia e Obstetrícia</option>
                                <option value="Neurologia">Neurologia</option>
                                <option value="Ortopedia">Ortopedia</option>
                                <option value="Pediatria">Pediatria</option>
                                <option value="Psiquiatria">Psiquiatria</option>
                                <option value="Urologia">Urologia</option>
                                <option value="Endocrinologia">Endocrinologia</option>
                                <option value="Oftalmologia">Oftalmologia</option>
                                <option value="Otorrinolaringologia">Otorrinolaringologia</option>
                                <option value="Outra">Outra</option>
                            </select>
                        </div>
                    </div>

                    <!-- Campo Unidade (Somente Leitura) -->
                    <div class="input-group">
                        <label for="unidade">Unidade de Trabalho</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-hospital icon-left"></i>
                            <!-- Se houver a variável Blade $unidadeLogada, exibe o nome e envia o ID -->
                            @if(isset($unidadeLogada))
                                <input type="text" id="unidade" name="unidade_display" value="{{ $unidadeLogada->nomeUnidade }}"
                                    readonly style="background-color: #f0f0f0; color: #666; cursor: not-allowed;" />
                                <input type="hidden" name="unidade_id" value="{{ $unidadeLogada->idUnidadePK }}">
                            @else
                                <input type="text" value="Nenhuma unidade encontrada" readonly
                                    style="background-color: #f0f0f0; color: #999; cursor: not-allowed;" />
                                <input type="hidden" name="unidade_id" value="1"> 
                            @endif
                        </div>
                    </div>

                    <button class="btn-login" type="submit">CADASTRAR</button>
                </form>
            </div>
        </div>
    </main>

    <script>
        // Substitua a seção do JavaScript existente por este código:

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('cadastroMedicoForm');
    const button = form.querySelector('button');
    const messagesDiv = document.getElementById('form-messages');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // 1. Lógica de Submissão do Formulário
    form.addEventListener('submit', function (event) {
        event.preventDefault();
        const formData = new FormData(form);
        button.disabled = true;
        button.textContent = 'Cadastrando...';
        messagesDiv.style.display = 'none';
        messagesDiv.innerHTML = '';

        fetch("{{ route('unidade.medicos.register') }}", {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(result => {
            if (result.success) {
                // Sucesso
                messagesDiv.innerHTML = result.message + " Redirecionando...";
                messagesDiv.classList.remove('alert-error');
                messagesDiv.classList.add('alert-success');
                messagesDiv.style.display = 'block';
                form.reset();
                
                // Redirecionamento
                setTimeout(function () {
                    window.location.href = "{{ route('unidade.manutencaoMedicos') }}";
                }, 2000);
            } else {
                throw (result);
            }
        })
        .catch(result => {
            let errorHtml = 'Houve erros de validação:';
            
            if (result.errors) {
                errorHtml += '<ul>';
                for (const key in result.errors) {
                    result.errors[key].forEach(error => {
                        errorHtml += `<li>${error}</li>`;
                    });
                }
                errorHtml += '</ul>';
            } else {
                errorHtml = result.message || 'Ocorreu um erro desconhecido ao cadastrar.';
            }
            
            messagesDiv.innerHTML = errorHtml;
            messagesDiv.classList.remove('alert-success');
            messagesDiv.classList.add('alert-error');
            messagesDiv.style.display = 'block';
        })
        .finally(() => {
            button.disabled = false;
            button.textContent = 'CADASTRAR';
        });
    });

    // 2. Lógica de Foco/Blur melhorada para inputs e selects
    const inputs = document.querySelectorAll(".input-wrapper input, .input-wrapper select");
    inputs.forEach(input => {
        const wrapper = input.parentElement;

        const applyFocusClass = () => {
            wrapper.classList.add("focused");
        };

        const removeFocusClass = () => {
            if (input.tagName === 'SELECT') {
                // Para select, mantém focused se tiver um valor selecionado que não seja o placeholder
                if (input.value === "" || input.value === null || input.selectedIndex === 0) {
                    wrapper.classList.remove("focused");
                }
            } else if (input.tagName === 'INPUT') {
                // Para input, remove focused se estiver vazio e não for readonly
                if ((input.value === "" || input.value === null) && !input.readOnly) {
                    wrapper.classList.remove("focused");
                }
            }
        };

        input.addEventListener("focus", applyFocusClass);
        input.addEventListener("blur", removeFocusClass);
        input.addEventListener("change", removeFocusClass); // Para selects
        
        // Aplica a classe 'focused' no carregamento se já houver valor
        if (input.tagName === 'SELECT') {
            if (input.value !== "" && input.value !== null && input.selectedIndex !== 0) {
                applyFocusClass();
            }
        } else if (input.tagName === 'INPUT') {
            if (input.value !== "" && input.value !== null && !input.readOnly) {
                applyFocusClass();
            }
        }
    });

    // 3. Máscara de CRM (XXXXXX/UU)
    const crmInput = document.getElementById('crmMedico');
    if (crmInput) {
        crmInput.addEventListener('input', function(e) {
            let value = e.target.value.toUpperCase();
            
            // Limpa tudo que não for dígito (para os 6 primeiros) ou letra (para os 2 últimos)
            value = value.replace(/[^A-Z0-9]/g, '');

            let digits = value.substring(0, 6).replace(/[^0-9]/g, ''); 
            let letters = '';
            
            if (value.length > 6) {
                letters = value.substring(6, 8).replace(/[^A-Z]/g, ''); 
            }

            // Monta a máscara
            let finalValue = digits;
            if (letters.length > 0) {
                finalValue += `/${letters}`;
            }
            
            e.target.value = finalValue;
        });
    }
});
    </script>
</body>
</html> 