<?php
include_once 'bd.php'; // Inclui o arquivo de conexão com o banco de dados.

if ($_SERVER['REQUEST_METHOD'] == 'POST') { // Verifica se a requisição é do tipo POST.
    $nome = $_POST['nome']; // Obtém o nome do formulário.
    $email = $_POST['email']; // Obtém o email do formulário.
    $senha = $_POST['senha']; // Obtém a senha do formulário.

    // Verifica se o email já está cadastrado no banco de dados.
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) { // Se o email já existir no banco de dados.
        $erro = "Email já cadastrado."; // Define a mensagem de erro.
    } else { // Se o email não existir no banco de dados.
        // Gera o hash da senha para armazenamento seguro no banco de dados.
        $hash_senha = password_hash($senha, PASSWORD_DEFAULT);

        // Insere o novo usuário no banco de dados.
        $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nome, $email, $hash_senha); // Associa os parâmetros.
        
        if ($stmt->execute()) { // Se a inserção for bem-sucedida.
            $sucesso = "Usuário registrado com sucesso!"; // Define a mensagem de sucesso.
        } else { // Se ocorrer um erro durante a inserção.
            $erro = "Erro ao registrar o usuário. Tente novamente."; // Define a mensagem de erro.
        }
    }

    $stmt->close(); // Fecha o statement.
}

$conn->close(); // Fecha a conexão com o banco de dados.
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Registro de Usuário</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #ffffff;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Roboto', sans-serif;
        }
        .card {
            background-color: rgba(255, 255, 255, 0.9);
        }
        .logo {
            max-height: 150px;
        }
    </style>
    <script>
        // Função que valida a senha
        function validarSenha() {
            // Obtém o valor do campo de senha
            var senha = document.getElementById("senha").value;

            // Define o regex para validar a senha
            var regex = /^(?=.*[A-Z])(?=.*[!@#$%^&*])(?=.*[0-9]).{8,}$/;
            
            // Variável para armazenar a mensagem de erro
            var mensagemErro = "";

            // Verifica se a senha não atende ao regex
            if (!regex.test(senha)) {
                // Verifica se a senha tem menos de 8 caracteres
                if (senha.length < 8) {
                    mensagemErro += "A senha deve ter pelo menos 8 caracteres.\n";
                }
                // Verifica se a senha não tem pelo menos uma letra maiúscula
                if (!/[A-Z]/.test(senha)) {
                    mensagemErro += "A senha deve ter pelo menos uma letra maiúscula.\n";
                }
                // Verifica se a senha não tem pelo menos um caractere especial
                if (!/[!@#$%^&*]/.test(senha)) {
                    mensagemErro += "A senha deve ter pelo menos um caractere especial (!@#$%^&*).\n";
                }
                // Verifica se a senha não tem pelo menos um número
                if (!/[0-9]/.test(senha)) {
                    mensagemErro += "A senha deve ter pelo menos um número.\n";
                }
                // Exibe a mensagem de erro
                alert(mensagemErro);
                // Impede o envio do formulário
                return false;
            }
            // Permite o envio do formulário se a senha for válida
            return true;
        }
    </script>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6 text-center">
                <img src="img/Logo.jpg" alt="Logo do App" class="logo mb-3">
                <h1 class="text-dark">Kick-Day Tasks</h1>
            </div>
        </div>
        <div class="row justify-content-center mt-3">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title text-center">Registro de Usuário</h2>
                        <?php if (isset($erro)) echo "<div class='alert alert-danger'>$erro</div>"; ?>
                        <?php if (isset($sucesso)) echo "<div class='alert alert-success'>$sucesso</div>"; ?>
                        <!-- Formulário de registro com validação de senha ao submeter -->
                        <form method="post" action="" onsubmit="return validarSenha();">
                            <div class="form-group">
                                <label for="nome">Nome:</label>
                                <input type="text" class="form-control" id="nome" name="nome" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email:</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="form-group">
                                <label for="senha">Senha:</label>
                                <input type="password" class="form-control" id="senha" name="senha" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Registrar</button>
                        </form>
                        <div class="text-center mt-3">
                            <a href="login.php" class="btn btn-link">Voltar para a Página de Login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
