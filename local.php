<?php
// 1. Simplesmente inclua o novo auth_guard. Ele faz todo o trabalho!
require_once 'php/auth_guard.php';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Serviços - Autonowe</title>
    <link rel="icon" type="image/png" href="img/LOGO.png">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="style/custom.css">
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
            <a href="login.html" class="menu-item" title="Login"><svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M11 7L9.6 8.4l2.6 2.6H2v2h10.2l-2.6 2.6L11 17l5-5-5-5zm9 12h-8v-2h8v2zm0-4h-8v-2h8v2zm0-4h-8V9h8v2z"/></svg><span>Login / Cadastro</span></a>
        </nav>
    </aside>

    <main class="main-content">
        <section class="main-section">
            <h2>Nossos Serviços</h2>
            <p>Conheça as categorias de serviços disponíveis na nossa plataforma.</p>
            <div class="service-grid">

                <a href="servico_detalhe.php?servico=Limpeza Geral" class="service-card-link">
                    <div class="service-card">
                        <img src="https://content.paodeacucar.com/wp-content/uploads/2019/06/produtos-de-limpeza2.jpg" alt="Limpeza">
                        <div class="card-content">
                            <h3>Limpeza Geral</h3>
                            <p>Varrer, esfregar, aspirar, lavar e polir superfícies.</p>
                            <span class="card-badge">Autonowe Valida</span>
                        </div>
                    </div>
                </a>

                <a href="servico_detalhe.php?servico=Pedreiro" class="service-card-link">
                    <div class="service-card">
                        <img src="https://emplaco.com.br/wp-content/uploads/2021/12/Pedreiro-em-Nova-Lima-1024x769.jpg" alt="Pedreiro">
                        <div class="card-content">
                            <h3>Pedreiro</h3>
                            <p>Construção de estruturas de concreto armado e Casas, além de reformar.</p>
                            <span class="card-badge">Autonowe Valida</span>
                        </div>
                    </div>
                </a>

                <a href="servico_detalhe.php?servico=Jardineiro" class="service-card-link">
                    <div class="service-card">
                        <img src="https://conecta.fg.com.br/wp-content/uploads/2019/11/jardineiro.png" alt="Jardineiro">
                        <div class="card-content">
                            <h3>Jardineiro</h3>
                            <p>Instalar e manter jardim com o uso de ferramentas.</p>
                            <span class="card-badge">Autonowe Valida</span>
                        </div>
                    </div>
                </a>

                <a href="servico_detalhe.php?servico=Segurança" class="service-card-link">
                    <div class="service-card">
                        <img src="https://www.verzani.com.br/wp-content/uploads/2022/05/post_thumbnail-4448d68024acf904ff00c094aa5e0d5a.jpeg" alt="Segurança">
                        <div class="card-content">
                            <h3>Segurança</h3>
                            <p>Manter segurança no estabelecimento.</p>
                            <span class="card-badge">Autonowe Valida</span>
                        </div>
                    </div>
                </a>

                <a href="servico_detalhe.php?servico=Animador de Festa" class="service-card-link">
                    <div class="service-card">
                        <img src="https://cdn.fixando.com/u_pt/h/495_teaser.jpg?x=932edd486881bd31df27873c4c0a3659" alt="Animador">
                        <div class="card-content">
                            <h3>Animador de Festa</h3>
                            <p>Manter a festa animada e divertida.</p>
                            <span class="card-badge">Autonowe Valida</span>
                        </div>
                    </div>
                </a>

                <a href="servico_detalhe.php?servico=Barman" class="service-card-link">
                    <div class="service-card">
                        <img src="https://www.drinkpedia.net.br/wp-content/uploads/2023/11/barman-ou-bartender-03-edited.png" alt="Barman">
                        <div class="card-content">
                            <h3>Barman</h3>
                            <p>Criar uma atmosfera diferente em um drink especial e sofisticado.</p>
                            <span class="card-badge">Autonowe Valida</span>
                        </div>
                    </div>
                </a>

                <a href="servico_detalhe.php?servico=Cabeleireiro" class="service-card-link">
                    <div class="service-card">
                        <img src="https://assets.institutoembelleze.com/images/site-v04/pt-br/cursos/barbeiro-profissional-pleno-estilista-de-cabelo/carousel/01-mobile.jpg" alt="Cabeleireiro">
                        <div class="card-content">
                            <h3>Cabeleireiro</h3>
                            <p>Manter o cabelo com um estilo único e diferente.</p>
                            <span class="card-badge">Autonowe Valida</span>
                        </div>
                    </div>
                </a>

                <a href="servico_detalhe.php?servico=Transporte de aplicativo" class="service-card-link">
                    <div class="service-card">
                        <img src="https://img.odcdn.com.br/wp-content/uploads/2021/04/aplicativo-de-transporte.jpg" alt="Transporte">
                        <div class="card-content">
                            <h3>Transporte de aplicativo</h3>
                            <p>Transportar o cliente para o local seguro com a melhor qualidade.</p>
                            <span class="card-badge">Autonowe Valida</span>
                        </div>
                    </div>
                </a>

            </div>
        </section>

        <section class="main-section">
            <h2>Nossa Localização</h2>
            <p>Estamos localizados no coração de Mogi Mirim, prontos para atender você.</p>
            <div id="map" class="map-container"></div>
        </section>
    </main>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="script/map.js" defer></script>
    <script src="script/session_handler.js" defer></script>
</body>
</html>