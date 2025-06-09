<?php
require 'config.php';

$email = $_POST['email'];
$senha_input = $_POST['senha'];

$sql = "SELECT id, nome, senha FROM usuarios WHERE email = ?";

if ($stmt = $conexao->prepare($sql)) {
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($id, $nome, $senha_hash);
        $stmt->fetch();

        if (password_verify($senha_input, $senha_hash)) {
            // Senha correta, inicia a sessão
            $_SESSION['loggedin'] = true;
            $_SESSION['id'] = $id;
            $_SESSION['nome'] = $nome;

            // Redireciona para o painel principal
            header("location: painel.php");
        } else {
            echo "Senha incorreta.";
            header("refresh:2;url=index.php");
        }
    } else {
        echo "Nenhum usuário encontrado com esse email.";
        header("refresh:2;url=index.php");
    }
    $stmt->close();
}
$conexao->close();
?>