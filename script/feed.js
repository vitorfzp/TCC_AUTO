document.getElementById('formFeedback').addEventListener('submit', function(event) {
    event.submitter.disabled = true;


    //let é usada quando a variável pode mudar de valor. 
    //const é usada quando a variável não deve mudar. 


    // Coleta dos valores do formulário
    let serv = document.getElementById('serv').value;
    let nomed= document.getElementById('nomed').value;
    let nome = document.getElementById('nome').value;
    let tipo = document.getElementById('tipo').value;
    let nota = document.getElementById('nota').value;
    let comentario = document.getElementById('comentario').value;

    // Formata a data
    let data = new Date();
    let dataFormatada = `${data.getDate()}/${data.getMonth() + 1}/${data.getFullYear()} ${data.getHours()}:${data.getMinutes()}`;

    // Cria o HTML do feedback
    let feedbackHTML = `
   <strong>${nome}</strong> - <strong>${serv}</strong> <strong>${nomed}</strong>(${tipo}) <span class="nota">${"⭐".repeat(nota)}</span>
        <p>${comentario}</p>
        <small>📅 ${dataFormatada}</small>
    `;

    // Cria o objeto de feedback
    let feedback = {
      nome: nome,
      nomed: nomed,
      serv: serv,
      tipo: tipo,
      nota: nota,
      comentario: comentario,
      data: dataFormatada,
      html: feedbackHTML
    };

    // Recupera os feedbacks existentes do localStorage ou cria um array vazio
    let feedbacks = JSON.parse(localStorage.getItem('feedbacks')) || [];
    feedbacks.push(feedback);

    // Salva o array atualizado no localStorage
    localStorage.setItem('feedbacks', JSON.stringify(feedbacks));

    // Atualiza a lista de feedbacks exibida na página
    let feedbackList = document.getElementById('listaFeedbacks');
  document.createElement('li');
  // Cria um elemento <li> para cada feedback
    li.innerHTML = feedbackHTML;
    feedbackList.appendChild(li);
    });