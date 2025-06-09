<?php
require 'config.php';
// Redireciona se não estiver logado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Nova Loja</title>
    <style>
        body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; background-color: #f0f2f5; padding: 2rem 0; }
        .form-container { padding: 2rem; background: #fff; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); width: 450px; }
        h2 { text-align: center; color: #333; }
        label { display: block; margin-bottom: 0.5rem; font-weight: bold; }
        input[type="text"], input[type="file"], textarea {
            display: block;
            width: 100%;
            padding: 0.7rem;
            margin-bottom: 1rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        textarea { resize: vertical; min-height: 80px; }
        button { width: 100%; padding: 0.8rem; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 1rem; }
        button:hover { background-color: #0056b3; }
        p { text-align: center; margin-top: 1rem; }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Cadastrar Nova Loja</h2>
        <form action="processa_cadastro_loja.php" method="post" enctype="multipart/form-data">
            <label for="nome">Nome da Loja:</label>
            <input type="text" id="nome" name="nome" required>

            <label for="cidade">Cidade:</label>
            <input type="text" id="cidade" name="cidade" required>

            <label for="estado">Estado (UF):</label>
            <input type="text" id="estado" name="estado" maxlength="2" required placeholder="Ex: SP, RJ, DF">
            
            <label for="localizacao">Endereço (Rua, Número, Bairro):</label>
            <input type="text" id="localizacao" name="localizacao" required>

            <label for="contato">Contato (Telefone ou E-mail):</label>
            <input type="text" id="contato" name="contato">

            <label for="descricao">Descrição:</label>
            <textarea id="descricao" name="descricao"></textarea>

            <label for="foto">Foto da Loja:</label>
            <input type="file" id="foto" name="foto" accept="image/png, image/jpeg">

            <button type="submit">Cadastrar Loja</button>
        </form>
        <p><a href="escolher_cadastro.php">Voltar</a></p>
    </div>
</body>
</html>