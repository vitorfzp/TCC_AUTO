<?php
// 1. Guarda de Autenticação: Garante que apenas usuários logados possam enviar feedback.
// (auth_guard.php já deve incluir session_start() e require_once 'config.php')
require_once 'auth_guard.php';

// 2. Validação do Método: Apenas requisições POST são permitidas.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Redireciona para a página de onde veio ou para o índice se não houver referer
    $redirect_url = $_SERVER['HTTP_REFERER'] ?? '../index.php';
    header('Location: ' . $redirect_url);
    exit;
}

// 3. Coleta e Limpeza dos Dados do Formulário
$prestador_cpf = trim($_POST['prestador_cpf'] ?? ''); // O CPF do prestador sendo avaliado (vem do <select>)
$nota = filter_input(INPUT_POST, 'nota', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 5]]); // Valida a nota como inteiro entre 1 e 5
$comentario = trim($_POST['comentario'] ?? '');
$servico_url = trim($_POST['servico_url'] ?? ''); // Usado para redirecionar de volta

// Obtém o ID do usuário da sessão (garantido pelo auth_guard.php)
$usuario_id = $_SESSION['user_id'] ?? null;

// --- PREPARA URL DE REDIRECIONAMENTO ---
// Define uma URL padrão caso $servico_url esteja vazio
$redirect_page = !empty($servico_url) ? '../servico_detalhe.php?servico=' . urlencode($servico_url) : '../local.php'; // Se servico_url for vazio, volta para a lista geral de serviços

// 4. Validação dos Dados Essenciais
if (empty($prestador_cpf) || $nota === false || empty($comentario) || empty($usuario_id)) {
    // Se algum campo essencial estiver faltando ou inválido, redireciona de volta com erro.
    $error_message = 'Todos os campos são obrigatórios e a nota deve ser válida.';
    header('Location: ' . $redirect_page . '&error=' . urlencode($error_message));
    exit;
}

// 5. VERIFICAÇÃO DE AUTOAVALIAÇÃO (Opcional, mas recomendado se usuário também pode ser prestador)
try {
    // Busca o CPF do USUÁRIO LOGADO na tabela 'usuario'
    $stmt_user = $pdo->prepare("SELECT cpf FROM usuario WHERE id = ?");
    $stmt_user->execute([$usuario_id]);
    $usuario_cpf = $stmt_user->fetchColumn();

    // Compara o CPF do usuário logado com o CPF do prestador sendo avaliado
    // Esta verificação só faz sentido se um usuário pode ter o mesmo CPF que um prestador
    // Se são sistemas separados (ex: usuário não pode ser prestador), pode remover esta parte.
    if ($usuario_cpf && $usuario_cpf === $prestador_cpf) {
        // Se forem iguais, é uma autoavaliação. Bloqueie.
        $error_message = 'Operação inválida. Você não pode avaliar a si mesmo.';
        header('Location: ' . $redirect_page . '&error=' . urlencode($error_message));
        exit;
    }

    // --- CORREÇÃO PRINCIPAL: INSERÇÃO NO BANCO DE DADOS ---
    // A query agora usa as colunas corretas: usuario_id e prestador_cpf
    $stmt = $pdo->prepare(
        "INSERT INTO feedbacks (usuario_id, prestador_cpf, nota, comentario) VALUES (?, ?, ?, ?)"
    );

    // Executa a query com os dados corretos
    $stmt->execute([
        $usuario_id,     // ID do usuário logado
        $prestador_cpf,  // CPF do prestador selecionado no formulário
        $nota,           // Nota dada
        $comentario      // Comentário escrito
    ]);
    // --- FIM DA CORREÇÃO ---

    // 7. Redirecionamento de Sucesso
    $success_message = 'Feedback enviado com sucesso! Obrigado por contribuir.';
    header('Location: ' . $redirect_page . '&success=' . urlencode($success_message));
    exit;

} catch (PDOException $e) {
    // 8. Tratamento de Erros do Banco de Dados
    error_log("Erro ao salvar feedback: " . $e->getMessage()); // Loga o erro real para o desenvolvedor

    // Mensagem genérica para o usuário
    $error_message = 'Ocorreu um erro ao salvar seu feedback. Por favor, tente novamente.';
    // Verifica se é um erro de chave duplicada (usuário já avaliou esse prestador, por exemplo)
    if ($e->getCode() == 23000) { // Código de erro SQLSTATE para violação de constraint (pode ser UNIQUE)
       // Você pode adicionar uma constraint UNIQUE(usuario_id, prestador_cpf) na tabela feedbacks
       // para impedir avaliações duplicadas e tratar esse erro aqui.
       // $error_message = 'Você já avaliou este profissional.';
    }
    
    header('Location: ' . $redirect_page . '&error=' . urlencode($error_message));
    exit;
}
?>