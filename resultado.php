<?php
// 1. Simplesmente inclua o novo auth_guard. Ele faz todo o trabalho!
require_once 'php/auth_guard.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-sc   ale=1.0">
  <title>Resultados - Autonowe</title>
  <link rel="icon" type="image/png" href="img/logoc.png">
  <link rel="stylesheet" href="style/style.css">
  <link rel="stylesheet" href="style/custom.css">
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-header">
            <img src="img/logoc.png" alt="Logo Autonowe" class="logo-icon" />
            <h2 class="brand-title">AUTONOWE</h2>
        </div>
        <nav class="sidebar-menu">
            <a href="index.php" class="menu-item" title="Início"><svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg><span>Início</span></a>
            <a href="local.php" class="menu-item" title="Serviços"><svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg><span>Serviços</span></a>
            <a href="resultado.php" class="menu-item active" title="Resultados"><svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M20 2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H4V4h16v12zM6 10h8v2H6zm0-4h12v2H6z"/></svg><span>Resultados</span></a>
            <a href="feedback.php" class="menu-item" title="Feedback"><svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-7 12h-2v-2h2v2zm0-4h-2V6h2v4z"/></svg><span>Feedback</span></a>
            <a href="estaticas.php" class="menu-item" title="Estatísticas"><svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/></svg><span>Estatísticas</span></a>
            <a href="login.html" class="menu-item" title="Login"><svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M11 7L9.6 8.4l2.6 2.6H2v2h10.2l-2.6 2.6L11 17l5-5-5-5zm9 12h-8v-2h8v2zm0-4h-8v-2h8v2zm0-4h-8V9h8v2z"/></svg><span>Login / Cadastro</span></a>
        </nav>
    </aside>

    <main class="main-content">
        <section class="main-section">
          <h2>Feedbacks Recebidos</h2>
          <p>Veja as últimas avaliações enviadas pela comunidade.</p>
          <ul id="feedbackResults" class="feedback-list">
            <li>Carregando feedbacks...</li>
          </ul>
        </section>
    </main>
  <script src="script/result.js" defer></script>
  <script src="script/session_handler.js" defer></script>
</body>
</html>