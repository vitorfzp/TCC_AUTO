async function loadFeedbacksFromServer() {
    const feedbackResultsList = document.getElementById('feedbackResults');
    try {
        // CORREÇÃO: O nome do arquivo estava errado (get_feedbacks.php -> get_feedback.php)
        const response = await fetch('php/get_feedback.php');
        if (!response.ok) {
            throw new Error(`Falha ao buscar os dados: ${response.statusText}`);
        }
        const feedbacks = await response.json();
        
        feedbackResultsList.innerHTML = ''; // Limpa a mensagem de "carregando"

        if (feedbacks.length === 0) {
            feedbackResultsList.innerHTML = '<li>Nenhum feedback encontrado.</li>';
            return;
        }

        feedbacks.forEach(feedback => {
            const li = document.createElement('li');
            
            // Adicionado mais estrutura para melhor estilização
            const feedbackHTML = `
                <div class="feedback-header">
                    <span class="user-info">${feedback.nome_usuario} avaliou <strong>${feedback.nome_prestador}</strong> (${feedback.profissao})</span>
                    <span class="feedback-date">📅 ${feedback.data_formatada}</span>
                </div>
                <div class="nota">${"⭐".repeat(parseInt(feedback.nota))}</div>
                <p class="comentario">${feedback.comentario}</p>
            `;
            li.innerHTML = feedbackHTML;
            feedbackResultsList.appendChild(li);
        });
    } catch (error) {
        console.error('Erro:', error);
        feedbackResultsList.innerHTML = '<li>Ocorreu um erro ao carregar os feedbacks. Tente novamente mais tarde.</li>';
    }
}

window.addEventListener('DOMContentLoaded', loadFeedbacksFromServer);