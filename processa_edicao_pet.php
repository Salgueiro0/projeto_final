<?php
require_once 'config.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $id_pet = intval($_POST['id_pet']);
    $id_usuario = $_SESSION['id'];
    $caminho_foto_atual = $_POST['foto_atual'];
    $novo_caminho_foto = $caminho_foto_atual; // Por padrão, mantém a foto antiga

    // Lógica para processar uma nova foto, se foi enviada
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
        
        // Apaga a foto antiga do servidor para não ocupar espaço
        if (!empty($caminho_foto_atual) && file_exists($caminho_foto_atual)) {
            unlink($caminho_foto_atual);
        }

        $pasta_uploads = 'uploads/';
        $nome_arquivo = 'pet_' . uniqid() . '_' . basename($_FILES['foto']['name']);
        $caminho_completo = $pasta_uploads . $nome_arquivo;
        
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $caminho_completo)) {
            $novo_caminho_foto = $caminho_completo; // Define o caminho da nova foto
        }
    }

    // Pega os outros dados do formulário
    $nome = $_POST['nome'];
    $tipo_animal = $_POST['tipo_animal'];
    $raca = $_POST['raca'];
    $idade = !empty($_POST['idade']) ? intval($_POST['idade']) : null;

    // Comando UPDATE para pets, com verificação de dono e atualização da foto
    $sql = "UPDATE pets SET nome = ?, tipo_animal = ?, raca = ?, idade = ?, caminho_foto = ? WHERE id = ? AND id_usuario = ?";
    
    if ($stmt = $conexao->prepare($sql)) {
        // LINHA CORRIGIDA: A string de tipos agora é "sssisii"
        $stmt->bind_param("sssisii", $nome, $tipo_animal, $raca, $idade, $novo_caminho_foto, $id_pet, $id_usuario);
        
        if ($stmt->execute()) {
            echo "Pet atualizado com sucesso!";
            header("refresh:2;url=meus_pets.php");
        } else {
            echo "Erro ao atualizar o pet: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Erro ao preparar a query: " . $conexao->error;
    }
    $conexao->close();
}
?>