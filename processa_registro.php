<?php
require_once 'config.php';

// Habilita a exibição de erros para ajudar a depurar
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Verifica se o formulário foi enviado usando o método POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Pega os dados do formulário
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha_input = $_POST['senha'];

    // 1. VERIFICA SE O E-MAIL JÁ EXISTE NO BANCO DE DADOS
    $sql_check = "SELECT id FROM usuarios WHERE email = ?";
    if ($stmt_check = $conexao->prepare($sql_check)) {
        $stmt_check->bind_param("s", $email);
        $stmt_check->execute();
        $stmt_check->store_result();

        // Se o número de linhas for maior que 0, significa que o e-mail já foi cadastrado
        if ($stmt_check->num_rows > 0) {
            die("<strong>Erro:</strong> Este e-mail já está cadastrado. Por favor, <a href='registrar.php'>tente com outro e-mail</a> ou <a href='index.php'>faça login</a>.");
        }
        $stmt_check->close();
    }

    // 2. SE O E-MAIL É NOVO, PROSSEGUE COM O CADASTRO
    // Cria um hash seguro da senha
    $senha_hash = password_hash($senha_input, PASSWORD_DEFAULT);

    // Prepara o comando SQL para inserir o novo usuário
    $sql_insert = "INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)";

    if ($stmt_insert = $conexao->prepare($sql_insert)) {
        $stmt_insert->bind_param("sss", $nome, $email, $senha_hash);

        if ($stmt_insert->execute()) {
            echo "Cadastro realizado com sucesso! Você será redirecionado para a página de login em 3 segundos.";
            header("refresh:3;url=index.php");
        } else {
            // Mostra um erro se a inserção falhar por outro motivo
            die("Erro ao executar o cadastro: " . $stmt_insert->error);
        }
        $stmt_insert->close();
    } else {
        // Mostra um erro se a preparação do SQL falhar
        die("Erro ao preparar o cadastro: " . $conexao->error);
    }

    $conexao->close();
}
?>