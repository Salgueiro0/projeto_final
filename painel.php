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
    <title>Painel Principal</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 2rem; background-color: #f4f4f4; }
        .painel-container { max-width: 800px; margin: auto; background: #fff; padding: 2rem; border-radius: 8px; }
        h1 { text-align: center; }
        .opcoes { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 2rem; }
        .opcoes a { text-decoration: none; color: white; padding: 1.5rem; border-radius: 8px; font-size: 1.2rem; text-align: center; }
        .pesquisar { background-color: #007bff; }
        .cadastrar { background-color: #28a745; }
        .gerenciar-lojas { background-color: #17a2b8; }
        .gerenciar-pets { background-color: #ffc107; color: #212529; }
        .opcoes a:hover { opacity: 0.9; }
        .logout { text-align: center; margin-top: 2rem; }
    </style>
</head>
<body>
    <div class="painel-container">
        <h1>Bem-vindo(a), <?php echo htmlspecialchars($_SESSION['nome']); ?>!</h1>
        <div class="opcoes">
            <a href="pesquisar.php" class="pesquisar">Pesquisar Loja/Pet</a>
            <a href="escolher_cadastro.php" class="cadastrar">Cadastrar Loja/Pet</a>
            <a href="minhas_lojas.php" class="gerenciar-lojas">Minhas Lojas</a>
            <a href="meus_pets.php" class="gerenciar-pets">Meus Pets</a>
        </div>
        <p class="logout"><a href="logout.php">Sair</a></p>
    </div>
</body>
</html>