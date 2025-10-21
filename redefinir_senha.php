<?php
session_start();

// Verificação de segurança NO SERVIDOR
// Isto substitui a verificação falha do JavaScript
if (!isset($_SESSION['reset_allowed']) || $_SESSION['reset_allowed'] !== true || !isset($_SESSION['reset_email'])) {
    // Se não está autorizado, volta para o login com erro
    header('Location: login.html?error=' . urlencode('Acesso inválido. Por favor, solicite a recuperação novamente.'));
    exit;
}

// Se chegou aqui, está autorizado. Pega o email da sessão para usar no formulário.
$email_from_session = $_SESSION['reset_email'];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Senha - Autonowe</title>
    <link rel="icon" type="image/png" href="img/logo_auto.png">
    <link rel="stylesheet" href="style/auth_style.css">
</head>
<body>
    <div class="auth-container">
        <div class="logo-section">
            <div class="logo-container">
                <img src="img/logo_auto.png" alt="AUTONOWE Logo">
            </div>
        </div>
        <div class="form-section">
            <h2>Crie uma Nova Senha</h2>
            <div id="message-area"></div>
            <form action="php/usuario/do_reset.php" method="post" id="resetForm" class="auth-form">
                
                <input type="hidden" id="email" name="email" value="<?php echo htmlspecialchars($email_from_session); ?>">

                <div class="form-group">
                    <label for="senha">Nova Senha (mínimo 6 caracteres)</label>
                    <input type="password" id="senha" name="senha" required minlength="6">
                </div>
                <div class="form-group">
                    <label for="confirma_senha">Confirmar Nova Senha</label>
                    <input type="password" id="confirma_senha" name="confirma_senha" required>
                </div>
                <button type="submit" class="auth-button">Redefinir Senha</button>
            </form>
             <p class="form-link">
                Lembrou? <a href="login.html">Voltar para o Login</a>
            </p>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const params = new URLSearchParams(window.location.search);
            const messageArea = document.getElementById('message-area');

            // --- CORREÇÃO: A verificação de acesso inválido foi REMOVIDA daqui ---
            // Ela agora é feita pelo PHP antes da página carregar, que é o correto.

            // Mostra mensagens de erro que venham do 'do_reset.php' (ex: senha curta)
            const error = params.get('error');
            if (error) {
                messageArea.innerHTML = `<p class="message error">${decodeURIComponent(error)}</p>`;
            }

            // Valida se as senhas coincidem antes de enviar (Esta parte é mantida)
            document.getElementById('resetForm').addEventListener('submit', function(event) {
                messageArea.innerHTML = ''; // Limpa mensagens anteriores
                const senha = document.getElementById('senha').value;
                const confirmaSenha = document.getElementById('confirma_senha').value;
                
                if (senha.length < 6) {
                     messageArea.innerHTML = `<p class="message error">A senha deve ter no mínimo 6 caracteres.</p>`;
                     event.preventDefault(); // Impede o envio
                } else if (senha !== confirmaSenha) {
                    messageArea.innerHTML = `<p class="message error">As senhas não coincidem!</p>`;
                    event.preventDefault(); // Impede o envio do formulário
                }
            });

             // Limpa a URL se houver erro
             if (error) {
                window.history.replaceState({}, document.title, window.location.pathname);
             }
        });
    </script>
</body>
</html>