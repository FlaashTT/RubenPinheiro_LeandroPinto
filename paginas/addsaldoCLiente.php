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
/*
if (empty($_POST['gestaoCarteira']) && 
    empty($_POST['adicionarSaldo']) && 
    empty($_POST['levantarSaldo'])) 
{
    header("Location: ../paginas/inicio.php");
    exit(); 
}*/


session_start();
include("../basedados/basedados.h");
require("../paginas/validar.php");

validar_acesso([3, 2, 1]);

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


if (isset($_GET['gestaoCarteira']) || isset($_POST['adicionarSaldo']) || isset($_POST['levantarSaldo'])) {
    if (isset($_GET['gestaoCarteira'])) {
        $AdicionarEmail = $_GET['gestaoCarteira'];
    } elseif (isset($_POST['adicionarSaldo'])) {
        $AdicionarEmail = $_POST['adicionarSaldo'];
    } elseif (isset($_POST['levantarSaldo'])) {
        $AdicionarEmail = $_POST['levantarSaldo'];
    }

    // Mostra as opções de carteira
    echo "
    <h2>Gestão de carteira</h2>
    <p>Selecione a opção:</p>
    <form method='POST'>
        <div class='botoes-inline'>
            <button class='opcao-btn' type='submit' name='adicionarSaldo' value='$AdicionarEmail'>Adicionar Saldo</button>
            <button class='opcao-btn' type='submit' name='levantarSaldo' value='$AdicionarEmail'>Levantar Saldo</button>
        </div>
    </form>
    ";
}


// Mostra o formulário para adicionar saldo quando clicado
if (isset($_POST['adicionarSaldo'])) {
    $AdicionarEmail = $_POST['adicionarSaldo'];

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

if (isset($_POST['confirmarAddSaldo'])) {
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

if (isset($_POST['levantarSaldo'])) {
    $AdicionarEmail = $_POST['levantarSaldo'];

    echo "
    <h3>Levantar Dinheiro</h3>
    <form method='POST' action='../paginas/acoes.php?confirmarLevantar=1'>
        <label for='valorLevantar'>Quantidade a levantar:</label>
        <input type='number' name='valorLevantar' min='1' step='1' placeholder='Valor a levantar'><br><br>
        <button type='submit'>Confirmar</button>
    </form>
    ";
}


if (isset($_POST['confirmarLevantar'])) {
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
?>