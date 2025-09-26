<?php
require_once 'config.php';

try {
    $query = "
        SELECT
            f.nome_prestador,
            f.profissao,
            f.nota,
            f.comentario,
            DATE_FORMAT(f.data_feedback, '%d/%m/%Y %H:%i') as data_formatada,
            u.nome as nome_usuario
        FROM
            feedbacks f
        JOIN
            usuario u ON f.usuario_id = u.id
        ORDER BY
            f.data_feedback DESC
    ";

    $stmt = $pdo->query($query);
    $feedbacks = $stmt->fetchAll();

    header('Content-Type: application/json');
    echo json_encode($feedbacks);
} catch (PDOException $e) {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => 'Erro ao buscar os feedbacks: ' . $e->getMessage()]);
}
?>