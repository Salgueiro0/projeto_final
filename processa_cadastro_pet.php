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
    $id_usuario = $_SESSION['id'];
    $cidade_id = !empty($_POST['cidade_id']) ? intval($_POST['cidade_id']) : NULL;

    // LÓGICA INTELIGENTE: Se um pet for associado a uma loja, sua localização é a da loja, não a manual.
    if ($id_loja !== NULL) {
        $cidade_id = NULL; // Limpa a cidade manual para não haver conflito de dados
    }

    $sql = "INSERT INTO pets (nome, tipo_animal, raca, idade, id_loja, caminho_foto, id_usuario, cidade_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    if ($stmt = $conexao->prepare($sql)) {
        $stmt->bind_param("sssiisii", $nome, $tipo_animal, $raca, $idade, $id_loja, $caminho_foto_pet, $id_usuario, $cidade_id);
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
}
?>