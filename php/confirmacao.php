<?php
session_start();
$dados = $_SESSION['dados_enviados'] ?? null;
if (!$dados) {
    header("Location: ../cadastro.html");
    exit;
}
unset($_SESSION['dados_enviados']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Confirmação de Cadastro</title>
  <link rel="icon" type="image/png" href="../img/logoc.png">
  <link rel="stylesheet" href="../style/admin_style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
  <div class="admin-container">
    <div class="admin-header confirmation-box">
        <div class="icon"><i class="fas fa-check-circle"></i></div>
        <h1>Cadastro Recebido com Sucesso!</h1>
        <p>Obrigado por se cadastrar, <strong><?php echo htmlspecialchars($dados['nome']); ?></strong>. Seus dados foram registrados:</p>
    </div>

    <div class="data-summary">
      <p><strong>Nome:</strong> <?php echo htmlspecialchars($dados['nome']); ?></p>
      <p><strong>CPF:</strong> <?php echo htmlspecialchars($dados['cpf']); ?></p>
      <p><strong>Email:</strong> <?php echo htmlspecialchars($dados['email']); ?></p>
      <p><strong>Profissão:</strong> <?php echo htmlspecialchars($dados['profissao']); ?></p>
      <p><strong>Mensagem:</strong> <?php echo !empty($dados['mensagem']) ? nl2br(htmlspecialchars($dados['mensagem'])) : '<span class="no-data">Nenhuma</span>'; ?></p>
    </div>

    <div class="admin-actions">
      <a href="../index.php" class="btn btn-secondary">Voltar para o Início</a>
      <a href="../cadastro.php" class="btn btn-secondary">Novo Cadastro</a>
      <a href="lista.php" class="btn btn-primary">Ver Cadastros</a>
    </div>
  </div>
</body>
</html>