/**
 * Cria e exibe o pop-up (modal) customizado para confirmar o logout.
 * @param {string} userName - O nome do usuário logado.
 */
function showCustomLogoutModal(userName) {
    // Evita criar múltiplos modais
    if (document.querySelector('.logout-modal-overlay')) {
        return;
    }

    const overlay = document.createElement('div');
    overlay.className = 'logout-modal-overlay';

    const modal = document.createElement('div');
    modal.className = 'logout-modal';

    modal.innerHTML = `
        <div class="logout-modal-content">
            <h3>Sessão Ativa</h3>
            <p>Você já está logado como <strong>${userName}</strong>.</p>
            <p>Deseja encerrar a sessão?</p>
        </div>
        <div class="logout-modal-buttons">
            <button id="logout-confirm-btn" class="modal-btn-confirm">Sim, Sair</button>
            <button id="logout-cancel-btn" class="modal-btn-cancel">Cancelar</button>
        </div>
    `;

    document.body.appendChild(overlay);
    document.body.appendChild(modal);
    
    // Animação de entrada
    setTimeout(() => {
        overlay.classList.add('show');
        modal.classList.add('show');
    }, 10);

    const closeModal = () => {
        overlay.classList.remove('show');
        modal.classList.remove('show');
        setTimeout(() => {
            if (document.body.contains(overlay)) {
                document.body.removeChild(overlay);
            }
            if (document.body.contains(modal)) {
                document.body.removeChild(modal);
            }
        }, 300);
    };

    // Eventos dos botões e do fundo
    document.getElementById('logout-confirm-btn').addEventListener('click', () => {
        window.location.href = 'php/usuario/logout.php';
    });
    document.getElementById('logout-cancel-btn').addEventListener('click', closeModal);
    overlay.addEventListener('click', closeModal);
}


// --- LÓGICA PRINCIPAL ---
// Executa somente quando o DOM estiver completamente carregado
document.addEventListener('DOMContentLoaded', () => {
    // Procura pelo item de menu de login/logout
    const loginMenuItem = document.querySelector('.sidebar-menu a[title="Login"]');

    if (loginMenuItem) {
        // Verifica o status da sessão no servidor
        fetch('php/usuario/check_session.php')
            .then(response => response.json())
            .then(data => {
                // Se o usuário estiver logado...
                if (data.loggedIn) {
                    // ...adiciona o evento de clique que abre o modal de logout.
                    loginMenuItem.addEventListener('click', function (event) {
                        event.preventDefault(); // Impede a navegação padrão do link
                        showCustomLogoutModal(data.userName); // Chama a função do modal
                    });
                }
            })
            .catch(error => {
                console.error('Erro ao verificar a sessão:', error);
            });
    }
});