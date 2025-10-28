<?php
require_once 'php/auth_guard.php';

// --- INÍCIO DA CORREÇÃO (BLOCO PHP ADICIONADO) ---
// Busca os dados do usuário para o menu
$user_info = null;
$user_type = $_SESSION['user_type'] ?? null;
$user_id = $_SESSION['user_id'] ?? null;

// Verifica se o usuário é um 'cliente' logado para buscar infos
if ($user_type === 'cliente' && $user_id) {
    try {
        if (isset($pdo)) { // $pdo deve vir do auth_guard.php (via config.php)
            $stmt = $pdo->prepare("SELECT nome, email FROM usuario WHERE id = ?");
            $stmt->execute([$user_id]);
            $user_info = $stmt->fetch();
        }
    } catch (PDOException $e) {
        error_log("Erro ao buscar dados do usuário em perfil_prestador.php: " . $e->getMessage());
        $user_info = null; // Garante que $user_info seja nulo em caso de erro
    }
}
// --- FIM DA CORREÇÃO ---


$nome_prestador = urldecode($_GET['nome'] ?? '');

if (empty($nome_prestador)) {
    die("Nenhum prestador especificado.");
}

try {
    // --- CORREÇÃO 1: Buscar o CPF do prestador ---
    // Pedimos também o 'cpf' do prestador
    $stmt_info = $pdo->prepare("SELECT cpf, mensagem, telefone, profissao FROM prestadores WHERE nome = ?");
    $stmt_info->execute([$nome_prestador]);
    $prestador_info = $stmt_info->fetch();

    if (!$prestador_info) {
        // Se não encontrar o prestador, encerra a execução.
        die("Prestador não encontrado.");
    }

    // --- FIM DA CORREÇÃO 1 ---

    // Busca TODOS os feedbacks individuais para este prestador
    // --- CORREÇÃO 2: Usar o CPF para buscar os feedbacks ---
    $stmt_feedbacks = $pdo->prepare("
        SELECT 
            f.nota, 
            f.comentario, 
            DATE_FORMAT(f.data_feedback, '%d/%m/%Y') as data_formatada,
            u.nome as nome_usuario
        FROM feedbacks f
        JOIN usuario u ON f.usuario_id = u.id
        WHERE f.prestador_cpf = ? -- <-- MUDANÇA AQUI (de 'nome_prestador' para 'prestador_cpf')
        ORDER BY f.data_feedback DESC
    ");
    // Usamos o CPF que buscamos na consulta anterior
    $stmt_feedbacks->execute([$prestador_info['cpf']]); // <-- MUDANÇA AQUI
    $feedbacks = $stmt_feedbacks->fetchAll();
    // --- FIM DA CORREÇÃO 2 ---

    // Calcula as estatísticas gerais a partir dos feedbacks buscados
    $total_avaliacoes = count($feedbacks);
    $media_notas = 0;
    if ($total_avaliacoes > 0) {
        $soma_notas = array_reduce($feedbacks, function($carry, $item) {
            return $carry + $item['nota'];
        }, 0);
        $media_notas = $soma_notas / $total_avaliacoes;
    }

} catch (PDOException $e) {
    die("Erro ao buscar perfil do prestador: " . $e->getMessage());
}

// NOVA FUNÇÃO: Prepara o link para o WhatsApp
function formatar_link_whatsapp($telefone, $nome_prestador) {
    if (empty($telefone)) {
        return null;
    }
    // Remove todos os caracteres que não são números
    $numero_limpo = preg_replace('/\D/', '', $telefone);
    // Cria uma mensagem padrão
    $mensagem = urlencode("Olá " . $nome_prestador . ", encontrei seu contato no site Autonowe e gostaria de mais informações.");
    // Retorna o link completo
    return "https://wa.me/55" . $numero_limpo . "?text=" . $mensagem;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de <?php echo htmlspecialchars($nome_prestador); ?> - Autonowe</title>
    <link rel="icon" type="image/png" href="img/LOGO.png"> 
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="style/custom.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        /* --- Refinamento do Cabeçalho do Perfil --- */
        .profile-header {
            background-color: #ffffff; /* Fundo branco */
            border-radius: 12px; /* Bordas mais arredondadas */
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08); /* Sombra mais suave */
            padding: 2rem; /* Mais espaçamento interno */
            display: flex; /* Continua flex */
            flex-wrap: wrap; /* Permite quebrar linha em telas menores */
            align-items: center; /* Alinha itens verticalmente */
            gap: 1.5rem; /* Espaço entre os elementos */
            margin-bottom: 2.5rem; /* Mais espaço abaixo */
        }

        /* --- Novo Avatar Estilizado --- */
        .profile-avatar {
            width: 100px; /* Tamanho maior */
            height: 100px;
            border-radius: 50%; /* Círculo perfeito */
            /* Gradiente com os azuis do tema */
            background-image: linear-gradient(135deg, #1e40af, #3b82f6);
            color: #ffffff; /* Ícone branco */
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(30, 64, 175, 0.3); /* Sombra do avatar */
        }
        .profile-avatar .fa-user-shield {
            font-size: 3rem; /* Ícone maior */
        }
        
        /* Ajuste no sumário e contato */
        .profile-summary {
            flex-grow: 1; /* Ocupa o espaço disponível */
        }
        .profile-summary h1 {
            font-size: 2.2rem; /* Título maior */
            color: #103352;
            margin-bottom: 0.25rem;
        }
        .profile-summary h2 {
            font-size: 1.25rem; /* Profissão */
            color: #3b82f6; /* Cor azul destacada */
            font-weight: 500;
            margin-top: 0;
            margin-bottom: 0.75rem;
        }
        .profile-overall-rating {
            font-size: 1rem;
            color: #475569;
        }
        .profile-contact {
            display: flex;
            flex-direction: column;
            gap: 10px;
            align-items: flex-start;
            margin-left: auto; /* Joga os botões para a direita em telas grandes */
        }

        /* --- Estilo dos Títulos de Seção (h3) --- */
        .profile-body h3 {
            font-size: 1.6rem;
            color: #103352;
            margin-top: 2.5rem; /* Mais espaço acima */
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem; /* Espaço abaixo do texto */
            border-bottom: 3px solid #e2e8f0; /* Linha sutil */
        }

        /* --- Card para a Seção "Sobre" --- */
        .profile-about-section {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }
        .profile-about-section h3 {
            margin-top: 0; /* Remove margem do h3 dentro do card */
            border-bottom: none; /* Remove borda do h3 dentro do card */
            padding-bottom: 0;
            margin-bottom: 1rem;
        }
        .profile-about-section p {
            font-size: 1.05rem;
            line-height: 1.7;
            color: #333;
        }

        /* --- Refinamento da Lista de Feedbacks --- */
        .feedback-item {
            border: 1px solid #e2e8f0; /* Borda sutil */
            border-radius: 8px; /* Bordas arredondadas */
            margin-bottom: 1.5rem; /* Mais espaço entre eles */
            overflow: hidden; /* Garante que o fundo se aplique */
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            transition: box-shadow 0.3s ease;
        }
        .feedback-item:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }
        .feedback-item-header {
            background-color: #f8f9fa; /* Fundo leve no cabeçalho do feedback */
            padding: 0.75rem 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .feedback-item-body {
            padding: 1.5rem;
            background-color: #ffffff;
        }
        .feedback-item-body .stars {
            margin-bottom: 0.5rem;
            display: block; /* Garante que fique acima do parágrafo */
        }
        .feedback-item-body p {
            font-size: 1rem;
            color: #333;
            line-height: 1.6;
            margin: 0;
        }


        /* --- Estilos do Modal de QR Code (Mantidos da última vez) --- */
        .btn-qr-code {
            background-color: #6c757d; color: #ffffff; padding: 10px 15px;
            border-radius: 5px; text-decoration: none; font-weight: bold;
            display: inline-flex; align-items: center; gap: 8px;
            border: none; cursor: pointer; font-size: 0.9rem;
            transition: background-color 0.3s ease;
        }
        .btn-qr-code:hover { background-color: #5a6268; }
        .qr-modal-overlay {
            display: none; position: fixed; z-index: 1000;
            left: 0; top: 0; width: 100%; height: 100%;
            overflow: auto; background-color: rgba(0, 0, 0, 0.6);
            justify-content: center; align-items: center;
        }
        .qr-modal-content {
            background-color: #fefefe; margin: auto; padding: 25px;
            border-radius: 8px; box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            width: 90%; max-width: 400px; text-align: center;
            position: relative; animation: fadeIn 0.3s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }
        .qr-modal-content h3 { margin-top: 0; color: #103352; }
        .qr-image-container {
            margin: 20px 0; padding: 10px; background-color: #f8f9fa;
            border: 1px solid #e2e8f0; border-radius: 8px;
            display: inline-block;
        }
        .qr-image-container img { display: block; width: 200px; height: 200px; }
        .modal-close-btn {
            position: absolute; top: 10px; right: 15px;
            color: #aaa; font-size: 28px; font-weight: bold; cursor: pointer;
        }
        .modal-close-btn:hover, .modal-close-btn:focus { color: #000; }
    </style>
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
                

            <?php if ($user_info && $user_type === 'cliente'): // Se for cliente logado ?>
                <a href="minha_conta.php" class="menu-item" title="Minha Conta"><i class="fas fa-user-circle"></i><span>Minha Conta</span></a>
                <a href="php/usuario/logout.php" class="menu-item" title="Sair"><i class="fas fa-sign-out-alt"></i><span>Sair</span></a>
            
            <?php else: // Para prestadores ou visitantes ?>
                <a href="login.html" class="menu-item" title="Login"><svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M11 7L9.6 8.4l2.6 2.6H2v2h10.2l-2.6 2.6L11 17l5-5-5-5zm9 12h-8v-2h8v2zm0-4h-8v-2h8v2zm0-4h-8V9h8v2z"/></svg><span>Login / Cadastro</span></a>
            <?php endif; ?>
        </nav>
        </aside>

    <main class="main-content">
        <section class="main-section">
            
            <div class="profile-header">
                <div class="profile-avatar"><i class="fas fa-user-shield"></i></div>
                <div class="profile-summary">
                    <h1><?php echo htmlspecialchars($nome_prestador); ?></h1>
                    <h2><?php echo htmlspecialchars($prestador_info['profissao']); ?></h2>
                    <div class="profile-overall-rating">
                        <span class="stars"><?php echo str_repeat('⭐', round($media_notas)); ?></span>
                        <strong><?php echo number_format($media_notas, 1, ','); ?></strong>
                        <span>(baseado em <?php echo $total_avaliacoes; ?> avaliações)</span>
                    </div>
                </div>

                <?php 
                $link_whatsapp = formatar_link_whatsapp($prestador_info['telefone'] ?? null, $nome_prestador);
                
                if ($link_whatsapp): 
                ?>
                    <div class="profile-contact">
                        <a href="<?php echo $link_whatsapp; ?>" class="btn-whatsapp" target="_blank">
                            <i class="fab fa-whatsapp"></i> Iniciar Conversa
                        </a>
                        <button id="openQrModalBtn" class="btn-qr-code">
                            <i class="fas fa-qrcode"></i> Escanear QR Code
                        </button>
                    </div>
                <?php endif; ?>
            </div>
            <div class="profile-body">
                
                <?php if ($prestador_info && !empty(trim($prestador_info['mensagem']))): ?>
                    <div class="profile-about-section">
                        <h3>Sobre o Profissional</h3>
                        <p><?php echo nl2br(htmlspecialchars($prestador_info['mensagem'])); ?></p>
                    </div>
                <?php endif; ?>

                <div class="profile-stats-panel">
                    <h3>Desempenho em Números</h3>
                    <div class="profile-kpi-grid">
                        <div class="kpi-card">
                            <div class="kpi-icon" style="background-color: #dbeafe; color: #3b82f6;"><i class="fas fa-comments"></i></div>
                            <div class="kpi-info">
                                <span class="kpi-title">Total de Avaliações</span>
                                <span class="kpi-value"><?php echo $total_avaliacoes; ?></span>
                            </div>
                        </div>
                        <div class="kpi-card">
                            <div class="kpi-icon" style="background-color: #dcfce7; color: #22c55e;"><i class="fas fa-star-half-alt"></i></div>
                            <div class="kpi-info">
                                <span class="kpi-title">Média de Notas</span>
                                <span class="kpi-value"><?php echo number_format($media_notas, 1, ','); ?> ⭐</span>
                            </div>
                        </div>
                    </div>
                    <div class="profile-chart-container">
                        <canvas id="profileFeedbackChart"></canvas>
                    </div>
                </div>


                <h3>O que os clientes dizem</h3>
                <div class="feedback-list-full">
                    <?php if ($total_avaliacoes > 0): ?>
                        <?php foreach ($feedbacks as $fb): ?>
                            <div class="feedback-item">
                                <div class="feedback-item-header">
                                    <div class="feedback-author"><i class="fas fa-user"></i><span><?php echo htmlspecialchars($fb['nome_usuario']); ?></span></div>
                                    <div class="feedback-date"><i class="fas fa-calendar-alt"></i><span><?php echo $fb['data_formatada']; ?></span></div>
                                </div>
                                <div class="feedback-item-body">
                                    <span class="stars"><?php echo str_repeat('⭐', $fb['nota']); ?></span>
                                    <p><?php echo htmlspecialchars($fb['comentario']); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Este profissional ainda não recebeu avaliações.</p>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main>

    <?php if ($link_whatsapp): ?>
        <div id="whatsAppQrModal" class="qr-modal-overlay">
            <div class="qr-modal-content">
                <span id="closeQrModalBtn" class="modal-close-btn">&times;</span>
                
                <h3>Abra o WhatsApp no seu celular</h3>
                <p>Escaneie o código abaixo para iniciar a conversa com <?php echo htmlspecialchars($nome_prestador); ?>.</p>
                
                <div class="qr-image-container">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?php echo urlencode($link_whatsapp); ?>" 
                         alt="QR Code para WhatsApp de <?php echo htmlspecialchars($nome_prestador); ?>">
                </div>
                <br>
                <small>Desenvolvido por <a href="https://goqr.me/" target="_blank">QR Code API</a></small>
            </div>
        </div>
    <?php endif; ?>
    <script>
        const feedbacksData = <?php echo json_encode($feedbacks); ?>;
    </script>
    <script src="script/session_handler.js" defer></script>
    <script src="script/perfil_chart.js" defer></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Seleciona os elementos do modal
            const modalOverlay = document.getElementById('whatsAppQrModal');
            const openModalBtn = document.getElementById('openQrModalBtn');
            const closeModalBtn = document.getElementById('closeQrModalBtn');

            // Se o botão de abrir existe, adiciona o evento de clique
            if (openModalBtn) {
                openModalBtn.addEventListener('click', () => {
                    modalOverlay.style.display = 'flex'; // Mostra o modal
                });
            }

            // Se o botão de fechar existe, adiciona o evento de clique
            if (closeModalBtn) {
                closeModalBtn.addEventListener('click', () => {
                    modalOverlay.style.display = 'none'; // Esconde o modal
                });
            }

            // Fecha o modal se o usuário clicar fora da caixa de conteúdo
            window.addEventListener('click', (event) => {
                if (event.target === modalOverlay) {
                    modalOverlay.style.display = 'none';
                }
            });
        });
    </script>
    </body>
</html>