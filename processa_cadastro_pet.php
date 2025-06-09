<?php
require_once 'config.php';

// Habilitar a exibição de todos os erros para depuração
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Redireciona se não estiver logado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: index.php");
    exit;
}

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- LÓGICA DE UPLOAD DA IMAGEM ---
    $caminho_foto_pet = null; // Começa como nulo

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
        $pasta_uploads = 'uploads/';
        $nome_arquivo = 'pet_' . uniqid() . '_' . basename($_FILES['foto']['name']);
        $caminho_completo = $pasta_uploads . $nome_arquivo;

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $caminho_completo)) {
            $caminho_foto_pet = $caminho_completo;
        }
    }
    // --- FIM DA LÓGICA DE UPLOAD ---


    // Pega os dados do formulário
    $nome = $_POST['nome'];
    $tipo_animal = $_POST['tipo_animal'];
    $raca = $_POST['raca'];

    // Tratamento para campos que podem ser nulos (vazios)
    $idade = !empty($_POST['idade']) ? intval($_POST['idade']) : NULL;
    $id_loja = !empty($_POST['id_loja']) ? intval($_POST['id_loja']) : NULL;

    // A instrução SQL para inserir os dados
    $sql = "INSERT INTO pets (nome, tipo_animal, raca, idade, id_loja, caminho_foto) VALUES (?, ?, ?, ?, ?, ?)";

    if ($stmt = $conexao->prepare($sql)) {
        
        // CORREÇÃO AQUI: A string de tipos foi ajustada para "sssiis"
        // s: string (nome, tipo_animal, raca)
        // i: integer (idade, id_loja)
        // s: string (caminho_foto)
        $stmt->bind_param("sssiis", $nome, $tipo_animal, $raca, $idade, $id_loja, $caminho_foto_pet);
        
        // Tenta executar o comando no banco de dados
        if ($stmt->execute()) {
            echo "Pet cadastrado com sucesso! Redirecionando...";
            header("refresh:2;url=painel.php");
        } else {
            // Se houver um erro na execução, ele será mostrado aqui
            echo "Erro ao executar o cadastro no banco de dados: " . $stmt->error;
        }
        $stmt->close();
    } else {
        // Se houver um erro de sintaxe no SQL, ele será mostrado aqui
        echo "Erro ao preparar a query: " . $conexao->error;
    }
    $conexao->close();

} else {
    // Se alguém tentar acessar o arquivo diretamente
    header("location: cadastrar_pet.php");
    exit();
}
?>