<?php
include("../basedados/basedados.h");
include("../paginas/menu_layout.php");
require("../paginas/validar.php");
session_start();

validar_acesso([3, 2, 1]);

// Verifica se o formulário foi enviado
if (isset($_POST['atualizarInformacoes'])) {
    $user = $_SESSION['user'];
    $idUser = $user['Id_User'];

    $nome = mysqli_real_escape_string($conn, $_POST['nome']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    if (!empty($password)) {
        // Criptografar a senha
        $passwordCriptada = md5($password);
        // Atualizar com senha nova
        $sql = "UPDATE users SET Nome='$nome', Email='$email', Password='$passwordCriptada' WHERE Id_User='$idUser'";
    } else {
        // Atualizar sem alterar a senha
        $sql = "UPDATE users SET Nome='$nome', Email='$email' WHERE Id_User='$idUser'";
    }

    // alterar dados de utilizador
    if (mysqli_query($conn, $sql)) {
        //para criar um alerta
        $dataAtual = date('Y-m-d');
        $sqlAlert = "INSERT INTO alertas (texto_alerta, data_emissao, id_remetentes, tipo) 
            VALUES ('Alterou dados de utilizador', '$dataAtual', " . $user['Id_User'] . ", 'Alertação de dados')";


        if (mysqli_query($conn, $sqlAlert)) {
            header("Refresh: 1; url=perfil.php");
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alterar Dados Pessoais</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        .content-Pedidos-Definicoes {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 700px;
            margin-left: 150px;
            padding: 20px;
        }

        .card-Definicoes {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 100%;
            max-width: 500px;
            margin-top: 20px;
        }

        .card-Definicoes h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        .campo {
            margin-bottom: 20px;
        }

        .campo label {
            display: block;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .campo input {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .campo button {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .campo button:hover {
            background-color: #0056b3;
        }

        @media (max-width: 800px) {
            .card-Definicoes {
                padding: 20px;
            }

            .campo input {
                font-size: 16px;
            }

            .campo button {
                font-size: 18px;
            }
        }
    </style>
</head>

<body>
    <div class="content-Pedidos-Definicoes">
        <div class="card-Definicoes">
            <h1>Alterar Dados Pessoais</h1>
            <?php
            $sql = "SELECT * FROM users WHERE Estado ='online'";

            if ($result = mysqli_query($conn, $sql)) {
                if (mysqli_num_rows($result) > 0) {
                    $user = mysqli_fetch_assoc($result);
                    echo "
                    <form method='POST'>
                        <div class='campo'>
                            <label for='nome'>Nome:</label>
                            <input type='text' id='nome' name='nome' value='" . $user['Nome'] . "' required>
                        </div>
                        <div class='campo'>
                            <label for='email'>Email:</label>
                            <input type='email' id='email' name='email' value='" . $user['Email'] . "' required>
                        </div>
                        <div class='campo'>
                            <label for='password'>Nova Password:</label>
                            <input type='password' id='password' name='password' placeholder='Nova password'>
                        </div>
                        <div class='campo'>
                            <button type='submit' name='atualizarInformacoes'>Atualizar Informações</button>
                        </div>
                    </form>";
                }
            }
            ?>
        </div>
    </div>
</body>

</html>