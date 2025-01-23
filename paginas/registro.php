<html>
<style>
  body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f9f9f9;
  }

  /* Centralizar o formulário */
  form {
    border: 3px solid #f1f1f1;
    max-width: 400px;
    margin: 50px auto;
    padding: 20px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  }

  h2 {
    text-align: center;
    margin-top: 50px;
  }

  /* Estilo dos inputs */
  input[type="text"],
  input[type="password"] {
    width: 100%;
    padding: 12px;
    margin: 8px 0;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
  }

  button {
    background-color: #04aa6d;
    color: white;
    padding: 14px;
    border: none;
    cursor: pointer;
    width: 100%;
    border-radius: 4px;
  }

  button:hover {
    background-color: #037f53;
  }

  a {
    display: block;
    text-align: center;
    margin-top: 10px;
    color: #04aa6d;
    text-decoration: none;
  }

  a:hover {
    text-decoration: underline;
  }
</style>

<body>
  <h2>Registar</h2>
  <form action="registro.php" method="POST">
    <label for="username">Nome de utilizador</label><br>
    <input type="text" placeholder="Enter Username" name="username" required><br>

    <label for="email">Email</label><br>
    <input type="text" placeholder="Enter Email" name="email" required><br>

    <label for="password">Senha</label><br>
    <input type="password" placeholder="Enter Password" name="password" required><br>



    <button type="submit" name="login">Efetuar registo</button><br>
    <a href="login.php"><b>Ja tenho conta</b></a>
  </form>

</body>

</html>

<?php
include("../basedados/basedados.h"); // Inclui a conexão à base de dados
session_start();

// Verifica se o formulário foi submetido
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verifica se todos os campos foram preenchidos
    if (!empty($_POST["email"]) && !empty($_POST["password"]) && !empty($_POST["username"])) {
        
      // Sanitização dos inputs para prevenir XSS
      $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);  // Filtra o e-mail
      $username = strip_tags($_POST['username']);
      $username = htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); // prevenção contra htmlInjection
      $password = $_POST['password'];

      // Verificação do e-mail válido
      if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
          echo "O endereço de e-mail fornecido não é válido.";
          exit;
      }

      // Verifica se a senha tem um tamanho adequado (exemplo: mínimo de 8 caracteres)
      if (strlen($password) < 8) {
          echo "A senha deve ter pelo menos 8 caracteres.";
          exit;
      }

      $hashedPassword = md5($password);

      // Verifica se o e-mail já existe na base de dados
      $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
      $stmt->bind_param("s", $email);
      $stmt->execute();
      $result = $stmt->get_result();
      if ($result->num_rows > 0) {
          echo "Já existe um utilizador com este e-mail.";
          exit;
      }
        

        // Preparar a consulta para criar o utilizador
        $stmt = $conn->prepare("INSERT INTO `users`(`Nome`, `Email`, `Password`) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $hashedPassword);

        if ($stmt->execute()) {
            // Regista o alerta de criação
            $dataAtual = date('Y-m-d');
            $alertStmt = $conn->prepare("INSERT INTO alertas (texto_alerta, data_emissao, id_remetentes, tipo) 
                                         VALUES ('Criou um novo registo', ?, (SELECT Id_User FROM users WHERE Nome = ? AND Email = ?), 'Novo registo')");
            $alertStmt->bind_param("sss", $dataAtual, $username, $email);

            if ($alertStmt->execute()) {
                echo "Registo efetuado com sucesso!";
                header("Refresh: 2; url=login.php");
                exit;
            } else {
                echo "Erro ao criar o alerta: " . mysqli_error($conn);
            }
        } else {
            echo "Erro ao criar o utilizador: " . mysqli_error($conn);
        }
    } else {
        echo "Por favor, preencha todos os campos!";
    }
}

// Fecha a conexão
$conn->close();
?>

