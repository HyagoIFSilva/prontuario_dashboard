@extends('medico.templates.medicoTemplate')

@section('title', 'Central de Ajuda')

@section('content')
  <link rel="stylesheet" href="{{ asset('css/medico/MedicoAjuda.css') }}">

  <main class="main-dashboardAjuda">
    <div class="help-container">

      <div class="title-bar fade-in" style="animation-delay: 0.2s;">
        <h1><i class="bi bi-person-heart"></i> Central de Ajuda</h1>

        <div class="search-box">
          <i class="bi bi-search"></i>
          <input type="text" id="faq-search" onkeyup="filterFAQ()" placeholder="Pesquisar nas Perguntas Frequentes...">
        </div>
      </div>

      <h2 class="fade-in" style="animation-delay: 0.4s;"><i class="bi bi-question-circle-fill"></i> Perguntas Frequentes
        (FAQ)</h2>

      <p id="no-results-message" style="display: none; color: #a00; font-weight: bold; margin-top: 15px;">
        Nenhuma pergunta encontrada. Por favor, digite sua dúvida no formulário abaixo.
      </p>
      <div id="faq-list">

        <details class="slide-up" style="animation-delay: 0.6s;">
          <summary>Como iniciar uma consulta pela dashboard?</summary>
          <p>Para iniciar uma consulta, acesse o menu lateral e clique em "Consultas". Lá você verá a lista de pacientes
            disponíveis. Basta selecionar um paciente que já está com a ficha aberta e clicar para começar o atendimento.
          </p>
        </details>

        <details class="slide-up" style="animation-delay: 0.8s;">
          <summary>Como visualizar o prontuário do paciente?</summary>
          <p>Na lista de pacientes atendidos, clique no ícone de visualização (o “olhinho”). Assim, você terá acesso ao
            prontuário completo do paciente com todas as informações registradas.</p>
        </details>

        <details class="slide-up" style="animation-delay: 1.2s;">
          <summary>Onde encontro todos os pacientes já atendidos?</summary>
          <p>Acesse o menu lateral e selecione "Pacientes Atendidos". Nesta seção você poderá visualizar o histórico de
            atendimentos e acessar os prontuários pelo ícone de visualização.</p>
        </details>

        <details class="slide-up" style="animation-delay: 1.4s;">
          <summary>Como altero minha senha ou dados de acesso?</summary>
          <p>No menu lateral, clique no ícone de segurança (cadeado). Na página de configurações você poderá definir uma
            nova senha sempre que necessário.</p>
        </details>

      </div>


      <h2 class="fade-in" style="animation-delay: 1.2s;"><i class="bi bi-headset"></i> Não encontrou o que procurava?</h2>
      <p class="fade-in" style="animation-delay: 1.4s;">Envie sua dúvida diretamente para nossa equipe de suporte através
        do formulário abaixo.</p>

      <form action="#" method="POST" class="contact-form slide-up" style="animation-delay: 1.6s;">
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