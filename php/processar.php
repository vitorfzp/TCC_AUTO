<?php
require_once 'config.php';
session_start();

$nome      = trim($_POST['nome'] ?? '');
$cpf       = trim($_POST['cpf'] ?? '');
$email     = trim($_POST['email'] ?? '');
$senha     = trim($_POST['senha'] ?? ''); // NOVO
$confirma  = trim($_POST['confirma_senha'] ?? ''); // NOVO
$telefone  = trim($_POST['telefone'] ?? '');
$profissao = trim($_POST['profissao'] ?? '');
$mensagem  = trim($_POST['mensagem'] ?? '');
$arquivo_nome = null;

// Validação (agora inclui senha)
if (empty($nome) || empty($cpf) || empty($email) || empty($telefone) || empty($profissao) || empty($senha)) {
     header('Location: ../cadastro.php?error=' . urlencode('Erro: Todos os campos são obrigatórios.'));
     exit;
}
if (strlen($senha) < 6) {
    header('Location: ../cadastro.php?error=' . urlencode('Erro: A senha deve ter no mínimo 6 caracteres.'));
    exit;
}
if ($senha !== $confirma) {
    header('Location: ../cadastro.php?error=' . urlencode('Erro: As senhas não coincidem.'));
    exit;
}

// Hash da senha (IMPORTANTE)
$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

// Lógica de Upload (sem alterações)
if (!empty($_FILES['arquivo']['name']) && $_FILES['arquivo']['error'] == UPLOAD_ERR_OK) {
    // ... (seu código de upload aqui) ...
}

try {
    // Query atualizada para incluir a senha hasheada
    $stmt = $pdo->prepare("INSERT INTO prestadores (cpf, nome, email, senha, telefone, profissao, arquivo, mensagem) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$cpf, $nome, $email, $senha_hash, $telefone, $profissao, $arquivo_nome, $mensagem]);

} catch (PDOException $e) {
     if ($e->getCode() == 23000) {
          header('Location: ../cadastro.php?error=' . urlencode('Erro: Este CPF ou E-mail já está cadastrado.'));
     } else {
          error_log("Erro no cadastro: " . $e->getMessage());
          header('Location: ../cadastro.php?error=' . urlencode('Ocorreu um erro ao processar seu cadastro.'));
     }
     exit;
}

// Redireciona de volta para a página de cadastro com uma flag de sucesso
header("Location: ../cadastro.php?success=true");
exit;
?>