<?php
require_once 'config.php';
// Redireciona se não estiver logado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: index.php");
    exit;
}
// Busca a lista de estados para preencher o primeiro dropdown
$estados = [];
$sql_estados = "SELECT id, uf, nome FROM estados ORDER BY nome ASC";
$resultado_estados = $conexao->query($sql_estados);
if ($resultado_estados) {
    while($linha = $resultado_estados->fetch_assoc()) {
        $estados[] = $linha;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Nova Loja</title>
    <style>body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; background-color: #f0f2f5; padding: 2rem 0; } .form-container { padding: 2rem; background: #fff; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); width: 450px; } h2 { text-align: center; } label { display: block; margin-bottom: 0.5rem; font-weight: bold; } input, textarea, select { display: block; width: 100%; padding: 0.7rem; margin-bottom: 1rem; border: 1px solid #ccc; border-radius: 4px; } button { width: 100%; padding: 0.8rem; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; } .loading-span { margin-left: 10px; color: #007bff; } p { text-align: center; margin-top: 1rem; }</style>
</head>
<body>
    <div class="form-container">
        <h2>Cadastrar Nova Loja</h2>
        <form action="processa_cadastro_loja.php" method="post" enctype="multipart/form-data">
            <label for="nome">Nome da Loja:</label>
            <input type="text" id="nome" name="nome" required>
            
            <label for="cep">CEP: <span id="cep-loading" class="loading-span"></span></label>
            <input type="text" id="cep" name="cep" placeholder="Digite o CEP e aguarde">

            <label for="estado">Estado:</label>
            <select id="estado" name="estado_id" required>
                <option value="">-- Selecione um Estado --</option>
                <?php foreach ($estados as $estado): ?>
                    <option value="<?php echo $estado['id']; ?>" data-uf="<?php echo $estado['uf']; ?>">
                        <?php echo htmlspecialchars($estado['nome']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="cidade">Cidade:</label>
            <select id="cidade" name="cidade_id" required>
                <option value="">-- Escolha um estado primeiro --</option>
            </select>
            
            <label for="localizacao">Endereço (Rua, Número, Bairro):</label>
            <input type="text" id="localizacao" name="localizacao" required>

            <label for="contato">Contato:</label>
            <input type="text" id="contato" name="contato">
            <label for="descricao">Descrição:</label>
            <textarea id="descricao" name="descricao"></textarea>
            <label for="foto">Foto da Loja:</label>
            <input type="file" id="foto" name="foto" accept="image/*">

            <button type="submit">Cadastrar Loja</button>
        </form>
        <p><a href="escolher_cadastro.php">Voltar</a></p>
    </div>

    <script src="cep_script.js"></script>
</body>
</html>