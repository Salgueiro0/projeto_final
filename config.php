<?php
// Definições do banco de dados
define('DB_SERVER', 'localhost:3310');
define('DB_USERNAME', 'root'); // Seu usuário do DB
define('DB_PASSWORD', 'GabrielSB0110'); // A senha fica vazia, entre aspas simples // Sua senha do DB
define('DB_NAME', 'meu_site'); // O nome do seu DB

// Tenta conectar ao banco de dados MySQL
$conexao = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Verifica a conexão
if ($conexao->connect_error) {
    die("Falha na conexão: " . $conexao->connect_error);
}

// Inicia a sessão em todas as páginas que incluírem este arquivo
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>