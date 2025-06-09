<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro</title>
    <style>
        body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f0f2f5; }
        .register-container { padding: 2rem; background: #fff; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        h2 { text-align: center; }
        input { display: block; width: 100%; padding: 0.5rem; margin-bottom: 1rem; border: 1px solid #ccc; border-radius: 4px; }
        button { width: 100%; padding: 0.7rem; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #218838; }
        p { text-align: center; margin-top: 1rem; }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Cadastre-se</h2>
        <form action="processa_registro.php" method="post">
            <label for="nome">Nome:</label>
            <input type="text" name="nome" required>
            <label for="email">Email:</label>
            <input type="email" name="email" required>
            <label for="senha">Senha:</label>
            <input type="password" name="senha" required>
            <button type="submit">Cadastrar</button>
        </form>
        <p>Já tem uma conta? <a href="index.php">Faça login</a></p>
    </div>
</body>
</html>