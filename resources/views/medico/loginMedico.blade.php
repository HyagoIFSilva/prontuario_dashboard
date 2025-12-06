<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Prontuário+ | Login Médico</title>

    <!-- Font Awesome para ícones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <link rel="stylesheet" href="{{ url('/css/medico/loginMedico.css') }}">
    <link rel="shortcut icon" href="{{ url('img/logo-azul.png') }}" type="image/x-icon" />
</head>

<body>
    <main class="main-container">
        <!-- Lado esquerdo (imagem) -->
        <div class="left-side">
            <img src="{{ asset('img/medico-logo2.png') }}" alt="Logo Médico">
        </div>

        <!-- Lado direito (formulário) -->
        <div class="right-side">
            <div class="login-content">
                <div class="logo">
                    <img src="{{ asset('img/icon-loginMédico.png') }}" alt="Logo Prontuário+">
                </div>

                <h2>Login Médico</h2>

                <div id="notification" class="notification"></div>

                <form id="medico-login-form" method="POST">
                    <div id="login-fields">
                        <div class="input-group">
                            <label for="crm">CRM</label>
                            <div class="input-wrapper">
                                <i class="fa-solid fa-user icon-left"></i>
                                <input type="text" id="crm" name="crm" required />
                            </div>
                        </div>

                        <div class="input-group">
                            <label for="senha">Senha</label>
                            <div class="input-wrapper">
                                <i class="fa-solid fa-lock icon-left"></i>
                                <input type="password" id="senha" name="senha" required />
                                <i id="togglePassword" class="fa-solid fa-eye-slash icon-right"></i>
                            </div>
                        </div>
                    </div>

                    <div id="especialidade-wrapper" style="display: none;">
                        <div class="input-group">
                            <label for="especialidade">Especialidade</label>
                            <div class="input-wrapper">
                                <input type="text" id="especialidade" name="especialidade" />
                            </div>
                        </div>
                    </div>

                    <button class="btn-login" type="submit" id="submit-button">ENTRAR</button>
                </form>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('medico-login-form');
            const notification = document.getElementById('notification');
            const especialidadeWrapper = document.getElementById('especialidade-wrapper');
            const submitButton = document.getElementById('submit-button');
            const crmInput = document.getElementById('crm');
            const senhaInput = document.getElementById('senha');

form.addEventListener('submit', async function (event) {
                event.preventDefault();

                let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                submitButton.disabled = true;
                submitButton.textContent = 'Aguarde...';
                notification.style.display = 'none';

                try {
                    const response = await fetch("{{ route('api.medico.login.check') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            crm: crmInput.value,
                            senha: senhaInput.value
                        })
                    });

                    const data = await response.json();

                    if (response.ok) {
                        if (data.need_password_change) {
                            if (data.new_csrf_token) {
                                csrfToken = data.new_csrf_token;
                                document.querySelector('meta[name="csrf-token"]').setAttribute('content', csrfToken);
                            }
                            
                            notification.textContent = data.message;
                            notification.className = 'notification info';
                            notification.style.display = 'block';

                            document.getElementById('login-fields').style.display = 'none';
                            especialidadeWrapper.innerHTML = `
                                <div class="input-group">
                                    <label for="nova_senha">Nova Senha</label>
                                    <div class="input-wrapper">
                                        <input type="password" id="nova_senha" name="nova_senha" required />
                                    </div>
                                </div>
                                <div class="input-group">
                                    <label for="nova_senha_confirmation">Confirme a Nova Senha</label>
                                    <div class="input-wrapper">
                                        <input type="password" id="nova_senha_confirmation" name="nova_senha_confirmation" required />
                                    </div>
                                </div>
                            `;

                            especialidadeWrapper.style.display = 'block';
                            submitButton.textContent = 'ALTERAR SENHA';
                            submitButton.disabled = false;
                            form.onsubmit = async function (e) {
                                e.preventDefault();
                                
                                submitButton.disabled = true;
                                submitButton.textContent = 'Aguarde...';
                                notification.style.display = 'none';
                                
                                const novaSenha = document.getElementById('nova_senha').value;
                                const confirmarSenha = document.getElementById('nova_senha_confirmation').value;

                                if (novaSenha !== confirmarSenha) {
                                    notification.textContent = 'As senhas não coincidem.';
                                    notification.className = 'notification error';
                                    notification.style.display = 'block';
                                    submitButton.disabled = false;
                                    submitButton.textContent = 'ALTERAR SENHA';
                                    return;
                                }
                                const formData = new FormData();
                                formData.append('crm', crmInput.value);
                                formData.append('nova_senha', novaSenha);
                                formData.append('nova_senha_confirmation', confirmarSenha);
                                formData.append('_token', csrfToken); 

                                try {
                                    const resp = await fetch("{{ route('api.medico.alterarSenhaPrimeiroLogin') }}", {
                                        method: 'POST',
                                        body: formData
                                    });
                                    if (resp.status === 419) {
                                         throw new Error('CSRF Token Expirado/Inválido.');
                                    }

                                    const respData = await resp.json();

                                    if (resp.ok && respData.success) {
                                        notification.textContent = respData.message;
                                        notification.className = 'notification success';
                                        notification.style.display = 'block';
                                        window.location.href = respData.redirect_url;
                                    } else {
                                        notification.textContent = respData.message || 'Erro ao alterar senha.';
                                        notification.className = 'notification error';
                                        notification.style.display = 'block';
                                        submitButton.disabled = false;
                                        submitButton.textContent = 'ALTERAR SENHA';
                                    }
                                } catch (err) {
                                    notification.textContent = 'Erro de conexão ou token inválido. Tente novamente.';
                                    notification.className = 'notification error';
                                    notification.style.display = 'block';
                                    submitButton.disabled = false;
                                    submitButton.textContent = 'ALTERAR SENHA';
                                }
                            };
                        } else {
                            window.location.href = data.redirect_url;
                        }
                    } else {
                        notification.textContent = data.message || 'Erro ao efetuar login.';
                        notification.className = 'notification error';
                        notification.style.display = 'block';
                        submitButton.disabled = false;
                        submitButton.textContent = 'ENTRAR';
                    }
                } catch (error) {
                    notification.textContent = 'Erro de conexão. Tente novamente.';
                    notification.className = 'notification error';
                    notification.style.display = 'block';
                    submitButton.disabled = false;
                    submitButton.textContent = 'ENTRAR';
                }
            });
            const togglePassword = document.getElementById("togglePassword");
            if (togglePassword && senhaInput) {
                togglePassword.addEventListener("click", () => {
                    const isPassword = senhaInput.type === "password";
                    senhaInput.type = isPassword ? "text" : "password";
                    togglePassword.classList.toggle("fa-eye");
                    togglePassword.classList.toggle("fa-eye-slash");
                });
            }
            const inputs = document.querySelectorAll(".input-wrapper input");
            inputs.forEach(input => {
                input.addEventListener("focus", () => input.parentElement.classList.add("focused"));
                input.addEventListener("blur", () => {
                    if (input.value === "") input.parentElement.classList.remove("focused");
                });
            });
        });
    </script>
</body>
</html>
