<html>


<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f5f5f5;
        margin: 0;
        padding: 30px;
    }

    p {
        font-size: 16px;
        color: #333;
        margin: 10px 0;
        padding: 10px;
        border: 2px solid black;
        border-radius: 5px;
    }

    a {
        text-decoration: none;
        color: #007BFF;
        font-weight: bold;
    }

    a:hover {
        text-decoration: underline;
    }

    form div {
        margin-bottom: 15px;
    }

    label {
        font-weight: bold;
    }

    input,
    select,
    button {
        margin-top: 5px;
        display: block;
        padding: 10px;
        font-size: 14px;
    }

    input[type="checkbox"] {
        display: inline;
        margin-right: 5px;
        /* Espaçamento entre a checkbox e o texto */
    }

    button {
        background-color: #28a745;
        color: #fff;
        border: none;
        cursor: pointer;
    }

    button:hover {
        background-color: #218838;
    }

    .error {
        color: red;
        font-weight: bold;
    }

    .botoes-Carteira {
        display: flex;
        justify-content: flex-start;
        gap: 10px;
    }

    .opcao-btn {
        display: inline-block;
    }
</style>

</html>

<?php
session_start();
include("../basedados/basedados.h");
require("../paginas/validar.php");

validar_acesso([3, 2]);

$user = $_SESSION['user'];
if ($user['TipoUser'] == 1) {
    echo '
    <div style="text-align: left; margin-bottom: 5px;">
        <a href="../paginas/perfil.php">Cancelar</a><br>
    </div>
    ';
} else {
    echo '
    <div style="text-align: left; margin-bottom: 5px;">
        <a href="../paginas/gestao_utilizadores.php">Cancelar</a><br>
    </div>
    ';
}



if (isset($_GET['editarConta'])) {
    $user_email = $_GET['editarConta'];
    echo "<h1>Alterar Dados</h1>";
    echo "<p>Email do utilizador: " . $user_email . "</p>";


    $sql = "SELECT * FROM users WHERE Email = '$user_email'";


    if ($result = mysqli_query($conn, $sql)) {
        if (mysqli_num_rows($result) > 0) {
            while ($user = mysqli_fetch_assoc($result)) {
                echo "
                <form method='POST' action=''>
                    <div>
                        <p>Id do utilizador: " . $user['Id_User'] . "</p>
                        <p>Estado do utilizador: " . $user['Estado'] . "</p>
                        <p>
                            <input type='hidden' name='nomeAntigo' value='" . $user['Nome'] . "'>
                            Nome do utilizador: " . $user['Nome'] . "  
                            <input type='text' name='novoNome' placeholder='Digite o novo nome' value=''>
                        </p>
                        <p>
                            <input type='hidden' name='emailAntigo' value='" . $user['Email'] . "'>
                            Email do utilizador: " . $user['Email'] . "
                            <input type='text' name='novoEmail' placeholder='Digite o novo Email' value=''>
                        </p>
                        <p>
                            <input type='hidden' name='autenticacaoAntiga' value='" . $user['Autenticacao'] . "'>
                            Estado de autenticação do utilizador: " . $user['Autenticacao'] . " 
                                <select id='alterarTipo' name='alterarTipo'>
                                    <option value='' selected disabled>Selecione uma opção</option>
                                    <option value='Aceitar'>Aceitar utilizador</option>
                                    <option value='Pendente'>Colocar Pendente</option>
                                    <option value='Rejeitar'>Rejeitar utilizador</option>
                                </select>
                        </p>
                        <input type='hidden' name='user_id' value='" . $user['Id_User'] . "'>
                        <button type='submit' name='alterarDados'>Alterar Autenticação</button>
                    </div>
                </form>";
            }
        } else {
            echo "<p>Nenhum utilizador encontrado.</p>";
        }
    } else {
        echo "<p>Erro na consulta: " . mysqli_error($conn) . "</p>";
    }
}

