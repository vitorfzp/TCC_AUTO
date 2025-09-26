<?php
require_once 'config_usuario';
session_start();


$nome     = $_POST['nome'] ?? '';
$cpf      = $_POST['cpf'] ?? ''; 
$email    = $_POST['email'] ?? '';


if (empty($nome) || empty($cpf) || empty($email)) {
     die("Erro: Nome, CPF e Email são obrigatórios.");
}


try {

    $stmt = $pdo->prepare("INSERT INTO usuario (cpf, nome, email,) VALUES (?, ?, ?, ?, ?)");

   
    $stmt->execute([$cpf, $nome, $email,]);

} catch (PDOException $e) {
     error_log("Erro ao inserir no banco de dados: " . $e->getMessage() . " - CPF: " . $cpf);
  
     if ($e->getCode() == 23000) { 
          die("Erro: Este CPF já está cadastrado.");
     } else {
          die("Ocorreu um erro ao processar seu cadastro. Tente novamente mais tarde ou contate o suporte.");
     }
}


$_SESSION['dados_enviados'] = [
    'nome' => $nome,
    'cpf' => $cpf,
    'email' => $email,
];


header("Location: confirmacao_u.php");
exit; 
?>