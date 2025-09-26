<?php
// 1. Guarda de Autenticação: Garante que apenas usuários logados possam enviar feedback.
require_once 'auth_guard.php';

// 2. Validação do Método: Apenas requisições POST são permitidas.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Redireciona para a página inicial se o método não for POST.
    header('Location: ../index.php');
    exit;
}

// 3. Coleta e Limpeza dos Dados do Formulário
// Usamos 'trim' para remover espaços em branco e o operador '??' para evitar erros.
$nome_prestador = trim($_POST['nome_prestador'] ?? '');
$profissao = trim($_POST['profissao'] ?? '');
$nota = $_POST['nota'] ?? 0;
$comentario = trim($_POST['comentario'] ?? '');
$servico_url = $_POST['servico_url'] ?? ''; // Para o redirecionamento

// 4. Validação dos Dados
// Verifica se os campos obrigatórios não estão vazios.
if (empty($nome_prestador) || empty($profissao) || empty($nota) || empty($comentario) || empty($servico_url)) {
    // Se algum campo estiver faltando, redireciona de volta com uma mensagem de erro.
    $redirect_url = '../servico_detalhe.php?servico=' . urlencode($servico_url) . '&error=' . urlencode('Todos os campos são obrigatórios.');
    header('Location: ' . $redirect_url);
    exit;
}

// 5. Inserção no Banco de Dados
try {
    // Prepara a query SQL para inserir o feedback. Usar 'prepare' previne SQL Injection.
    $stmt = $pdo->prepare(
        "INSERT INTO feedbacks (usuario_id, nome_prestador, profissao, nota, comentario) VALUES (?, ?, ?, ?, ?)"
    );
    // Executa a query, passando os dados de forma segura.
    // $_SESSION['user_id'] vem do 'auth_guard.php' e contém o ID do usuário logado.
    $stmt->execute([
        $_SESSION['user_id'],
        $nome_prestador,
        $profissao,
        $nota,
        $comentario
    ]);

    // 6. Redirecionamento de Sucesso
    // Se a inserção for bem-sucedida, redireciona com uma mensagem de sucesso.
    $redirect_url = '../servico_detalhe.php?servico=' . urlencode($servico_url) . '&success=' . urlencode('Feedback enviado com sucesso! Obrigado por contribuir.');
    header('Location: ' . $redirect_url);
    exit;

} catch (PDOException $e) {
    // 7. Tratamento de Erros do Banco de Dados
    // Se ocorrer um erro na query, registra o erro para o desenvolvedor.
    error_log("Erro ao salvar feedback: " . $e->getMessage());
    
    // Redireciona o usuário com uma mensagem de erro genérica.
    $redirect_url = '../servico_detalhe.php?servico=' . urlencode($servico_url) . '&error=' . urlencode('Ocorreu um erro ao salvar seu feedback. Tente novamente.');
    header('Location: ' . $redirect_url);
    exit;
}
?>