if (isset($_POST['alterarDados'])) {
    if ($_POST['novoNome'] === "" && $_POST['novoEmail'] === "" && !isset($_POST['alterarTipo'])) {
        echo "nao colocou dados novos nenhum";
    } else {
        $user_id = $_POST['user_id'];
        $nomeAntigo = $_POST['nomeAntigo'];
        $novoNome = $_POST['novoNome'];
        $emailAntigo = $_POST['emailAntigo'];
        $novoemail = $_POST['novoEmail'];
        $autenticacaoAntiga = $_POST['autenticacaoAntiga'];
        $novoTipo = $_POST['alterarTipo'];


        if ($novoNome != "" && $novoNome !== $nomeAntigo) {
            $sql = "UPDATE users SET Nome = '$novoNome' WHERE Id_User = '$user_id'";
            if (mysqli_query($conn, $sql)) {
                echo "Nome atualizado com sucesso!<br>";
                header("Refresh: 2; url=../paginas/gestao_utilizadores.php");
            } else {
                echo "Erro ao atualizar o nome: " . mysqli_error($conn);
            }
        } else {
            echo "Novo nome igual ao anterior<br>";
        }

        if ($novoemail != "" && $novoemail !== $emailAntigo) {
            $sql = "UPDATE users SET Email = '$novoemail' WHERE Id_User = '$user_id'";
            if (mysqli_query($conn, $sql)) {
                echo "Email atualizado com sucesso!<br>";
                header("Refresh: 2; url=../paginas/gestao_utilizadores.php");
            } else {
                echo "Erro ao atualizar o nome: " . mysqli_error($conn);
            }
        } else {
            echo "Nova origem igual á anterior<br>";
        }

        if ($novoTipo != "" && $novoTipo !== $autenticacaoAntiga) {
            $sql = "UPDATE users SET Autenticacao = '$novoTipo' WHERE Id_User = '$user_id'";
            if (mysqli_query($conn, $sql)) {
                echo "Autenticaçao atualizada com sucesso!<br>";
                header("Refresh: 2; url=../paginas/gestao_utilizadores.php");
            } else {
                echo "Erro ao atualizar o nome: " . mysqli_error($conn);
            }
        } else {
            echo "Novo destino igual ao anterior<br>";
        }
    }
    //para criar um alerta
    $dataAtual = date('Y-m-d');
    $user = $_SESSION['user'];
    $tipoUser = $user['TipoUser'];
    $sqlAlert = "INSERT INTO alertas (texto_alerta, data_emissao, id_remetentes, tipo) 
     VALUES ('O administrador, com id" . $user['Id_User'] . ", aletrou dados do cliente Id: $user_id', '$dataAtual', " . $user['Id_User'] . ", 'Alterar Dados')";

    if (mysqli_query($conn, $sqlAlert)) {
        header("Refresh: 2; url=../paginas/gestao_utilizadores.php");
    }
}


if (isset($_GET['gestaoCarteira']) || isset($_GET['adicionarSaldo']) || isset($_GET['levantarSaldo'])) {
    if (isset($_GET['gestaoCarteira'])) {
        $AdicionarEmail = $_GET['gestaoCarteira'];
    } elseif (isset($_GET['adicionarSaldo'])) {
        $AdicionarEmail = $_GET['adicionarSaldo'];
    } elseif (isset($_GET['levantarSaldo'])) {
        $AdicionarEmail = $_GET['levantarSaldo'];
    }

    // Mostra as opções de carteira
    echo "
    <h2>Gestão de carteira</h2>
    <p>Selecione a opção:</p>
    <form>
        <div class='botoes-inline'>
            <button class='opcao-btn' type='submit' name='adicionarSaldo' value='$AdicionarEmail'>Adicionar Saldo</button>
            <button class='opcao-btn' type='submit' name='levantarSaldo' value='$AdicionarEmail'>Levantar Saldo</button>
        </div>
    </form>
    ";
}

