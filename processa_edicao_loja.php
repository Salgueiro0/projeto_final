<?php
// Crie este novo arquivo: processa_edicao_loja.php
require_once 'config.php';

// Redireciona se não estiver logado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $id_loja = intval($_POST['id_loja']);
    $id_usuario = $_SESSION['id'];

    // Lógica de upload de nova foto (se enviada)
    $caminho_foto = $_POST['foto_atual'] ?? null; // Assume a foto atual por padrão
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
        // Apaga a foto antiga se existir
        // (Adicionar lógica para apagar o arquivo antigo do servidor)
        
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
    $cidade_id = $_POST['cidade_id'];
    $contato = $_POST['contato'];
    $descricao = $_POST['descricao'];

    // O comando UPDATE para atualizar os dados da loja
    // A cláusula WHERE garante que só o dono possa editar
    $sql = "UPDATE lojas SET nome = ?, localizacao = ?, cidade_id = ?, contato = ?, descricao = ?, caminho_foto = ? WHERE id = ? AND id_usuario = ?";

    if ($stmt = $conexao->prepare($sql)) {
        $stmt->bind_param("ssisssii", $nome, $localizacao, $cidade_id, $contato, $descricao, $caminho_foto, $id_loja, $id_usuario);
        
        if ($stmt->execute()) {
            echo "Loja atualizada com sucesso!";
            header("refresh:2;url=minhas_lojas.php");
        } else {
            echo "Erro ao atualizar a loja: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Erro ao preparar a query: " . $conexao->error;
    }
    $conexao->close();
}
?>