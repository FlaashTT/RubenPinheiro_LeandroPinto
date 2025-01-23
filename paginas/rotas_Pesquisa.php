<?php
include("../paginas/menu_layout.php");
include("../paginas/validar.php");
session_start();

validar_acesso([3, 2, 1]);

?>

<!DOCTYPE html>
<html lang="pt-pt">
<style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f7f7f7;
    }

    .container {
        max-width: 600px;
        margin: 20px auto;
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .bus-info {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px;
        border-bottom: 1px solid #ddd;
    }

    .bus-info div {
        text-align: center;
    }

    .bus-info .time {
        font-size: 1.2rem;
        font-weight: bold;
    }

    .details {
        padding: 16px;
    }

    .details .route {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
    }

    .details .info-icons {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #555;
    }

    .price {
        text-align: right;
        font-size: 1.2rem;
        font-weight: bold;
        color: #27ae60;
        margin-bottom: 8px;
    }

    .continue-btn {
        display: block;
        width: 100%;
        background-color: #27ae60;
        color: #fff;
        text-align: center;
        padding: 12px;
        font-size: 1rem;
        font-weight: bold;
        text-decoration: none;
        border: none;
        cursor: pointer;
        border-radius: 0 0 8px 8px;
    }

    .continue-btn:hover {
        background-color: #1e8e50;
    }

    .content-Rotas-Pesquisa {
        max-width: 1200px;
        margin: 250px;
        padding: 20px;
        margin-left: 500px;
        margin-top: 70px;
        background: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .content-Rotas-Pesquisa .card-Pesquisa {
        background: #fff;
        padding: 16px;
        margin-bottom: 20px;
        border-radius: 8px;
        border: 1px solid #ddd;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .hidden {
        display: none;
    }

    .resultado-card {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 16px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        font-family: Arial, sans-serif;
    }

    .horarios {
        display: flex;
        justify-content: space-between;
        font-size: 1.2rem;
        font-weight: bold;
        margin-bottom: 8px;
    }

    .locais {
        display: flex;
        justify-content: space-between;
        font-size: 1rem;
        color: #555;
        margin-bottom: 8px;
    }

    .info-detalhes {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 8px;
    }

    .icones {
        display: flex;
        gap: 8px;
        font-size: 0.9rem;
        color: #777;
    }

    .preco-e-btn {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .preco {
        font-size: 1.5rem;
        font-weight: bold;
        color: #27ae60;
    }

    .btn-continuar {
        background-color: #27ae60;
        color: white;
        border: none;
        padding: 8px 16px;
        font-size: 1rem;
        border-radius: 4px;
        cursor: pointer;
    }

    .btn-continuar:hover {
        background-color: #1e8e50;
    }
</style>

<body>
    <div class="content-Rotas-Pesquisa">
        <div class="card-Pesquisa">
            <form action="" class="formulario" method="POST">

                <div class="campo">
                    <label for="de">De:</label>
                    <input type="text" id="de" class="input-text" name="de">
                </div>

                <div class="campo">
                    <label for="para">Para:</label>
                    <input type="text" id="para" class="input-text" name="para">
                </div>

                <div class="campo">
                    <label for="data">Data ida:</label>
                    <input type="date" id="data" class="input-text" name="dataIda">
                </div>
                <div id="dataVoltaContainer" class="hidden">
                    <label for="dataVolta">Data de Volta:</label>
                    <input type="date" id="dataVolta" class="input-text" name="dataVolta">
                </div>

                <div class="campo">
                    <label for="passageiros">Passageiros:</label>
                    <input type="number" id="passageiros" class="input-text" name="passageiros" min="1" value="1">
                </div>

                <div class="campo">
                    <button type="submit" class="btn-pesquisar">Pesquisar</button>
                </div>
            </form>
        </div>


        <div class="card">
            <?php
            function gestaoUtilizadores($de = '', $para = '', $dataIda = '')
            {
                include("../basedados/basedados.h");

                $sql = "SELECT bilhetes.*, rota.*, veiculo.*
                FROM bilhetes
                INNER JOIN rota ON bilhetes.id_rota = rota.id_rota
                INNER JOIN veiculo ON bilhetes.id_veiculo = veiculo.id_veiculo
                WHERE estado_bilhete = 'Ativo'";


                if (!empty($de)) {
                    $sql .= " AND rota.origem LIKE '%$de%'";
                }
                if (!empty($para)) {
                    $sql .= " AND rota.destino LIKE '%$para%'";
                }
                if (!empty($dataIda)) {
                    $sql .= " AND bilhetes.data LIKE '%$dataIda%'";
                }

                $result = mysqli_query($conn, $sql);
                if (!$result) {
                    die('Erro na consulta: ' . mysqli_error($conn));
                }

                return $result;
            }

            $de = isset($_POST['de']) ? $_POST['de'] : '';
            $para = isset($_POST['para']) ? $_POST['para'] : '';
            $dataIda = isset($_POST['dataIda']) ? $_POST['dataIda'] : '';

            if (empty($de) && empty($para) && empty($dataIda)) {
                echo "<p>Por favor, preencha pelo menos um campo para pesquisa.</p>";
            } else {
                $rota = gestaoUtilizadores($de, $para, $dataIda);
                if (mysqli_num_rows($rota) > 0) {
                    while ($row = mysqli_fetch_assoc($rota)) {
                        echo '
                    <div class="resultado-card">
                        
                        <div class="locais">
                            <span class="origem">De:</span>
                            <span class="destino">Para:</span>
                        </div>
                        <div class="horarios">
                            <span class="origem">' . $row['origem'] . '</span>
                            <span class="hora">Hora Saída: ' . substr($row['hora'], 0, 5) . ' h</span>
                            <span class="destino">' . $row['destino'] . '</span>
                        </div>
                        <div class="horarios" style="display: flex; justify-content: center; margin-right: 70px; align-items: center; height: 50px; font-size: 1.2rem; font-weight: bold; color: #555;>
                            <span class="origem">Dia: ' . $row["data"] . '</span>
                        </div>


                        <div class="info-detalhes">
                            <div class="icones">
                                <span>🚍 Autocarro direto</span>
                                <span>📶 WIFI</span>
                                ';
                        $quantidadeRestante = $row['capacidade_veiculo'] - $row['lugaresComprados'];
                        if ($quantidadeRestante == 0) {
                            echo "<span>👥 Autocarro cheio</span>";
                        } elseif ($quantidadeRestante < 10) {
                            echo "<span>👥 Autocarro quase cheio</span>";
                        } else {
                            echo "<span>👥 Autocarro livre</span>";
                        }
                        echo '</div>';

                        if ($quantidadeRestante == 0) {
                            echo '
                                <div class="preco-e-btn">
                                <span class="preco">Indisponivel</span>
                                </div>';
                        } elseif (!isset($_SESSION['user'])) {
                            echo '
                                <div class="preco-e-btn">
                                <span class="preco">Inicie Sessão para comprar</span>
                                </div>';
                        } else {
                            echo '
                            <div class="preco-e-btn">
                                <span class="preco">' . $row['preco'] . '€</span>
                                <form method = "POST">
                                    <input type="hidden" name="precoBilhete" value="' . $row["preco"] . '  ">
                                    <button class="btn-continuar" name=comprarBilhete value =' . $row['id_bilhete'] . '>Continuar</button>
                                </form
                            </div>';
                        }
                        echo '
                        </div>
                    </div>
                    ';
                    }
                } else {
                    echo "<p>Nenhuma rota encontrada.</p>";
                }
            }
            if (isset($_POST['comprarBilhete'])) {
                $idBilheteComprar = $_POST['comprarBilhete'];
                $preco = $_POST['precoBilhete'];




                $user = $_SESSION['user'];

                // Verifica se o bilhete já foi comprado
                $sql = "SELECT * FROM bilhetescomprados 
                        WHERE id_bilhete = $idBilheteComprar 
                        AND id_utilizador_comprador = " . $user['Id_User'];
                $result = mysqli_query($conn, $sql);

                if (!$result) {
                    echo "Erro ao verificar bilhete comprado: " . mysqli_error($conn);
                    exit();
                }

                if (mysqli_num_rows($result) > 0) {
                    echo "Já tem este bilhete comprado.";
                    exit();
                }

                // Busca o saldo atual do usuário
                $sql = "SELECT Saldo FROM users WHERE Id_User = " . $user['Id_User'];
                $result = mysqli_query($conn, $sql);

                if (!$result || mysqli_num_rows($result) == 0) {
                    echo "Erro ao verificar saldo do utilizador.";
                    exit();
                }

                $row = mysqli_fetch_assoc($result);
                $saldoAtual = $row['Saldo'];

                // Valida saldo do usuário
                if ($saldoAtual == 0) {
                    echo "Não tem dinheiro na sua carteira.";
                    exit();
                } elseif ($saldoAtual < $preco) {
                    echo "O seu saldo é insuficiente para comprar este bilhete.";
                    exit();
                }

                // Atualiza o saldo do utilizador
                $novoSaldo = $saldoAtual - $preco;
                $sql = "UPDATE users SET Saldo = '$novoSaldo' WHERE Id_User = " . $user['Id_User'];

                if (!mysqli_query($conn, $sql)) {
                    echo "Erro ao atualizar o saldo do utilizador: " . mysqli_error($conn);
                    exit();
                }

                // Insere a compra do bilhete
                $sqlInsert = "INSERT INTO bilhetescomprados (id_bilhete, id_utilizador_comprador) 
                              VALUES ('$idBilheteComprar', '" . $user['Id_User'] . "')";

                if (mysqli_query($conn, $sqlInsert)) {
                    
                    $dataAtual = date('Y-m-d');
                    $sqlAlert = "INSERT INTO alertas (texto_alerta, data_emissao, id_remetentes, tipo) 
                        VALUES ('Comprou o bilhete " . $idBilheteComprar . "', '$dataAtual', " . $user['Id_User'] . ", 'Compra Bilhete')";


                    if (mysqli_query($conn, $sqlAlert)) {
                        echo "Sucesso ao comprar o bilhete.";
                        
                    }
                } else {
                    echo "Erro ao registrar a compra do bilhete: " . mysqli_error($conn);
                }
                
                
            }







            ?>
        </div>
    </div>

</body>

</html>