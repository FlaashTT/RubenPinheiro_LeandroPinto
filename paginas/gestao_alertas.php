<?php
include("../basedados/basedados.h");
include("../paginas/menu_layout.php");
require("../paginas/validar.php");
session_start();

validar_acesso([3]);

function gestaoAlertas($filtro = '')
{
    global $conn;
    $sql = "SELECT alertas.*, users.nome, users.Id_User, alertas.texto_alerta, alertas.data_emissao 
            FROM alertas 
            INNER JOIN users ON alertas.id_remetentes = users.Id_User";

    // adiciona o filtro (se houver)
    if (!empty($filtro)) {

        $sql .= " WHERE tipo LIKE '%$filtro%' OR nome LIKE '%$filtro%' OR id_remetentes LIKE '%$filtro%'";
    }

    $result = mysqli_query($conn, $sql);

    // Verifica se a consulta foi bem-sucedida
    if (!$result) {
        die('Erro na consulta: ' . mysqli_error($conn)); // Se houver um erro na consulta
    }

    return $result; // Retorna o resultado da consulta
}


// Verifica se o botão de reset foi clicado
if (isset($_POST['reset']) && $_POST['reset'] == 'true') {
    // Limpa o filtro
    $filtro = '';
} else {
    // Se não foi clicado o botão de reset, pega o filtro do POST
    $filtro = isset($_POST['filtro']) ? $_POST['filtro'] : '';
}

?>

<!DOCTYPE html>
<html lang="pt-pt">
<style>
    .resultado-card {
        display: block;
        gap: 16px;
        padding: 0;
    }

    .alertas-card {
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        padding: 16px;
        font-family: Arial, sans-serif;
    }

    .alertas-card p strong {
        font-weight: bold;
        color: #333;
    }

    .alertas-card h3 {
        margin-top: 0;
        font-size: 1.5em;
    }

    .alerta-nome {
        font-size: 18px;
        font-weight: bold;
        color: #333;
        margin-bottom: 10px;
        text-transform: capitalize;
    }

    .alertas-card p {
        font-size: 14px;
        color: #555;
        line-height: 1.6;
        margin: 5px 0;
    }

    /* Cores e destaques */
    .alertas-card .tipo {
        color: #007bff;
        font-weight: bold;
    }

    .alertas-card .data {
        color: #888;
    }

    .alertas-card .alerta-texto {
        color: #444;
        font-style: italic;
    }
</style>

<body>
    <!-- Conteúdo Principal -->
    <div class="content-Alertas">
        <h1>Dashboard - Gestão de alertas</h1>
        <h2>Visão geral dos alertas</h2>
        <!-- Formulário de Filtro -->

        <form method="POST" action="">
            <input class="filtro" type="text" name="filtro" value="<?php echo htmlspecialchars($filtro); ?>" placeholder="Filtrar por Id_Remetentes, Tipo ou Nome">
            <button type="submit" class="filtrar-btn">Filtrar</button>
            <button type="submit" name="reset" value="true" class="limpar-btn">Limpar Filtro</button>
        </form>

        <div class="card">
            <h2>Aviso de Alertas</h2>
            <?php




            $sql = "SELECT alertas.*,users.*   
                    FROM alertas
                    INNER JOIN users ON alertas.id_remetentes = users.Id_Users;
                     ";
            $alertas = gestaoAlertas($filtro);



            // Executa a consulta da tabela alerta e users
            if (mysqli_num_rows($alertas) > 0) {
                $userOn = $_SESSION['user'];
                $tipoUserOn = $userOn['TipoUser'];
                echo "<div class='resultado-card'>";
                while ($alerta = mysqli_fetch_assoc($alertas)) {

                    echo '
                        <div class="alertas-card">
                            <h3 class="alerta-nome">Nome: ' . $alerta['nome'] . '</h3>
                            <p>ID: ' . $alerta['Id_User'] . '</p>
                            <p class="tipo"><strong>Tipo: </strong>' . $alerta['tipo'] . '</p>
                            <p class="data"><strong>Data: </strong>' . $alerta['data_emissao'] . '</p>
                            <p class="alerta-texto">Texto do alerta: ' . $alerta['texto_alerta'] . '</p>
                        </div>
                    
                ';
                }
                echo "</div>";
            } else {
                echo "Nenhum alerta encontrado.";
            }



            ?>

        </div>
    </div>
    </div>
</body>

</html>