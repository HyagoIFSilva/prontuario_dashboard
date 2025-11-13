<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Prontuário+ | Cadastro Médico</title>

    <link rel="stylesheet" href="{{ url('/css/unidade/cadastroMedico.css') }}">
    <link rel="shortcut icon" href="{{url('img/logo-azul.png')}}" type="image/x-icon" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body>
    <main class="main-container">
        <div class="left-side">
            <img src="{{ asset('img/unidade-logo2.png') }}" alt="Logo ilustrativa">
        </div>

        <div class="right-side">
            <div class="login-content fade-in">
                <h2>Cadastro de Médico</h2>
                <div id="form-messages" class="alert-error" style="display: none;"></div>
                <form id="cadastroMedicoForm">
                    @csrf
                    <div class="input-group">
                        <label for="nomeMedico">Nome completo</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-user-doctor icon-left"></i>
                            <input type="text" id="nomeMedico" name="nomeMedico" required />
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="crmMedico">CRM</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-id-card icon-left"></i>
                            {{-- ============================================= --}}
                            {{-- --- ADICIONADO: maxlength="9" e placeholder --- --}}
                            {{-- ============================================= --}}
                            <input type="text" id="crmMedico" name="crmMedico" required maxlength="9" placeholder="Ex: 123456/SP" />
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="emailUsuario">E-mail</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-envelope icon-left"></i>
                            <input type="email" id="emailUsuario" name="emailUsuario" required />
                        </div>
                    </div>

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

                    <div class="input-group">
                        <label for="unidade">Unidade de Trabalho</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-hospital icon-left"></i>
                            @if($unidadeLogada)
                                <input type="text" id="unidade" name="unidade" value="{{ $unidadeLogada->nomeUnidade }}"
                                    readonly style="background-color: #f0f0f0; color: #666; cursor: not-allowed;" />
                                <input type="hidden" name="unidade_id" value="{{ $unidadeLogada->idUnidadePK }}">
                            @else
                                <input type="text" value="Nenhuma unidade encontrada" readonly
                                    style="background-color: #f0f0f0; color: #999; cursor: not-allowed;" />
                            @endif
                        </div>
                    </div>

                    <button class="btn-login" type="submit">CADASTRAR</button>
                </form>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('cadastroMedicoForm');
            const button = form.querySelector('button');
            const messagesDiv = document.getElementById('form-messages');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            form.addEventListener('submit', function (event) {
                event.preventDefault();
                const formData = new FormData(form);
                button.disabled = true;
                button.textContent = 'Cadastrando...';
                messagesDiv.style.display = 'none';

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
                        messagesDiv.textContent = result.message + " Redirecionando...";
                        messagesDiv.classList.remove('error');
                        messagesDiv.classList.add('success');
                        messagesDiv.style.display = 'block';
                        form.reset();
                        setTimeout(function () {
                            window.location.href = "{{ route('unidade.manutencaoMedicos') }}";
                        }, 2000);
                    } else {
                        throw (result);
                    }
                })
                .catch(result => {
                    let errorText = result.message || 'Ocorreu um erro.';
                    if (result.errors) {
                        errorText = Object.values(result.errors).flat().join(' ');
                    }
                    messagesDiv.textContent = errorText;
                    messagesDiv.classList.remove('success');
                    messagesDiv.classList.add('error');
                    messagesDiv.style.display = 'block';
                })
                .finally(() => {
                    button.disabled = false;
                    button.textContent = 'CADASTRAR';
                });
            });

            const inputs = document.querySelectorAll(".input-wrapper input, .input-wrapper select");
            inputs.forEach(input => {
                input.addEventListener("focus", () => {
                    input.parentElement.classList.add("focused");
                });
                input.addEventListener("blur", () => {
                    if (input.value === "") {
                        input.parentElement.classList.remove("focused");
                    }
                });
                if (input.value !== "") {
                    input.parentElement.classList.add("focused");
                }
            });

            // =========================================================
            // --- ADICIONADO: Máscara de CRM (XXXXXX/UU) ---
            // =========================================================
            const crmInput = document.getElementById('crmMedico');
            if (crmInput) {
                crmInput.addEventListener('input', function(e) {
                    let value = e.target.value.toUpperCase();
                    
                    // Limpa tudo que não for dígito (para os 6 primeiros) ou letra (para os 2 últimos)
                    value = value.replace(/[^A-Z0-9]/g, ''); 

                    let digits = value.substring(0, 6).replace(/[^0-9]/g, ''); // Garante 6 primeiros são dígitos
                    let letters = '';

                    if (value.length > 6) {
                        // Garante que depois do 6º dígito, só entram letras (máx 2)
                        letters = value.substring(6, 8).replace(/[^A-Z]/g, ''); 
                    }

                    // Monta a máscara
                    if (value.length > 6) {
                        e.target.value = `${digits}/${letters}`;
                    } else {
                        e.target.value = digits;
                    }
                });
            }
            // =========================================================
        });
    </script>
</body>
</html>