// Mostra o formulário para adicionar saldo quando clicado
if (isset($_GET['adicionarSaldo'])) {
    $AdicionarEmail = $_GET['adicionarSaldo'];

    echo "
    <h2>Adicionar Saldo</h2>
    <form method='POST' action='../paginas/acoes.php?confirmarAddSaldo=1'> 
        <label>
            <input type='checkbox' name='addSaldo[]' value='5'> 5 &euro;<br>
            <input type='checkbox' name='addSaldo[]' value='10'> 10 &euro;<br>
            <input type='checkbox' name='addSaldo[]' value='20'> 20 &euro;<br><br>
        </label>
        <input type='checkbox' name='addSaldo[]' value='OutraOp'>
        <label for='OutraOp'>Adicionar outra opção:</label><br>
        <input type='number' name='valorOutraOpcao' min='1' step='1'><br><br>
        <button class='eliminar-btn' type='submit'>Confirmar</button>
    </form>
    ";
}

if (isset($_GET['confirmarAddSaldo'])) {
    $user = $_SESSION['user'];
    $AdicionarEmail = $user['Email'];

    // Processa a adição de saldo após a confirmação
    if (isset($_POST["addSaldo"]) && is_array($_POST["addSaldo"])) {
        $opcoes = $_POST["addSaldo"];
    } else {
        $opcoes = [];
    }

    // Verifica se nenhuma opção foi selecionada
    if (count($opcoes) === 0) {
        echo "ERRO! Tem de adicionar uma quantidade que deseja adicionar.";
        header("Refresh: 2; url=?gestaoCarteira=$AdicionarEmail");
        exit();
    } elseif (count($opcoes) > 1) {
        echo "ERRO! Apenas uma opção pode ser selecionada.";
        header("Refresh: 2; url=?gestaoCarteira=$AdicionarEmail");
        exit();
    }

    //se Apenas uma opção está marcada, obtém o valor
    $opcao = $opcoes[0];

    $xxx  = 0;

    switch ($opcao) {
        case "5":
        case "10":
        case "20":
            $valorToUpdate = $opcao + 0;


            //para criar um alerta
            $dataAtual = date('Y-m-d');
            $sqlAlert = "INSERT INTO alertas (texto_alerta, data_emissao, id_remetentes, tipo) 
                VALUES ('Adicionou " . $valorToUpdate . "€ á sua carteira', '$dataAtual', " . $user['Id_User'] . ", 'Adicionar saldo')";


            if (mysqli_query($conn, $sqlAlert)) {
                $sql = "SELECT * FROM users WHERE Email = '$AdicionarEmail'";
                $result = mysqli_query($conn, $sql);

                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $proxSaldo = $row['Saldo'] + $valorToUpdate;
                        $update_sql = "UPDATE users SET Saldo = '$proxSaldo' WHERE Email = '$AdicionarEmail'";
                        $update_result = mysqli_query($conn, $update_sql);
                    }
                }
            }
            echo ("Adicionou $valorToUpdate à carteira do utilizador com email $AdicionarEmail");
            header("Refresh: 2; url=?gestaoCarteira=$AdicionarEmail");
            break;

        case "OutraOp":
            if (!isset($_POST["valorOutraOpcao"]) || $_POST["valorOutraOpcao"] === "") {
                echo ("Tem de inserir o valor");
                header("Refresh: 2; url=?gestaoCarteira=$AdicionarEmail");
                exit();
            }

            $valorToUpdate = $_POST["valorOutraOpcao"] + 0;



            //para criar um alerta
            $dataAtual = date('Y-m-d');
            $sqlAlert = "INSERT INTO alertas (texto_alerta, data_emissao, id_remetentes, tipo) 
                VALUES ('Adicionou " . $valorToUpdate . "€ á sua carteira', '$dataAtual', " . $user['Id_User'] . ", 'Adicionar saldo')";


            if (mysqli_query($conn, $sqlAlert)) {

                $sql = "SELECT * FROM users WHERE Email = '$AdicionarEmail'";
                $result = mysqli_query($conn, $sql);
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $proxSaldo = $row['Saldo'] + $valorToUpdate;
                        $update_sql = "UPDATE users SET Saldo = '$proxSaldo' WHERE Email = '$AdicionarEmail'";
                        $update_result = mysqli_query($conn, $update_sql);
                    }
                }
            }
            echo ("Adicionou $valorToUpdate à carteira do utilizador com email $AdicionarEmail");
            header("Refresh: 2; url=?gestaoCarteira=$AdicionarEmail");
            break;

        default:
            echo "Erro: Opção inválida.";
            header("Refresh: 2; url=?gestaoCarteira=$AdicionarEmail");
            exit();
    }
}



