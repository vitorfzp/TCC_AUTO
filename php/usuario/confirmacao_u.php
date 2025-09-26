<?php
session_start();
$dados = $_SESSION['dados_enviados'] ?? null;

if (!$dados) {
    header("Location: feedback.html");
    exit;
}

unset($_SESSION['dados_enviados']);

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Autonowe</title>
  <link  rel="icon" type="image/png" href="img/logoc.png">
  <link rel="stylesheet" href="style/feed.css">
  <script src="script/feed.js" defer></script>
</head>
<body>
    <header>
        <nav class="navbar">
            <a class="logo" href="index.html"><img src="img/Logo.jpeg" alt="Logo"></a>
            <h1 id="h1">Autonowe</h1>
            <ul class="menu-links">
                <li><a href="index.html"><strong>HOME</strong></a></li>
                <li><a href="local.html"><strong>SERVIÇOS</strong></a></li>
                <li><a href="resultado.html"><strong>RESULTADOS</strong></a></li>
                <li><a href="estaticas.html"><strong>ESTATISTICAS</strong></a></li>
            </ul>
      </header>
   
  </header>
  <main>
      <section class="feedback-form">
          <h2><strong>Deixe seu Feedback</strong></h2>
          <form id="formFeedback">
              <label for="nome"><strong>Nome:</strong></label>
              <input type="text" id="nome" name="nome" required>
              <label for="nomed"><strong>Nome do Prestador:</strong></label>
              <input type="text" id="nomed" name="nomed" required>
              <label for="serv"><strong>Profissão:</strong></label>
              <select id="serv" name="serv">
                  <option value="Limpeza Geral">Limpeza Geral</option>
                  <option value="Pedreiro">Pedreiro</option>
                  <option value="Jardineiro">Jardineiro</option>
                  <option value="Segurança">Segurança</option>
                  <option value="Animnador de Festa">Animador de Festa</option>
                  <option value="BarMan">Barman</option>
                  <option value="Cabeleireiro">Cabeleireiro</option>
                  <option value="Transporte de Aplicativo">Transporte de Aplicativo</option>
              </select>

              <label for="tipo"><strong>Tipo:</strong></label>
              <select id="tipo" name="tipo">
                  <option value="serviços">Serviço</option>
                  <option value="entretenimento">Entretenimento</option>
                  <option value="outro">Outro</option>
              </select>

              <label for="nota"><strong>Nota:</strong></label>
              <select id="nota" name="nota">
                  <option value="5">⭐⭐⭐⭐⭐ (5)</option>
                  <option value="4">⭐⭐⭐⭐ (4)</option>
                  <option value="3">⭐⭐⭐ (3)</option>
                  <option value="2">⭐⭐ (2)</option>
                  <option value="1">⭐ (1)</option>
              </select>

              <label for="comentario"><strong>Comentário:</strong></label>
              <textarea id="comentario" name="comentario" required></textarea>

              <button type="submit"><strong>Enviar Feedback</strong></button>
          </form>
      </section>
  </main>
      <a href="feedback.html">Novo Cadastro</a>
      
    </div>
  </div>
</body>
</html>