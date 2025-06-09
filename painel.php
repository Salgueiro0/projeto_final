<?php
require 'config.php';

// Verifica se o usuário está logado, senão redireciona para a página de login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel Principal</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 2rem; background-color: #f4f4f4; }
        .painel-container { max-width: 800px; margin: auto; background: #fff; padding: 2rem; border-radius: 8px; }
        h1 { text-align: center; }
        .opcoes { display: flex; justify-content: space-around; margin-top: 2rem; }
        .opcoes a { text-decoration: none; color: white; background-color: #007bff; padding: 1.5rem; border-radius: 8px; font-size: 1.2rem; text-align: center; width: 40%; }
        .opcoes a:hover { background-color: #0056b3; }
        .logout { text-align: center; margin-top: 2rem; }
    </style>
</head>
<body>
    <div class="painel-container">
        <h1>Bem-vindo(a), <?php echo htmlspecialchars($_SESSION['nome']); ?>!</h1>
        <div class="opcoes">
            <a href="pesquisar.php">Pesquisar Loja/Pet</a>
            <a href="escolher_cadastro.php">Cadastrar Loja/Pet</a>
        </div>
        <p class="logout"><a href="logout.php">Sair</a></p>
    </div>
</body>
</html>