<?php
require 'config.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: index.php");
    exit;
}

// Pega o ID do usuário logado para filtrar as lojas
$id_usuario_logado = $_SESSION['id'];

// Busca apenas as lojas que pertencem ao usuário logado
$lojas = [];
// A query SQL agora tem a cláusula WHERE para filtrar pelo dono
$sql_lojas = "SELECT id, nome FROM lojas WHERE id_usuario = ? ORDER BY nome";

if ($stmt = $conexao->prepare($sql_lojas)) {
    // Faz o bind do ID do usuário logado na query
    $stmt->bind_param("i", $id_usuario_logado);
    $stmt->execute();
    $resultado = $stmt->get_result();

    while ($linha = $resultado->fetch_assoc()) {
        $lojas[] = $linha;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Pet</title>
    <style>
        body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; background-color: #f0f2f5; }
        .form-container { padding: 2rem; background: #fff; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); width: 400px; }
        h2 { text-align: center; }
        input, select { display: block; width: 100%; padding: 0.5rem; margin-bottom: 1rem; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 0.7rem; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
        p { text-align: center; margin-top: 1rem; }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Cadastrar Novo Pet</h2>
        <form action="processa_cadastro_pet.php" method="post">
            <label for="nome">Nome do Pet:</label>
            <input type="text" name="nome" required>
            <label for="raca">Raça:</label>
            <input type="text" name="raca">
            <label for="id_loja">Ligar a uma de Suas Lojas (Opcional):</label>
            <select name="id_loja">
                <option value="">Nenhuma / Pet sem loja</option>
                <?php if (empty($lojas)): ?>
                    <option value="" disabled>Você não possui lojas cadastradas</option>
                <?php else: ?>
                    <?php foreach ($lojas as $loja): ?>
                        <option value="<?php echo $loja['id']; ?>">
                            <?php echo htmlspecialchars($loja['nome']); ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
            <button type="submit">Cadastrar Pet</button>
        </form>
        <p><a href="escolher_cadastro.php">Voltar</a></p>
    </div>
</body>
</html>