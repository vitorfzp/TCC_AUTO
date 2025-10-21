<?php
// --- INÍCIO DO BLOCO PHP CORRIGIDO ---
session_start();
require_once 'php/config.php'; 
$user_info = null;
$user_type = $_SESSION['user_type'] ?? null; // Pega o tipo de usuário da sessão

// Verifica se o utilizador está logado ANTES de buscar os dados
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    try {
        if ($user_type === 'cliente' && isset($_SESSION['user_id'])) {
            // É um CLIENTE, busca na tabela 'usuario'
            $stmt = $pdo->prepare("SELECT nome, email FROM usuario WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user_info = $stmt->fetch();
        } elseif ($user_type === 'prestador' && isset($_SESSION['user_cpf'])) {
            // É um PRESTADOR, busca na tabela 'prestadores'
            $stmt = $pdo->prepare("SELECT nome, email FROM prestadores WHERE cpf = ?");
            $stmt->execute([$_SESSION['user_cpf']]);
            $user_info = $stmt->fetch();
        }
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
     
      <?php if ($user_info && $user_type === 'prestador'): ?>
        <a href="minha_conta_prestador.php" class="menu-item" title="Meu Painel"><i class="fas fa-tachometer-alt"></i><span>Meu Painel</span></a>
        <a href="php/usuario/logout.php" class="menu-item" title="Sair"><i class="fas fa-sign-out-alt"></i><span>Sair</span></a>
      
      <?php elseif ($user_info && $user_type === 'cliente'): ?>
        <a href="#" class="menu-item" title="Minha Conta"><i class="fas fa-user-circle"></i><span>Minha Conta</span></a>
        <a href="php/usuario/logout.php" class="menu-item" title="Sair"><i class="fas fa-sign-out-alt"></i><span>Sair</span></a>
      
      <?php else: ?>
        <a href="login.html" class="menu-item" title="Login"><svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M11 7L9.6 8.4l2.6 2.6H2v2h10.2l-2.6 2.6L11 17l5-5-5-5zm9 12h-8v-2h8v2zm0-4h-8v-2h8v2zm0-4h-8V9h8v2z"/></svg><span>Login / Cadastro</span></a>
      <?php endif; ?>
      </nav>
  </aside>

  <main class="main-content" id="index-main">
    
    <?php if ($user_info): ?>
    <div class="user-info-display">
        Logado como <?php echo ($user_type === 'prestador') ? 'Prestador' : 'Cliente'; ?>: 
        <strong><?php echo htmlspecialchars($user_info['nome']); ?></strong>
        <span>(<?php echo htmlspecialchars($user_info['email']); ?>)</span>
    </div>
    <?php endif; ?>

    <section class="new-hero">
        ```

---

### Erro 2: Mesmo E-mail para Usuário e Prestador

**O Problema:**
Você está certo, isso é um grande problema. Se alguém se cadastra com `user@email.com` como **cliente** e depois com o *mesmo* `user@email.com` como **prestador**:

1.  O script de login `login_action.php` (que fizemos na etapa anterior) verifica a tabela `usuario` primeiro.
2.  Ele encontra o `cliente` e faz o login.
3.  A pessoa **nunca** conseguirá acessar seu painel de prestador, pois o login sempre a identificará como cliente.

**A Solução:**
Devemos proibir que o mesmo e-mail seja usado em ambas as tabelas. Faremos uma verificação cruzada nos dois scripts de cadastro.

#### 1. `php/processar.php` (Cadastro de Prestador)
Vamos impedir que um novo prestador se cadastre se o e-mail dele já existir na tabela `usuario`.

```php
<?php
require_once 'config.php';
session_start();

$nome      = trim($_POST['nome'] ?? '');
$cpf       = trim($_POST['cpf'] ?? '');
$email     = trim($_POST['email'] ?? '');
$senha     = trim($_POST['senha'] ?? ''); 
$confirma  = trim($_POST['confirma_senha'] ?? ''); 
$telefone  = trim($_POST['telefone'] ?? '');
$profissao = trim($_POST['profissao'] ?? '');
$mensagem  = trim($_POST['mensagem'] ?? '');
$arquivo_nome = null;

// Validação (agora inclui senha)
if (empty($nome) || empty($cpf) || empty($email) || empty($telefone) || empty($profissao) || empty($senha)) {
     header('Location: ../cadastro.php?error=' . urlencode('Erro: Todos os campos são obrigatórios.'));
     exit;
}
if (strlen($senha) < 6) {
    header('Location: ../cadastro.php?error=' . urlencode('Erro: A senha deve ter no mínimo 6 caracteres.'));
    exit;
}
if ($senha !== $confirma) {
    header('Location: ../cadastro.php?error=' . urlencode('Erro: As senhas não coincidem.'));
    exit;
}

// Hash da senha (IMPORTANTE)
$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

// Lógica de Upload (sem alterações)
if (!empty($_FILES['arquivo']['name']) && $_FILES['arquivo']['error'] == UPLOAD_ERR_OK) {
    // ... (seu código de upload aqui) ...
}

try {
    // --- NOVA VERIFICAÇÃO CRUZADA ---
    // Verifica se o e-mail já existe na tabela de USUÁRIOS (clientes)
    $stmt_check_user = $pdo->prepare("SELECT id FROM usuario WHERE email = ?");
    $stmt_check_user->execute([$email]);
    if ($stmt_check_user->fetch()) {
        header('Location: ../cadastro.php?error=' . urlencode('Erro: Este e-mail já está em uso por uma conta de cliente. Use um e-mail diferente.'));
        exit;
    }
    // --- FIM DA VERIFICAÇÃO ---

    // Query atualizada para incluir a senha hasheada
    $stmt = $pdo->prepare("INSERT INTO prestadores (cpf, nome, email, senha, telefone, profissao, arquivo, mensagem) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$cpf, $nome, $email, $senha_hash, $telefone, $profissao, $arquivo_nome, $mensagem]);

} catch (PDOException $e) {
     if ($e->getCode() == 23000) {
          // Erro de duplicidade (CPF ou Email) na própria tabela 'prestadores'
          header('Location: ../cadastro.php?error=' . urlencode('Erro: Este CPF ou E-mail já está cadastrado como prestador.'));
     } else {
          error_log("Erro no cadastro: " . $e->getMessage());
          header('Location: ../cadastro.php?error=' . urlencode('Ocorreu um erro ao processar seu cadastro.'));
     }
     exit;
}

// Redireciona de volta para a página de cadastro com uma flag de sucesso
header("Location: ../cadastro.php?success=true");
exit;
?>