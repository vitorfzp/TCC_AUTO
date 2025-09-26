<?php
session_start();

// Define o cabeçalho como JSON para a resposta
header('Content-Type: application/json');

// Verifica se a sessão do usuário existe
if (isset($_SESSION['user_id']) && isset($_SESSION['user_name'])) {
    // Se estiver logado, retorna um JSON com o status e o nome do usuário
    echo json_encode([
        'loggedIn' => true,
        'userName' => $_SESSION['user_name']
    ]);
} else {
    // Se não estiver logado, retorna um JSON com o status falso
    echo json_encode(['loggedIn' => false]);
}
?>