<?php
// Inclui as classes do PHPMailer que serão usadas
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// --- CORREÇÃO: Inicia a sessão para limpar dados antigos ---
session_start();

// Garante que um usuário não possa solicitar um código
// e pular direto para a redefinição se tiver uma sessão antiga.
unset($_SESSION['reset_allowed']);
unset($_SESSION['reset_user_id']);
unset($_SESSION['reset_email']);
// --- FIM DA CORREÇÃO ---


// Inclui o ficheiro de configuração da base de dados
require_once '../config.php';

// --- CARREGAMENTO DO PHPMailer ---
require '../../vendor/autoload.php';

// --- Suas Configurações ---
$smtp_host = 'smtp.gmail.com';
$smtp_username = 'autonowetcc@gmail.com';
$smtp_password = 'eeiwegzmqakihdje'; // Sua senha de aplicativo
// ATENÇÃO: É mais seguro usar variáveis de ambiente para credenciais

// Verifica se o formulário foi enviado via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../esqueci_senha.html');
    exit;
}

$email = trim($_POST['email'] ?? '');

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    // Retorna para a página anterior com erro
    header('Location: ../../esqueci_senha.html?error=' . urlencode('Formato de e-mail inválido.'));
    exit;
}

try {
    // Busca o utilizador na base de dados (com o email)
    $stmt = $pdo->prepare("SELECT id, nome, email FROM usuario WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    $success_message = 'Se o seu e-mail estiver na nossa base de dados, receberá um código para redefinir a senha. Verifique sua caixa de entrada e spam.';

    if ($user) {
        // --- GERAÇÃO DO CÓDIGO ---
        $codigo = random_int(100000, 999999); // Gera um código de 6 dígitos
        $expires_at = date('Y-m-d H:i:s', strtotime('+15 minutes')); // Código expira em 15 minutos

        // Atualiza o usuário com o código e a data de expiração
        $stmt_update = $pdo->prepare("UPDATE usuario SET reset_token = ?, reset_token_expires_at = ? WHERE id = ?");
        $stmt_update->execute([$codigo, $expires_at, $user['id']]);

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

            // Remetente e Destinatário
            $mail->setFrom($smtp_username, 'Autonowe');
            $mail->addAddress($user['email'], $user['nome']);

            // Conteúdo do e-mail (agora com o código)
            $mail->isHTML(true);
            $mail->Subject = 'Codigo de Recuperacao de Senha - Autonowe';
            $mail->Body    = "
                <div style='font-family: sans-serif; padding: 20px; background-color: #f4f4f4;'>
                    <h2 style='color: #103352;'>Código de Recuperação</h2>
                    <p>Olá, {$user['nome']},</p>
                    <p>Você solicitou a recuperação da sua senha na plataforma Autonowe.</p>
                    <p>Utilize o código abaixo para criar uma nova senha:</p>
                    <p style='margin: 20px 0; font-size: 24px; font-weight: bold; color: #103352; text-align: center;'>
                        {$codigo}
                    </p>
                    <p>Este código é válido por 15 minutos.</p>
                    <p>Se não foi você quem solicitou, ignore este e-mail.</p>
                    <p>Atenciosamente,<br>Equipa Autonowe</p>
                </div>
            ";
            $mail->AltBody = "Seu código de recuperação Autonowe é: " . $codigo . ". Este código expira em 15 minutos.";

            $mail->send();
             // Mensagem de sucesso específica após enviar o e-mail
             $success_message = 'Código enviado para ' . htmlspecialchars($email) . '! Verifique sua caixa de entrada (e spam) e insira o código na <a href="../../inserir_codigo.html?email=' . urlencode($email) . '">página de verificação</a>.';


        } catch (Exception $e) {
            error_log("PHPMailer Error em request_reset.php: {$mail->ErrorInfo}");
            // Não informa o usuário sobre o erro de envio por segurança, mas mantém a mensagem genérica
        }
    } else {
         error_log("Tentativa de recuperação para email não cadastrado: " . $email);
         // Mantém a mensagem genérica para não revelar se o email existe ou não
    }

    // Redireciona de volta para a página 'esqueci_senha.html' com a mensagem de sucesso genérica
    // Adiciona o email na URL para a próxima página saber qual usuário validar
    header('Location: ../../esqueci_senha.html?success=' . urlencode($success_message) . '&email=' . urlencode($email));
    exit;

} catch (PDOException $e) {
    error_log("Erro de PDO em request_reset.php: " . $e->getMessage());
    header('Location: ../../esqueci_senha.html?error=' . urlencode('Ocorreu um erro no servidor. Por favor, tente novamente.'));
    exit;
}
?>