@extends('admin.templates.admTemplate')

@section('content')
@php $admin = auth()->guard('admin')->user(); @endphp

@section('title', 'Dashboard - Painel Administrativo')


    <link rel="stylesheet" href="{{ asset('css/admin/ajuda.css') }}">

    <main class="main-dashboardAjuda">
        <div class="help-container">

            <div class="title-bar fade-in" style="animation-delay: 0.2s;">
                <h1><i class="bi bi-person-heart"></i>Central de Ajuda</h1>

                <div class="search-box">
                    <i class="bi bi-search"></i>
                    <input type="text" id="faq-search" onkeyup="filterFAQ()"
                        placeholder="Pesquisar nas Perguntas Frequentes...">
                </div>
            </div>


            <h2 class="fade-in" style="animation-delay: 0.4s;"><i class="bi bi-question-circle-fill"></i> Perguntas
                Frequentes (FAQ)</h2>

            <p id="no-results-message" style="display: none; color: #a00; font-weight: bold; margin-top: 15px;">
                Nenhuma pergunta encontrada. Por favor, digite sua dúvida no formulário abaixo.
            </p>

            <div id="faq-list">

                <details class="slide-up" style="animation-delay: 0.6s;">
                    <summary>Como cadastrar uma nova Unidade de Saúde?</summary>
                    <p>Para cadastrar uma nova unidade, acesse o menu lateral e clique em "Unidades de Saúde". Em seguida,
                        selecione
                        o botão "+ Nova Unidade", preencha todas as informações necessárias e finalize clicando em "Salvar".
                    </p>
                </details>

                <details class="slide-up" style="animation-delay: 1.0s;">
                    <summary>Como cadastrar um novo paciente?</summary>
                    <p>Acesse a seção "Pacientes" no menu lateral e clique em "+ Novo Paciente". Preencha os dados pessoais,
                        informações
                        clínicas iniciais e salve para que o registro seja criado no sistema.</p>
                </details>

                <details class="slide-up" style="animation-delay: 1.2s;">
                    <summary>Consigo editar informações de pacientes cadastrados?</summary>
                    <p>Sim. Na lista de pacientes, clique no botão de editar ao lado do registro desejado. Lembre-se de
                        salvar após fazer
                        qualquer alteração.</p>
                </details>

                <details class="slide-up" style="animation-delay: 1.4s;">
                    <summary>Como altero minha senha de administrador?</summary>
                    <p>No menu lateral, clique no ícone de cadeado (<i class="bi bi-shield-lock-fill"></i>). Na página de
                        segurança,
                        você encontrará a opção para atualizar sua senha com segurança.</p>
                </details>

                <details class="slide-up" style="animation-delay: 1.8s;">
                    <summary>O admin consegue visualizar todos os acessos do sistema?</summary>
                    <p>Sim. Como administrador, você tem acesso completo aos registros do sistema, podendo visualizar dados
                        de usuários,
                        unidades e movimentações relevantes.</p>
                </details>

            </div>


            <h2 class="fade-in" style="animation-delay: 1.2s;"><i class="bi bi-headset"></i> Não encontrou o que procurava?
            </h2>
            <p class="fade-in" style="animation-delay: 1.4s;">Envie sua dúvida diretamente para nossa equipe de suporte
                através do formulário abaixo.</p>

            <form action="{{ route('admin.ajuda.enviar') }}" method="POST" class="contact-form slide-up"
                style="animation-delay: 1.6s;">
                @csrf
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