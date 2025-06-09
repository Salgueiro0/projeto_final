<?php
// Crie este novo arquivo: meus_pets.php
require_once 'config.php';
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: index.php");
    exit;
}

$id_usuario = $_SESSION['id'];
$pets = [];
// Query que busca apenas os pets do usuário logado
$sql = "SELECT id, nome, tipo_animal, raca FROM pets WHERE id_usuario = ? ORDER BY nome";
if($stmt = $conexao->prepare($sql)) {
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();
    while($linha = $resultado->fetch_assoc()) {
        $pets[] = $linha;
    }
    $stmt->close();
}
$conexao->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Meus Pets</title>
    <style>body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 2rem; } .container { max-width: 900px; margin: auto; background: #fff; padding: 2rem; border-radius: 8px; } .item { display: flex; justify-content: space-between; align-items: center; padding: 1rem; border: 1px solid #ddd; margin-bottom: 1rem; border-radius: 5px; } .item .acoes a { margin-left: 10px; text-decoration: none; padding: 8px 12px; border-radius: 4px; color: white; } .btn-editar { background-color: #ffc107; } .btn-apagar { background-color: #dc3545; }</style>
</head>
<body>
    <div class="container">
        <h1>Gerenciar Meus Pets</h1>
        <p><a href="painel.php">Voltar ao Painel</a></p>
        <hr>
        <?php if(empty($pets)): ?>
            <p>Você ainda não cadastrou nenhum pet.</p>
        <?php else: ?>
            <?php foreach($pets as $pet): ?>
                <div class="item">
                    <div>
                        <strong><?php echo htmlspecialchars($pet['nome']); ?></strong><br>
                        <small><?php echo htmlspecialchars($pet['tipo_animal']); ?> - <?php echo htmlspecialchars($pet['raca']); ?></small>
                    </div>
                    <div class="acoes">
                        <a href="editar_pet.php?id=<?php echo $pet['id']; ?>" class="btn-editar">Editar</a> <a href="apagar_pet.php?id=<?php echo $pet['id']; ?>" class="btn-apagar" onclick="return confirm('Tem certeza que deseja apagar este pet?');">Apagar</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>