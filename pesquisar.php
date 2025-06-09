<?php
require 'config.php';
// Redireciona se não estiver logado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: index.php");
    exit;
}

// Pega a cidade selecionada no filtro (se houver)
// trim() remove espaços em branco do início e do fim
$cidade_filtro = isset($_GET['cidade']) ? trim($_GET['cidade']) : '';

// Busca todas as cidades distintas que têm lojas, para preencher o menu do filtro
$cidades_disponiveis = [];
$sql_cidades = "SELECT DISTINCT cidade FROM lojas WHERE cidade IS NOT NULL AND cidade != '' ORDER BY cidade ASC";
$resultado_cidades = $conexao->query($sql_cidades);
if ($resultado_cidades) {
    while($cidade_row = $resultado_cidades->fetch_assoc()) {
        $cidades_disponiveis[] = $cidade_row['cidade'];
    }
}

// Monta a query principal para buscar as lojas
// Adiciona a cláusula WHERE apenas se uma cidade foi selecionada no filtro
$sql_lojas = "SELECT id, nome, localizacao, contato, descricao, caminho_foto, cidade, estado FROM lojas";
if (!empty($cidade_filtro)) {
    $sql_lojas .= " WHERE cidade = ?";
}
$sql_lojas .= " ORDER BY nome";

$stmt_lojas = $conexao->prepare($sql_lojas);

// Se o filtro estiver ativo, faz o "bind" do valor da cidade na query
if (!empty($cidade_filtro)) {
    $stmt_lojas->bind_param("s", $cidade_filtro);
}

$stmt_lojas->execute();
$resultado_lojas = $stmt_lojas->get_result();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Pesquisar Lojas e Pets</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 2rem; }
        .container { max-width: 900px; margin: auto; background: #fff; padding: 2rem; border-radius: 8px; }
        .filtro-container { margin-bottom: 2rem; padding: 1.5rem; border: 1px solid #ddd; border-radius: 8px; background-color: #f9f9f9; }
        .filtro-container h3 { margin-top: 0; }
        .filtro-container select, .filtro-container button, .filtro-container a { padding: 0.6rem; border-radius: 4px; border: 1px solid #ccc; text-decoration: none; }
        .filtro-container button { background-color: #007bff; color: white; cursor: pointer; border-color: #007bff; }
        .filtro-container a { background-color: #6c757d; color: white; }
        .loja { border: 1px solid #ddd; padding: 1.5rem; margin-bottom: 1.5rem; border-radius: 8px; background-color: #fdfdfd; }
        .loja h3 { margin-top: 0; color: #0056b3; }
        .loja img { max-width: 100%; height: auto; border-radius: 5px; margin-bottom: 1rem; border: 1px solid #eee; }
        .pet { margin-left: 2rem; padding: 0.5rem; border-left: 3px solid #007bff; }
        .voltar { margin-top: 2rem; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Lojas e Pets Cadastrados</h1>

        <div class="filtro-container">
            <h3>Filtrar por Cidade</h3>
            <form action="pesquisar.php" method="get">
                <select name="cidade">
                    <option value="">Todas as Cidades</option>
                    <?php foreach ($cidades_disponiveis as $cidade_item): ?>
                        <option value="<?php echo htmlspecialchars($cidade_item); ?>" <?php if ($cidade_filtro == $cidade_item) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($cidade_item); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">Filtrar</button>
                <a href="pesquisar.php">Limpar Filtro</a>
            </form>
        </div>

        <h2><?php echo !empty($cidade_filtro) ? "Exibindo lojas em " . htmlspecialchars($cidade_filtro) : "Todas as Lojas"; ?></h2>
        
        <?php
        if ($resultado_lojas && $resultado_lojas->num_rows > 0) {
            while ($loja = $resultado_lojas->fetch_assoc()) {
                echo "<div class='loja'>";
                
                // Exibe a imagem somente se o caminho existir no banco
                if (!empty($loja['caminho_foto']) && file_exists($loja['caminho_foto'])) {
                    echo "<img src='" . htmlspecialchars($loja['caminho_foto']) . "' alt='Foto de " . htmlspecialchars($loja['nome']) . "'>";
                }
                
                echo "<h3>" . htmlspecialchars($loja['nome']) . "</h3>";
                echo "<p><strong>Cidade/UF:</strong> " . htmlspecialchars($loja['cidade']) . "/" . htmlspecialchars($loja['estado']) . "</p>";
                echo "<p><strong>Endereço:</strong> " . htmlspecialchars($loja['localizacao']) . "</p>";
                if (!empty($loja['contato'])) { echo "<p><strong>Contato:</strong> " . htmlspecialchars($loja['contato']) . "</p>"; }
                if (!empty($loja['descricao'])) { echo "<p><strong>Descrição:</strong><br>" . nl2br(htmlspecialchars($loja['descricao'])) . "</p>"; }
                
                echo "<h4>Pets nesta loja:</h4>";
                $sql_pets = "SELECT nome, raca FROM pets WHERE id_loja = ? ORDER BY nome";
                if ($stmt_pets = $conexao->prepare($sql_pets)) {
                    $stmt_pets->bind_param("i", $loja['id']);
                    $stmt_pets->execute();
                    $resultado_pets = $stmt_pets->get_result();
                    if ($resultado_pets->num_rows > 0) {
                        while ($pet = $resultado_pets->fetch_assoc()) {
                            echo "<div class='pet'><strong>" . htmlspecialchars($pet['nome']) . "</strong> (" . htmlspecialchars($pet['raca']) . ")</div>";
                        }
                    } else {
                        echo "<p>Nenhum pet cadastrado para esta loja.</p>";
                    }
                    $stmt_pets->close();
                }
                echo "</div>"; // Fecha a div .loja
            }
        } else {
            echo "<p>Nenhuma loja encontrada com os critérios selecionados.</p>";
        }
        ?>
        
        <p class="voltar"><a href="painel.php">Voltar ao Painel</a></p>
    </div>
</body>
</html>
<?php
$conexao->close();
?>