<?php
// 1. Guarda de Autenticação: Garante que apenas usuários logados possam enviar feedback.
// (auth_guard.php já deve incluir session_start() e require_once 'config.php')
require_once 'auth_guard.php'; 

// 2. Validação do Método: Apenas requisições POST são permitidas.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

// 3. Coleta e Limpeza dos Dados do Formulário
// --- CAMPO ADICIONADO ---
$prestador_cpf = trim($_POST['prestador_cpf'] ?? ''); // O CPF do prestador sendo avaliado
// --- FIM DA ADIÇÃO ---

$nome_prestador = trim($_POST['nome_prestador'] ?? '');
$profissao = trim($_POST['profissao'] ?? '');
$nota = $_POST['nota'] ?? 0;
$comentario = trim($_POST['comentario'] ?? '');
$servico_url = $_POST['servico_url'] ?? ''; // Para o redirecionamento

// 4. Validação dos Dados
// Adicionamos a verificação do $prestador_cpf
if (empty($nome_prestador) || empty($profissao) || empty($nota) || empty($comentario) || empty($servico_url) || empty($prestador_cpf)) {
    // Se algum campo estiver faltando, redireciona de volta com uma mensagem de erro.
    $redirect_url = '../servico_detalhe.php?servico=' . urlencode($servico_url) . '&error=' . urlencode('Todos os campos são obrigatórios.');
    header('Location: ' . $redirect_url);
    exit;
}

// 5. VERIFICAÇÃO DE AUTOAVALIAÇÃO (A LÓGICA PRINCIPAL)
try {
    // Pega o ID do usuário da sessão (definido em auth_guard.php)
    $usuario_id = $_SESSION['user_id'];

    // Busca o CPF do USUÁRIO LOGADO na tabela 'usuario'
    $stmt_user = $pdo->prepare("SELECT cpf FROM usuario WHERE id = ?");
    $stmt_user->execute([$usuario_id]);
    $usuario_cpf = $stmt_user->fetchColumn();

    // Compara o CPF do usuário logado com o CPF do prestador sendo avaliado
    if ($usuario_cpf && $usuario_cpf === $prestador_cpf) {
        // Se forem iguais, é uma autoavaliação. Bloqueie.
        $redirect_url = '../servico_detalhe.php?servico=' . urlencode($servico_url) . '&error=' . urlencode('Operação inválida. Você não pode avaliar a si mesmo.');
        header('Location: ' . $redirect_url);
        exit;
    }

    // 6. Inserção no Banco de Dados (Se não for autoavaliação)
    // Prepara a query SQL para inserir o feedback.
    $stmt = $pdo->prepare(
        "INSERT INTO feedbacks (usuario_id, nome_prestador, profissao, nota, comentario) VALUES (?, ?, ?, ?, ?)"
    );
    // Executa a query
    $stmt->execute([
        $usuario_id,
        $nome_prestador,
        $profissao,
        $nota,
        $comentario
    ]);

    // 7. Redirecionamento de Sucesso
    $redirect_url = '../servico_detalhe.php?servico=' . urlencode($servico_url) . '&success=' . urlencode('Feedback enviado com sucesso! Obrigado por contribuir.');
    header('Location: ' . $redirect_url);
    exit;

} catch (PDOException $e) {
    // 8. Tratamento de Erros do Banco de Dados
    error_log("Erro ao salvar feedback: " . $e->getMessage());
    
    $redirect_url = '../servico_detalhe.php?servico=' . urlencode($servico_url) . '&error=' . urlencode('Ocorreu um erro ao salvar seu feedback. Tente novamente.');
    header('Location: ' . $redirect_url);
    exit;
}
?>