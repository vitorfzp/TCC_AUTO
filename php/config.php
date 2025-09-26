<?php
// Detalhes da conexão com o banco de dados
$host = 'localhost';
$db   = 'cadastro_site';
$user = 'root';
$pass = 'aluno123';
$charset = 'utf8mb4';

// DSN (Data Source Name)
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// Opções do PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     error_log("Erro de conexão com o banco de dados: " . $e->getMessage());
     die("Ocorreu um erro ao conectar ao banco de dados.");
}
?>