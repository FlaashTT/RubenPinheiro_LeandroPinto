<?php
include("../basedados/basedados.h");
session_start();

// Verifica se o usuário está logado
if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
    $tipoUser = $user['TipoUser'];
}else{
    //senao atibui um tipo para ser visitante
    $tipoUser=0;
}


?>

<!DOCTYPE html>
<html lang="pt-pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - FelixBus</title>
    <link rel="stylesheet" href="styleMenu.css">
</head>

<body>
    <!-- Navbar -->
    <div class="navbar">
        <div class="logo">FelixBus</div>
        <div class="Hora" id="hora"></div>
    </div>

    <!-- Sidebar -->
    <div class="sidebar">
        <a href="inicio.php">Início</a>

        <?php
        // Adiciona links extra dependendo do tipo de usuário
        if ($tipoUser == '1') {
            echo '<a href="perfil.php">Perfil</a>';
            echo '<a href="logout.php" class="logout">Sair</a>';
        } elseif ($tipoUser == '2') {
            echo '<a href="perfil.php">Perfil</a>';
            echo '<a href="veiculos.php">Veículos</a>';
            echo '<a href="gestao_rotas.php">Gestão de rotas</a>';
            echo '<a href="gestao_utilizadores.php">Gestão de utilizadores</a>';
            echo '<a href="gestao_bilhetes.php">Gestão de bilhetes</a>';
            echo '<a href="logout.php" class="logout">Sair</a>';
        } elseif ($tipoUser == '3') {
            echo '<a href="veiculos.php">Veículos</a>';
            echo '<a href="gestao_pedidos.php">Pedidos</a>';
            echo '<a href="gestao_rotas.php">Gestão de rotas</a>';
            echo '<a href="gestao_utilizadores.php">Gestão de utilizadores</a>';
            echo '<a href="gestao_alertas.php">Gestão de alertas</a>';
            echo '<a href="gestao_bilhetes.php">Gestão de bilhetes</a>';
            echo '<a href="perfil.php">Perfil</a>';
            echo '<a href="logout.php" class="logout">Sair</a>';
        }
        if ($tipoUser === 0) {
            echo '<a href="login.php" class="logout">Iniciar Sessão</a>';
        } 
        ?>


    </div>

    <!-- Conteúdo Principal -->


    <script>
        // Exibir a hora dinâmica na navbar
        function updateTime() {
            const date = new Date();
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            const seconds = String(date.getSeconds()).padStart(2, '0');
            document.getElementById('hora').textContent = hours + ":" + minutes + ":" + seconds;
        }
        setInterval(updateTime, 1000);
        updateTime(); // Inicializa a hora ao carregar a página
    </script>
</body>

</html>