if (isset($_GET['levantarSaldo'])) {
    $AdicionarEmail = $_GET['levantarSaldo'];

    echo "
    <h3>Levantar Dinheiro</h3>
    <form method='POST' action='../paginas/acoes.php?confirmarLevantar=1'>
        <label for='valorLevantar'>Quantidade a levantar:</label>
        <input type='number' name='valorLevantar' min='1' step='1' placeholder='Valor a levantar'><br><br>
        <button type='submit'>Confirmar</button>
    </form>
    ";
}


if (isset($_GET['confirmarLevantar'])) {
    $user = $_SESSION['user'];
    $AdicionarEmail = $user['Email'];

    if (!isset($_POST['valorLevantar']) || $_POST['valorLevantar'] <= 0) {
        echo "ERRO! Por favor, insere um valor válido para levantar.";
        exit();
    }

    $valorLevantar = $_POST['valorLevantar'];

    // Verifica se o utilizador tem saldo suficiente
    $sql = "SELECT Saldo FROM users WHERE Email = '$AdicionarEmail'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        if ($row['Saldo'] >= $valorLevantar) {
            $novoSaldo = $row['Saldo'] - $valorLevantar;

            //para criar um alerta
            $dataAtual = date('Y-m-d');
            $sqlAlert = "INSERT INTO alertas (texto_alerta, data_emissao, id_remetentes, tipo) 
                VALUES ('Levantou " . $valorLevantar . "€ da sua conta', '$dataAtual', " . $user['Id_User'] . ", 'Levantar saldo')";


            if (mysqli_query($conn, $sqlAlert)) {

                // Atualiza o saldo do utilizador
                $update_sql = "UPDATE users SET Saldo = '$novoSaldo' WHERE Email = '$AdicionarEmail'";
                if (mysqli_query($conn, $update_sql)) {
                    echo "Levantaste $valorLevantar € da tua conta. Saldo atual: $novoSaldo €.";
                } else {
                    echo "Erro ao atualizar o saldo.";
                }
            }
        } else {
            echo "ERRO! Saldo insuficiente para levantar $valorLevantar €.";
        }
    } else {
        echo "Utilizador não encontrado.";
    }

    // Redireciona para a página principal de gestão de carteira
    header("Refresh: 3; url=?gestaoCarteira=$AdicionarEmail");
}

if (isset($_GET['alterarTipoConta'])) {
    $AlterarEmail = $_GET['alterarTipoConta'];
    textoTrocaTipoUser($AlterarEmail);
}

if (isset($_GET['ConfirmarTrocaTipo'])) {
    $AlterarEmail = $_GET['ConfirmarTrocaTipo'];

    // Verifica se a opção foi selecionada
    if (!isset($_GET["alterarTipo"]) || empty($_GET["alterarTipo"])) {
        echo 'Tem de selecionar um tipo!';
        textoTrocaTipoUser($AlterarEmail);
        exit();
    }

    
    $opcoes = $_GET["alterarTipo"];

    //atualizar o tipo de utilizador
    $sql = "UPDATE users SET tipoUser = '$opcoes' WHERE Email = '$AlterarEmail'";

    if (mysqli_query($conn, $sql)) {
        //para criar um alerta
        $dataAtual = date('Y-m-d');
        $user = $_SESSION['user'];
        $tipoUser = $user['TipoUser'];
        $sqlAlert = "INSERT INTO alertas (texto_alerta, data_emissao, id_remetentes, tipo) 
     VALUES ('O administrador, com id" . $user['Id_User'] . ", alterou o tipo de utilizador no utilizador " . $AlterarEmail . " para o tipo " . $opcoes . "', '$dataAtual', " . $user['Id_User'] . ", 'Alterar Tipo User')";

        if (mysqli_query($conn, $sqlAlert)) {
            echo "<p>Utilizador atualizado com sucesso! </p>";
        }
    } else {
        echo "Erro ao atualizar o utilizador: " . mysqli_error($conn);
    }
}

