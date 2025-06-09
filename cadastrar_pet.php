<?php
require_once 'config.php';
// ... (toda a lógica PHP no topo continua a mesma) ...
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: index.php");
    exit;
}
$id_usuario_logado = $_SESSION['id'];
$lojas = [];
$sql_lojas = "SELECT id, nome FROM lojas WHERE id_usuario = ? ORDER BY nome";
if ($stmt_lojas = $conexao->prepare($sql_lojas)) {
    $stmt_lojas->bind_param("i", $id_usuario_logado);
    $stmt_lojas->execute();
    $resultado_lojas = $stmt_lojas->get_result();
    while ($linha = $resultado_lojas->fetch_assoc()) {
        $lojas[] = $linha;
    }
    $stmt_lojas->close();
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

            <label for="contato_pet">Contato (Telefone ou E-mail):</label>
            <input type="text" id="contato_pet" name="contato_pet">

            <label for="foto">Foto do Pet:</label>
            <input type="file" id="foto" name="foto" accept="image/png, image/jpeg">

            <label for="id_loja">Ligar a uma de Suas Lojas (Opcional):</label>
            <select id="id_loja" name="id_loja">
                <option value="">Nenhuma loja</option>
                <?php foreach ($lojas as $loja): ?>
                    <option value="<?php echo $loja['id']; ?>"><?php echo htmlspecialchars($loja['nome']); ?></option>
                <?php endforeach; ?>
            </select>

            <div id="localizacao_pet_div">
                <label for="cep_pet">CEP (para pets sem loja):</label>
                <input type="text" id="cep_pet" name="cep_pet" placeholder="Digite o CEP">
            </div>

            <button type="submit">Cadastrar Pet</button>
        </form>
        <p><a href="escolher_cadastro.php">Voltar</a></p>
    </div>

    <script>
    // Script para mostrar/esconder o campo de CEP
    document.getElementById('id_loja').addEventListener('change', function() {
        document.getElementById('localizacao_pet_div').style.display = (this.value === "") ? 'block' : 'none';
    });
    </script>
</body>
</html>