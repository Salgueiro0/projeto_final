<?php
require 'config.php';

// Redireciona se não estiver logado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: index.php");
    exit;
}

$caminho_foto = null; // Inicia o caminho da foto como nulo

// Verifica se um arquivo foi enviado e se não houve erro no upload
if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
    $pasta_uploads = 'uploads/'; // A pasta onde as imagens serão salvas
    
    // Gera um nome único para o arquivo para evitar que um substitua o outro
    $nome_arquivo = uniqid() . '_' . basename($_FILES['foto']['name']);
    $caminho_completo = $pasta_uploads . $nome_arquivo;

    // Tenta mover o arquivo da pasta temporária do servidor para a nossa pasta 'uploads'
    if (move_uploaded_file($_FILES['foto']['tmp_name'], $caminho_completo)) {
        // Se o upload deu certo, salvamos o caminho para guardar no banco de dados
        $caminho_foto = $caminho_completo;
    } else {
        // Se falhar, podemos avisar o usuário, mas o cadastro continua sem a foto
        echo "Atenção: Houve um erro ao fazer o upload da imagem, mas a loja será cadastrada sem ela.";
    }
}

// Pega todos os dados do formulário, incluindo os novos
$nome = $_POST['nome'];
$localizacao = $_POST['localizacao'];
$cidade = $_POST['cidade'];
$estado = $_POST['estado'];
$contato = $_POST['contato'];
$descricao = $_POST['descricao'];
$id_usuario = $_SESSION['id']; // Pega o ID do usuário logado

// SQL atualizado para incluir TODOS os novos campos
$sql = "INSERT INTO lojas (nome, localizacao, id_usuario, contato, descricao, caminho_foto, cidade, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

if ($stmt = $conexao->prepare($sql)) {
    // bind_param atualizado com os tipos e variáveis corretos
    // s = string, i = integer
    $stmt->bind_param("ssisssss", $nome, $localizacao, $id_usuario, $contato, $descricao, $caminho_foto, $cidade, $estado);

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