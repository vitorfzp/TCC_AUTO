<?php
require_once 'php/auth_guard.php';

// --- LÓGICA PHP ATUALIZADA ---
$servico_selecionado = $_GET['servico'] ?? 'N/A';
$termo_busca = trim($_GET['busca'] ?? '');

$descricoes_servicos = [
    'Limpeza Geral' => 'Profissionais avaliados para limpeza residencial ou comercial. De faxinas pesadas a manutenções diárias, encontre aqui a solução para um ambiente impecável.',
    'Pedreiro' => 'Encontre pedreiros qualificados para construções, reformas e reparos em geral. Qualidade e confiança para a sua obra.',
    'Jardineiro' => 'Especialistas em paisagismo e manutenção de jardins. Deixe seu espaço verde mais bonito e bem cuidado.',
    'Segurança' => 'Serviços de segurança para eventos e estabelecimentos, garantindo a tranquilidade e proteção que você precisa.',
    'Animador de Festa' => 'Leve mais alegria para sua festa com animadores criativos e divertidos para todas as idades.',
    'Barman' => 'Barmen experientes para preparar drinks incríveis e tornar seu evento inesquecível.',
    'Cabeleireiro' => 'Cortes, penteados e tratamentos com os melhores cabeleireiros da região.',
    'Transporte de aplicativo' => 'Motoristas parceiros para te levar ao seu destino com segurança e conforto.'
];
$descricao_atual = $descricoes_servicos[$servico_selecionado] ?? 'Encontre os profissionais mais bem avaliados pela nossa comunidade.';

