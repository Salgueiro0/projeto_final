<?php
require_once 'config.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $caminho_foto_pet = null;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
        $pasta_uploads = 'uploads/';
        $nome_arquivo = 'pet_' . uniqid() . '_' . basename($_FILES['foto']['name']);
        $caminho_completo = $pasta_uploads . $nome_arquivo;
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $caminho_completo)) {
            $caminho_foto_pet = $caminho_completo;
        }
    }

    $nome = $_POST['nome'];
    $tipo_animal = $_POST['tipo_animal'];
    $raca = $_POST['raca'];
    $idade = !empty($_POST['idade']) ? intval($_POST['idade']) : NULL;
    $id_loja = !empty($_POST['id_loja']) ? intval($_POST['id_loja']) : NULL;
    // Pega o ID do usuário da sessão para salvar como "dono" do pet
    $id_usuario = $_SESSION['id'];

    // SQL ATUALIZADO para incluir o id_usuario
    $sql = "INSERT INTO pets (nome, tipo_animal, raca, idade, id_loja, caminho_foto, id_usuario) VALUES (?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = $conexao->prepare($sql)) {
        // bind_param ATUALIZADO para incluir o id_usuario (sssiisi)
        $stmt->bind_param("sssiisi", $nome, $tipo_animal, $raca, $idade, $id_loja, $caminho_foto_pet, $id_usuario);
        
        if ($stmt->execute()) {
            echo "Pet cadastrado com sucesso! Redirecionando...";
            header("refresh:2;url=painel.php");
        } else {
            echo "Erro ao executar o cadastro no banco de dados: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Erro ao preparar a query: " . $conexao->error;
    }
    $conexao->close();

} else {
    header("location: cadastrar_pet.php");
    exit();
}
?>