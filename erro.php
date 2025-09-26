<?php
// router.php - Nosso roteador para o servidor de desenvolvimento do PHP

// Pega o caminho do arquivo que o navegador está pedindo
$requested_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Constrói o caminho completo no sistema de arquivos
$file_path = __DIR__ . $requested_path;

// Verifica se o arquivo solicitado é um arquivo real E existe
if (is_file($file_path)) {
    // Se for um arquivo que existe, deixa o servidor entregá-lo normalmente
    return false; 
} else {
    // Se o arquivo NÃO existe, nós assumimos o controle
    // e incluímos nossa página de erro personalizada.
    http_response_code(404); // Define o código de status para 404
    require __DIR__ . '/erro.php'; // Carrega a página de erro
    exit; // Encerra o script
}
?>