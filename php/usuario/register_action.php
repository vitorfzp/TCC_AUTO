<?php
require_once '../config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Redireciona para o novo formulário de cadastro
    header('Location: ../../register.html?error=Método inválido');
    exit;
}

$nome = trim($_POST['nome'] ?? '');
$cpf = trim($_POST['cpf'] ?? '');
$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';
$confirma_senha = $_POST['confirma_senha'] ?? '';

$errors = [];
if (empty($nome)) $errors[] = "Nome é obrigatório.";
if (empty($cpf)) $errors[] = "CPF é obrigatório.";
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Formato de Email inválido.";
if (strlen($senha) < 6) $errors[] = "Senha deve ter no mínimo 6 caracteres.";
if ($senha !== $confirma_senha) $errors[] = "As senhas não coincidem.";

if (!empty($errors)) {
    // Redireciona para o novo formulário de cadastro
    header('Location: ../../register.html?error=' . urlencode(implode(' ', $errors)));
    exit;
}

$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

try {
    $stmt_check = $pdo->prepare("SELECT id FROM usuario WHERE cpf = ? OR email = ?");
    $stmt_check->execute([$cpf, $email]);
    if ($stmt_check->fetch()) {
        // Redireciona para o novo formulário de cadastro
        header('Location: ../../register.html?error=' . urlencode("CPF ou Email já cadastrado."));
        exit;
    }

    $stmt_insert = $pdo->prepare("INSERT INTO usuario (nome, cpf, email, senha) VALUES (?, ?, ?, ?)");
    $stmt_insert->execute([$nome, $cpf, $email, $senha_hash]);

    // --- MUDANÇA APLICADA AQUI ---
    // Em vez de redirecionar para o login, criamos a sessão e vamos para a página inicial.

    // 1. Pega o ID do usuário que acabamos de criar.
    $new_user_id = $pdo->lastInsertId();

    // 2. Cria a sessão de login para o novo usuário.
    $_SESSION['user_id'] = $new_user_id;
    $_SESSION['user_name'] = $nome; 

    // 3. Redireciona para a página principal (index.php) com uma mensagem de boas-vindas.
    // A função strtok($nome, ' ') pega apenas o primeiro nome para a mensagem.
    header('Location: ../../index.php?success=' . urlencode('Cadastro realizado! Bem-vindo(a), ' . strtok($nome, ' ') . '!'));
    exit;

} catch (PDOException $e) {
    error_log("Erro no registro: " . $e->getMessage());
    // Redireciona para o novo formulário de cadastro
    header('Location: ../../register.html?error=' . urlencode("Erro ao realizar o cadastro. Tente novamente."));
    exit;
}
?>