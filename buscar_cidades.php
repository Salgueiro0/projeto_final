<?php
// Este script busca cidades no banco de dados e as retorna em formato JSON

require_once 'config.php';

// Pega o ID do estado enviado via GET (pela URL)
$estado_id = isset($_GET['estado_id']) ? intval($_GET['estado_id']) : 0;

$cidades = [];

if ($estado_id > 0) {
    $sql = "SELECT id, nome FROM cidades WHERE estado_id = ? ORDER BY nome ASC";
    if ($stmt = $conexao->prepare($sql)) {
        $stmt->bind_param("i", $estado_id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        while($linha = $resultado->fetch_assoc()) {
            $cidades[] = $linha;
        }
        $stmt->close();
    }
}

// Define o cabeçalho da resposta como JSON
header('Content-Type: application/json');

// Converte o array de cidades para o formato JSON e o exibe
echo json_encode($cidades);

$conexao->close();
?>