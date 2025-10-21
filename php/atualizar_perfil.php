<?php
// 1. Inclui o guarda (protege contra acesso direto e pega o CPF)
require_once 'guardas/prestador_guard.php';

// 2. Verifica se o método é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../minha_conta_prestador.php');
    exit;
}

// 3. Coleta e valida os dados do formulário
$telefone = trim($_POST['telefone'] ?? '');
$mensagem = trim($_POST['mensagem'] ?? '');

if (empty($telefone)) {
    header('Location: ../minha_conta_prestador.php?error=' . urlencode('O campo telefone é obrigatório.'));
    exit;
}

// 4. Atualiza o banco de dados
try {
    $stmt = $pdo->prepare("UPDATE prestadores SET telefone = ?, mensagem = ? WHERE cpf = ?");
    $stmt->execute([$telefone, $mensagem, $prestador_cpf_logado]);

    // 5. Redireciona de volta com sucesso
    header('Location: ../minha_conta_prestador.php?success=' . urlencode('Perfil atualizado com sucesso!'));
    exit;

} catch (PDOException $e) {
    error_log("Erro ao atualizar perfil: " . $e->getMessage());
    header('Location: ../minha_conta_prestador.php?error=' . urlencode('Ocorreu um erro ao atualizar seu perfil.'));
    exit;
}
?>