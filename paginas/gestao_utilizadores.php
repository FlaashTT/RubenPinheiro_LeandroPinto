<?php
session_start();
include("../basedados/basedados.h");
include("../paginas/menu_layout.php");
require("../paginas/validar.php");

validar_acesso([3, 2]);


// Função para obter as rotas com filtro
function gestaoUtilizadores($filtro = '')
{
    global $conn;
    $sql = "SELECT * FROM users";

    
    $user = $_SESSION['user'];
    if ($user['TipoUser'] == 2) {
        // Se TipoUser for 2(funcionario), restringe a consulta para TipoUser = 1 que é apenas clientes
        $sql .= " WHERE TipoUser = 1";
    } else {
        // adiciona o filtro (se houver)
        if (!empty($filtro)) {
            $sql .= " WHERE Nome LIKE '%$filtro%' OR Email LIKE '%$filtro%' OR TipoUser LIKE '%$filtro%'";
        }
    }
    
    // Ordenação por TipoUser
    $sql .= " ORDER BY TipoUser DESC";

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


<body>


    <!-- Conteúdo Principal -->
    <div class="content-Utilizadores">
        <h1>Dashboard - Página Utilizadores</h1>
        <h2>Visão geral dos utilizadores</h2> 

        <!-- Formulário de Filtro -->
        <form method="POST" action="">
            <input class="filtro" type="text" name="filtro" value="<?php echo htmlspecialchars($filtro); ?>" placeholder="Filtrar por nome, email ou tipo_user">
            <button type="submit" class="filtrar-btn">Filtrar</button>
            <button type="submit" name="reset" value="true" class="limpar-btn">Limpar Filtro</button>
        </form>

        <table class="utilizadores-tabela">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Autenticação</th>
                    <th>Estado</th>
                    <th>Tipo</th>
                    <th>Saldo</th>
                    <th>Adicionar saldo</th>
                    <th>Editar</th>
                    <th>Alterar tipo Utilizador</th>
                    <th>Remover Utilizador</th>
                </tr>
            </thead>
            <tbody>
                <?php

                // Exibir os usuários com o filtro
                $utilizadores = gestaoUtilizadores($filtro);
                if (mysqli_num_rows($utilizadores) > 0) {
                    while ($user = mysqli_fetch_assoc($utilizadores)) {
                        

                        echo "<tr>";
                        echo "<td>" . $user['Id_User'] . "</td>";
                        echo "<td>" . $user['Nome'] . "</td>";
                        echo "<td>" . $user['Email'] . "</td>";
                        echo "<td>" . $user['Autenticacao'] . "</td>";
                        echo "<td>" . $user['Estado'] . "</td>";
                        echo "<td>" . $user['TipoUser'] . "</td>";
                        echo "<td>" . $user['Saldo'] . "€" . "</td>";
                        echo "<td><a href='../paginas/acoes.php?gestaoCarteira=" . $user['Email'] . "'>Adicionar saldo</a></td>";
                        $userON = $_SESSION['user'];
                        $tipoUserOn = $userON['TipoUser'];
                        if ((int)$tipoUserOn == 2 ) {
                            echo "<td>Não tem permissão</td>";  
                            echo "<td>Não tem permissão</td>";
                            echo "<td>Não tem permissão</td>";
                        } else {
                            echo "<td><a href='../paginas/acoes.php?editarConta=" . $user['Email'] . "'>Editar</a></td>";
                            echo "<td><a href='../paginas/acoes.php?alterarTipoConta=" . $user['Email'] . "'>Alterar tipo</a></td>"; 
                            echo "<td><a href='../paginas/acoes.php?EliminarConta=" . $user['Email'] . "'>Remover</a></td>";
                        }
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='11'>Nenhum usuário encontrado.</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <div class="tabela-legenda">
            <p>
                Legenda: 1- Cliente,
                2- Funcionario,
                3- Admin
                
            </p>
        </div>
    </div>


</body>

</html>