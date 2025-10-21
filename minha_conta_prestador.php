<?php
// 1. Inclui o guarda. Isso protege a página e nos dá $prestador_cpf_logado
require_once 'php/guarda/prestador_guard.php';

// 2. Busca os dados ATUAIS do prestador
try {
    $stmt = $pdo->prepare("SELECT * FROM prestadores WHERE cpf = ?");
    $stmt->execute([$prestador_cpf_logado]);
    $prestador = $stmt->fetch();

    // 3. Busca os feedbacks desse prestador
    // (Note que usamos a coluna 'nome_prestador' por enquanto, como no seu 'servico_detalhe.php')
    // O ideal seria usar o 'prestador_cpf' que adicionamos no Passo 0
    $stmt_feed = $pdo->prepare("
        SELECT f.*, u.nome as nome_usuario 
        FROM feedbacks f 
        JOIN usuario u ON f.usuario_id = u.id 
        WHERE f.nome_prestador = ? AND f.profissao = ?
        ORDER BY f.data_feedback DESC
    ");
    $stmt_feed->execute([$prestador['nome'], $prestador['profissao']]);
    $feedbacks = $stmt_feed->fetchAll();

    // 4. Calcula estatísticas
    $total_avaliacoes = count($feedbacks);
    $soma_notas = 0;
    foreach ($feedbacks as $fb) {
        $soma_notas += $fb['nota'];
    }
    $media_notas = ($total_avaliacoes > 0) ? ($soma_notas / $total_avaliacoes) : 0;

} catch (PDOException $e) {
    die("Erro ao carregar dados do perfil: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Painel - <?php echo htmlspecialchars($prestador['nome']); ?></title>
    <link rel="icon" type="image/png" href="img/LOGO.png">
    <link rel="stylesheet" href="style/style.css"> <link rel="stylesheet" href="style/dashboard.css"> <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-header">
            <img src="img/LOGO.png" alt="Logo Autonowe" class="logo-icon" />
            <h2 class="brand-title">AUTONOWE</h2>
        </div>
        <nav class="sidebar-menu">
            <a href="index.php" class="menu-item" title="Ver Site"><i class="fas fa-home"></i><span>Ver Site</span></a>
            <a href="minha_conta_prestador.php" class="menu-item active" title="Meu Painel"><i class="fas fa-tachometer-alt"></i><span>Meu Painel</span></a>
            <a href="php/usuario/logout.php" class="menu-item" title="Sair"><i class="fas fa-sign-out-alt"></i><span>Sair</span></a>
        </nav>
    </aside>

    <main class="main-content">
        <section class="service-header">
            <h1>Meu Painel</h1>
            <p>Olá, <strong><?php echo htmlspecialchars($prestador['nome']); ?></strong>! Gerencie seu perfil e suas avaliações aqui.</p>
        </section>

        <div id="message-area-dashboard" style="margin-bottom: 1rem; padding: 0 2rem;"></div>

        <section class="dashboard-stats">
            <div class="stat-card">
                <i class="fas fa-star stat-icon" style="color: #ffc107;"></i>
                <div class="stat-info">
                    <h3>Nota Média</h3>
                    <p><?php echo number_format($media_notas, 1, ',', '.'); ?> / 5.0</p>
                </div>
            </div>
            <div class="stat-card">
                <i class="fas fa-comment stat-icon" style="color: #007bff;"></i>
                <div class="stat-info">
                    <h3>Avaliações</h3>
                    <p><?php echo $total_avaliacoes; ?> totais</p>
                </div>
            </div>
            <div class="stat-card">
                <i class="fas fa-briefcase stat-icon" style="color: #28a745;"></i>
                <div class="stat-info">
                    <h3>Sua Profissão</h3>
                    <p><?php echo htmlspecialchars($prestador['profissao']); ?></p>
                </div>
            </div>
        </section>

        <div class="dashboard-layout">
            
            <div class="dashboard-card">
                <h2>Editar Perfil</h2>
                <p>Mantenha suas informações de contato e sua mensagem de apresentação atualizadas.</p>
                
                <form action="php/atualizar_perfil.php" method="POST" class="custom-form">
                    <div class="form-group">
                        <label for="nome">Nome Completo (Não editável)</label>
                        <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($prestador['nome']); ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="cpf">CPF (Não editável)</label>
                        <input type="text" id="cpf" name="cpf" value="<?php echo htmlspecialchars($prestador['cpf']); ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="email">Email (Não editável)</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($prestador['email']); ?>" readonly>
                    </div>

                    <hr style="margin: 20px 0;">

                    <div class="form-group">
                        <label for="telefone">WhatsApp (Telefone)</label>
                        <input type="tel" id="telefone" name="telefone" value="<?php echo htmlspecialchars($prestador['telefone']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="mensagem">Mensagem de Apresentação</label>
                        <textarea id="mensagem" name="mensagem" rows="5"><?php echo htmlspecialchars($prestador['mensagem']); ?></textarea>
                    </div>
                    
                    <button type="submit" class="custom-button">Salvar Alterações</button>
                </form>
            </div>

            <div class="dashboard-card">
                <h2>Minhas Avaliações Recentes</h2>
                <div class="feedback-list">
                    <?php if (count($feedbacks) > 0): ?>
                        <?php foreach ($feedbacks as $fb): ?>
                            <div class="feedback-card-small">
                                <p class="comment">"<?php echo htmlspecialchars($fb['comentario']); ?>"</p>
                                <div class="author-info">
                                    <span><strong><?php echo htmlspecialchars($fb['nome_usuario']); ?></strong></span>
                                    <span class="stars-small"><?php echo str_repeat('⭐', $fb['nota']); ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Você ainda não recebeu nenhuma avaliação.</p>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </main>

    <script>
    // Script para mostrar mensagens de sucesso/erro vindas do 'atualizar_perfil.php'
    document.addEventListener('DOMContentLoaded', () => {
        const params = new URLSearchParams(window.location.search);
        const messageArea = document.getElementById('message-area-dashboard');
        
        const error = params.get('error');
        const success = params.get('success');

        if (error) {
            messageArea.innerHTML = `<p class="message error">${decodeURIComponent(error)}</p>`;
        } else if (success) {
            messageArea.innerHTML = `<p class="message success">${decodeURIComponent(success)}</p>`;
        }
        
        // Limpa a URL
        if(error || success) {
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    });
    </script>
</body>
</html>