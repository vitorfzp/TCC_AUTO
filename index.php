<?php
// --- INÍCIO DO BLOCO PHP CORRIGIDO ---
session_start();
require_once 'php/config.php'; // Adiciona a conexão com a base de dados
$user_info = null; // Garante que a variável sempre exista

// Verifica se o utilizador está logado antes de tentar buscar os dados
if (isset($_SESSION['user_id'])) {
    try {
        $stmt = $pdo->prepare("SELECT nome, email FROM usuario WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user_info = $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Erro ao buscar dados do usuário: " . $e->getMessage());
        $user_info = null; // Se der erro, a variável fica nula
    }
}
// --- FIM DO BLOCO PHP CORRIGIDO ---
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Autonowe - Conectando Profissionais e Clientes</title>
  <link rel="icon" type="image/png" href="img/LOGO.png">
  <link rel="stylesheet" href="style/style.css" />
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
      <a href="index.php" class="menu-item active" title="Início"><svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg><span>Início</span></a>
      <a href="local.php" class="menu-item" title="Serviços"><svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg><span>Serviços</span></a>
     <a href="login.html" class="menu-item" title="Login"><svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M11 7L9.6 8.4l2.6 2.6H2v2h10.2l-2.6 2.6L11 17l5-5-5-5zm9 12h-8v-2h8v2zm0-4h-8v-2h8v2zm0-4h-8V9h8v2z"/></svg><span>Login / Cadastro</span></a>
    </nav>
  </aside>

  <main class="main-content" id="index-main">
    
    <?php if ($user_info): ?>
    <div class="user-info-display">
        <strong><?php echo htmlspecialchars($user_info['nome']); ?></strong>
        <span><?php echo htmlspecialchars($user_info['email']); ?></span>
    </div>
    <?php endif; ?>

    <section class="new-hero">
        <div class="hero-text">
            <h1>Conectando você aos melhores profissionais.</h1>
            <p>Encontre prestadores de serviço de confiança, avaliados pela nossa comunidade, para resolver qualquer necessidade com qualidade e segurança.</p>
            <div class="hero-buttons">
                <a href="local.php" class="btn btn-primary">Encontrar um Profissional</a>
                <a href="cadastro.php" class="btn btn-secondary">Sou um Profissional</a>
            </div>
        </div>
        <div class="hero-image">
            <img src="https://soscasacuritiba.com.br/wp-content/uploads/2023/11/como-iniciar-na-profissao-de-pedreiro.webp" alt="Profissionais qualificados">
        </div>
    </section>

    <section class="about-us-section">
        <div class="about-us-content">
            <span class="section-tagline">Nossa Missão</span>
            <h2>Não é sobre serviços, é sobre confiança.</h2>
            <p>Nascemos de uma ideia simples: encontrar um profissional qualificado não deveria ser uma tarefa difícil. A Autonowe é mais que uma plataforma, é uma comunidade construída sobre a base da confiança, onde cada serviço realizado fortalece os laços entre clientes e prestadores.</p>
            <ul class="our-values">
                <li><i class="fas fa-shield-alt"></i> <strong>Segurança em Primeiro Lugar:</strong> Verificamos e validamos profissionais para sua tranquilidade.</li>
                <li><i class="fas fa-award"></i> <strong>Compromisso com a Qualidade:</strong> Um sistema de avaliação transparente que promove apenas os melhores.</li>
                <li><i class="fas fa-rocket"></i> <strong>Tecnologia que Facilita:</strong> Uma experiência intuitiva para você encontrar o que precisa, sem complicações.</li>
            </ul>
        </div>
        <div class="about-us-image">
            <img src="https://images.pexels.com/photos/3184418/pexels-photo-3184418.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1" alt="Equipe Autonowe construindo uma comunidade de confiança">
        </div>
    </section>

    <section class="how-it-works">
        <h2>Tudo o que você precisa, em 3 passos simples.</h2>
        <div class="steps">
            <div class="step">
                <div class="step-icon"><i class="fas fa-search"></i></div>
                <h3>Encontre o serviço que precisar</h3>
                <p>Descreva o que você precisa. É rápido, fácil e de graça.</p>
            </div>
       
            <div class="step">
                <div class="step-icon"><i class="fas fa-handshake"></i></div>
                <h3>Escolha quem você vai contratar</h3>
                <p>Negocie direto com eles e escolha o profissional ideal para o serviço.</p>
            </div>
        </div>
    </section>

    <section class="featured-services">
        <h2>Serviços Populares</h2>
        <div class="service-cards">
            <div class="service-card">
                <div class="service-card-image">
                    <img src="https://content.paodeacucar.com/wp-content/uploads/2019/06/produtos-de-limpeza2.jpg" alt="Serviços Domésticos">
                </div>
                <div class="service-card-content">
                    <h3>Serviços Domésticos</h3>
                    <p>Diaristas e profissionais de limpeza para deixar sua casa brilhando.</p>
                    <a href="local.php">Ver mais</a>
                </div>
            </div>
            <div class="service-card">
                 <div class="service-card-image">
                    <img src="https://jconstrucaoereformas.com.br/wp-content/uploads/2023/01/imagem-60.jpg" alt="Reformas e Reparos">
                </div>
                <div class="service-card-content">
                    <h3>Reformas e Reparos</h3>
                    <p>Pedreiros, pintores e eletricistas para a sua obra ou reparo.</p>
                    <a href="local.php">Ver mais</a>
                </div>
            </div>
            <div class="service-card">
                 <div class="service-card-image">
                    <img src="https://www.sp.senac.br/documents/20125/86544648/21798_01-04-2023.webp/4961fbe7-7fdc-0cee-8e8f-69155fe0379a?version=1.0&t=1724680707955null&download=true" alt="Jardinagem">
                </div>
                <div class="service-card-content">
                    <h3>Jardinagem</h3>
                    <p>Cuide do seu jardim com os melhores jardineiros da região.</p>
                    <a href="local.php">Ver mais</a>
                </div>
            </div>
        </div>
    </section>

    <footer class="main-footer">
        <div class="footer-content">
            <div class="footer-section">
                <h4>Principais Serviços</h4>
                <ul>
                    <li><a href="local.php">Limpeza Geral</a></li>
                    <li><a href="local.php">Pedreiro</a></li>
                    <li><a href="local.php">Jardineiro</a></li>
                    <li><a href="local.php">Segurança</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Autonowe</h4>
                <ul>
                    
                    <li><a href="termos.html">Termos de Uso e Política de Privacidade</a></li>
                    
                </ul>
            </div>
            <div class="footer-section">
                <h4>Redes Sociais</h4>
                <div class="social-icons">
                    <a href="https://www.instagram.com/autonowe_tcc/"><i class="fab fa-instagram"></i></a>
                    <a href="https://mail.google.com/mail/u/0/#inbox?compose=CllgCJvkXKnJPbXbxkqjTqfmBGxptkLbnlmFJSzJHNLGsWGwlSmZlNrmkznKxPwJNKNVSDKcbkg"><i class="fa fa-envelope" aria-hidden="true"></i></a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 Autonowe. Todos os direitos reservados.</p>
        </div>
    </footer>
  </main>

  <script src="script/session_handler.js" defer></script>
  <script src="script/toast_handler.js" defer></script>
</body>
</html>