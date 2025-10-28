<?php
// 1. Inclui o 'auth_guard', que deve iniciar a sessão e carregar o 'config.php'
require_once 'php/auth_guard.php';

// 2. Define variáveis e verifica se o usuário é um CLIENTE
$user_info = null;
$feedbacks_feitos = [];
$user_type = $_SESSION['user_type'] ?? null;
$user_id = $_SESSION['user_id'] ?? null;

// 3. Se não for um cliente logado, redireciona para o login
if ($user_type !== 'cliente' || !$user_id) {
    header("Location: login.html?error=" . urlencode("Você precisa estar logado como cliente para ver esta página."));
    exit;
}

// 4. Se for um cliente, busca os dados no banco
try {
    // Query 1: Buscar os dados da conta do usuário
    $stmt_info = $pdo->prepare("
        SELECT nome, email, cpf, DATE_FORMAT(data_cadastro, '%d/%m/%Y') as data_cadastro_fmt 
        FROM usuario 
        WHERE id = ?
    ");
    $stmt_info->execute([$user_id]);
    $user_info = $stmt_info->fetch();

    if (!$user_info) {
        // Segurança: Se o ID da sessão não corresponder a ninguém, desloga.
        header("Location: php/usuario/logout.php");
        exit;
    }

    // Query 2: Buscar o histórico de avaliações feitas por este usuário
    $stmt_feedbacks = $pdo->prepare("
        SELECT 
            f.nota, 
            f.comentario, 
            DATE_FORMAT(f.data_feedback, '%d/%m/%Y') as data_fmt,
            p.nome as nome_prestador,
            p.profissao
        FROM feedbacks f
        JOIN prestadores p ON f.prestador_cpf = p.cpf 
        WHERE f.usuario_id = ?
        ORDER BY f.data_feedback DESC
    ");
    $stmt_feedbacks->execute([$user_id]);
    $feedbacks_feitos = $stmt_feedbacks->fetchAll();

} catch (PDOException $e) {
    die("Erro ao buscar dados da conta: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minha Conta - <?php echo htmlspecialchars($user_info['nome']); ?> - Autonowe</title>
    <link rel="icon" type="image/png" href="img/logoc.png">
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="style/custom.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-header">
          <img src="img/LOGO.png" alt="Logo Autonowe" class="logo-icon" />
          <h2 class="brand-title">AUTONOWE</h2>
        </div>
        
        <nav class="sidebar-menu">
          <a href="index.php" class="menu-item" title="Início"><svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg><span>Início</span></a>
          <a href="local.php" class="menu-item" title="Serviços"><svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg><span>Serviços</span></a>
        

          <?php // Verifica se o $user_info (definido no topo) existe e é cliente
          if ($user_info && $user_type === 'cliente'): ?>
            <a href="minha_conta.php" class="menu-item active" title="Minha Conta"><i class="fas fa-user-circle"></i><span>Minha Conta</span></a>
            <a href="php/usuario/logout.php" class="menu-item" title="Sair"><i class="fas fa-sign-out-alt"></i><span>Sair</span></a>
          
          <?php else: // Se não for cliente, mostra Login
            header("Location: login.html"); // Segurança extra
            exit;
          ?>
          <?php endif; ?>
        </nav>
        </aside>

    <main class="main-content">
        <section class="main-section">
            <div class="profile-header">
                <div class="profile-avatar"><i class="fas fa-user-circle"></i></div>
                <div class="profile-summary">
                    <h1><?php echo htmlspecialchars($user_info['nome']); ?></h1>
                    <h2>Cliente Autonowe</h2>
                    <div class="profile-overall-rating">
                        <span>Membro desde: <strong><?php echo $user_info['data_cadastro_fmt']; ?></strong></span>
                    </div>
                </div>
            </div>

            <div class="profile-body">
                
                <div class="profile-about-section">
                    <h3>Informações da Conta</h3>
                    <ul class="info-list">
                        <li>
                            <i class="fas fa-envelope"></i>
                            <strong>Email:</strong>
                            <span><?php echo htmlspecialchars($user_info['email']); ?></span>
                        </li>
                        <li>
                            <i class="fas fa-id-card"></i>
                            <strong>CPF:</strong>
                            <span><?php echo htmlspecialchars(substr($user_info['cpf'], 0, 3)); ?>.***.***-**</span>
                        </li>
                    </ul>
                </div>

                <h3>Minhas Avaliações (<?php echo count($feedbacks_feitos); ?>)</h3>
                <div class="feedback-list-full">
                    <?php if (count($feedbacks_feitos) > 0): ?>
                        <?php foreach ($feedbacks_feitos as $fb): ?>
                            <div class="feedback-item">
                                <div class="feedback-item-header">
                                    <div class="feedback-author">
                                        <i class="fas fa-user-shield"></i>
                                        <span>Você avaliou: <strong><?php echo htmlspecialchars($fb['nome_prestador']); ?></strong> (<?php echo htmlspecialchars($fb['profissao']); ?>)</span>
                                    </div>
                                    <div class="feedback-date"><i class="fas fa-calendar-alt"></i><span><?php echo $fb['data_fmt']; ?></span></div>
                                </div>
                                <div class="feedback-item-body">
                                    <span class="stars"><?php echo str_repeat('⭐', $fb['nota']); ?></span>
                                    <p><?php echo htmlspecialchars($fb['comentario']); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Você ainda não fez nenhuma avaliação.</p>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main>

    <script src="script/session_handler.js" defer></script>
</body>
</html>