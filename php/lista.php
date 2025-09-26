<?php
require_once 'config.php'; 
try {
    $stmt = $pdo->query("SELECT nome, cpf, email, profissao, mensagem, DATE_FORMAT(data_cadastro, '%d/%m/%Y %H:%i') as data_formatada FROM prestadores ORDER BY data_cadastro DESC");
    $dados = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Erro ao buscar cadastros: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Lista de Cadastros</title>
  <link rel="icon" type="image/png" href="../img/logoc.png">
  <link rel="stylesheet" href="../style/admin_style.css">
</head>
<body>
  <div class="admin-container">
    <div class="admin-header">
        <h1>Todos os Cadastros</h1>
        <p>Lista de todos os profissionais cadastrados na plataforma.</p>
    </div>

    <?php if (count($dados) > 0): ?>
        <ul class="cadastro-list">
          <?php foreach ($dados as $p): ?>
            <li class="cadastro-item">
              <p><strong>Nome:</strong> <?php echo htmlspecialchars($p['nome']); ?></p>
              <p><strong>Profissão:</strong> <?php echo htmlspecialchars($p['profissao']); ?></p>
              <p><strong>Email:</strong> <?php echo htmlspecialchars($p['email']); ?></p>
              <div class="meta-info">
                  <span><strong>CPF:</strong> <?php echo htmlspecialchars($p['cpf']); ?></span> | 
                  <span><strong>Data:</strong> <?php echo $p['data_formatada']; ?></span>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p style="text-align: center;">Nenhum cadastro encontrado.</p>
    <?php endif; ?>

    <div class="admin-actions">
        <a href="../index.php" class="btn btn-secondary">Voltar para o Início</a>
        <a href="../cadastro.php" class="btn btn-primary">← Novo Cadastro</a>
    </div>
  </div>
</body>
</html>