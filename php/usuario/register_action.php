<?php
// --- Inclusão do PHPMailer ---
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once '../config.php'; 
session_start();

// Carrega o Autoload do Composer para usar o PHPMailer
require '../../vendor/autoload.php'; //

// --- Configurações de E-mail (as mesmas do seu 'request_reset.php') ---
$smtp_host = 'smtp.gmail.com';
$smtp_username = 'autonowetcc@gmail.com';
$smtp_password = 'eeiwegzmqakihdje'; // Sua senha de aplicativo
// ---------------------------------

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../register.html?error=Método inválido');
    exit;
}

// Coleta e limpa os dados
$nome = trim($_POST['nome'] ?? '');
$cpf = trim($_POST['cpf'] ?? '');
$email = trim($_POST['email'] ?? '');
$senha = trim($_POST['senha'] ?? '');
$confirma_senha = trim($_POST['confirma_senha'] ?? '');

// Validação
if (empty($nome) || empty($cpf) || empty($email) || empty($senha)) {
    header('Location: ../../register.html?error=' . urlencode('Todos os campos são obrigatórios.'));
    exit;
}
if ($senha !== $confirma_senha) {
    header('Location: ../../register.html?error=' . urlencode('As senhas não coincidem.'));
    exit;
}
if (strlen($senha) < 6) {
    header('Location: ../../register.html?error=' . urlencode('A senha deve ter no mínimo 6 caracteres.'));
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ../../register.html?error=' . urlencode('Formato de e-mail inválido.'));
    exit;
}

// Hash da senha
$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

try {
    // --- VERIFICAÇÃO CRUZADA (ERRO 2) ---
    // 1. Verifica se o e-mail já existe na tabela de PRESTADORES
    $stmt_check_prestador = $pdo->prepare("SELECT cpf FROM prestadores WHERE email = ?");
    $stmt_check_prestador->execute([$email]);
    if ($stmt_check_prestador->fetch()) {
        header('Location: ../../register.html?error=' . urlencode('Erro: Este e-mail já está em uso por uma conta de prestador. Use um e-mail diferente.'));
        exit;
    }
    // --- FIM DA VERIFICAÇÃO ---

    // 2. Verifica duplicidade na própria tabela 'usuario'
    $stmt_check_user = $pdo->prepare("SELECT id FROM usuario WHERE cpf = ? OR email = ?");
    $stmt_check_user->execute([$cpf, $email]);
    if ($stmt_check_user->fetch()) {
        header('Location: ../../register.html?error=' . urlencode("Este CPF ou E-mail já está cadastrado."));
        exit;
    }

    // 3. Insere na tabela 'usuario'
    $stmt_insert = $pdo->prepare("INSERT INTO usuario (nome, cpf, email, senha) VALUES (?, ?, ?, ?)");
    $stmt_insert->execute([$nome, $cpf, $email, $senha_hash]);

    // --- LÓGICA DE E-MAIL DE BOAS-VINDAS ---
    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = $smtp_host;
        $mail->SMTPAuth   = true;
        $mail->Username   = $smtp_username;
        $mail->Password   = $smtp_password;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom($smtp_username, 'Autonowe');
        $mail->addAddress($email, $nome);

        $mail->isHTML(true);
        $mail->Subject = 'Bem-vindo(a) a Autonowe!';
        $mail->Body    = "
            <div style='font-family: sans-serif; padding: 20px; background-color: #f4f4f4;'>
                <h2 style='color: #103352;'>Cadastro Realizado com Sucesso!</h2>
                <p>Olá, <strong>" . htmlspecialchars(strtok($nome, ' ')) . ")</strong>,</p>
                <p>Seja muito bem-vindo(a) à Autonowe! Estamos felizes por ter você em nossa comunidade.</p>
                <p>Agora você pode encontrar os melhores profissionais ou, se for um prestador, gerenciar seu perfil e suas avaliações.</p>
                <p>Faça seu login para começar.</p>
                <p style='margin-top: 25px;'>
                    <a href='http://localhost/TCC_AUTO/login.html' style='background-color: #103352; color: white; padding: 12px 20px; text-decoration: none; border-radius: 8px;'>
                        Ir para o Login
                    </a>
                </p>
                <br>
                <p>Atenciosamente,<br>Equipe Autonowe</p>
            </div>
        ";
        $mail->AltBody = "Olá, " . htmlspecialchars($nome) . ".\n\nSeja bem-vindo(a) à Autonowe! Seu cadastro foi realizado com sucesso. Faça seu login em nosso site.";

        $mail->send();
    } catch (Exception $e) {
        // Se o e-mail falhar, não impede o cadastro. Apenas registra o erro.
        error_log("PHPMailer Error em register_action.php: {$mail->ErrorInfo}");
    }
    // --- FIM DA LÓGICA DE E-MAIL ---


    // Redireciona para o login com mensagem de sucesso
    header('Location: ../../login.html?success=' . urlencode('Cadastro realizado com sucesso! Verifique seu e-mail de boas-vindas e faça seu login.'));
    exit;

} catch (PDOException $e) {
    error_log("Erro no registro (PDO): " . $e->getMessage());
    header('Location: ../../register.html?error=' . urlencode('Ocorreu um erro ao processar seu cadastro. Tente novamente.'));
    exit;
}
?>