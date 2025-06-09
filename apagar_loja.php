<?php
// Crie este novo arquivo: apagar_loja.php
require_once 'config.php';
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: index.php");
    exit;
}

// Pega o ID da loja pela URL e o ID do usuário pela sessão
$id_loja = isset($_GET['id']) ? intval($_GET['id']) : 0;
$id_usuario = $_SESSION['id'];

if ($id_loja > 0) {
    // Comando SQL seguro: apaga a loja APENAS SE o ID da loja e o ID do dono corresponderem
    $sql = "DELETE FROM lojas WHERE id = ? AND id_usuario = ?";
    if($stmt = $conexao->prepare($sql)) {
        $stmt->bind_param("ii", $id_loja, $id_usuario);
        $stmt->execute();
        $stmt->close();
    }
}

// Redireciona de volta para a lista de lojas
header("location: minhas_lojas.php");
exit();
?>