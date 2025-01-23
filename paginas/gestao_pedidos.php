<?php
session_start();
include("../paginas/menu_layout.php");
include("../basedados/basedados.h");
require("../paginas/validar.php");  

validar_acesso([3]); // Apenas admins (TipoUser = 3) têm acesso

?>

<!DOCTYPE html>
<html lang="pt-pt">
<body>
    <div class="content-Pedidos">
        <h1>Dashboard - Gestão de pedidos pendentes</h1>
        <h2>Visão geral de pedidos</h2>

        <div class="card">
            <?php
            $sql = "SELECT * FROM users WHERE Autenticacao = 'Pendente'";
            $result = mysqli_query($conn, $sql);

            if ($result && mysqli_num_rows($result) > 0) {
                while ($userToverf = mysqli_fetch_assoc($result)) {
                    echo "<form action='' method='GET'>";
                    echo "<p class ='textoNome-Pedidos '>Nome: " . $userToverf['Nome'] . "</p>";
                    echo "<p class ='textoEmail-Pedidos '>Email: " . $userToverf['Email'] . "</p>";
                    echo '<button class="aceitar-btn" type="submit" name="AceitarPedido" value="' . $userToverf['Email'] . '">Aceitar Utilizador</button>';
                    echo '<button class="recusar-btn" type="submit" name="RejeitarPedido" value="' . $userToverf['Email'] . '">Rejeitar Utilizador</button>';
                    echo "<hr>";
                    echo "</form>";
                }
            } else {
                echo "Sem nenhum utilizador para verificar";
            }
            if (isset($_GET['AceitarPedido'])) {
                $user_email = $_GET['AceitarPedido'];

                // Atualiza o campo 'Autenticacao' para 'Aceite' na base de dados
                $sql = "UPDATE users SET Autenticacao = 'Aceite' WHERE Email = '$user_email'";

                if (mysqli_query($conn, $sql)) {
                    echo "<p>Utilizador aceito com sucesso! </p>";
                    echo "<meta http-equiv='refresh' content='1;url=?page=Pedidos'>";
                } else {
                    echo "Erro ao aceitar o usuário: " . mysqli_error($conn);
                }
            }

            if (isset($_GET['RejeitarPedido'])) {
                $user_email = $_GET['RejeitarPedido'];

                // Atualiza o campo 'Autenticacao' para 'rejeitado' na base de dados
                $sql = "UPDATE users SET Autenticacao = 'Rejeitado' WHERE Email = '$user_email'";

                if (mysqli_query($conn, $sql)) {
                    echo "<p>Utilizador Rejeitado com sucesso! </p>";
                    echo "<meta http-equiv='refresh' content='1;url=?page=Pedidos'>";
                } else {
                    echo "Erro ao aceitar o usuário: " . mysqli_error($conn);
                }
            }


            ?>

        </div>
    </div>
</body>
</html>