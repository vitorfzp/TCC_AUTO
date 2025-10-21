<?php
require_once '../config.php';
session_start(); // Inicia a sessão para verificar a autorização

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../login.html');
    exit;
}

// --- VERIFICAÇÃO DE AUTORIZAÇÃO VIA SESSÃO ---
if (!isset($_SESSION['reset_allowed']) || $_SESSION['reset_allowed'] !== true || !isset($_SESSION['reset_user_id'])) {
    // Se não está autorizado (não passou pela validação do código)
    header('Location: ../../login.html?error=' . urlencode('Acesso inválido para redefinição de senha.'));
    exit;
}
// --- FIM DA VERIFICAÇÃO ---


// Pega os dados do formulário
// REMOVIDO: $token = $_POST['token'] ?? '';
$senha = $_POST['senha'] ?? '';
$confirma_senha = $_POST['confirma_senha'] ?? '';
$user_id = $_SESSION['reset_user_id']; // Pega o ID do usuário da sessão

// Validações das senhas
// REMOVIDA validação do token
if (empty($senha) || strlen($senha) < 6 || $senha !== $confirma_senha) {
    // Redireciona de volta para a página de redefinição com erro
    header('Location: ../../redefinir_senha.html?error=' . urlencode('Dados inválidos. Verifique se as senhas coincidem e têm no mínimo 6 caracteres.'));
    exit;
}

try {
    // O ID do usuário já foi validado implicitamente pela sessão.
    // Atualiza a senha diretamente usando o user_id da sessão.
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    // Atualiza a senha e garante que o token (que agora é o código) seja NULO.
    // Não precisamos mais verificar o token aqui.
    $stmt_update = $pdo->prepare("UPDATE usuario SET senha = ?, reset_token = NULL, reset_token_expires_at = NULL WHERE id = ?");
    $stmt_update->execute([$senha_hash, $user_id]);

    // Limpa as variáveis de sessão usadas para a redefinição
    unset($_SESSION['reset_allowed']);
    unset($_SESSION['reset_user_id']);
    unset($_SESSION['reset_email']);

    // Redireciona para o login com mensagem de sucesso
    header('Location: ../../login.html?success=' . urlencode('Senha redefinida com sucesso! Pode fazer o login.'));
    exit;

} catch (PDOException $e) {
    error_log("Erro em do_reset.php: " . $e->getMessage());
    // Redireciona de volta para a página de redefinição com erro genérico
    header('Location: ../../redefinir_senha.html?error=' . urlencode('Ocorreu um erro ao atualizar a senha. Por favor, tente novamente.'));
    exit;
}
?>