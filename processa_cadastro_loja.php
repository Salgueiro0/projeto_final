<?php
require 'config.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: index.php");
    exit;
}

$caminho_foto = null;
if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
    $pasta_uploads = 'uploads/';
    $nome_arquivo = uniqid() . '_' . basename($_FILES['foto']['name']);
    $caminho_completo = $pasta_uploads . $nome_arquivo;
    if (move_uploaded_file($_FILES['foto']['tmp_name'], $caminho_completo)) {
        $caminho_foto = $caminho_completo;
    }
}

// Pega os dados do formulário
$nome = $_POST['nome'];
$localizacao = $_POST['localizacao'];
$cidade_id = $_POST['cidade_id']; // NOVO: agora pegamos o ID da cidade
$contato = $_POST['contato'];
$descricao = $_POST['descricao'];
$id_usuario = $_SESSION['id'];

// SQL atualizado para usar o 'cidade_id'
$sql = "INSERT INTO lojas (nome, localizacao, id_usuario, contato, descricao, caminho_foto, cidade_id) VALUES (?, ?, ?, ?, ?, ?, ?)";

if ($stmt = $conexao->prepare($sql)) {
    // bind_param atualizado: ssisssi
    $stmt->bind_param("ssisssi", $nome, $localizacao, $id_usuario, $contato, $descricao, $caminho_foto, $cidade_id);

    if ($stmt->execute()) {
        echo "Loja cadastrada com sucesso! Redirecionando...";
        header("refresh:2;url=painel.php");
    } else {
        echo "Erro ao cadastrar a loja no banco de dados: " . $stmt->error;
    }
    $stmt->close();
} else {
    echo "Erro ao preparar a query: " . $conexao->error;
}
$conexao->close();
?>