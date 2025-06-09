<?php
require 'config.php';
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: index.php");
    exit;
}

$nome = $_POST['nome'];
$raca = $_POST['raca'];
// Se nenhuma loja for selecionada, o valor é uma string vazia. Convertemos para NULL.
$id_loja = !empty($_POST['id_loja']) ? $_POST['id_loja'] : NULL;

$sql = "INSERT INTO pets (nome, raca, id_loja) VALUES (?, ?, ?)";

if ($stmt = $conexao->prepare($sql)) {
    // O tipo para id_loja é 'i' (integer) se não for nulo, ou 's' (string) se for nulo. 
    // É mais seguro tratar como 's' e deixar o MySQL converter, ou verificar o tipo.
    // Usaremos bind_param com verificação.
    $stmt->bind_param("ssi", $nome, $raca, $id_loja);
    
    if ($stmt->execute()) {
        echo "Pet cadastrado com sucesso! Redirecionando...";
        header("refresh:2;url=painel.php");
    } else {
        echo "Erro: " . $stmt->error;
    }
    $stmt->close();
}
$conexao->close();
?>