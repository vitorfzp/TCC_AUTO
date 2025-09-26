<?php
// Inclui as classes do PHPMailer que serão usadas
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Inclui o ficheiro de configuração da base de dados
require_once '../config.php';

// --- CARREGAMENTO DO PHPMailer ---
require '../../vendor/autoload.php';

// --- Suas Configurações ---
$smtp_host = 'smtp.gmail.com';
$smtp_username = 'autonowetcc@gmail.com';
$smtp_password = 'eeiwegzmqakihdje';
$site_url = 'http://localhost/tcc';

// Verifica se o formulário foi enviado via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../esqueci_senha.html');
    exit;
}

$email = trim($_POST['email'] ?? '');

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ../../esqueci_senha.html?success=' . urlencode('Formato de e-mail inválido.'));
    exit;
}

try {
    // Busca o utilizador na base de dados (com o email)
    $stmt = $pdo->prepare("SELECT id, nome, email FROM usuario WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        $token = bin2hex(random_bytes(32));
        $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $stmt_update = $pdo->prepare("UPDATE usuario SET reset_token = ?, reset_token_expires_at = ? WHERE id = ?");
        $stmt_update->execute([$token, $expires_at, $user['id']]);

        $mail = new PHPMailer(true);

        try {
            // Configurações do Servidor SMTP
            $mail->isSMTP();
            $mail->Host       = $smtp_host;
            $mail->SMTPAuth   = true;
            $mail->Username   = $smtp_username;
            $mail->Password   = $smtp_password;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->CharSet    = 'UTF-8';
            
            // MODO DE DEPURAÇÃO DESATIVADO PARA PRODUÇÃO
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER;

            // Remetente e Destinatário
            $mail->setFrom($smtp_username, 'Autonowe');
            $mail->addAddress($user['email'], $user['nome']);

            // Conteúdo do e-mail
            $reset_link = $site_url . '/redefinir_senha.html?token=' . $token;
            $mail->isHTML(true);
            $mail->Subject = 'Redefinicao de Senha - Autonowe';
            $mail->Body    = "
                <div style='font-family: sans-serif; padding: 20px; background-color: #f4f4f4;'>
                    <h2 style='color: #103352;'>Redefinição de Senha</h2>
                    <p>Olá, {$user['nome']},</p>
                    <p>Recebemos um pedido para redefinir a sua senha na plataforma Autonowe. Se não foi você, por favor, ignore este e-mail.</p>
                    <p>Para criar uma nova senha, clique no botão abaixo:</p>
                    <p style='margin: 20px 0;'>
                        <a href='{$reset_link}' style='background-color: #3b82f6; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Redefinir a minha senha agora</a>
                    </p>
                    <p>Este link é válido por 1 hora.</p>
                    <p>Atenciosamente,<br>Equipa Autonowe</p>
                </div>
            ";
            $mail->AltBody = "Para redefinir sua senha, copie e cole o seguinte link no seu navegador: " . $reset_link;

            $mail->send();

        } catch (Exception $e) {
            // Se o envio falhar, regista o erro num log interno sem parar o script.
            error_log("PHPMailer Error: {$mail->ErrorInfo}");
        }
    }

    // Redireciona para a página de sucesso
    header('Location: ../../esqueci_senha.html?success=' . urlencode('Se o seu e-mail estiver na nossa base de dados, receberá um link para redefinir a senha.'));
    exit;

} catch (PDOException $e) {
    error_log("Erro em request_reset.php: " . $e->getMessage());
    header('Location: ../../esqueci_senha.html?success=' . urlencode('Ocorreu um erro no servidor. Por favor, tente novamente.'));
    exit;
}
?>