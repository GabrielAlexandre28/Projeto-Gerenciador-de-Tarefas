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


<!DOCTYPE html> <!-- Declaração do tipo de documento HTML -->
<html lang="pt-BR"> <!-- Abertura da tag html com atributo lang definindo o idioma como português do Brasil -->
<head> <!-- Abertura da seção de cabeçalho -->
    <meta charset="UTF-8"> <!-- Meta tag para definir o conjunto de caracteres como UTF-8 -->
    <title>Registro de Usuário</title> <!-- Título da página -->
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet"> <!-- Link para importar o CSS do Bootstrap -->
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet"> <!-- Link para importar as fontes do Google Fonts -->
    <style>
        body {
            background-color: #ffffff; /* Cor de fundo branco */
            height: 100vh; /* Altura da página igual a 100% da altura da viewport */
            display: flex; /* Exibição em formato de flexbox */
            align-items: center; /* Alinhamento verticalmente no centro */
            justify-content: center; /* Alinhamento horizontalmente no centro */
            font-family: 'Roboto', sans-serif; /* Aplicando a fonte do Google Fonts */
        }
        .card {
            background-color: rgba(255, 255, 255, 0.9); /* Fundo branco semi-transparente */
        }
        .logo {
            max-height: 150px; /* Altura máxima para a logo */
        }
    </style> 
</head> 
<body> 
    <div class="container"> <!-- Container do Bootstrap -->
        <div class="row justify-content-center mt-5"> <!-- Linha com conteúdo centralizado -->
            <div class="col-md-6 text-center"> <!-- Coluna com largura média (md) e conteúdo centralizado -->
                <img src="img/Logo.jpg" alt="Logo do App" class="logo mb-3">  <!-- Imagem da logo -->
                <h1 class="text-dark">Kick-Day Tasks</h1> <!-- Título principal -->
            </div> 
        </div> 
        <div class="row justify-content-center mt-3"> <!-- Outra linha com conteúdo centralizado -->
            <div class="col-md-6"> <!-- Coluna com largura média (md) -->
                <div class="card"> <!-- Cartão do Bootstrap -->
                    <div class="card-body"> <!-- Corpo do cartão -->
                        <h2 class="card-title text-center">Registro de Usuário</h2> <!-- Título do cartão -->
                        <?php if (isset($erro)) echo "<div class='alert alert-danger'>$erro</div>"; ?> <!-- Exibe mensagem de erro caso exista -->
                        <?php if (isset($sucesso)) echo "<div class='alert alert-success'>$sucesso</div>"; ?> <!-- Exibe mensagem de sucesso caso exista -->
                        <form method="post" action=""> <!-- Formulário de registro -->
                            <div class="form-group"> <!-- Grupo de formulário -->
                                <label for="nome">Nome:</label> <!-- Rótulo do campo de nome -->
                                <input type="text" class="form-control" id="nome" name="nome" required> <!-- Campo de entrada de texto para o nome -->
                            </div> <!-- Fechamento do grupo de formulário -->
                            <div class="form-group"> 
                                <label for="email">Email:</label> <!-- Rótulo do campo de email -->
                                <input type="email" class="form-control" id="email" name="email" required> <!-- Campo de entrada de texto para o email -->
                            </div> <!-- Fechamento do grupo de formulário -->
                            <div class="form-group">
                                <label for="senha">Senha:</label> <!-- Rótulo do campo de senha -->
                                <input type="password" class="form-control" id="senha" name="senha" required> <!-- Campo de entrada de texto para a senha -->
                            </div> <!-- Fechamento do grupo de formulário -->
                            <button type="submit" class="btn btn-primary btn-block">Registrar</button> <!-- Botão de submissão do formulário -->
                        </form> <!-- Fechamento do formulário -->
                        <div class="text-center mt-3"> <!-- Div de texto centralizado com margem superior -->
                            <a href="index.php" class="btn btn-link">Voltar para a Página de Login</a> <!-- Link para voltar à página de login -->
                        </div>
                    </div> 
                </div> 
            </div> 
        </div> 
    </div> 
    <!-- Bootstrap JS (Essencial) -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script> <!-- Script JavaScript do Bootstrap -->
</body> 
</html>
