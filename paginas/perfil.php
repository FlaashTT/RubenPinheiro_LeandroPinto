<?php
include("../basedados/basedados.h");
include("../paginas/menu_layout.php");
include("../paginas/validar.php");
session_start();

validar_acesso([3, 2, 1]);
ob_start();
?>
<!DOCTYPE html>
<html lang="pt-pt">
<style>
    .settings-icon {
        position: fixed;
        top: 20px;
        right: 20px;
        margin-top: 40px;
        padding: 10px;
        z-index: 1000;
        margin-right: 5px;
    }

    .settings-icon a {
        color: #333;
        text-decoration: none;
        font-size: 25px;
    }

    .resultado-card {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 16px;
        background: none;
        border: none;
        padding: 0;
    }

    .bilhete-card {
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        padding: 16px;
        font-family: Arial, sans-serif;
    }

    .eliminar-btn {
        background-color: rgb(255, 38, 0);
        color: white;
        border: none;
        padding: 8px 16px;
        font-size: 1rem;
        border-radius: 4px;
        cursor: pointer;
    }

    .reembolsar-btn {
        background-color: #27ae60;
        color: white;
        border: none;
        padding: 8px 16px;
        font-size: 1rem;
        border-radius: 4px;
        cursor: pointer;
    }
</style>

<body>
    <!-- Conteúdo Principal -->
    <span class="settings-icon">
        <a href="../paginas/definicoes.php" title="Definições"> ⚙️
            <i class="fas fa-cog"></i>
        </a>

    </span>
    <div class="content-Pedidos">

        <div class="card"><!--informaçoes do utilizador-->

            <?php
            $sql = "SELECT * FROM users WHERE Estado ='online'";


            if ($result = mysqli_query($conn, $sql)) {
                if (mysqli_num_rows($result) > 0) {
                    $user = mysqli_fetch_assoc($result);

                    echo "
                <h1> Olá " . $user['Nome'] . "</h1>
                <form method ='POST'>
                    <p>Saldo " . $user['Saldo'] . "€  <a href='../paginas/addSaldoCliente.php?gestaoCarteira=" . $user['Email'] . "'>Adicionar saldo</a></p>
                </form>";
                }
            }

            ?>
        </div>
        <div class="card">
            <h1>Seus bilhetes</h1>
            <?php
            $user = $_SESSION['user'];
            $sql = "SELECT b.*, bc.*, r.*
            FROM bilhetescomprados bc
            INNER JOIN bilhetes b ON bc.id_bilhete = b.id_bilhete
            INNER JOIN rota r ON b.id_rota = r.id_rota
            WHERE bc.id_utilizador_comprador = " . $user['Id_User'] . "
            ORDER BY b.data DESC";


            if ($result = mysqli_query($conn, $sql)) {
                if (mysqli_num_rows($result) > 0) {
                    echo "<div class='resultado-card'>";
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<div class='bilhete-card'>";
                        echo "<p>Bilhete comprado ID: " . $row['id_bilheteComprado'] . "</p>";
                        echo "<p>Data do bilhete: " . $row['data'] . "</p>";
                        echo "
                            <div>
                            <p>ID rota: " . $row['id_rota'] . "</p>
                            <p>De: " . $row['origem'] . "</p>
                            <p>Para: " . $row['destino'] . "</p>
                            </div>
                        ";
                        echo "<p>preço Bilhete: " . $row['preco'] . "</p>";
                        echo "<p>Estado do bilhete: " . $row['estado_bilhete'] . "</p>";
                        if ($row['estado_bilhete'] === "Expirado" || $row['estado_bilhete'] === "Cancelado") {
                            echo "<form method='POST'>
                                <button class='eliminar-btn' type='submit' name='eliminarBilhete' value='" . $row['id_bilheteComprado'] . "'>Eliminar Bilhete do perfil</button>
                            </form>";
                        } else {
                            echo "<form method='POST'>
                                <input type='hidden' name='valorAReceber' value='" . $row['preco'] . "'>
                                <button class='reembolsar-btn' type='submit' name='reembolsarBilhete' value='" . $row['id_bilheteComprado'] . "'>Reembolsar bilhete</button>
                            </form>";
                        }
                        echo "</div>";
                    }
                    echo "</div>";
                } else {
                    echo "Nao tem bilhetes comprados";
                }
            }


            if (isset($_POST['eliminarBilhete'])) {
                $bilheteParaEliminar = $_POST['eliminarBilhete'];
                $sql = "DELETE FROM bilhetescomprados WHERE id_bilheteComprado ='$bilheteParaEliminar'";

                if ($result = mysqli_query($conn, $sql)) {
                    echo "Bilhete removido do perfil com sucesso";
                    header("Refresh: 2; url=../paginas/perfil.php");
                    exit();
                } else {
                    echo "Erro ao remover bilhete";
                    header("Refresh: 2; url=../paginas/perfil.php");
                    exit();
                }
            }

            if (isset($_POST['reembolsarBilhete']) && isset($_POST['valorAReceber'])) {
                $precobilhete = $_POST['valorAReceber'];
                $reembolsarBilhete = $_POST['reembolsarBilhete'];

                $user = $_SESSION['user'];
                $idUser = $user['Id_User'];


                $sql = "UPDATE users SET Saldo = Saldo + $precobilhete WHERE Id_User = '" . $user['Id_User'] . "'";


                if ($result = mysqli_query($conn, $sql)) {
                    $sql = "DELETE FROM bilhetescomprados WHERE id_bilheteComprado = '$reembolsarBilhete' AND id_utilizador_comprador = '$idUser'";


                    if ($result = mysqli_query($conn, $sql)) {
                        $dataAtual = date('Y-m-d');
                        $sqlAlert = "INSERT INTO alertas (texto_alerta, data_emissao, id_remetentes, tipo) 
                            VALUES ('Cliente reembolsou bilhete id" . $reembolsarBilhete . "', '$dataAtual', " . $idUser . ", 'Reembolso Bilhete')";

                        if (mysqli_query($conn, $sqlAlert)) {
                            header("Location: perfil.php");
                            exit();
                        }
                    } else {
                        echo "Erro ao reembolsar bilhete";
                        header("Refresh: 2; url=../paginas/perfil.php");
                        exit();
                    }
                }
            }
            ob_end_flush();
            ?>
        </div>
    </div>

    </div>
</body>

</html>