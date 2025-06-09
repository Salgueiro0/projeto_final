<?php
require_once 'config.php';
// Redireciona se não estiver logado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: index.php");
    exit;
}
// Busca as lojas do usuário para o dropdown
$id_usuario_logado = $_SESSION['id'];
$lojas = [];
$sql_lojas = "SELECT id, nome FROM lojas WHERE id_usuario = ? ORDER BY nome";
if ($stmt_lojas = $conexao->prepare($sql_lojas)) {
    $stmt_lojas->bind_param("i", $id_usuario_logado);
    $stmt_lojas->execute();
    $resultado = $stmt_lojas->get_result();
    while ($linha = $resultado->fetch_assoc()) {
        $lojas[] = $linha;
    }
    $stmt_lojas->close();
}
// Busca os estados para o novo dropdown de localização do pet
$estados = [];
$sql_estados = "SELECT id, nome FROM estados ORDER BY nome ASC";
$res_estados = $conexao->query($sql_estados);
if ($res_estados) {
    while ($row = $res_estados->fetch_assoc()) {
        $estados[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Novo Pet</title>
    <style>
        body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; background-color: #f0f2f5; padding: 2rem 0; }
        .form-container { padding: 2rem; background: #fff; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); width: 450px; }
        h2 { text-align: center; }
        label { display: block; margin-bottom: 0.5rem; font-weight: bold; }
        input, select { display: block; width: 100%; padding: 0.7rem; margin-bottom: 1rem; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 0.8rem; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; }
        p { text-align: center; margin-top: 1rem; }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Cadastrar Novo Pet</h2>
        <form action="processa_cadastro_pet.php" method="post" enctype="multipart/form-data">
            <label for="nome">Nome do Pet:</label>
            <input type="text" id="nome" name="nome" required>
            <label for="tipo_animal">Tipo de Animal:</label>
            <select id="tipo_animal" name="tipo_animal">
                <option value="">-- Selecione --</option>
                <option value="Cachorro">Cachorro</option>
                <option value="Gato">Gato</option>
                <option value="Pássaro">Pássaro</option>
                <option value="Roedor">Roedor</option>
                <option value="Outro">Outro</option>
            </select>
            <label for="raca">Raça:</label>
            <input type="text" id="raca" name="raca">
            <label for="idade">Idade (anos):</label>
            <input type="number" id="idade" name="idade" min="0">
            <label for="foto">Foto do Pet:</label>
            <input type="file" id="foto" name="foto" accept="image/png, image/jpeg">
            <label for="id_loja">Ligar a uma de Suas Lojas (Opcional):</label>
            <select id="id_loja" name="id_loja">
                <option value="">Nenhuma / Definir localização manual</option>
                <?php foreach ($lojas as $loja): ?>
                    <option value="<?php echo $loja['id']; ?>"><?php echo htmlspecialchars($loja['nome']); ?></option>
                <?php endforeach; ?>
            </select>
            <div id="localizacao_pet_div">
                <label for="estado">Estado do Pet:</label>
                <select id="estado" name="estado_id">
                    <option value="">-- Selecione um Estado --</option>
                    <?php foreach ($estados as $estado): ?>
                        <option value="<?php echo $estado['id']; ?>"><?php echo htmlspecialchars($estado['nome']); ?></option>
                    <?php endforeach; ?>
                </select>
                <label for="cidade">Cidade do Pet:</label>
                <select id="cidade" name="cidade_id" disabled>
                    <option value="">-- Escolha um estado primeiro --</option>
                </select>
            </div>
            <button type="submit">Cadastrar Pet</button>
        </form>
        <p><a href="escolher_cadastro.php">Voltar</a></p>
    </div>
    <script>
    document.getElementById('estado').addEventListener('change', function() {
        const estadoId = this.value;
        const cidadeSelect = document.getElementById('cidade');
        cidadeSelect.innerHTML = '<option value="">Carregando...</option>';
        cidadeSelect.disabled = true;
        if (estadoId) {
            fetch('buscar_cidades.php?estado_id=' + estadoId)
                .then(response => response.json())
                .then(cidades => {
                    cidadeSelect.innerHTML = '<option value="">-- Selecione uma Cidade --</option>';
                    cidades.forEach(cidade => {
                        const option = document.createElement('option');
                        option.value = cidade.id;
                        option.textContent = cidade.nome;
                        cidadeSelect.appendChild(option);
                    });
                    cidadeSelect.disabled = false;
                });
        } else {
            cidadeSelect.innerHTML = '<option value="">-- Escolha um estado primeiro --</option>';
        }
    });
    document.getElementById('id_loja').addEventListener('change', function() {
        const localizacaoDiv = document.getElementById('localizacao_pet_div');
        if (this.value === "") {
            localizacaoDiv.style.display = 'block';
        } else {
            localizacaoDiv.style.display = 'none';
        }
    });
    </script>
</body>
</html>