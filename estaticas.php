<?php
// O auth_guard.php já garante que o usuário está logado.
require_once 'php/auth_guard.php';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Painel de Estatísticas - Autonowe</title>
  <link rel="icon" type="image/png" href="img/logoc.png">
  <link rel="stylesheet" href="style/style.css">
  <link rel="stylesheet" href="style/custom.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            <a href="estaticas.php" class="menu-item active" title="Estatísticas"><svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/></svg><span>Estatísticas</span></a>
            <a href="login.html" class="menu-item" title="Login"><svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M11 7L9.6 8.4l2.6 2.6H2v2h10.2l-2.6 2.6L11 17l5-5-5-5zm9 12h-8v-2h8v2zm0-4h-8v-2h8v2zm0-4h-8V9h8v2z"/></svg><span>Login / Cadastro</span></a>
        </nav>
    </aside>

    <main class="main-content">
        <section class="main-section">
            <h2>Painel de Desempenho</h2>
            <p>Analise a performance das avaliações na plataforma.</p>

            <div class="kpi-grid">
                <div class="kpi-card">
                    <div class="kpi-icon" style="background-color: #dbeafe; color: #3b82f6;">
                        <i class="fas fa-comments"></i>
                    </div>
                    <div class="kpi-info">
                        <span class="kpi-title">Total de Avaliações</span>
                        <span class="kpi-value" id="kpi-total-avaliacoes">--</span>
                    </div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-icon" style="background-color: #dcfce7; color: #22c55e;">
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                    <div class="kpi-info">
                        <span class="kpi-title">Média Geral</span>
                        <span class="kpi-value" id="kpi-media-geral">--</span>
                    </div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-icon" style="background-color: #fef9c3; color: #f59e0b;">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <div class="kpi-info">
                        <span class="kpi-title">Prestador em Destaque</span>
                        <span class="kpi-value" id="kpi-prestador-destaque">--</span>
                    </div>
                </div>
            </div>

            <div class="dashboard-panel">
                <h4>Análise Detalhada</h4>
                <div class="filters-container">
                    <div class="filter">
                        <label for="prestadorFilter">Filtrar por Prestador:</label>
                        <select id="prestadorFilter" class="custom-select">
                            <option value="all">Todos os Prestadores</option>
                        </select>
                    </div>
                    <div class="filter">
                        <label for="profissaoFilter">Filtrar por Profissão:</label>
                        <select id="profissaoFilter" class="custom-select">
                            <option value="all">Todas as Profissões</option>
                        </select>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="feedbackChart"></canvas>
                </div>
            </div>
        </section>
    </main>
    <script src="script/session_handler.js" defer></script>
    <script src="script/estatistica.js" defer></script>
</body>
</html>