try {
    // Busca TODOS os prestadores daquela área (para a lista principal e para o formulário)
    $sql_prestadores = "
        SELECT
            p.nome AS nome_prestador, p.profissao, AVG(f.nota) AS media_notas, COUNT(f.id) AS total_avaliacoes
        FROM prestadores p
        LEFT JOIN feedbacks f ON p.nome = f.nome_prestador AND p.profissao = f.profissao
        WHERE p.profissao = :servico";

    if (!empty($termo_busca)) {
        $sql_prestadores .= " AND p.nome LIKE :busca";
    }
    $sql_prestadores .= " GROUP BY p.cpf, p.nome, p.profissao ORDER BY media_notas DESC, p.nome ASC";
    $stmt_prestadores = $pdo->prepare($sql_prestadores);
    $params = [':servico' => $servico_selecionado];
    if (!empty($termo_busca)) {
        $params[':busca'] = '%' . $termo_busca . '%';
    }
    $stmt_prestadores->execute($params);
    $prestadores = $stmt_prestadores->fetchAll();

    // Busca os 3 feedbacks MAIS RECENTES para a barra lateral
    $stmt_recentes = $pdo->prepare("
        SELECT f.comentario, f.nota, u.nome as nome_usuario, f.nome_prestador
        FROM feedbacks f JOIN usuario u ON f.usuario_id = u.id
        WHERE f.profissao = ? ORDER BY f.data_feedback DESC LIMIT 3
    ");
    $stmt_recentes->execute([$servico_selecionado]);
    $feedbacks_recentes = $stmt_recentes->fetchAll();


} catch (PDOException $e) {
    die("Erro ao buscar dados: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profissionais de <?php echo htmlspecialchars($servico_selecionado); ?> - Autonowe</title>
    <link rel="icon" type="image/png" href="img/LOGO.png">
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
            <a href="local.php" class="menu-item active" title="Serviços"><svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg><span>Serviços</span></a>
            <a href="estaticas.php" class="menu-item" title="Estatísticas"><svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/></svg><span>Estatísticas</span></a>
            <a href="login.html" class="menu-item" title="Login"><svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M11 7L9.6 8.4l2.6 2.6H2v2h10.2l-2.6 2.6L11 17l5-5-5-5zm9 12h-8v-2h8v2zm0-4h-8v-2h8v2zm0-4h-8V9h8v2z"/></svg><span>Login / Cadastro</span></a>
        </nav>
    </aside>

    <main class="main-content">
        <section class="service-header">
            <h1><?php echo htmlspecialchars($servico_selecionado); ?></h1>
            <p><?php echo htmlspecialchars($descricao_atual); ?></p>
            <div class="service-actions">
                <button id="openFeedbackModalBtn" class="btn btn-primary"><i class="fas fa-star"></i> Avaliar um Profissional</button>
                <a href="local.php" class="btn btn-secondary">Voltar aos Serviços</a>
            </div>
        </section>

        <section class="search-tool-section">
            <form action="servico_detalhe.php" method="GET">
                <input type="hidden" name="servico" value="<?php echo htmlspecialchars($servico_selecionado); ?>">
                <div class="search-wrapper">
                    <i class="fas fa-search"></i>
                    <input type="text" name="busca" placeholder="Buscar por nome do profissional..." value="<?php echo htmlspecialchars($termo_busca); ?>">
                </div>
                <button type="submit">Buscar</button>
            </form>
        </section>

        <div class="content-layout">
            <div class="prestador-list-main">
                <h2>Profissionais Encontrados</h2>
                <div id="message-area" style="margin-bottom: 1rem;"></div>
                <div class="prestador-list">
                    <?php if (count($prestadores) > 0): ?>
                        <?php foreach ($prestadores as $prestador): ?>
                            <div class="prestador-card">
                                <div class="prestador-avatar"><i class="fas fa-user-circle"></i></div>
                                <div class="prestador-info">
                                    <h3><?php echo htmlspecialchars($prestador['nome_prestador']); ?></h3>
                                    <div class="prestador-rating">
                                        <?php if ($prestador['total_avaliacoes'] > 0): ?>
                                            <span class="stars"><?php echo str_repeat('⭐', round($prestador['media_notas'])); ?></span>
                                            <span class="rating-text">
                                                <strong><?php echo number_format($prestador['media_notas'], 1, ','); ?></strong>
                                                (<?php echo $prestador['total_avaliacoes']; ?> avaliações)
                                            </span>
                                        <?php else: ?>
                                            <span class="rating-text">Nenhuma avaliação ainda</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="prestador-action">
                                    <a href="perfil_prestador.php?nome=<?php echo urlencode($prestador['nome_prestador']); ?>" class="btn-ver-perfil">Ver Perfil Completo</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="no-results">Nenhum profissional encontrado para "<?php echo htmlspecialchars($servico_selecionado); ?>". <a href="cadastro.php">Seja o primeiro a se cadastrar!</a></p>
                    <?php endif; ?>
                </div>
            </div>

            <aside class="recent-feedbacks-sidebar">
                <h3>Feedbacks Recentes</h3>
                <?php if (count($feedbacks_recentes) > 0): ?>
                    <?php foreach ($feedbacks_recentes as $fb): ?>
                        <div class="feedback-card-small">
                            <p class="comment">"<?php echo htmlspecialchars($fb['comentario']); ?>"</p>
                            <div class="author-info">
                                <span><strong><?php echo htmlspecialchars($fb['nome_usuario']); ?></strong> avaliou <strong><?php echo htmlspecialchars($fb['nome_prestador']); ?></strong></span>
                                <span class="stars-small"><?php echo str_repeat('⭐', $fb['nota']); ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Ainda não há feedbacks para este serviço.</p>
                <?php endif; ?>
            </aside>
        </div>
    </main>

    <div id="feedbackFormModal" class="feedback-modal-overlay">
        <div class="feedback-modal-content">
            <div class="feedback-modal-header">
                <h3>Deixe sua Avaliação</h3>
                <button id="closeFeedbackModalBtn" class="modal-close-btn">&times;</button>
            </div>
            <div class="feedback-modal-body">
                
                <?php if (count($prestadores) > 0): ?>
                    <p>Sua avaliação é muito importante para a comunidade.</p>
                    <form class="custom-form" action="php/submit_feedback.php" method="POST">
                        <input type="hidden" name="servico_url" value="<?php echo htmlspecialchars($servico_selecionado); ?>">

                        <div class="form-group">
                            <label for="nome_prestador">Selecione o Prestador:</label>
                            <select id="nome_prestador" name="nome_prestador" class="custom-select" required>
                                <option value="" disabled selected>Escolha um profissional...</option>
                                <?php foreach ($prestadores as $p): ?>
                                    <option value="<?php echo htmlspecialchars($p['nome_prestador']); ?>">
                                        <?php echo htmlspecialchars($p['nome_prestador']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="profissao">Profissão:</label>
                            <input type="text" id="profissao" name="profissao" value="<?php echo htmlspecialchars($servico_selecionado); ?>" readonly style="background-color: #e9ecef;">
                        </div>
                        <div class="form-group">
                            <label for="nota">Nota:</label>
                            <select id="nota" name="nota" class="custom-select" required>
                                <option value="5">⭐⭐⭐⭐⭐ (Excelente)</option>
                                <option value="4">⭐⭐⭐⭐ (Bom)</option>
                                <option value="3">⭐⭐⭐ (Regular)</option>
                                <option value="2">⭐⭐ (Ruim)</option>
                                <option value="1">⭐ (Péssimo)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="comentario">Comentário:</label>
                            <textarea id="comentario" name="comentario" required rows="4" placeholder="Descreva sua experiência..."></textarea>
                        </div>
                        <button type="submit" class="custom-button">Enviar Avaliação</button>
                    </form>
                <?php else: ?>
                    <p class="no-results" style="margin-top: 1rem; font-size: 1.1rem;">
                        Não há profissionais cadastrados nesta categoria para avaliar no momento.
                    </p>
                <?php endif; ?>
                </div>
        </div>
    </div>

    <script src="script/session_handler.js" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modalOverlay = document.getElementById('feedbackFormModal');
            const openModalBtn = document.getElementById('openFeedbackModalBtn');
            const closeModalBtn = document.getElementById('closeFeedbackModalBtn');

            if (openModalBtn) {
                openModalBtn.addEventListener('click', () => {
                    modalOverlay.style.display = 'flex';
                });
            }
            if (closeModalBtn) {
                closeModalBtn.addEventListener('click', () => {
                    modalOverlay.style.display = 'none';
                });
            }
            window.addEventListener('click', (event) => {
                if (event.target === modalOverlay) {
                    modalOverlay.style.display = 'none';
                }
            });

            const params = new URLSearchParams(window.location.search);
            const messageArea = document.getElementById('message-area');
            const error = params.get('error');
            const success = params.get('success');
            if (error) {
                messageArea.innerHTML = `<p class="message error">${decodeURIComponent(error)}</p>`;
            } else if (success) {
                messageArea.innerHTML = `<p class="message success">${decodeURIComponent(success)}</p>`;
            }
            // Limpa a URL para a mensagem não reaparecer
            if(error || success) {
                window.history.replaceState({}, document.title, window.location.pathname + '?servico=' + encodeURIComponent('<?php echo $servico_selecionado; ?>'));
            }
        });
    </script>
</body>
</html>