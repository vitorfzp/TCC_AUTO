<?php
require_once '../config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../login_form.html?error=Método inválido');
    exit;
}

$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';

if (empty($email) || empty($senha)) {
    header('Location: ../../login_form.html?error=Email e senha são obrigatórios.');
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id, nome, senha FROM usuario WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($senha, $user['senha'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['nome']; 

        // **AQUI ESTÁ A MUDANÇA PRINCIPAL**
        // Agora, redirecionamos com um parâmetro de sucesso na URL
        header('Location: ../../index.php?success=Login realizado com sucesso!');
        exit;
    } else {
        header('Location: ../../login_form.html?error=Email ou senha inválidos.');
        exit;
    }
} catch (PDOException $e) {
    error_log("Erro no login: " . $e->getMessage());
    header('Location: ../../login_form.html?error=Ocorreu um erro no servidor.');
    exit;
}
?>