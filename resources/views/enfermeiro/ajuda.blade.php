@extends('enfermeiro.templates.enfermeiroTemplate')

@section('content')

  @section('title', 'Dashboard - Painel Administrativo')

   
    <link rel="stylesheet" href="{{ asset('css/enfermeiro/ajuda.css') }}">

    <main class="main-dashboardAjuda">
        <div class="help-container">

            <div class="title-bar">
                <h1><i class="bi bi-person-heart"></i>Central de Ajuda</h1>
                <div class="search-box">
                    <i class="bi bi-search"></i>
                    <input type="text" id="faq-search" onkeyup="filterFAQ()"
                        placeholder="Pesquisar nas Perguntas Frequentes...">
                </div>
            </div>
            <h2><i class="bi bi-question-circle-fill"></i> Perguntas Frequentes (FAQ)</h2>

            <p id="no-results-message" style="display: none; color: #a00; font-weight: bold; margin-top: 15px;">
                Nenhuma pergunta encontrada. Por favor, digite sua dúvida no formulário abaixo.
            </p>

            <div id="faq-list">

                <details class="slide-up" style="animation-delay: 0.6s;">
                    <summary>Como realizar a triagem de um paciente?</summary>
                    <p>
                        Para realizar a triagem, acesse o menu lateral e clique em <strong>Triagem</strong>.
                        Em seguida, escolha o paciente que está com a ficha aberta, preencha os dados necessários
                        e envie a triagem para o médico.
                    </p>
                </details>

                <details class="slide-up" style="animation-delay: 0.8s;">
                    <summary>Como visualizar o histórico de atendimentos do paciente?</summary>
                    <p>
                        Vá até a lista de <strong>Pacientes Atendidos</strong>.
                        Clique no ícone de visualização (olhinho) para abrir o histórico completo de atendimentos do
                        paciente.
                    </p>
                </details>

                <details class="slide-up" style="animation-delay: 1.0s;">
                    <summary>Como saber se o paciente já foi encaminhado para o médico?</summary>
                    <p>
                        Após finalizar a triagem, o sistema muda automaticamente o status do paciente para
                        <strong>Enviado ao médico</strong>.
                        Você pode verificar isso na própria lista de triagem.
                    </p>
                </details>


                <details class="slide-up" style="animation-delay: 1.4s;">
                    <summary>Como alterar minha senha?</summary>
                    <p>
                        No menu lateral, clique no ícone de segurança (<i class="bi bi-shield-lock-fill"></i>).
                        Na tela que abrir, você poderá definir uma nova senha.
                    </p>
                </details>

            </div>

            <h2><i class="bi bi-headset"></i> Não encontrou o que procurava?</h2>
            <p>Envie sua dúvida diretamente para nossa equipe de suporte através do formulário abaixo.</p>

            <form action="enviar_ajuda.php" method="POST" class="contact-form">
                <input type="text" name="assunto" placeholder="Assunto" required>
                <textarea name="mensagem" rows="6" placeholder="Digite sua mensagem aqui..." required></textarea>
                <button type="submit">Enviar Mensagem</button>
            </form>

        </div>
    </main>


    <script>
        function filterFAQ() {
            let input, filter, faqList, detailsElements, summaryText, i, matchCount = 0;
            input = document.getElementById('faq-search');
            filter = input.value.toUpperCase();
            faqList = document.getElementById('faq-list');
            detailsElements = faqList.getElementsByTagName('details');
            const noResultsMessage = document.getElementById('no-results-message');

            for (i = 0; i < detailsElements.length; i++) {
                summaryText = detailsElements[i].querySelector('summary').textContent;

                if (summaryText.toUpperCase().indexOf(filter) > -1) {
                    detailsElements[i].style.display = "";
                    matchCount++;
                } else {
                    detailsElements[i].style.display = "none";
                }
            }

            if (matchCount === 0 && filter.length > 0) {
                noResultsMessage.style.display = "block";
            } else {
                noResultsMessage.style.display = "none";
            }
        }
    </script>
    @endsection