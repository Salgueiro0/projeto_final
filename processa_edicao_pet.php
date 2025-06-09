<?php
// Crie este novo arquivo: processa_edicao_pet.php
require_once 'config.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $id_pet = intval($_POST['id_pet']);
    $id_usuario = $_SESSION['id'];

    $nome = $_POST['nome'];
    $tipo_animal = $_POST['tipo_animal'];
    $raca = $_POST['raca'];
    $idade = intval($_POST['idade']);

    // Comando UPDATE para pets, com verificação de dono
    $sql = "UPDATE pets SET nome = ?, tipo_animal = ?, raca = ?, idade = ? WHERE id = ? AND id_usuario = ?";
    
    if ($stmt = $conexao->prepare($sql)) {
        $stmt->bind_param("sssiii", $nome, $tipo_animal, $raca, $idade, $id_pet, $id_usuario);
        
        if ($stmt->execute()) {
            echo "Pet atualizado com sucesso!";
            header("refresh:2;url=meus_pets.php");
        } else {
            echo "Erro ao atualizar o pet: " . $stmt->error;
        }
        $stmt->close();
    }
    $conexao->close();
}
?>