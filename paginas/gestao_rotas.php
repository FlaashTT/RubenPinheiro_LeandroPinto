<?php
include("../paginas/menu_layout.php");
include("../basedados/basedados.h");
require("../paginas/validar.php");
session_start();

validar_acesso([3, 2]);

function gestaoRotas($filtro = '')
{
    global $conn;
    $sql = "SELECT * FROM rota";



    // adiciona o filtro (se houver)
    if (!empty($filtro)) {
        $sql .= " WHERE nome_rota LIKE '%$filtro%' OR id_rota LIKE '%$filtro%' OR origem LIKE '%$filtro%'";
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

<div class="content-Alertas">
    <h1>Dashboard - Gestão de Rotas</h1>
    <h2>Visão geral de rotas</h2>
    <?php
    $userOn = $_SESSION['user'];
    $tipoUserOn = $user['TipoUser'];
    if ($tipoUserOn == 3) {
        echo '
        <form action="" method="POST">
            <button class="adicionarRota-btnQ type="submit" name="adicionarRota">Adicionar rota</button>
        </form>
        ';
    }

    // Verifica se o botão 'adicionarRota' foi pressionado
    if (isset($_POST['adicionarRota'])) {
        echo "
        <form method='POST' action=''>
            <div class='card'>
                <h1>Adicionar Rota</h1>
                <div>
                    <p>Nome da rota</p>
                    <input class='texto-Adicionar' type='text' name='nomeRota' placeholder='Digite o nome' >
                </div> 
                <div>
                    <p>Origem da rota</p>
                    <input class='texto-Adicionar' type='text' name='origemRota' placeholder='Digite o nome' >
                </div>  
                <div>
                    <p>Destino da rota</p>
                    <input class='texto-Adicionar' type='text' name='destinoRota' placeholder='Digite o nome' >
                </div> 
                <div>
                    <p>Distancia da rota(valor aproximado em Km)</p>
                    <input class='texto-Adicionar' type='text' name='distanciaRota' placeholder='Digite a distancia' >
                </div> 
                <button class='aceitar-btn' type='submit' name='ConfirmarAddRota'>Criar Rota</button>
                <button class='recusar-btn' type='submit' action ='../paginas/gestao_rotas.php'>Cancelar </button> 
            </div>
        </form>";
    }

    if (isset($_POST['ConfirmarAddRota'])) {
        if (isset($_POST['nomeRota']) && !empty($_POST['nomeRota']) && isset($_POST['origemRota']) && !empty($_POST['origemRota']) && isset($_POST['destinoRota']) && !empty($_POST['destinoRota']) && isset($_POST['distanciaRota']) && !empty($_POST['distanciaRota'])) {
            $nomeRota = $_POST['nomeRota'];
            $origemRota = $_POST['origemRota'];
            $destinoRota = $_POST['destinoRota'];
            $distancia = $_POST['distanciaRota'];

            $sql = "SELECT * FROM rota WHERE origem ='$origemRota' AND destino ='$destinoRota'";

            if ($result = mysqli_query($conn, $sql)) {
                if (mysqli_num_rows($result) > 0) {
                    echo "ja existe uma rota com essas especificações";
                    header("Refresh: 2; url=../paginas/gestao_rotas.php");
                } else {
                    $sql = "INSERT INTO rota(nome_rota,origem,destino,distancia) values ('$nomeRota', '$origemRota', '$destinoRota','$distancia')";

                    if ($result = mysqli_query($conn, $sql)) {
                        //para criar um alerta
                        $dataAtual = date('Y-m-d');
                        $user = $_SESSION['user'];
                        $tipoUser = $user['TipoUser'];
                        $sqlAlert = "INSERT INTO alertas (texto_alerta, data_emissao, id_remetentes, tipo) 
                         VALUES ('O administrador, com id" . $user['Id_User'] . ", criou um nova rota', '$dataAtual', " . $user['Id_User'] . ", 'Criar rota')";

                        if (mysqli_query($conn, $sqlAlert)) {
                            echo "rota adicionada com sucesso";
                            header("Refresh: 2; url=../paginas/gestao_rotas.php");
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


    <h1>Rotas ativas</h1>
    <!-- Formulário de Filtro -->
    <form method="POST" action="">
        <input class="filtro" type="text" name="filtro" value="<?php echo htmlspecialchars($filtro); ?>" placeholder="Filtrar por Nome_Rota, Id_Rota ou Origem">
        <button type="submit" class="filtrar-btn">Filtrar</button>
        <button type="submit" name="reset" value="true" class="limpar-btn">Limpar Filtro</button>
    </form>

    <div class='grid-container'>
        <?php
        $sql = "SELECT * FROM rota";



        $rotas = gestaoRotas($filtro);
        if (mysqli_num_rows($rotas) > 0) {
            $userOn = $_SESSION['user'];
            $tipoUserOn = $userOn['TipoUser'];
            while ($rota = mysqli_fetch_assoc($rotas)) {
                echo "
            <div class='grid-container-lado'>
                <h1>id rota: " . $rota['id_rota'] . "</h1>
                <p>Nome da rota " . $rota['nome_rota'] . "</p>
                <p>Origem da rota " . $rota['origem'] . "</p>
                <p>Destino da rota " . $rota['destino'] . "</p>
                <p>Distancia " . $rota['distancia'] . " km</p>
        ";

                if ($tipoUserOn == 3) {
                    echo "
                    <form method='POST'>
                        <button class='aceitar-btn' type='submit' name='editarRota' value='" . $rota['id_rota'] . "'>Editar Rota</button>
                        <button class='recusar-btn' type='submit' name='eliminarRota' value='" . $rota['id_rota'] . "'>Eliminar Rota</button>
                    </form>
            ";
                }
                echo "</div>";
            }
        } else {
            echo "<p>Não existem rotas</p>";
        }
        if (isset($_POST['editarRota'])) {
            $idRota = $_POST['editarRota'];
            $sql = "SELECT * FROM rota where id_rota = $idRota";


            if ($result = mysqli_query($conn, $sql)) {
                if (mysqli_num_rows($result) > 0) {
                    while ($rota = mysqli_fetch_assoc($result)) {
                        echo "
                            <div class='edit-form'>
                                <form method='POST' action=''>
                                    <h1>Alterar Rota ID: " . $rota['id_rota'] . "</h1>
                                    <div>
                                        <input type='hidden' name='nomeAntigo' value='" . $rota['nome_rota'] . "'>
                                        <label for='nomeRota'>Novo Nome</label>
                                        <input type='text' name='novoNomeRota' value=''>
                                    </div> 
                                    <div>
                                        <input type='hidden' name='origemAntiga' value=" . $rota['origem'] . ">
                                        <label for='origemRota'>Nova Origem</label>
                                        <input type='text' name='novaOrigemRota' value=''>
                                    </div>  
                                    <div>
                                        <input type='hidden' name='destinoAntigo' value=" . $rota['destino'] . ">
                                        <label for='destinoRota'>Novo Destino</label>
                                        <input type='text' name='novoDestinoRota' value=''>
                                    </div> 
                                    <div>
                                        <input type='hidden' name='distanciaAntiga' value=" . $rota['distancia'] . ">
                                        <label for='destinoRota'>Novo Destino</label>
                                        <input type='text' name='novaDistancia' value=''>
                                    </div> 
                                    <input type='hidden' name='idRota' value=" . $rota['id_rota'] . ">
                                    <button class='aceitar-btn' type='submit' name='ConfirmarAlterRota'>Alterar Rota</button>
                                    <button class='recusar-btn' type='submit' action ='../paginas/gestao_rotas.php'>Cancelar edição</button> 
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
        if (isset($_POST['ConfirmarAlterRota'])) {
            if ($_POST['novoNomeRota'] === "" && $_POST['novaOrigemRota'] === "" && $_POST['novoDestinoRota'] === "" && $_POST['novaDistancia'] === "") {
                echo "nao colocou dados novos nenhum";
            } else {
                $idRota = $_POST['idRota'];
                $nomeAntigo = $_POST['nomeAntigo'];
                $origemAntiga = $_POST['origemAntiga'];
                $destinoAntigo = $_POST['destinoAntigo'];
                $distanciaAntiga = $_POST['distanciaAntiga'];

                $novoNomeRota = $_POST['novoNomeRota'];
                $novaOrigemRota = $_POST['novaOrigemRota'];
                $novoDestinoRota = $_POST['novoDestinoRota'];
                $novaDistancia = $_POST['novaDistancia'];

                if ($novoNomeRota != "" && $novoNomeRota !== $nomeAntigo) {
                    $sql = "UPDATE rota SET nome_rota = '$novoNomeRota' WHERE id_rota = '$idRota'";
                    if (mysqli_query($conn, $sql)) {
                        echo "Nome atualizado com sucesso!<br>";
                    } else {
                        echo "Erro ao atualizar o nome: " . mysqli_error($conn);
                    }
                } elseif ($novoNomeRota === $nomeAntigo) {
                    echo "Novo nome igual ao anterior<br>";
                }

                if ($novaOrigemRota != "" && $novaOrigemRota !== $origemAntiga) {
                    $sql = "UPDATE rota SET origem = '$novaOrigemRota' WHERE id_rota = '$idRota'";
                    if (mysqli_query($conn, $sql)) {
                        echo "Origem atualizada com sucesso! <br>";
                    } else {
                        echo "Erro ao atualizar a origem: " . mysqli_error($conn);
                    }
                } elseif ($novaOrigemRota === $origemAntiga) {
                    echo "Nova origem igual á anterior<br>";
                }

                if ($novoDestinoRota != "" && $novoDestinoRota !== $destinoAntigo) {
                    $sql = "UPDATE rota SET destino = '$novoDestinoRota' WHERE id_rota = '$idRota'";
                    if (mysqli_query($conn, $sql)) {
                        echo "Destino atualizado com sucesso!<br>";
                    } else {
                        echo "Erro ao atualizar o destino: " . mysqli_error($conn);
                    }
                } elseif ($novoDestinoRota === $destinoAntigo) {
                    echo "Novo destino igual ao anterior<br>";
                }

                if ($novaDistancia != "" && $novaDistancia !== $distanciaAntiga) {
                    $sql = "UPDATE rota SET distancia = '$novaDistancia' WHERE id_rota = '$idRota'";
                    if (mysqli_query($conn, $sql)) {
                        echo "Distancia atualizada com sucesso!<br>";
                    } else {
                        echo "Erro ao atualizar o nome: " . mysqli_error($conn);
                    }
                } elseif ($novaDistancia === $distanciaAntiga) {
                    echo "Distancia igual á anterior<br>";
                }

                //para criar um alerta
                $dataAtual = date('Y-m-d');
                $user =$_SESSION['user'];
                $tipoUser = $user['TipoUser'];
                $sqlAlert = "INSERT INTO alertas (texto_alerta, data_emissao, id_remetentes, tipo) 
                 VALUES ('O administrador, com id" . $user['Id_User'] . ", alterou uma rota', '$dataAtual', " . $user['Id_User'] . ", 'alterar rota')";

                if (mysqli_query($conn, $sqlAlert)) {
                    header("Refresh: 2; url=../paginas/gestao_rotas.php");
                }
            }
        }


        if (isset($_POST['eliminarRota'])) {
            $eliminarRota = $_POST['eliminarRota'];
            echo '
            <form method="POST">
                <div>
                    <label for="ConfirmarCheckbox">
                        <input type="checkbox" name="ConfirmarEliminar" id="ConfirmarCheckbox" required>
                        Tenho e certeza que quero eliminar a rota com o id' . $eliminarRota . '
                    </label>
                    <br>
                    <button type="submit" name="ConfirmarEliminar" value="' . $eliminarRota . '">Eliminar rota </button>
                    
                </div>
            </form>
            ';
        }
        if (isset($_POST['ConfirmarEliminar'])) {

            $eliminarRota = $_POST['ConfirmarEliminar'];
            $sql = "DELETE FROM rota WHERE id_rota = '$eliminarRota'";


            if (mysqli_query($conn, $sql)) {
                //para criar um alerta
                $dataAtual = date('Y-m-d');
                $user = $_SESSION['user'];
                $tipoUser = $user['TipoUser'];
                $sqlAlert = "INSERT INTO alertas (texto_alerta, data_emissao, id_remetentes, tipo) 
                 VALUES ('O administrador, com id" . $user['Id_User'] . ", eliminou uma rota', '$dataAtual', " . $user['Id_User'] . ", 'Elim rota')";

                if (mysqli_query($conn, $sqlAlert)) {
                    echo "<p>Rota eliminado com sucesso! </p>";
                    header("Refresh: 2; url=../paginas/gestao_rotas.php");
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