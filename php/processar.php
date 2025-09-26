<?php
require_once 'config.php';
session_start();

$nome     = $_POST['nome'] ?? '';
$cpf      = $_POST['cpf'] ?? '';
$email    = $_POST['email'] ?? '';
$profissao = $_POST['profissao'] ?? '';
$mensagem = $_POST['mensagem'] ?? '';
$arquivo_nome = null;

// Validação (agora inclui a profissão)
if (empty($nome) || empty($cpf) || empty($email) || empty($profissao)) {
     // MUDANÇA AQUI: Em vez de die(), redirecionamos com uma mensagem de erro
     header('Location: ../../cadastro.php?error=' . urlencode('Erro: Nome, CPF, Email e Profissão são obrigatórios.'));
     exit;
}

// Lógica de Upload
if (!empty($_FILES['arquivo']['name']) && $_FILES['arquivo']['error'] == UPLOAD_ERR_OK) {
    $uploadDir = __DIR__ . '/../uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    $original_filename = basename($_FILES['arquivo']['name']);
    $safe_filename = preg_replace("/[^a-zA-Z0-9._-]/", "_", $original_filename);
    $ext = pathinfo($safe_filename, PATHINFO_EXTENSION);
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx'];
    if (in_array(strtolower($ext), $allowed_extensions)) {
        $arquivo_nome = 'file_' . uniqid() . '.' . strtolower($ext);
        if (!move_uploaded_file($_FILES['arquivo']['tmp_name'], $uploadDir . $arquivo_nome)) {
            $arquivo_nome = null;
        }
    }
}

try {
    // Query atualizada para incluir a profissão
    $stmt = $pdo->prepare("INSERT INTO prestadores (cpf, nome, email, profissao, arquivo, mensagem) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$cpf, $nome, $email, $profissao, $arquivo_nome, $mensagem]);
} catch (PDOException $e) {
     if ($e->getCode() == 23000) {
          // MUDANÇA AQUI: Redirecionamos com a mensagem de erro
          header('Location: ../../cadastro.php?error=' . urlencode('Erro: Este CPF já está cadastrado.'));
          exit;
     } else {
          // MUDANÇA AQUI: Redirecionamos com a mensagem de erro
          header('Location: ../../cadastro.php?error=' . urlencode('Ocorreu um erro ao processar seu cadastro.'));
          exit;
     }
}

// Salva a profissão na sessão para mostrar na confirmação
$_SESSION['dados_enviados'] = [
    'nome' => $nome,
    'cpf' => $cpf,
    'email' => $email,
    'profissao' => $profissao,
    'mensagem' => $mensagem,
    'arquivo' => $arquivo_nome
];

header("Location: confirmacao.php");
exit;
?>