if (isset($_GET['EliminarConta'])) {
    $eliminarEmail = $_GET['EliminarConta'];
    echo '<h1>Eliminar Dados</h1>';
    echo "<p>Utilizador: " . $eliminarEmail . "</p>";
    echo '
    <form method="GET">
        <div>
            <label for="ConfirmarCheckbox">
                <input type="checkbox" name="ConfirmarEliminar" id="ConfirmarCheckbox" required>
                Confirmo que quero eliminar a conta do utilizador de forma permanente, tendo a consciência de que não posso reverter essa ação!
            </label>
            <br>
            <button class="eliminar-btn" type="submit" name="ConfirmarButton" value="' . $eliminarEmail . '">Eliminar Utilizador</button>
        </div>
    </form>
    ';
}

if (isset($_GET['ConfirmarButton'])) {
    $user_email = $_GET['ConfirmarButton'];

    // Atualizar o estado do utilizador para "Eliminado"
    $sqlUpdate = "UPDATE users SET Autenticacao = 'Eliminado' WHERE Email = '$user_email'";

        if (mysqli_query($conn, $sqlUpdate)) {
            // Apagar o utilizador da base de dados
            $sqlDelete = "DELETE FROM users WHERE Email = '$user_email'";

        if (mysqli_query($conn, $sql)) {
            //para criar um alerta
            $dataAtual = date('Y-m-d');
            $user = $_SESSION['user'];
            $tipoUser = $user['TipoUser'];
            $sqlAlert = "INSERT INTO alertas (texto_alerta, data_emissao, id_remetentes, tipo) 
            VALUES ('O administrador, com id" . $user['Id_User'] . ", eliminou um utilizador', '$dataAtual', " . $user['Id_User'] . ", 'Elimnar utilizador')";

            if (mysqli_query($conn, $sqlAlert)) {
                echo "<p>Utilizador eliminado com sucesso! </p>";
                header("Refresh: 2; url=../paginas/gestao_utilizadores.php");
            }
        } else {
            echo "Erro ao aceitar o usuário: " . mysqli_error($conn);
        }
    }else {
        echo "Erro ao atualizar o estado do utilizador: " . mysqli_error($conn);
    }
}

header(" url=inicio.php?page=gerenciar-utilizadores");



function processarSaldo($AdicionarEmail, $valorToUpdate)
{
    include("../basedados/basedados.h");

    $sql = "SELECT * FROM users WHERE Email = '$AdicionarEmail'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $proxSaldo = $row['Saldo'] + $valorToUpdate;
            $update_sql = "UPDATE users SET Saldo = '$proxSaldo' WHERE Email = '$AdicionarEmail'";
            $_SESSION['user']['Saldo'] = $proxSaldo;
            $update_result = mysqli_query($conn, $update_sql);
        }
    } else {
        echo "erro";
    }
    echo ("Adicionou $valorToUpdate à carteira !");

    $user = $_SESSION['user'];
    if ($user['TipoUser'] == 1) {
        header("Location: ../paginas/perfil.php");
        exit();
    } else {
        header("Refresh: 2; url=../paginas/gestao_utilizadores.php");
        exit();
    }
}

function textoTrocaTipoUser($AlterarEmail)
{
    echo '<h1>Alterar Tipo</h1>';
    echo "<p>Utilizador: " . $AlterarEmail . "</p>";
    echo '
    <form method="GET">
        <label>Escolha uma opção:</label>
        <select id="alterarTipo" name="alterarTipo">
            <option value="" selected disabled>Selecione uma opção</option>
            <option value="1">Cliente</option>
            <option value="2">Funcionario</option>
            <option value="3">Administrador</option>
        </select>
        <input type="hidden" name="ConfirmarTrocaTipo" value="' . $AlterarEmail . '">
        <button type="submit">Alterar tipo conta</button>
    </form>';
}
?>