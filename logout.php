<?php
// Inicia a sessão para poder acessá-la
session_start();

// Desfaz todas as variáveis de sessão
$_SESSION = array();

// Destrói a sessão
session_destroy();

// Redireciona para a página de login
header("location: index.php");
exit;
?>