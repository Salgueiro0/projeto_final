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

    $id_loja = !empty($_POST['id_loja']) ? intval($_POST['id_loja']) : NULL;
    $id_usuario = $_SESSION['id'];
    $cidade_id = NULL; 

    if ($id_loja === NULL && !empty($_POST['cep_pet'])) {
        $cep = preg_replace('/[^0-9]/', '', $_POST['cep_pet']);
        if(strlen($cep) == 8) {
            $url = "https://viacep.com.br/ws/{$cep}/json/";
            $json_data = @file_get_contents($url);
            $data = json_decode($json_data);
            if ($data && !isset($data->erro)) {
                $nome_cidade = $data->localidade;
                $uf_estado = $data->uf;
                $sql_cidade = "SELECT c.id FROM cidades c JOIN estados e ON c.estado_id = e.id WHERE c.nome = ? AND e.uf = ?";
                if($stmt_cidade = $conexao->prepare($sql_cidade)) {
                    $stmt_cidade->bind_param("ss", $nome_cidade, $uf_estado);
                    $stmt_cidade->execute();
                    $resultado_cidade = $stmt_cidade->get_result();
                    if($resultado_cidade->num_rows == 1) {
                        $cidade_encontrada = $resultado_cidade->fetch_assoc();
                        $cidade_id = $cidade_encontrada['id'];
                    }
                    $stmt_cidade->close();
                }
            }
        }
    }

    // Pega os dados do formulário
    $nome = $_POST['nome'];
    $tipo_animal = $_POST['tipo_animal'];
    $raca = $_POST['raca'];
    $idade = !empty($_POST['idade']) ? intval($_POST['idade']) : NULL;
    // Pega o novo campo de contato
    $contato_pet = $_POST['contato_pet'];

    // SQL ATUALIZADO para incluir o campo 'contato'
    $sql = "INSERT INTO pets (nome, tipo_animal, raca, idade, id_loja, caminho_foto, id_usuario, cidade_id, contato) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    if ($stmt = $conexao->prepare($sql)) {
        // bind_param ATUALIZADO (sssiisiis)
        $stmt->bind_param("sssiisiis", $nome, $tipo_animal, $raca, $idade, $id_loja, $caminho_foto_pet, $id_usuario, $cidade_id, $contato_pet);
        
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