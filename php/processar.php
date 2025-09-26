<?php
require_once 'config.php';
session_start();

$nome      = trim($_POST['nome'] ?? '');
$cpf       = trim($_POST['cpf'] ?? '');
$email     = trim($_POST['email'] ?? '');
$telefone  = trim($_POST['telefone'] ?? ''); // Pega o telefone
$profissao = trim($_POST['profissao'] ?? '');
$mensagem  = trim($_POST['mensagem'] ?? '');
$arquivo_nome = null;

// Validação (agora inclui a profissão e telefone)
if (empty($nome) || empty($cpf) || empty($email) || empty($telefone) || empty($profissao)) {
     header('Location: ../cadastro.php?error=' . urlencode('Erro: Todos os campos são obrigatórios.'));
     exit;
}

// Lógica de Upload (sem alterações)
if (!empty($_FILES['arquivo']['name']) && $_FILES['arquivo']['error'] == UPLOAD_ERR_OK) {
    // ... (seu código de upload aqui, mantido como está)
}

try {
    // Query atualizada para incluir o telefone
    $stmt = $pdo->prepare("INSERT INTO prestadores (cpf, nome, email, telefone, profissao, arquivo, mensagem) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$cpf, $nome, $email, $telefone, $profissao, $arquivo_nome, $mensagem]);

} catch (PDOException $e) {
     if ($e->getCode() == 23000) {
          header('Location: ../cadastro.php?error=' . urlencode('Erro: Este CPF já está cadastrado.'));
     } else {
          error_log("Erro no cadastro: " . $e->getMessage());
          header('Location: ../cadastro.php?error=' . urlencode('Ocorreu um erro ao processar seu cadastro.'));
     }
     exit;
}

$_SESSION['dados_enviados'] = [
    'nome' => $nome,
    'cpf' => $cpf,
    'email' => $email,
    'telefone' => $telefone, // Adicionado para a página de confirmação
    'profissao' => $profissao,
    'mensagem' => $mensagem,
    'arquivo' => $arquivo_nome
];

header("Location: confirmacao.php");
exit;
?>