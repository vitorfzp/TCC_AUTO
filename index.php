<?php
// --- INÍCIO DO BLOCO PHP CORRIGIDO ---
session_start();
require_once 'php/config.php'; // Adiciona a conexão com a base de dados
$user_info = null; // Garante que a variável sempre exista

// Verifica se o utilizador está logado antes de tentar buscar os dados
if (isset($_SESSION['user_id']) && ($_SESSION['user_type'] ?? null) === 'cliente') {
    try {
        $stmt = $pdo->prepare("SELECT nome, email FROM usuario WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user_info = $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Erro ao buscar dados do usuário: " . $e->getMessage());
        $user_info = null; // Se der erro, a variável fica nula
    }
}

// --- NOVA QUERY PARA PROFISSIONAIS EM DESTAQUE (DINÂMICO) ---
try {
    // Busca 3 profissionais aleatoriamente da tabela prestadores
    $stmt_destaques = $pdo->query(
        "SELECT nome, profissao, mensagem, cpf 
         FROM prestadores 
         ORDER BY RAND() 
         LIMIT 3"
    );
    $profissionais_destaque = $stmt_destaques->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Erro ao buscar profissionais em destaque: " . $e->getMessage());
    $profissionais_destaque = []; // Se der erro, a lista fica vazia
}
// --- FIM DO BLOCO PHP ---
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

  <style>
    /* Seção: Profissionais em Destaque (Estilo do card mantido) */
    .sponsored-section {
      padding: 3rem 2rem;
      background-color: #f8f9fa;
      text-align: center;
    }
    .sponsored-section h2 {
      font-size: 2.2rem;
      color: #103352;
      margin-bottom: 2.5rem;
    }
    .sponsored-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 1.5rem;
      max-width: 1200px;
      margin: 0 auto;
      text-align: left;
    }
    .sponsored-card {
      background-color: #ffffff;
      border: 1px solid #e2e8f0;
      border-radius: 8px;
      padding: 2rem;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      /* Define uma altura mínima para os cards ficarem mais uniformes */
      min-height: 280px; 
      display: flex;
      flex-direction: column; /* Organiza o conteúdo em coluna */
    }
    .sponsored-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
    }
    .sponsored-card h3 {
      font-size: 1.3rem;
      color: #1e40af;
      margin-bottom: 0.5rem;
    }
    .sponsored-card .profession {
      display: block;
      font-weight: 600;
      color: #475569;
      margin-bottom: 1rem;
    }
    .sponsored-card p {
      color: #555;
      line-height: 1.6;
      margin-bottom: 1.5rem;
      font-size: 0.95rem;
      flex-grow: 1; /* Faz o parágrafo "empurrar" o botão para baixo */
    }
    .sponsored-card .btn-perfil {
      display: inline-block;
      align-self: flex-start; /* Alinha o botão ao início */
      background-color: transparent;
      color: #1e40af;
      border: 2px solid #1e40af;
      padding: 0.6rem 1.25rem;
      border-radius: 6px;
      text-decoration: none;
      font-weight: bold;
      transition: all 0.3s ease;
    }
    .sponsored-card .btn-perfil:hover {
      background-color: #1e40af;
      color: #ffffff;
    }


    /* --- Nova Seção: Patrocinador Oficial (COM FOTO) --- */
    .official-sponsor-section {
      padding: 4rem 2rem;
      background-color: #ffffff; /* Fundo branco */
      text-align: center;
      border-top: 1px solid #e2e8f0; /* Linha de separação */
    }
    .official-sponsor-section .sponsor-tagline {
      display: block;
      font-size: 1rem;
      color: #475569;
      font-weight: 600;
      text-transform: uppercase;
      margin-bottom: 0.5rem;
    }
    .official-sponsor-section h2 {
      font-size: 2.2rem;
      color: #103352;
      margin-bottom: 2.5rem;
    }
    .sponsor-content {
      display: flex;
      flex-wrap: wrap; /* Permite quebrar linha em telas menores */
      align-items: center;
      justify-content: center;
      gap: 3rem; /* Espaço entre a imagem e o texto */
      max-width: 900px;
      margin: 0 auto;
      text-align: left; /* Alinha o texto à esquerda por padrão */
    }
    .sponsor-image {
      flex-basis: 250px; /* Largura base da imagem */
      flex-grow: 1;
      max-width: 300px;
    }
    .sponsor-image img {
      width: 100%;
      border-radius: 8px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .sponsor-details {
      flex-basis: 400px; /* Largura base do texto */
      flex-grow: 2; /* Texto ocupa mais espaço */
    }
    .sponsor-details h3 {
      font-size: 1.8rem;
      color: #1e40af;
      margin-bottom: 0.75rem;
    }
    .sponsor-details p {
      font-size: 1.05rem;
      line-height: 1.7;
      color: #333;
      margin-bottom: 1.5rem;
    }
    .sponsor-details .btn-sponsor {
      /* Reutilizando estilo do botão primário do hero */
      display: inline-block;
      background-color: #1e40af;
      color: #ffffff;
      padding: 12px 28px;
      text-decoration: none;
      border-radius: 6px;
      font-weight: bold;
      font-size: 1rem;
      transition: background-color 0.3s ease;
    }
    .sponsor-details .btn-sponsor:hover {
      background-color: #103352;
    }
    
    /* Ajuste para telas menores */
    @media (max-width: 768px) {
      .sponsor-content {
        text-align: center; /* Centraliza tudo */
      }
      .sponsor-details {
        text-align: center;
      }
    }
  </style>
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

      <?php if ($user_info): ?>
        <a href="minha_conta.php" class="menu-item" title="Minha Conta"><i class="fas fa-user-circle"></i><span>Minha Conta</span></a>
        <a href="php/usuario/logout.php" class="menu-item" title="Sair"><i class="fas fa-sign-out-alt"></i><span>Sair</span></a>
      <?php else: ?>
        <a href="login.html" class="menu-item" title="Login"><svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M11 7L9.6 8.4l2.6 2.6H2v2h10.2l-2.6 2.6L11 17l5-5-5-5zm9 12h-8v-2h8v2zm0-4h-8v-2h8v2zm0-4h-8V9h8v2z"/></svg><span>Login / Cadastro</span></a>
      <?php endif; ?>
    </nav>
  </aside>

  <main class="main-content" id="index-main">
    
    <?php if ($user_info): ?>
    <div class="user-info-display">
        <strong><?php echo htmlspecialchars($user_info['nome']); ?></strong>
        <span>(<?php echo htmlspecialchars($user_info['email']); ?>)</span>
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
            <p>Nascemos de uma ideia simples: encontrar um profissional qualificado não deveria ser uma tarefa difícil. O Autonowe é mais que uma plataforma,com ela é possivel ter acesso via whatsapp de varios prestadores além disso, é uma comunidade construída sobre a base da confiança, onde cada serviço realizado fortalece os laços entre clientes e prestadores.</p>
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

    <section class="sponsored-section">
        <h2>Profissionais em Destaque</h2>
        <div class="sponsored-grid">
            
            <?php if (empty($profissionais_destaque)): ?>
                <p style="text-align:center; grid-column: 1 / -1; color: #555;">Nenhum profissional em destaque no momento.</p>
            <?php else: ?>
                <?php foreach ($profissionais_destaque as $destaque): ?>
                    <div class="sponsored-card">
                        <h3><?php echo htmlspecialchars($destaque['nome']); ?></h3>
                        <span class="profession"><?php echo htmlspecialchars($destaque['profissao']); ?></span>
                        
                        <p>
                          <?php 
                            $descricao = !empty($destaque['mensagem']) ? $destaque['mensagem'] : 'Profissional com cadastro verificado em nossa plataforma. Clique para ver mais detalhes.';
                            echo htmlspecialchars($descricao);
                          ?>
                        </p>
                        
                        <a href="perfil_prestador.php?nome=<?php echo urlencode($destaque['nome']); ?>" class="btn-perfil">
                          Ver Perfil
                        </a> 
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>
    </section>
    <section class="official-sponsor-section">
        <span class="sponsor-tagline">Parceiro Oficial</span>
        <h2>Conheça Nosso Patrocinador</h2>
        
        <div class="sponsor-content">
            
            <div class="sponsor-image">
                <img src="img/vasinformatica-logo-topo.png" alt="Logo do Patrocinador">
            </div>

            <div class="sponsor-details">
                <h3>VAS INFORMATICA</h3>
                <p>
                A VAS INFORMÁTICA é uma empresa que há mais de 10 anos no mercado vem prestando excelentes serviços de Wirelless, Trabalhando com fibra óptica
                </p>
                <a href="https://vasinformatica.com.br/" class="btn-sponsor" target="_blank" rel="noopener">
                    Visite o Site
                </a>
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
                    
                    <li><a href="https://wa.me/5511999999999" target="_blank" rel="noopener">Contato via WhatsApp</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Redes Sociais</h4>
                <div class="social-icons">
                    <a href="https://www.instagram.com/autonowe_tcc/" target="_blank" rel="noopener"><i class="fab fa-instagram"></i></a>
                    <a href="https://mail.google.com/mail/u/0/#inbox?compose=CllgCJvkXKnJPbXbxkqjTqfmBGxptkLbnlmFJSzJHNLGsWGwlSmZlNrmkznKxPwJNKNVSDKcbkg" target="_blank" rel="noopener"><i class="fa fa-envelope" aria-hidden="true"></i></a>
                    
                    <a href="https://wa.me/5511999999999" target="_blank" rel="noopener"><i class="fab fa-whatsapp"></i></a>
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