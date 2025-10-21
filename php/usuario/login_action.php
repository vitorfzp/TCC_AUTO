<?php
require_once '../config.php';
session_start();

$email = trim($_POST['email'] ?? '');
$senha = trim($_POST['senha'] ?? '');

if (empty($email) || empty($senha)) {
    header('Location: ../../login.html?error=' . urlencode('E-mail e senha são obrigatórios.'));
    exit;
}

try {
    // 1. Tenta fazer login como USUÁRIO (cliente)
    $stmt = $pdo->prepare("SELECT id, nome, senha FROM usuario WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($senha, $user['senha'])) {
        // --- LOGADO COMO CLIENTE ---
        session_regenerate_id(true);
        $_SESSION['logged_in'] = true;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['nome'];
        $_SESSION['user_type'] = 'cliente'; // Define o tipo
        
        header('Location: ../../index.php'); // Redireciona cliente para a home
        exit;
    }

    // 2. Se falhar, tenta fazer login como PRESTADOR
    $stmt = $pdo->prepare("SELECT cpf, nome, senha FROM prestadores WHERE email = ?");
    $stmt->execute([$email]);
    $prestador = $stmt->fetch();

    if ($prestador && password_verify($senha, $prestador['senha'])) {
        // --- LOGADO COMO PRESTADOR ---
        session_regenerate_id(true);
        $_SESSION['logged_in'] = true;
        $_SESSION['user_cpf'] = $prestador['cpf']; // Guarda o CPF, que é o ID único
        $_SESSION['user_name'] = $prestador['nome'];
        $_SESSION['user_type'] = 'prestador'; // Define o tipo

        header('Location: ../../minha_conta_prestador.php'); // Redireciona prestador para o painel
        exit;
    }

    // 3. Se ambos falharem
    header('Location: ../../login.html?error=' . urlencode('E-mail ou senha inválidos.'));
    exit;

} catch (PDOException $e) {
    error_log("Erro no login: " . $e->getMessage());
    header('Location: ../../login.html?error=' . urlencode('Ocorreu um erro no servidor.'));
    exit;
}
?>