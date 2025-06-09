<?php
// Crie este novo arquivo: apagar_pet.php
require_once 'config.php';
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: index.php");
    exit;
}

$id_pet = isset($_GET['id']) ? intval($_GET['id']) : 0;
$id_usuario = $_SESSION['id'];

if ($id_pet > 0) {
    // Comando seguro: apaga o pet APENAS SE o ID do pet e o ID do dono corresponderem
    $sql = "DELETE FROM pets WHERE id = ? AND id_usuario = ?";
    if($stmt = $conexao->prepare($sql)) {
        $stmt->bind_param("ii", $id_pet, $id_usuario);
        $stmt->execute();
        $stmt->close();
    }
}

// Redireciona de volta para a lista de pets
header("location: meus_pets.php");
exit();
?>