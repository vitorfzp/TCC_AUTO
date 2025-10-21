<?php
require_once '../config.php';
session_start(); // Inicia a sessão para guardar a autorização

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../inserir_codigo.html');
    exit;
}

$email = trim($_POST['email'] ?? '');
$codigo = trim($_POST['codigo'] ?? '');

// Validações básicas
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL) || empty($codigo) || !preg_match('/^\d{6}$/', $codigo)) {
    // Limpa sessão em caso de dados inválidos
    unset($_SESSION['reset_allowed']);
    unset($_SESSION['reset_user_id']);
    unset($_SESSION['reset_email']);
    header('Location: ../../inserir_codigo.html?email=' . urlencode($email) . '&error=' . urlencode('E-mail ou código inválido.'));
    exit;
}

try {
    // Busca o usuário pelo email, código E verifica a expiração
    $stmt = $pdo->prepare("SELECT id FROM usuario WHERE email = ? AND reset_token = ? AND reset_token_expires_at > NOW()");
    $stmt->execute([$email, $codigo]);
    $user = $stmt->fetch();

    if ($user) {
        // Código válido!
        // Limpa o token/código no DB para não ser reutilizado
        $stmt_clear = $pdo->prepare("UPDATE usuario SET reset_token = NULL, reset_token_expires_at = NULL WHERE id = ?");
        $stmt_clear->execute([$user['id']]);

        // Guarda na sessão que este usuário está autorizado a redefinir a senha
        $_SESSION['reset_user_id'] = $user['id'];
        $_SESSION['reset_allowed'] = true;
        $_SESSION['reset_email'] = $email; // Guardar o email pode ser útil

        // --- CORREÇÃO AQUI ---
        // Redireciona para a página .php, não mais .html
        header('Location: ../../redefinir_senha.php');
        exit;

    } else {
        // Código inválido ou expirado
        // Limpa sessão se o código estiver errado
        unset($_SESSION['reset_allowed']);
        unset($_SESSION['reset_user_id']);
        unset($_SESSION['reset_email']);
        
        header('Location: ../../inserir_codigo.html?email=' . urlencode($email) . '&error=' . urlencode('Código inválido ou expirado. Tente novamente ou solicite um novo código.'));
        exit;
    }

} catch (PDOException $e) {
    // Limpa sessão em caso de erro no DB
    unset($_SESSION['reset_allowed']);
    unset($_SESSION['reset_user_id']);
    unset($_SESSION['reset_email']);
    
    error_log("Erro em validar_codigo.php: " . $e->getMessage());
    header('Location: ../../inserir_codigo.html?email=' . urlencode($email) . '&error=' . urlencode('Ocorreu um erro no servidor. Tente novamente.'));
    exit;
}
?>