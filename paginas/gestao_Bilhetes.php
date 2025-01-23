<?php
include("../paginas/menu_layout.php");
include("../basedados/basedados.h");
require("../paginas/validar.php");
session_start();

validar_acesso([3, 2]);

?>
<!DOCTYPE html>
<html lang="pt-pt">

<body>
    <!-- Conteúdo Principal -->
    <div class="content-Bilhetes">
        <h1>Dashboard - Página Gestão Bilhetes</h1>
        <h2>Visão geral de Bilhetes</h2>

        <form action="" method="POST">
            <button class='adicionarbilhete-btn' type="submit" name="adicionarBilhete">Adicionar bilhetes</button>
            <button class='adicionarbilhete-btn' type="submit" action="../paginas/gestao_Bilhetes">Ver bilhetes ativos</button>
            <button class='adicionarbilhete-btn' type="submit" name="bilheteExpirado">Ver bilhetes expirados</button>
            <button class='adicionarbilhete-btn' type="submit" name="bilheteCancelado">Ver bilhetes cancelados</button>

        </form>


        <?php
        if (isset($_POST['adicionarBilhete'])) {
            echo "
        <form method='POST' action=''>
            <div class='card'>
                <h1>Adicionar Bilhete</h1>
                <div>
                    <p>indique o id da rota</p>
                    <input class='texto-Adicionar' type='text' name='idRota' placeholder='Digite o id da rota' >
                </div> 
                <div>
                    <p>Data para o bilhete</p>
                    <input class='texto-Adicionar' type='date' name='dataBilhete' >
                </div>  
                <div>
                    <p>Hora para o bilhete</p>
                    <input class='texto-Adicionar' type='time' name='horaBilhete' >
                </div>  
                
                <div>
                    <p>Indique o id do veiuculo </p>
                    <input class='texto-Adicionar' type='text' name='idVeiculo' placeholder='Digite o id do veiculo'  >
                </div> 
                <button class='aceitar-btn' type='submit' name='AddBilhete'>Criar bilhete</button>
                <button class='recusar-btn' type='submit' action ='../paginas/gestao_Bilhetes.php'>Cancelar </button> 
            </div>
        </form>";
        }

        if (isset($_POST['AddBilhete'])) {
            if (
                isset($_POST['idRota']) && !empty($_POST['idRota']) &&
                isset($_POST['dataBilhete']) && !empty($_POST['dataBilhete']) &&
                isset($_POST['horaBilhete']) && !empty($_POST['horaBilhete']) &&
                isset($_POST['idVeiculo']) && !empty($_POST['idVeiculo'])
            ) {

                $idRota = $_POST['idRota'];
                $dataBilhete = $_POST['dataBilhete'];
                $horaBilhete = $_POST['horaBilhete'];
                $idVeiculo = $_POST['idVeiculo'];
                $precoPorKm = 0.1;

                $dataAtual = date('Y-m-d');
                $horaAtual = date('H:i:s');
                if ($dataBilhete < $dataAtual) {
                    echo "Erro,não pode criar bilhetes para dias anteriores";
                    header("Refresh: 2; url=../paginas/gestao_Bilhetes.php");
                    exit();
                }
                if ($dataBilhete == $dataAtual && $horaBilhete < $horaAtual) {
                    echo "Não pode criar bilhetes para horas anteriores";
                    header("Refresh: 2; url=../paginas/gestao_Bilhetes.php");
                    exit();
                }

                $sql = "SELECT * FROM rota where id_rota ='$idRota'";
                if ($result = mysqli_query($conn, $sql)) {
                    if (mysqli_num_rows($result) === 0) {
                        echo "Nao existe nenhuma rota com esse id";
                        header("Refresh: 2; url=../paginas/gestao_Bilhetes.php");
                        exit();
                    }
                }
                $sql = "SELECT * FROM veiculo where id_veiculo ='$idVeiculo'";
                if ($result = mysqli_query($conn, $sql)) {
                    if (mysqli_num_rows($result) === 0) {
                        echo "Nao existe nenhum veiculo com esse id";
                        header("Refresh: 2; url=../paginas/gestao_Bilhetes.php");
                        exit();
                    }
                }


                //para verificar se ja existe algum bilhete
                $sql = "SELECT * FROM bilhetes 
                        WHERE data = '$dataBilhete' 
                        AND hora = '$horaBilhete' 
                        AND id_Rota = $idRota 
                        AND id_veiculo = $idVeiculo";
                if ($result = mysqli_query($conn, $sql)) {
                    if (mysqli_num_rows($result) > 0) {
                        echo "Ja existe um bilhete com essas especificações";
                    } else {
                        $sql = "
                            SELECT r.origem, r.destino,r.distancia, 
                                v.nome_veiculo, v.matricula, v.capacidade_veiculo
                            FROM rota r
                            INNER JOIN veiculo v ON v.id_veiculo = " . $idVeiculo . "
                            WHERE r.id_rota = " . $idRota;

                        if ($result = mysqli_query($conn, $sql)) {
                            $row = mysqli_fetch_assoc($result);

                            echo "
                                <div>
                                    <form method='post'>
                                        <p>Deseja criar um bilhete com origem em<b> " . $row['origem'] . "</b> e destino em<b> " . $row['destino'] . "</b></p>
                                        <p>
                                            Utilizando o veículo <b>" . $row['nome_veiculo'] . "</b> com a matrícula <b>" . $row['matricula'] . "</b>
                                            e com capacidade total de <b> " . $row['capacidade_veiculo'] . " </b>passageiros
                                        </p>
                                        <p>A ser realizado no dia <b> " . $dataBilhete . "</b>, pelas<b> " . $horaBilhete . "</b>
                                        <p>Com o preço final atribuido de <b>" . ($row['distancia'] * $precoPorKm) . "</b>€</p>

                                        <!-- Campos ocultos para enviar os dados -->
                                        <input type='hidden' name='precoFinal' value='" . ($row['distancia'] * $precoPorKm) . "'>
                                        <input type='hidden' name='idRota' value='" . $idRota . "'>
                                        <input type='hidden' name='dataBilhete' value='" . $dataBilhete . "'>
                                        <input type='hidden' name='horaBilhete' value='" . $horaBilhete . "'>
                                        <input type='hidden' name='idVeiculo' value='" . $idVeiculo . "'>
                                        <button class='aceitar-btn' type='submit' name='ConfirmarAddBilhete'>Confirmar</button>
                                    </form>
                                </div>";
                        } else {
                            echo (mysqli_error($conn));
                        }
                    }
                }
            }
        }
        if (isset($_POST['ConfirmarAddBilhete'])) {
            if (
                isset($_POST['idRota']) && !empty($_POST['idRota']) &&
                isset($_POST['dataBilhete']) && !empty($_POST['dataBilhete']) &&
                isset($_POST['horaBilhete']) && !empty($_POST['horaBilhete']) &&
                isset($_POST['idVeiculo']) && !empty($_POST['idVeiculo']) &&
                isset($_POST['precoFinal']) && !empty($_POST['precoFinal'])
            ) {
                $precoFinal = $_POST['precoFinal'];
                $idRota = $_POST['idRota'];
                $dataBilhete = $_POST['dataBilhete'];
                $horaBilhete = $_POST['horaBilhete'];
                $idVeiculo = $_POST['idVeiculo'];

                $sql = "INSERT INTO bilhetes(preco,data,hora,id_veiculo,id_rota) values ('$precoFinal', '$dataBilhete', '$horaBilhete','$idVeiculo','$idRota')";

                //para criar um alerta
                $dataAtual = date('Y-m-d');
                $user = $_SESSION['user'];
                $tipoUserOn = $user['TipoUser'];
                $sqlAlert = "INSERT INTO alertas (texto_alerta, data_emissao, id_remetentes, tipo) ";

                if ($tipoUserOn == 2) {
                    $sqlAlert .= " VALUES ('O funcionario, com id" . $user['Id_User'] . ", criou um novo bilhete', '$dataAtual', " . $user['Id_User'] . ", 'Criar bilhete')";
                } elseif ($tipoUserOn == 3) {
                    $sqlAlert .= " VALUES ('O administrador, com id" . $user['Id_User'] . ", criou um novo bilhete', '$dataAtual', " . $user['Id_User'] . ", 'Criar bilhete')";
                }
                if (mysqli_query($conn, $sqlAlert)) {
                    if ($result = mysqli_query($conn, $sql)) {
                        echo "Bilhete adicionado com sucesso";
                        header("Refresh: 2; url=../paginas/gestao_Bilhetes.php");
                        exit();
                    }
                }
            } else {
                echo "erro ao receber dados dados";
            }
        }
        ?>


        <h1>Bilhetes</h1>
        <div class='grid-container'>
            <?php
            $estado = "ativos";
            $sql = "SELECT b.*, v.*, r.*
            FROM bilhetes b
            INNER JOIN veiculo v ON b.id_veiculo = v.id_veiculo
            INNER JOIN rota r ON b.id_rota = r.id_rota
            WHERE estado_bilhete = 'ativo'";


            if (isset($_POST['bilheteExpirado'])) {
                $estado = "expirados";
                $sql = "SELECT b.*, v.*, r.*
                FROM bilhetes b
                INNER JOIN veiculo v ON b.id_veiculo = v.id_veiculo
                INNER JOIN rota r ON b.id_rota = r.id_rota
                WHERE estado_bilhete = 'Expirado'";
            } elseif (isset($_POST['bilheteCancelado'])) {
                $estado = "cancelados";
                $sql = "SELECT b.*, v.*, r.*
                FROM bilhetes b
                INNER JOIN veiculo v ON b.id_veiculo = v.id_veiculo
                INNER JOIN rota r ON b.id_rota = r.id_rota
                WHERE estado_bilhete = 'cancelado'";
            }




            if ($result = mysqli_query($conn, $sql)) {
                if (mysqli_num_rows($result) > 0) {
                    while ($bilhete = mysqli_fetch_assoc($result)) {

                        $dataAtual = date('Y-m-d');
                        $horaAtual = date('H:i:s');

                        //condiçao para apurar se ja passou a hora e dia do bilhete
                        if (
                            ($bilhete['estado_bilhete'] == 'Ativo') &&
                            (
                                ($bilhete['data'] < $dataAtual) ||
                                ($bilhete['data'] == $dataAtual && $bilhete['hora'] < $horaAtual)
                            )
                        ) {
                            $sql = "UPDATE bilhetes SET estado_bilhete = 'Expirado' WHERE id_bilhete = '" . $bilhete['id_bilhete'] . "'";
                            if ($result = mysqli_query($conn, $sql)) {
                                echo "O bilhete " . $bilhete['id_bilhete'] . " foi expirado!";
                                header("Refresh: 2; url=../paginas/gestao_bilhetes.php");
                                exit();
                            }
                        }

                        //para eliminar automaticamente apos 1 dia 
                        $dataParaEliminar = date('Y-m-d', strtotime($bilhete['data'] . ' +1 days'));
                        if ($bilhete['data'] >= $dataParaEliminar) {
                            $sql = "DELETE FROM bilhetes WHERE id_bilhete ='" . $bilhete['id_bilhete'] . "'";
                            if ($result = mysqli_query($conn, $sql)) {
                                echo "O bilhete " . $bilhete['id_bilhete'] . " foi removido automaticamente!";
                                header("Refresh: 2; url=../paginas/gestao_bilhetes.php");
                                exit();
                            }
                        }


                        echo "
                        <div class='grid-container-lado'>
                            <h1>Id bilhete: " . $bilhete['id_bilhete'] . "</h1>
                            <h2>Id Rota: " . $bilhete['id_rota'] . "</h2>
                            <h2>Id Veiculo: " . $bilhete['id_veiculo'] . "</h2>
                            <p>Data do bilhete " . $bilhete['data'] . "</p>
                            <p>Hora da bilhete " . $bilhete['hora'] . "</p>
                            <p>Estado do bilhete: " . $bilhete['estado_bilhete'] . "</p>
                            <p>De " . $bilhete['origem'] . " para " . $bilhete['destino'] . "</p>
                            <p>Lotação maxima de " . $bilhete['capacidade_veiculo'] . ", com total de "
                            . $bilhete['lugaresComprados'] . " lugares ja ocupados</p>
                            <p>Preço do bilhete: " . $bilhete['preco'] . "€
                            
                            <form method='POST'>";
                        if ($bilhete['estado_bilhete'] == "Ativo") {
                            echo "<button class='aceitar-btn' type='submit' name='editarbilhete' value='" . $bilhete['id_bilhete'] . "'>Editar bilhete</button>";
                        }
                        echo "<button class='recusar-btn' type='submit' name='eliminarbilhete' value='" . $bilhete['id_bilhete'] . "'>Eliminar bilhete</button>
                            </form>
                        </div>
                    ";
                    }
                } else {
                    echo "<p>Nao existem bilhetes " . $estado . "</p>";
                }
            }

            if (isset($_POST['editarbilhete'])) {
                $idbilhete = $_POST['editarbilhete'];
                $sql = "SELECT * FROM bilhetes where id_bilhete = $idbilhete";


                if ($result = mysqli_query($conn, $sql)) {
                    if (mysqli_num_rows($result) > 0) {
                        while ($bilhete = mysqli_fetch_assoc($result)) {
                            echo "
                            <div class='edit-form'>
                                <form method='POST' action=''>
                                    <h1>Alterar bilhete ID: " . $bilhete['id_bilhete'] . "</h1>
                                    <div>
                                        <input type='hidden' name='dataAntiga' value='" . $bilhete['data'] . "'>
                                        <label for='nomebilhete'>Nova data</label>
                                        <input type='date' name='novaData' value=''>
                                    </div> 
                                    <div>
                                        <input type='hidden' name='horaAntiga' value=" . $bilhete['hora'] . ">
                                        <label for='origembilhete'>Nova hora</label>
                                        <input type='time' name='novaHora' value=''>
                                    </div>  
                                    <div>
                                        <input type='hidden' name='lugaresCompradosAntigos' value=" . $bilhete['lugaresComprados'] . ">
                                        <label for='destinobilhete'>Nova quantidade de lugares comprados</label>
                                        <input type='text' name='novaQuantidadeLugares' value=''>
                                    </div> 
                                    <div>
                                        <input type='hidden' name='id_rotaAntiga' value=" . $bilhete['id_rota'] . ">
                                        <label for='destinobilhete'>Novo id de rota</label>
                                        <input type='text' name='novoIdrota' value=''>
                                    </div>
                                    <div>
                                        <input type='hidden' name='id_veiculoAntigo' value=" . $bilhete['id_veiculo'] . ">
                                        <label for='destinobilhete'>Novo id de veiculo</label>
                                        <input type='text' name='novoIdVeiculo' value=''>
                                    </div> 
                                    <input type='hidden' name='idbilhete' value=" . $bilhete['id_bilhete'] . ">
                                    <button class='aceitar-btn' type='submit' name='Alterbilhete'>Alterar bilhete</button>
                                    <button class='recusar-btn' type='submit' action ='../paginas/gestao_bilhetes.php'>Cancelar edição</button> 
                                </form>
                            </div>
                        ";
                        }
                        echo "</div>";
                    }
                } else {
                    echo 'nao foi possivel encotrar essa bilhete';
                }
            }
            if (isset($_POST['Alterbilhete'])) {
                // Garantir que pelo menos um campo foi preenchido
                if (empty($_POST['novaData']) && empty($_POST['novaHora']) && empty($_POST['novaQuantidadeLugares']) && empty($_POST['novoIdVeiculo']) && empty($_POST['novoIdrota'])) {
                    echo "Não colocou nenhum dado novo.";
                    exit();
                }
                if (!empty($_POST['novaHora'])) {
                    $novaHora = $_POST['novaHora'] . ":00";
                } else {
                    $novaHora = $_POST['horaAntiga'];
                }

                // Recupera os valores do formulário
                $idbilhete = $_POST['idbilhete'];
                $dataAntiga = $_POST['dataAntiga'];
                $horaAntiga = $_POST['horaAntiga'];
                $lugaresCompradosAntigos = $_POST['lugaresCompradosAntigos'];
                $id_veiculoAntigo = $_POST['id_veiculoAntigo'];
                $id_rotaAntiga = $_POST['id_rotaAntiga'];

                $novaData = $_POST['novaData'];
                $novaQuantidadeLugares = $_POST['novaQuantidadeLugares'];
                $novoIdVeiculo = $_POST['novoIdVeiculo'];
                $novoIdrota = $_POST['novoIdrota'];

                // Atualizar Data
                if ($dataAntiga !== $novaData && !empty($novaData)) {
                    $sql = "UPDATE bilhetes SET data = '$novaData' WHERE id_bilhete = '$idbilhete'";
                    if (mysqli_query($conn, $sql)) {
                        echo "Data bilhete atualizada com sucesso!<br>";
                    } else {
                        echo "Erro ao atualizar a data: " . mysqli_error($conn);
                    }
                }

                // Atualizar Hora
                if ($horaAntiga !== $novaHora && !empty($novaHora)) {
                    $sql = "UPDATE bilhetes SET hora = '$novaHora' WHERE id_bilhete = '$idbilhete'";
                    if (mysqli_query($conn, $sql)) {
                        echo "Hora atualizada com sucesso!<br>";
                    } else {
                        echo "Erro ao atualizar a hora: " . mysqli_error($conn);
                    }
                }

                // Atualizar Quantidade de Lugares
                if ($lugaresCompradosAntigos !== $novaQuantidadeLugares && !empty($novaQuantidadeLugares)) {
                    $sql = "UPDATE bilhetes SET lugaresComprados = '$novaQuantidadeLugares' WHERE id_bilhete = '$idbilhete'";
                    if (mysqli_query($conn, $sql)) {
                        echo "Quantidade de lugares atualizada com sucesso!<br>";
                    } else {
                        echo "Erro ao atualizar a quantidade de lugares: " . mysqli_error($conn);
                    }
                }

                // Verificar e Atualizar Id da Rota
                if ($id_rotaAntiga !== $novoIdrota && !empty($novoIdrota)) {
                    // Verifica se o novo ID de rota existe
                    $sql = "SELECT * FROM rota WHERE id_rota = '$novoIdrota'";
                    $result = mysqli_query($conn, $sql);
                    $row = mysqli_fetch_assoc($result);
                    if (mysqli_num_rows($result) == 0) {
                        echo "Não existe rota com esse ID!";
                    } else {
                        $precoPorKm = 0.1;
                        $novopreco = ($row['distancia'] * $precoPorKm);
                        $sql = "UPDATE bilhetes 
                            SET preco = '$novopreco', 
                                id_rota = '$novoIdrota' 
                            WHERE id_bilhete = '$idbilhete'";

                        if (mysqli_query($conn, $sql)) {
                            echo "ID da rota e preço atualizados com sucesso!<br>";
                        } else {
                            echo "Erro ao atualizar o ID da rota e o preço: " . mysqli_error($conn);
                        }
                    }
                }

                // Verificar e Atualizar Id do Veículo
                if ($id_veiculoAntigo !== $novoIdVeiculo && !empty($novoIdVeiculo)) {
                    // Verifica se o novo ID de veículo existe
                    $sql = "SELECT * FROM veiculo WHERE id_veiculo = '$novoIdVeiculo'";
                    $result = mysqli_query($conn, $sql);
                    if (mysqli_num_rows($result) == 0) {
                        echo "Não existe veículo com esse ID!";
                    } else {
                        $sql = "UPDATE bilhetes SET id_veiculo = '$novoIdVeiculo' WHERE id_bilhete = '$idbilhete'";
                        if (mysqli_query($conn, $sql)) {
                            echo "ID do veículo atualizado com sucesso!<br>";
                        } else {
                            echo "Erro ao atualizar o ID do veículo: " . mysqli_error($conn);
                        }
                    }
                }

                $dataAtual = date('Y-m-d');
                $user = $_SESSION['user'];
                $tipoUserOn = $user['TipoUser'];
                $idUser = $user['Id_User'];
                

                $sqlAlert = "INSERT INTO alertas (texto_alerta, data_emissao, id_remetentes, tipo) ";

                if ($tipoUserOn == 2) {
                    $sqlAlert .= "VALUES ('O funcionario, com id $idUser, alertou um bilhete com id $idbilhete', '$dataAtual', $idUser, 'alterar bilhete')";
                } elseif ($tipoUserOn == 3) {
                    $sqlAlert .= "VALUES ('O administrador, com id $idUser, alertou um bilhete com id $idbilhete', '$dataAtual', $idUser, 'alterar bilhete')";
                }

                if (mysqli_query($conn, $sqlAlert)) {
                    header("Refresh: 2; url=../paginas/gestao_bilhetes.php");
                    exit();
                }
            }





            if (isset($_POST['eliminarbilhete'])) {
                $eliminarbilhete = $_POST['eliminarbilhete'];
                echo '
            <form method="POST">
                <div>
                    <label for="ConfirmarCheckbox">
                        <input type="checkbox" name="ConfirmarEliminar" id="ConfirmarCheckbox" required>
                        Tenho e certeza que quero eliminar a bilhete com o id' . $eliminarbilhete . '
                    </label>
                    <br>
                    <button type="submit" name="ConfirmarEliminar" value="' . $eliminarbilhete . '">Eliminar bilhete </button>
                    
                </div>
            </form>
            ';
            }
            if (isset($_POST['ConfirmarEliminar'])) {

                $eliminarbilhete = $_POST['ConfirmarEliminar'];
                $sql = "DELETE FROM bilhetes WHERE id_bilhete = '$eliminarbilhete'";


                if (mysqli_query($conn, $sql)) {
                    //para criar um alerta 
                    $dataAtual = date('Y-m-d');
                    $user = $_SESSION['user'];
                    $tipoUserOn = $user['TipoUser'];
                    $sqlAlert = "INSERT INTO alertas (texto_alerta, data_emissao, id_remetentes, tipo) ";

                    if ($tipoUserOn == 2) {
                        $sqlAlert .= " VALUES ('O funcionario, com id" . $user['Id_User'] . ", eliminou um bilhete', '$dataAtual', " . $user['Id_User'] . ", 'Eliminar bilhete')";
                    } elseif ($tipoUserOn == 3) {
                        $sqlAlert .= " VALUES ('O administrador, com id" . $user['Id_User'] . ", eliminou um bilhete', '$dataAtual', " . $user['Id_User'] . ", 'Eliminar bilhete')";
                    }
                    if (mysqli_query($conn, $sqlAlert)) {
                        echo "<p>bilhete eliminado com sucesso! </p>";
                        header("Refresh: 2; url=../paginas/gestao_bilhetes.php");
                        exit();
                    }
                } else {
                    echo "Erro ao eliminar o usuário: " . mysqli_error($conn);
                }
            }
            ?>
        </div>
    </div>
    </div>


</html>