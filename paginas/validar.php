<?php
function validar_acesso($tipos_permitidos = []) {
    if (!isset($_SESSION['user'])) {
        // Redireciona para o login se o utilizador não estiver autenticado
        header("Location: login.php");
        exit;
    }

    $tipoUser = $_SESSION['user']['TipoUser']; // Obtém o tipo de utilizador da sessão

    // Verifica se o tipo de utilizador está na lista de tipos permitidos
    if (!in_array($tipoUser, $tipos_permitidos)) {
        // Redireciona para uma página de acesso negado ou inicial
        header("Location: inicio.php");
        exit;
    }
}
?>
