<?php
require_once __DIR__ . '/../config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Protege a página
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['user_type'] !== 'prestador') {
    // Se não for um prestador logado, expulsa para o login
    session_destroy();
    header('Location: login.html?error=' . urlencode('Acesso negado. Faça login como prestador.'));
    exit;
}

// Se chegou aqui, é um prestador. Pega o CPF da sessão para usar na página.
$prestador_cpf_logado = $_SESSION['user_cpf'];
?>