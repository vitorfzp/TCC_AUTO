<?php
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../login.html');
    exit;
}

$token = $_POST['token'] ?? '';
$senha = $_POST['senha'] ?? '';
$confirma_senha = $_POST['confirma_senha'] ?? '';

// Validações
if (empty($token) || empty($senha) || strlen($senha) < 6 || $senha !== $confirma_senha) {
    header('Location: ../../redefinir_senha.html?token=' . urlencode($token) . '&error=' . urlencode('Dados inválidos. Verifique se as senhas coincidem e têm no mínimo 6 caracteres.'));
    exit;
}

try {
    // 1. Encontra o utilizador pelo token e verifica se não expirou
    $stmt = $pdo->prepare("SELECT id FROM usuario WHERE reset_token = ? AND reset_token_expires_at > NOW()");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if (!$user) {
        header('Location: ../../redefinir_senha.html?token=' . urlencode($token) . '&error=' . urlencode('Token inválido ou expirado. Por favor, solicite um novo link.'));
        exit;
    }

    // 2. Se o token for válido, atualiza a senha
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
    
    // Limpa o token para que não possa ser reutilizado
    $stmt_update = $pdo->prepare("UPDATE usuario SET senha = ?, reset_token = NULL, reset_token_expires_at = NULL WHERE id = ?");
    $stmt_update->execute([$senha_hash, $user['id']]);

    // 3. Redireciona para o login com mensagem de sucesso
    header('Location: ../../login.html?success=' . urlencode('Senha redefinida com sucesso! Pode fazer o login.'));
    exit;

} catch (PDOException $e) {
    error_log("Erro em do_reset.php: " . $e->getMessage());
    header('Location: ../../redefinir_senha.html?token=' . urlencode($token) . '&error=' . urlencode('Ocorreu um erro. Por favor, tente novamente.'));
    exit;
}
?>