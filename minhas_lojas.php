<?php
// Crie este novo arquivo: minhas_lojas.php
require_once 'config.php';
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: index.php");
    exit;
}

$id_usuario = $_SESSION['id'];
$lojas = [];
$sql = "SELECT id, nome, localizacao FROM lojas WHERE id_usuario = ? ORDER BY nome";
if($stmt = $conexao->prepare($sql)) {
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();
    while($linha = $resultado->fetch_assoc()) {
        $lojas[] = $linha;
    }
    $stmt->close();
}
$conexao->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Minhas Lojas</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 2rem; }
        .container { max-width: 900px; margin: auto; background: #fff; padding: 2rem; border-radius: 8px; }
        .loja-item { display: flex; justify-content: space-between; align-items: center; padding: 1rem; border: 1px solid #ddd; margin-bottom: 1rem; border-radius: 5px; }
        .loja-item .acoes a { margin-left: 10px; text-decoration: none; padding: 8px 12px; border-radius: 4px; color: white; }
        .btn-editar { background-color: #ffc107; }
        .btn-apagar { background-color: #dc3545; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Gerenciar Minhas Lojas</h1>
        <p><a href="painel.php">Voltar ao Painel</a></p>
        <hr>
        <?php if(empty($lojas)): ?>
            <p>Você ainda não cadastrou nenhuma loja.</p>
        <?php else: ?>
            <?php foreach($lojas as $loja): ?>
                <div class="loja-item">
                    <div>
                        <strong><?php echo htmlspecialchars($loja['nome']); ?></strong><br>
                        <small><?php echo htmlspecialchars($loja['localizacao']); ?></small>
                    </div>
                    <div class="acoes">
                    <a href="editar_loja.php?id=<?php echo $loja['id']; ?>" class="btn-editar">Editar</a> <a href="apagar_loja.php?id=<?php echo $loja['id']; ?>" class="btn-apagar" onclick="return confirm('Tem certeza que deseja apagar esta loja? Os pets ficarão sem loja, mas não serão apagados.');">Apagar</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>