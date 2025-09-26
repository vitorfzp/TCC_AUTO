<?php
require_once '../config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // ATUALIZADO: Redireciona para o novo formulário de cadastro
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
    // ATUALIZADO: Redireciona para o novo formulário de cadastro
    header('Location: ../../register.html?error=' . urlencode(implode(' ', $errors)));
    exit;
}

$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

try {
    $stmt_check = $pdo->prepare("SELECT id FROM usuario WHERE cpf = ? OR email = ?");
    $stmt_check->execute([$cpf, $email]);
    if ($stmt_check->fetch()) {
        // ATUALIZADO: Redireciona para o novo formulário de cadastro
        header('Location: ../../register.html?error=' . urlencode("CPF ou Email já cadastrado."));
        exit;
    }

    $stmt_insert = $pdo->prepare("INSERT INTO usuario (nome, cpf, email, senha) VALUES (?, ?, ?, ?)");
    $stmt_insert->execute([$nome, $cpf, $email, $senha_hash]);

    // **ATUALIZAÇÃO PRINCIPAL**: Redireciona para a nova página de login com mensagem de sucesso
    header('Location: ../../login_form.html?success=' . urlencode("Cadastro realizado com sucesso! Faça o login."));
    exit;

} catch (PDOException $e) {
    error_log("Erro no registro: " . $e->getMessage());
    // ATUALIZADO: Redireciona para o novo formulário de cadastro
    header('Location: ../../register.html?error=' . urlencode("Erro ao realizar o cadastro. Tente novamente."));
    exit;
}
?>