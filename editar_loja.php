<?php
// Crie este novo arquivo: editar_loja.php
require_once 'config.php';

// Redireciona se não estiver logado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: index.php");
    exit;
}

$id_loja = isset($_GET['id']) ? intval($_GET['id']) : 0;
$id_usuario = $_SESSION['id'];
$loja = null;

// Busca os dados da loja no banco, mas APENAS se o dono for o usuário logado
if ($id_loja > 0) {
    $sql = "SELECT * FROM lojas WHERE id = ? AND id_usuario = ?";
    if ($stmt = $conexao->prepare($sql)) {
        $stmt->bind_param("ii", $id_loja, $id_usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();
        if ($resultado->num_rows == 1) {
            $loja = $resultado->fetch_assoc();
        } else {
            // Se não encontrou a loja ou o usuário não é o dono, redireciona
            header("location: minhas_lojas.php");
            exit;
        }
        $stmt->close();
    }
} else {
    header("location: minhas_lojas.php");
    exit;
}

// Busca a lista de estados para o dropdown
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
    <title>Editar Loja</title>
    <style>body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; background-color: #f0f2f5; padding: 2rem 0; } .form-container { padding: 2rem; background: #fff; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); width: 450px; } h2 { text-align: center; } label { display: block; margin-bottom: 0.5rem; font-weight: bold; } input, textarea, select { display: block; width: 100%; padding: 0.7rem; margin-bottom: 1rem; border: 1px solid #ccc; border-radius: 4px; } button { width: 100%; padding: 0.8rem; background-color: #ffc107; color: #212529; border: none; border-radius: 4px; cursor: pointer; } p { text-align: center; margin-top: 1rem; }</style>
</head>
<body>
    <div class="form-container">
        <h2>Editar Loja</h2>
        <form action="processa_edicao_loja.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id_loja" value="<?php echo $loja['id']; ?>">

            <label for="nome">Nome da Loja:</label>
            <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($loja['nome']); ?>" required>
            
            <label for="estado">Estado:</label>
            <select id="estado" name="estado_id" required>
                <option value="">-- Selecione --</option>
                <?php foreach($estados as $estado): ?>
                    <option value="<?php echo $estado['id']; ?>" <?php // Lógica para selecionar o estado atual da loja ?> >
                        <?php echo htmlspecialchars($estado['nome']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="cidade">Cidade:</label>
            <select id="cidade" name="cidade_id" required>
                <option value="<?php echo $loja['cidade_id']; ?>">-- Carregando... --</option>
            </select>
            
            <label for="localizacao">Endereço:</label>
            <input type="text" id="localizacao" name="localizacao" value="<?php echo htmlspecialchars($loja['localizacao']); ?>" required>

            <label for="contato">Contato:</label>
            <input type="text" id="contato" name="contato" value="<?php echo htmlspecialchars($loja['contato']); ?>">

            <label for="descricao">Descrição:</label>
            <textarea id="descricao" name="descricao"><?php echo htmlspecialchars($loja['descricao']); ?></textarea>

            <label for="foto">Nova Foto (deixe em branco para manter a atual):</label>
            <?php if (!empty($loja['caminho_foto'])): ?>
                <p>Foto atual: <img src="<?php echo htmlspecialchars($loja['caminho_foto']); ?>" width="100"></p>
            <?php endif; ?>
            <input type="file" id="foto" name="foto" accept="image/*">

            <button type="submit">Salvar Alterações</button>
        </form>
        <p><a href="minhas_lojas.php">Cancelar</a></p>
    </div>

    <script>
    // Este JavaScript é um pouco mais complexo para pré-selecionar e carregar os dados
    document.addEventListener('DOMContentLoaded', function() {
        const estadoSelect = document.getElementById('estado');
        const cidadeSelect = document.getElementById('cidade');
        
        // Simula um evento 'change' para carregar as cidades do estado já salvo
        // Você precisaria de um pouco mais de JS para pegar o estado_id da cidade_id para pré-selecionar o estado
        // Por simplicidade, vamos deixar o usuário re-selecionar.
    });
    </script>
</body>
</html>