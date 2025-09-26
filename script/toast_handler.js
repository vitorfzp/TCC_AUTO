document.addEventListener('DOMContentLoaded', () => {
    // Procura por um parâmetro 'success' na URL da página
    const params = new URLSearchParams(window.location.search);
    const successMessage = params.get('success');

    // Se uma mensagem de sucesso for encontrada...
    if (successMessage) {
        // 1. Cria o elemento HTML da notificação
        const toast = document.createElement('div');
        toast.className = 'toast-notification';
        toast.textContent = decodeURIComponent(successMessage);
        
        // 2. Adiciona a notificação à página
        document.body.appendChild(toast);

        // 3. Ativa a animação de entrada
        setTimeout(() => {
            toast.classList.add('show');
        }, 100);

        // 4. Agenda a remoção da notificação após 5 segundos
        setTimeout(() => {
            toast.classList.remove('show');
            // Espera a animação de saída terminar antes de remover o elemento do HTML
            setTimeout(() => {
                if (document.body.contains(toast)) {
                    document.body.removeChild(toast);
                }
            }, 500);
        }, 5000);

        // 5. Limpa a URL para que a mensagem não apareça novamente se a página for recarregada
        window.history.replaceState({}, document.title, window.location.pathname);
    }
});