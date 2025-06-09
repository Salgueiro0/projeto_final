<?php
require_once 'config.php';
// Redireciona se não estiver logado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: index.php");
    exit;
}

// --- LÓGICA DO FILTRO ---
$estado_filtro_id = isset($_GET['estado_id']) ? intval($_GET['estado_id']) : 0;
$cidade_filtro_id = isset($_GET['cidade_id']) ? intval($_GET['cidade_id']) : 0;

// Busca todos os estados para preencher o menu do filtro
$estados = [];
$sql_estados_filtro = "SELECT id, nome FROM estados ORDER BY nome ASC";
$res_estados = $conexao->query($sql_estados_filtro);
if($res_estados) { while($row = $res_estados->fetch_assoc()) { $estados[] = $row; } }

// --- LÓGICA DA BUSCA DE LOJAS (COM FILTRO) ---
$sql_lojas = "SELECT l.id, l.nome, l.localizacao, l.contato, l.descricao, l.caminho_foto, c.nome as cidade_nome, e.uf as estado_uf FROM lojas l LEFT JOIN cidades c ON l.cidade_id = c.id LEFT JOIN estados e ON c.estado_id = e.id";
$where_clauses_lojas = []; 
$params_lojas = []; 
$types_lojas = '';
if ($estado_filtro_id > 0) { $where_clauses_lojas[] = "e.id = ?"; $params_lojas[] = $estado_filtro_id; $types_lojas .= 'i'; }
if ($cidade_filtro_id > 0) { $where_clauses_lojas[] = "l.cidade_id = ?"; $params_lojas[] = $cidade_filtro_id; $types_lojas .= 'i'; }
if (!empty($where_clauses_lojas)) { $sql_lojas .= " WHERE " . implode(" AND ", $where_clauses_lojas); }
$sql_lojas .= " ORDER BY l.nome";
$stmt_lojas = $conexao->prepare($sql_lojas);
if (!empty($params_lojas)) { $stmt_lojas->bind_param($types_lojas, ...$params_lojas); }
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
        h1, h2, h3, h4 { color: #333; }
        h4 { color: #0056b3; }
        .filtro-container { margin-bottom: 2rem; padding: 1.5rem; border: 1px solid #ddd; border-radius: 8px; background-color: #f9f9f9; }
        .filtro-form { display: flex; flex-wrap: wrap; gap: 1rem; align-items: center; }
        .filtro-form div { display: flex; flex-direction: column; }
        .filtro-container select, .filtro-container button, .filtro-container a { padding: 0.6rem; border-radius: 4px; border: 1px solid #ccc; text-decoration: none; }
        .filtro-container button { background-color: #007bff; color: white; cursor: pointer; border-color: #007bff; }
        .filtro-container a { background-color: #6c757d; color: white; display: inline-block; line-height: 1.5; text-align: center;}
        .loja { border: 1px solid #ddd; padding: 1.5rem; margin-bottom: 1.5rem; border-radius: 8px; background-color: #fff; }
        .loja img { max-width: 100%; height: auto; border-radius: 5px; margin-bottom: 1rem; border: 1px solid #eee; }
        .pet { display: flex; align-items: center; gap: 15px; margin-left: 1rem; padding: 0.75rem; border-left: 3px solid #28a745; margin-top: 10px; }
        .pet-foto { width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 2px solid #eee; }
        .voltar { margin-top: 2rem; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Lojas e Pets Cadastrados</h1>
        
        <div class="filtro-container">
            <h3>Filtrar por Localização</h3>
            <form class="filtro-form" action="pesquisar.php" method="get">
                <div>
                    <label for="estado_filtro">Estado:</label>
                    <select id="estado_filtro" name="estado_id">
                        <option value="">Todos os Estados</option>
                        <?php foreach($estados as $estado): ?>
                            <option value="<?php echo $estado['id']; ?>" <?php if($estado['id'] == $estado_filtro_id) echo 'selected'; ?>><?php echo htmlspecialchars($estado['nome']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="cidade_filtro">Cidade:</label>
                    <select id="cidade_filtro" name="cidade_id" <?php if($estado_filtro_id == 0) echo 'disabled'; ?>>
                        <option value="">-- Selecione um estado --</option>
                    </select>
                </div>
                <div style="align-self: flex-end;"><button type="submit">Filtrar</button></div>
                <div style="align-self: flex-end;"><a href="pesquisar.php">Limpar Filtro</a></div>
            </form>
        </div>

        <h2><?php echo !empty($cidade_filtro_id) || !empty($estado_filtro_id) ? "Lojas Encontradas no Filtro" : "Todas as Lojas"; ?></h2>
        <?php
        if ($resultado_lojas && $resultado_lojas->num_rows > 0) {
            while ($loja = $resultado_lojas->fetch_assoc()) {
                echo "<div class='loja'>";
                if (!empty($loja['caminho_foto']) && file_exists($loja['caminho_foto'])) { echo "<img src='".htmlspecialchars($loja['caminho_foto'])."' alt='Foto de ".htmlspecialchars($loja['nome'])."'>"; }
                echo "<h3>".htmlspecialchars($loja['nome'])."</h3>";
                if (!empty($loja['cidade_nome'])) { echo "<p><strong>Localização:</strong> " . htmlspecialchars($loja['cidade_nome']) . "/" . htmlspecialchars($loja['estado_uf']) . "</p>"; }
                echo "<p><strong>Endereço:</strong> " . htmlspecialchars($loja['localizacao']) . "</p>";
                if (!empty($loja['contato'])) { echo "<p><strong>Contato:</strong> " . htmlspecialchars($loja['contato']) . "</p>"; }
                if (!empty($loja['descricao'])) { echo "<p><strong>Descrição:</strong><br>" . nl2br(htmlspecialchars($loja['descricao'])) . "</p>"; }
                
                echo "<h4>Pets nesta loja:</h4>";
                $sql_pets_na_loja = "SELECT nome, raca, tipo_animal, idade, caminho_foto, contato FROM pets WHERE id_loja = ? ORDER BY nome";
                if ($stmt_pets = $conexao->prepare($sql_pets_na_loja)) {
                    $stmt_pets->bind_param("i", $loja['id']);
                    $stmt_pets->execute();
                    $resultado_pets = $stmt_pets->get_result();
                    if ($resultado_pets->num_rows > 0) {
                        while ($pet = $resultado_pets->fetch_assoc()) {
                            echo "<div class='pet'>";
                            if (!empty($pet['caminho_foto']) && file_exists($pet['caminho_foto'])) { echo "<img class='pet-foto' src='".htmlspecialchars($pet['caminho_foto'])."' alt='Foto de ".htmlspecialchars($pet['nome'])."'>"; }
                            echo "<div><strong>".htmlspecialchars($pet['nome'])."</strong><br>";
                            $detalhes_pet = [];
                            if (!empty($pet['tipo_animal'])) { $detalhes_pet[] = htmlspecialchars($pet['tipo_animal']); }
                            if (!empty($pet['raca'])) { $detalhes_pet[] = htmlspecialchars($pet['raca']); }
                            if (!empty($pet['idade'])) { $detalhes_pet[] = htmlspecialchars($pet['idade']) . " anos"; }
                            if (!empty($pet['contato'])) { $detalhes_pet[] = "Contato: " . htmlspecialchars($pet['contato']); }
                            echo implode(' &bull; ', $detalhes_pet);
                            echo "</div></div>";
                        }
                    } else { echo "<p>Nenhum pet cadastrado para esta loja.</p>"; }
                    $stmt_pets->close();
                }
                echo "</div>";
            }
        } else {
            echo "<p>Nenhuma loja encontrada com os critérios selecionados.</p>";
        }
        ?>
        
        <hr>
        <h2><?php echo !empty($cidade_filtro_id) || !empty($estado_filtro_id) ? "Pets Sem Loja no Filtro" : "Pets Sem Loja"; ?></h2>
        <?php
        $sql_pets_sem_loja = "SELECT p.nome, p.raca, p.tipo_animal, p.idade, p.caminho_foto, p.contato, c.nome as cidade_nome, e.uf as estado_uf FROM pets p LEFT JOIN cidades c ON p.cidade_id = c.id LEFT JOIN estados e ON c.estado_id = e.id WHERE p.id_loja IS NULL";
        
        $where_clauses_pets = []; $params_pets = []; $types_pets = '';
        if ($estado_filtro_id > 0) { $where_clauses_pets[] = "e.id = ?"; $params_pets[] = $estado_filtro_id; $types_pets .= 'i'; }
        if ($cidade_filtro_id > 0) { $where_clauses_pets[] = "p.cidade_id = ?"; $params_pets[] = $cidade_filtro_id; $types_pets .= 'i'; }
        if (!empty($where_clauses_pets)) { $sql_pets_sem_loja .= " AND " . implode(" AND ", $where_clauses_pets); }
        $sql_pets_sem_loja .= " ORDER BY p.nome";
        
        $stmt_pets_sem_loja = $conexao->prepare($sql_pets_sem_loja);
        if (!empty($params_pets)) { $stmt_pets_sem_loja->bind_param($types_pets, ...$params_pets); }
        $stmt_pets_sem_loja->execute();
        $resultado_pets_sem_loja = $stmt_pets_sem_loja->get_result();

        if ($resultado_pets_sem_loja && $resultado_pets_sem_loja->num_rows > 0) {
            while ($pet = $resultado_pets_sem_loja->fetch_assoc()) {
                echo "<div class='pet'>";
                if (!empty($pet['caminho_foto']) && file_exists($pet['caminho_foto'])) { echo "<img class='pet-foto' src='".htmlspecialchars($pet['caminho_foto'])."' alt='Foto de ".htmlspecialchars($pet['nome'])."'>"; }
                echo "<div><strong>".htmlspecialchars($pet['nome'])."</strong><br>";
                $detalhes_pet = [];
                if (!empty($pet['tipo_animal'])) { $detalhes_pet[] = htmlspecialchars($pet['tipo_animal']); }
                if (!empty($pet['raca'])) { $detalhes_pet[] = htmlspecialchars($pet['raca']); }
                if (!empty($pet['idade'])) { $detalhes_pet[] = htmlspecialchars($pet['idade']) . " anos"; }
                if (!empty($pet['contato'])) { $detalhes_pet[] = "Contato: " . htmlspecialchars($pet['contato']); }
                if (!empty($pet['cidade_nome'])) { $detalhes_pet[] = "Localização: " . htmlspecialchars($pet['cidade_nome']) . '/' . htmlspecialchars($pet['estado_uf']); }
                echo implode(' &bull; ', $detalhes_pet);
                echo "</div></div>";
            }
        } else {
            echo "<p>Nenhum pet sem loja encontrado com os critérios selecionados.</p>";
        }
        ?>
        
        <p class="voltar"><a href="painel.php">Voltar ao Painel</a></p>
    </div>

    <script>
    const estadoSelect = document.getElementById('estado_filtro');
    const cidadeSelect = document.getElementById('cidade_filtro');
    const estadoInicialId = <?php echo $estado_filtro_id; ?>;
    const cidadeInicialId = <?php echo $cidade_filtro_id; ?>;
    function carregarCidades(estadoId, cidadeSelecionadaId = null) {
        if (!estadoId || estadoId === "") {
            cidadeSelect.innerHTML = '<option value="">-- Selecione um estado --</option>';
            cidadeSelect.disabled = true;
            return;
        }
        cidadeSelect.innerHTML = '<option value="">Carregando...</option>';
        cidadeSelect.disabled = false;
        fetch('buscar_cidades.php?estado_id=' + estadoId)
            .then(response => response.json())
            .then(cidades => {
                cidadeSelect.innerHTML = '<option value="">Todas as Cidades</option>';
                cidades.forEach(cidade => {
                    const option = document.createElement('option');
                    option.value = cidade.id;
                    option.textContent = cidade.nome;
                    if (cidade.id == cidadeSelecionadaId) { option.selected = true; }
                    cidadeSelect.appendChild(option);
                });
            });
    }
    estadoSelect.addEventListener('change', () => carregarCidades(estadoSelect.value));
    if (estadoInicialId > 0) {
        carregarCidades(estadoInicialId, cidadeInicialId);
    }
    </script>
</body>
</html>
<?php
$conexao->close();
?>