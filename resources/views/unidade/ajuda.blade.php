@extends('unidade.templates.unidadeTemplate')

@section('content')

@section('title', 'Dashboard - Painel Administrativo')

    @php $unidade = auth()->guard('unidade')->user(); @endphp


    <link rel="stylesheet" href="{{ asset('css/unidade/ajuda.css') }}">

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

                <details class="slide-up" style="animation-delay: 0.2s;">
                    <summary>Como cadastrar médicos, enfermeiros ou recepcionistas?</summary>
                    <p>No menu lateral, acesse a área “Profissionais”. Clique no icone e selecione o tipo de
                        profissional (médico, enfermeiro ou recepcionista). Depois, preencha as informações obrigatórias e
                        finalize no botão “Salvar”.</p>
                </details>

                <details class="slide-up" style="animation-delay: 0.4s;">
                    <summary>Onde posso visualizar os dados de todos os profissionais cadastrados?</summary>
                    <p>Entre no menu “Profissionais”. Lá você verá uma lista completa com todos os médicos, enfermeiros e
                        recepcionistas cadastrados na unidade. Você pode visualizar detalhes, editar ou desativar um perfil
                        sempre que necessário.</p>
                </details>

                <details class="slide-up" style="animation-delay: 0.6s;">
                    <summary>Como editar informações de um profissional?</summary>
                    <p>Na lista de profissionais, clique no botão de edição (ícone de lápis). A partir daí, você pode
                        atualizar dados pessoais, informações de contato e permissões de acesso.</p>
                </details>

                <details class="slide-up" style="animation-delay: 0.8s;">
                    <summary>Posso desativar ou remover um profissional do sistema?</summary>
                    <p>Sim. Na página de profissionais, selecione o profissional e clique em “Desativar”. Assim, ele perde o
                        acesso ao sistema, mas seu histórico permanece registrado para segurança e auditoria.</p>
                </details>

                <details class="slide-up" style="animation-delay: 1.2s;">
                    <summary>Onde encontro informações gerais da unidade?</summary>
                    <p>No menu lateral, clique em “Perfil”. Lá você consegue acessar dados
                        administrativos, realizar alterações e ajustar preferências do painel.</p>
                </details>

                <details class="slide-up" style="animation-delay: 1.4s;">
                    <summary>Como altero a senha do painel administrativo da unidade?</summary>
                    <p>No menu lateral, clique no ícone de cadeado. Na área de Segurança, você poderá criar uma nova senha e
                        atualizar suas credenciais de acesso.</p>
                </details>

                <details class="slide-up" style="animation-delay: 1.8s;">
                    <summary>O que fazer se um profissional estiver com dificuldade de acessar?</summary>
                    <p>Verifique no menu “Profissionais” se o cadastro dele está ativo. Caso esteja, você pode redefinir a
                        senha ou atualizar os dados de login dele.</p>
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