<?php
include("../basedados/basedados.h");
include("../paginas/menu_layout.php");
session_start();
?>

<!DOCTYPE html>
<html lang="pt-pt">
<body>
    <!-- Conteúdo Principal -->
    <div class="content-Inicio">
        <h1>Dashboard - Página Inicial</h1>
        <h2>Visão geral de Procura Bilhetes</h2>

        <div class="card">
            <form action="rotas_Pesquisa.php" class="formulario" method="POST" >
                <div class="campo">
                    <label for="para">De:</label>
                    <input type="text" id="para" class="input-text" name="de">
                </div>

                <div class="campo">
                    <label for="para">Para:</label>
                    <input type="text" id="para" class="input-text" name="para">
                </div>

                <div class="campo">
                    <label for="data">Data ida:</label>
                    <input type="date" id="data" class="input-text" name="data">
                </div>

                <div class="campo">
                    <label for="passageiros">Passageiros:</label>
                    <input type="number" id="passageiros" class="input-text" name="passageiros" min="1" value="1">
                </div>

                <div class="campo">
                    <button type="submit" class="btn-pesquisar">Pesquisar</button>
                </div>
            </form>
        </div>
        
    
    <div class="cards-container">
        <div class="card-Inicio">
            <h3>Saúde e Segurança</h3>
            <p>Mantém-te a ti e aos outros em segurança enquanto viajas connosco.</p>
            <a href="#" class="btn-saber-mais">Saber mais</a>
        </div>

        <div class="card-Inicio">
            <h3>Conforto a bordo</h3>
            <p>Os nossos autocarros estão equipados com assentos grandes e confortáveis, WC, Wi-Fi e tomadas.</p>
            <a href="#" class="btn-saber-mais">O nosso serviço a bordo</a>
        </div>

        <div class="card-Inicio">
            <h3>Grande rede de autocarros</h3>
            <p>Escolhe a partir de 3 000 destinos de viagem em 35 países e descobre a Europa com a FlexBus.</p>
            <a href="#" class="btn-saber-mais">Para a nossa rede</a>
        </div>

        <div class="card-Inicio">
            <h3>Viaja de forma ecológica</h3>
            <p>Os nossos autocarros provaram ter uma excelente pegada de carbono por passageiro conduzido/quilómetro.</p>
            <a href="#" class="btn-saber-mais">Saber mais</a>
        </div>
    </div>
    </div>
</body>

</html>