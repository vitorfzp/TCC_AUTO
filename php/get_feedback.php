<?php
require_once 'config.php';

try {
    // --- CONSULTA CORRIGIDA ---
    // Tivemos que adicionar a tabela 'prestadores' (p) para pegar o nome e a profissão
    $query = "
        SELECT
            p.nome as nome_prestador,    -- VEM DA TABELA 'prestadores'
            p.profissao,                 -- VEM DA TABELA 'prestadores'
            f.nota,
            f.comentario,
            DATE_FORMAT(f.data_feedback, '%d/%m/%Y %H:%i') as data_formatada,
            u.nome as nome_usuario
        FROM
            feedbacks f
        JOIN
            usuario u ON f.usuario_id = u.id
        JOIN
            prestadores p ON f.prestador_cpf = p.cpf -- LIGAÇÃO CORRETA
        ORDER BY
            f.data_feedback DESC
    ";
    // --- FIM DA CORREÇÃO ---

    $stmt = $pdo->query($query);
    $feedbacks = $stmt->fetchAll();

    header('Content-Type: application/json');
    echo json_encode($feedbacks);
} catch (PDOException $e) {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => 'Erro ao buscar os feedbacks: ' . $e->getMessage()]);
}
?>