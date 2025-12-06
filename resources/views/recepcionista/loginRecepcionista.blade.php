<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Prontuário+ | Login Recepcionista</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <link rel="stylesheet" href="{{ url('/css/recepcionista/loginRecepcionista.css') }}">
    <link rel="shortcut icon" href="{{ url('img/logo-azul.png') }}" type="image/x-icon" />
</head>

<body>
    <main class="main-container">

        <div class="left-side">
            <img src="{{ asset('img/recepcionista-logo2.png') }}" alt="Logo Recepcionista">
        </div>

        <div class="right-side">
            <div class="login-content">
                <div class="logo">
                    <img src="{{ asset('img/icon-recepcionista.png') }}" alt="Logo Prontuário+">
                </div>

                <h2>Login Recepcionista</h2>

                <div id="notification" class="notification"></div>

                <form id="email-login-form" method="POST">
                    <div class="input-group">
                        <label for="email">E-mail</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-envelope icon-left"></i>
                            <input type="email" id="email" name="email" required />
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

                    <button class="btn-login" type="submit" id="submit-button">ENTRAR</button>
                </form>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('email-login-form');
            const notification = document.getElementById('notification');
            const submitButton = document.getElementById('submit-button');
            const emailInput = document.getElementById('email');
            const senhaInput = document.getElementById('senha');
            
            // --- AJUSTE PRINCIPAL ---
            // Define a URL para onde o AJAX vai enviar o formulário
            const url = "{{ route('recepcionista.login.submit') }}";
            // --- FIM DO AJUSTE ---

            form.addEventListener('submit', async function (event) {
                event.preventDefault();

                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                let body = {
                    email: emailInput.value,
                    senha: senhaInput.value 
                };

                submitButton.disabled = true;
                submitButton.textContent = 'Aguarde...';
                notification.style.display = 'none';

                try {
                    const response = await fetch(url, { // A variável 'url' agora está definida
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(body)
                    });

                    const responseData = await response.json();

                    if (response.ok) {
                        notification.textContent = 'Login realizado com sucesso!';
                        notification.className = 'notification success';
                        notification.style.display = 'block';
                        // Redireciona para a URL enviada pelo Controller
                        window.location.href = responseData.redirect_url; 
                    } else {
                        let errorMessage = responseData.message || 'Ocorreu um erro.';
                        if (responseData.errors) {
                            // Pega a primeira mensagem de erro (ex: "Credenciais incorretas")
                            errorMessage = Object.values(responseData.errors)[0][0];
                        }
                        notification.textContent = errorMessage;
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

            // Mostrar/ocultar senha
            const togglePassword = document.getElementById("togglePassword");
            if (togglePassword && senhaInput) {
                togglePassword.addEventListener("click", () => {
                    const isPassword = senhaInput.type === "password";
                    senhaInput.type = isPassword ? "text" : "password";
                    togglePassword.classList.toggle("fa-eye");
                    togglePassword.classList.toggle("fa-eye-slash");
                });
            }

            // Efeito de foco nos inputs
            const inputs = document.querySelectorAll(".input-wrapper input");
            inputs.forEach(input => {
                input.addEventListener("focus", () => {
                    input.parentElement.classList.add("focused");
                });
                input.addEventListener("blur", () => {
                    if (input.value === "") {
                        input.parentElement.classList.remove("focused");
                    }
                });
            });
        });
    </script>
</body>
</html>

