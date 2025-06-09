<?php
// Crie este novo arquivo: editar_pet.php
require_once 'config.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: index.php");
    exit;
}

$id_pet = isset($_GET['id']) ? intval($_GET['id']) : 0;
$id_usuario = $_SESSION['id'];
$pet = null;

// Busca os dados do pet, garantindo que o usuário logado é o dono
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
    <style>body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; background-color: #f0f2f5; padding: 2rem 0; } .form-container { padding: 2rem; background: #fff; border-radius: 8px; width: 450px; } h2 { text-align: center; } label { display: block; margin-bottom: 0.5rem; } input, select { width: 100%; padding: 0.7rem; margin-bottom: 1rem; border: 1px solid #ccc; border-radius: 4px; } button { width: 100%; padding: 0.8rem; background-color: #ffc107; color: #212529; border: none; border-radius: 4px; cursor: pointer; }</style>
</head>
<body>
    <div class="form-container">
        <h2>Editar Pet</h2>
        <form action="processa_edicao_pet.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id_pet" value="<?php echo $pet['id']; ?>">
            
            <label for="nome">Nome do Pet:</label>
            <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($pet['nome']); ?>" required>

            <label for="tipo_animal">Tipo:</label>
            <input type="text" id="tipo_animal" name="tipo_animal" value="<?php echo htmlspecialchars($pet['tipo_animal']); ?>">

            <label for="raca">Raça:</label>
            <input type="text" id="raca" name="raca" value="<?php echo htmlspecialchars($pet['raca']); ?>">
            
            <label for="idade">Idade:</label>
            <input type="number" id="idade" name="idade" value="<?php echo $pet['idade']; ?>">

            <button type="submit">Salvar Alterações</button>
        </form>
        <p><a href="meus_pets.php">Cancelar</a></p>
    </div>
</body>
</html>