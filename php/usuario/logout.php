<?php
// Inicia a sessão para poder manipulá-la
session_start();

// Remove todas as variáveis da sessão
session_unset();

// Destrói a sessão
session_destroy();

// --- CORREÇÃO APLICADA AQUI ---
// Alterado de login.html para login.php para corresponder ao arquivo correto.
header("Location: ../../login.html?success=Logout realizado com sucesso!");
exit;
?>