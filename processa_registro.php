<?php
// Habilita a exibição de erros para nos ajudar a depurar
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// SOLUÇÃO 1: Usando require_once para evitar que o arquivo seja incluído mais de uma vez.
require_once 'config.php';

// Verifica se os dados foram enviados via POST para evitar erros
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha_input = $_POST['senha'];

    // SOLUÇÃO 2: VERIFICAR SE O E-MAIL JÁ EXISTE ANTES DE TENTAR INSERIR
    $sql_check = "SELECT id FROM usuarios WHERE email = ?";
    if ($stmt_check = $conexao->prepare($sql_check)) {
        $stmt_check->bind_param("s", $email);
        $stmt_check->execute();
        $stmt_check->store_result();

        // Se o número de linhas for maior que 0, o e-mail já existe
        if ($stmt_check->num_rows > 0) {
            die("Erro: Este e-mail já está cadastrado. Por favor, use outro e-mail ou faça login.");
        }
        $stmt_check->close();
    }

    // Se o código chegou até aqui, o e-mail é novo e podemos prosseguir.
    $senha_hash = password_hash($senha_input, PASSWORD_DEFAULT);

    $sql_insert = "INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)";

    if ($stmt_insert = $conexao->prepare($sql_insert)) {
        $stmt_insert->bind_param("sss", $nome, $email, $senha_hash);

        if ($stmt_insert->execute()) {
            echo "Cadastro realizado com sucesso! Você será redirecionado para o login.";
            header("refresh:3;url=index.php");
        } else {
            die("Erro ao executar o cadastro: " . $stmt_insert->error);
        }
        $stmt_insert->close();
    } else {
        die("Erro ao preparar o cadastro: " . $conexao->error);
    }

    $conexao->close();
} else {
    // Se alguém tentar acessar o arquivo diretamente pelo navegador, sem enviar o formulário
    echo "Acesso inválido.";
}
?>