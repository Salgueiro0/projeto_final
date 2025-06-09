<?php
require 'config.php';
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Escolha o Cadastro</title>
    <link rel="stylesheet" href="style.css"> <style>
        body { font-family: Arial, sans-serif; padding: 2rem; background-color: #f4f4f4; }
        .container { max-width: 800px; margin: auto; background: #fff; padding: 2rem; border-radius: 8px; }
        h1 { text-align: center; }
        .opcoes { display: flex; justify-content: space-around; margin-top: 2rem; }
        .opcoes a { text-decoration: none; color: white; background-color: #28a745; padding: 1.5rem; border-radius: 8px; font-size: 1.2rem; text-align: center; width: 40%; }
        .opcoes a:hover { background-color: #218838; }
        .voltar { text-align: center; margin-top: 2rem; }
    </style>
</head>
<body>
    <div class="container">
        <h1>O que você deseja cadastrar?</h1>
        <div class="opcoes">
            <a href="cadastrar_loja.php">Cadastrar Loja</a>
            <a href="cadastrar_pet.php">Cadastrar Pet</a>
        </div>
        <p class="voltar"><a href="painel.php">Voltar ao Painel</a></p>
    </div>
</body>
</html>