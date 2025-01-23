<?php
include("../basedados/basedados.h");
include("../paginas/menu_layout.php");
include("../paginas/validar.php");
session_start();

validar_acesso([3, 2]);
?>

<!DOCTYPE html>
<html lang="pt-pt">

<body>
    <!-- Conteúdo Principal -->
    <div class="content-Pedidos">
        <h1>Dashboard - Gestão de veiculos</h1>
        <h2>Visão geral dos veiculos</h2>

        <form action="" method="POST">
            <button class='adicionarRota-btn' type="submit" name="adicionarRota">Adicionar veiculo</button>
        </form>


        <?php
        // Verifica se o botão 'adicionarRota' foi pressionado
        if (isset($_POST['adicionarRota'])) {
            echo "
        <form method='POST' action=''>
            <div class='card'>
                <h1>Adicionar Veiculo</h1>
                <div>
                    <p>Nome  do veiculo</p>
                    <input class='texto-Adicionar' type='text' name='nomeVeiculo' placeholder='Digite o nome' >
                </div> 
                <div>
                    <p>Capacidade do veiculo</p>
                    <input class='texto-Adicionar' type='text' name='capacidade' placeholder='Digite a capacidade ' >
                </div>  
                <div>
                    <p>Matricula do veiculo</p>
                    <input class='texto-Adicionar' type='text' name='matricula' placeholder='Digite a matricula ' >
                </div> 
                <button class='aceitar-btn' type='submit' name='ConfirmarAddveiculo'>adicionar veiculo</button>
                <button class='recusar-btn' type='submit' action ='../paginas/veiculos.php'>Cancelar </button> 
            </div>
        </form>";
        }

        if (isset($_POST['ConfirmarAddveiculo'])) {
            if (isset($_POST['nomeVeiculo']) && !empty($_POST['nomeVeiculo']) && isset($_POST['capacidade']) && !empty($_POST['capacidade']) && isset($_POST['matricula']) && !empty($_POST['matricula'])) {
                $nomeVeiculo = $_POST['nomeVeiculo'];
                $capacidade = $_POST['capacidade'];
                $matricula = $_POST['matricula'];

                $sql = "SELECT * FROM veiculo WHERE nome_veiculo ='$nomeVeiculo' AND matricula ='$matricula'";

                if ($result = mysqli_query($conn, $sql)) {
                    if (mysqli_num_rows($result) > 0) {
                        echo "ja existe um veiculo com essas especificações";
                        header("Refresh: 2; url=../paginas/veiculos.php");
                    } else {
                        $sql = "INSERT INTO veiculo(nome_veiculo,capacidade_veiculo,matricula) values ('$nomeVeiculo', '$capacidade', '$matricula')";

                        if ($result = mysqli_query($conn, $sql)) {


                            //para criar um alerta
                            $dataAtual = date('Y-m-d');
                            $user = $_SESSION['user'];
                            $tipoUser = $user['TipoUser'];
                            $sqlAlert = "INSERT INTO alertas (texto_alerta, data_emissao, id_remetentes, tipo) ";

                            if ($tipoUserOn == 2) {
                                $sqlAlert .= " VALUES ('O funcionario, com id" . $user['Id_User'] . ", criou um novo veiculo com matricula:" . $matricula . "', '$dataAtual', " . $user['Id_User'] . ", 'Criar veiculo')";
                            } elseif ($tipoUserOn == 3) {
                                $sqlAlert .= " VALUES ('O administrador, com id" . $user['Id_User'] . ", criou um novo veiculo com matricula:" . $matricula . "', '$dataAtual', " . $user['Id_User'] . ", 'Criar veiculo')";
                            }
                            if (mysqli_query($conn, $sqlAlert)) {
                                echo "veiculo adicionado com sucesso";
                                header("Refresh: 2; url=../paginas/veiculos.php");
                            }
                        }
                    }
                } else {
                    echo "erro ao fazer o select";
                }
            } else {
                echo "Tem de preencher todos os campos";
            }
        }

        ?>


        <h1>Veiculos </h1>
        <div class='grid-container'>
            <?php
            $sql = "SELECT * FROM veiculo";


            if ($result = mysqli_query($conn, $sql)) {
                if (mysqli_num_rows($result) > 0) {
                    while ($veiculo = mysqli_fetch_assoc($result)) {
                        echo "
                        <div class='grid-container-lado'>
                            <h1>id veiculo: " . $veiculo['id_veiculo'] . "</h1>
                            <p>Nome do veiculo " . $veiculo['nome_veiculo'] . "</p>
                            <p>Capacidade do veiculo " . $veiculo['capacidade_veiculo'] . "</p>
                            <p>Matricula do veiculo " . $veiculo['matricula'] . "</p>
                            <form method='POST'>
                                <button class='aceitar-btn' type='submit' name='editarVeiculo' value='" . $veiculo['id_veiculo'] . "'>Editar dados do veiculo</button>
                                ";
                        $user = $_SESSION['user'];
                        $tipoUser = $user['TipoUser'];
                        if ($tipoUser == 3) {
                            echo "<button class='recusar-btn' type='submit' name='eliminarVeiculo' value='" . $veiculo['id_veiculo'] . "'>Eliminar dados do veiculo</button>";
                        } else {
                            echo "
                                    </form>
                                </div>
                            ";
                        }
                    }
                } else {
                    echo "<p>Nao existem veiculos disponiveis</p>";
                }
            }

            if (isset($_POST['editarVeiculo'])) {
                $idVeiculo = $_POST['editarVeiculo'];
                $sql = "SELECT * FROM veiculo where id_veiculo = $idVeiculo";


                if ($result = mysqli_query($conn, $sql)) {
                    if (mysqli_num_rows($result) > 0) {
                        while ($veiculo = mysqli_fetch_assoc($result)) {
                            echo "
                            <div class='edit-form'>
                                <form method='POST' action=''>
                                    <h1>Alterar veiculo ID: " . $veiculo['id_veiculo'] . "</h1>
                                    <div>
                                        <input type='hidden' name='nomeAntigo' value='" . $veiculo['nome_veiculo'] . "'>
                                        <label for='novoNomeVeiculo'>Novo Nome</label>
                                        <input type='text' name='novoNomeVeiculo' value=''>
                                    </div> 
                                    <div>
                                        <input type='hidden' name='capacidadeAntiga' value=" . $veiculo['capacidade_veiculo'] . ">
                                        <label for='novaCapacidade'>Nova Capacidade</label>
                                        <input type='text' name='novaCapacidade' value=''>
                                    </div>  
                                    <div>
                                        <input type='hidden' name='matriculaAntiga' value=" . $veiculo['matricula'] . ">
                                        <label for='novaMatricula'>Nova matricula</label>
                                        <input type='text' name='novaMatricula' value=''>
                                    </div> 
                                    <input type='hidden' name='idveiculo' value=" . $veiculo['id_veiculo'] . ">
                                    <button class='aceitar-btn' type='submit' name='ConfirmarEditarVeiculo'>Alterar veiculo</button>
                                    <button class='recusar-btn' type='submit' action ='../paginas/veiculos.php'>Cancelar edição</button> 
                                </form>
                            </div>
                        ";
                        }
                        echo "</div>";
                    }
                } else {
                    echo 'nao foi possivel encotrar essa rota';
                }
            }
            if (isset($_POST['ConfirmarEditarVeiculo'])) {
                if ($_POST['nomeAntigo'] === "" && $_POST['capacidadeAntiga'] === "" && $_POST['matriculaAntiga'] === "") {
                    echo "nao colocou dados novos nenhum";
                } else {
                    $idVeiculo = $_POST['idveiculo'];
                    $nomeAntigo = $_POST['nomeAntigo'];
                    $capacidadeAntiga = $_POST['capacidadeAntiga'];
                    $matriculaAntiga = $_POST['matriculaAntiga'];

                    $novoNomeVeiculo = $_POST['novoNomeVeiculo'];
                    $novaCapacidade = $_POST['novaCapacidade'];
                    $novaMatricula = $_POST['novaMatricula'];

                    if ($novoNomeVeiculo != "" && $novoNomeVeiculo !== $nomeAntigo) {
                        $sql = "UPDATE veiculo SET nome_veiculo = '$novoNomeVeiculo' WHERE id_veiculo = '$idVeiculo'";
                        if (mysqli_query($conn, $sql)) {
                            echo "Nome atualizado com sucesso!<br>";
                        } else {
                            echo "Erro ao atualizar o nome: " . mysqli_error($conn);
                        }
                    } else {
                        echo "Novo nome igual ao anterior<br>";
                    }

                    if ($novaCapacidade != "" && $novaCapacidade !== $capacidadeAntiga) {
                        $sql = "UPDATE veiculo SET capacidade_veiculo = '$novaCapacidade' WHERE id_veiculo = '$idVeiculo'";
                        if (mysqli_query($conn, $sql)) {
                            echo "Origem atualizada com sucesso! <br>";
                        } else {
                            echo "Erro ao atualizar a origem: " . mysqli_error($conn);
                        }
                    } else {
                        echo "Nova origem igual á anterior<br>";
                    }

                    if ($novaMatricula != "" && $novaMatricula !== $matriculaAntiga) {
                        $sql = "UPDATE veiculo SET matricula = '$novaMatricula' WHERE id_veiculo = '$idVeiculo'";
                        if (mysqli_query($conn, $sql)) {
                            echo "Destino atualizado com sucesso!<br>";
                        } else {
                            echo "Erro ao atualizar o destino: " . mysqli_error($conn);
                        }
                    } else {
                        echo "Novo destino igual ao anterior<br>";
                    }

                    //para criar um alerta
                    $dataAtual = date('Y-m-d');
                    $user = $_SESSION['user'];
                    $tipoUser = $user['TipoUser'];
                    $sqlAlert = "INSERT INTO alertas (texto_alerta, data_emissao, id_remetentes, tipo) ";

                    if ($tipoUserOn == 2) {
                        $sqlAlert .= " VALUES ('O funcionario, com id" . $user['Id_User'] . ", alterou dados do veiculo com matricula:" . $matricula . "', '$dataAtual', " . $user['Id_User'] . ", 'Alterar dados de veiculo')";
                    } elseif ($tipoUserOn == 3) {
                        $sqlAlert .= " VALUES ('O administrador, com id" . $user['Id_User'] . ", alterou dados do veiculo com matricula:" . $matricula . "', '$dataAtual', " . $user['Id_User'] . ", 'Alterar dados de veiculo')";
                    }
                    mysqli_query($conn, $sqlAlert);
                }

                header("Refresh: 2; url=../paginas/veiculos.php");
            }



            //
            if (isset($_POST['eliminarVeiculo'])) {
                $eliminarVeiculo = $_POST['eliminarVeiculo'];
                echo '
            <form method="POST">
                <div>
                    <label for="ConfirmarCheckbox">
                        <input type="checkbox" name="ConfirmarEliminar" id="ConfirmarCheckbox" required>
                        Tenho e certeza que quero eliminar o veiculo com o id' . $eliminarVeiculo . '
                    </label>
                    <br>
                    <button type="submit" name="ConfirmarEliminar" value="' . $eliminarVeiculo . '">Eliminar </button>
                </div>
            </form>
            ';
            }
            if (isset($_POST['ConfirmarEliminar'])) {

                $eliminarVeiculo = $_POST['ConfirmarEliminar'];
                $sql = "DELETE FROM veiculo WHERE id_veiculo = '$eliminarVeiculo'";


                //para criar um alerta
                $dataAtual = date('Y-m-d');
                $user = $_SESSION['user'];
                $tipoUser = $user['TipoUser'];
                $sqlAlert = "INSERT INTO alertas (texto_alerta, data_emissao, id_remetentes, tipo) 
                        VALUES ('O administrador, com id" . $user['Id_User'] . ", eliminou um veiculo', '$dataAtual', " . $user['Id_User'] . ", 'Eliminar veiculo')";

                if (mysqli_query($conn, $sqlAlert)) {
                    if (mysqli_query($conn, $sql)) {
                        echo "<p>Veiculo eliminado com sucesso! </p>";
                        header("Refresh: 2; url=../paginas/veiculos.php");
                    } else {
                        echo "Erro ao eliminar o usuário: " . mysqli_error($conn);
                    }
                }
            }


            ?>
        </div>
    </div>


</body>

</html>