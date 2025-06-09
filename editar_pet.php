<?php
require_once 'config.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: index.php");
    exit;
}

$id_pet = isset($_GET['id']) ? intval($_GET['id']) : 0;
$id_usuario = $_SESSION['id'];
$pet = null;

if($id_pet > 0) {
    $sql = "SELECT * FROM pets WHERE id = ? AND id_usuario = ?";
    if($stmt = $conexao->prepare($sql)) {
        $stmt->bind_param("ii", $id_pet, $id_usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();
        if($resultado->num_rows == 1) {
            $pet = $resultado->fetch_assoc();
        } else {
            header("location: meus_pets.php");
            exit;
        }
    }
} else {
    header("location: meus_pets.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Pet</title>
    <style>body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; background-color: #f0f2f5; padding: 2rem 0; } .form-container { padding: 2rem; background: #fff; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); width: 450px; } h2 { text-align: center; } label { display: block; margin-bottom: 0.5rem; font-weight: bold; } input, select { display: block; width: 100%; padding: 0.7rem; margin-bottom: 1rem; border: 1px solid #ccc; border-radius: 4px; } button { width: 100%; padding: 0.8rem; background-color: #ffc107; color: #212529; border: none; border-radius: 4px; cursor: pointer; }</style>
</head>
<body>
    <div class="form-container">
        <h2>Editar Pet</h2>
        <form action="processa_edicao_pet.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id_pet" value="<?php echo $pet['id']; ?>">
            <input type="hidden" name="foto_atual" value="<?php echo htmlspecialchars($pet['caminho_foto']); ?>">
            
            <label for="nome">Nome do Pet:</label>
            <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($pet['nome']); ?>" required>

            <label for="tipo_animal">Tipo de Animal:</label>
            <select id="tipo_animal" name="tipo_animal">
                <option value="Cachorro" <?php if($pet['tipo_animal'] == 'Cachorro') echo 'selected'; ?>>Cachorro</option>
                <option value="Gato" <?php if($pet['tipo_animal'] == 'Gato') echo 'selected'; ?>>Gato</option>
                <option value="Pássaro" <?php if($pet['tipo_animal'] == 'Pássaro') echo 'selected'; ?>>Pássaro</option>
                <option value="Roedor" <?php if($pet['tipo_animal'] == 'Roedor') echo 'selected'; ?>>Roedor</option>
                <option value="Outro" <?php if($pet['tipo_animal'] == 'Outro') echo 'selected'; ?>>Outro</option>
            </select>

            <label for="raca">Raça:</label>
            <input type="text" id="raca" name="raca" value="<?php echo htmlspecialchars($pet['raca']); ?>">
            
            <label for="idade">Idade (anos):</label>
            <input type="number" id="idade" name="idade" value="<?php echo $pet['idade']; ?>" min="0">
            
            <label for="foto">Alterar Foto:</label>
            <?php if (!empty($pet['caminho_foto']) && file_exists($pet['caminho_foto'])): ?>
                <p><img src="<?php echo htmlspecialchars($pet['caminho_foto']); ?>" width="100" alt="Foto atual"></p>
            <?php endif; ?>
            <input type="file" id="foto" name="foto" accept="image/*">

            <button type="submit">Salvar Alterações</button>
        </form>
        <p><a href="meus_pets.php">Cancelar</a></p>
    </div>
</body>
</html>