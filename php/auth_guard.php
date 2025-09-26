<?php
session_start();

// 1. FUNÇÃO DE GUARDA: Verifica se o usuário NÃO está logado.
// Se não estiver, redireciona para a página de login e encerra o script.
if (!isset($_SESSION['user_id'])) {
    // Corrigido para apontar para login.html, como ajustamos anteriormente
    header('Location: login.html?error=Você precisa estar logado para acessar esta página.');
    exit;
}

// 2. FUNÇÃO DE BUSCA DE DADOS: Se o script chegou até aqui, o usuário ESTÁ logado.
// Agora, vamos buscar o nome e o e-mail dele.
require_once 'config.php'; // Conexão com o banco de dados

$user_info = null; // Prepara a variável para receber os dados

try {
    $stmt = $pdo->prepare("SELECT nome, email FROM usuario WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user_info = $stmt->fetch(); // Armazena os dados na variável $user_info
} catch (PDOException $e) {
    error_log("Erro ao buscar dados do usuário: " . $e->getMessage());
    $user_info = null; // Se der erro, a variável fica nula
}
?>