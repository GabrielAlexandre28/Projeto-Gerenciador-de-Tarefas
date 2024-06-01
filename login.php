<?php
session_start(); // Inicia a sessão PHP para gerenciar variáveis de sessão

include_once 'bd.php'; // Inclui o arquivo de conexão com o banco de dados

if ($_SERVER['REQUEST_METHOD'] == 'POST') { // Verifica se o método de requisição é POST
    $email = $_POST['email']; // Obtém o email enviado pelo formulário
    $senha = $_POST['senha']; // Obtém a senha enviada pelo formulário

    // Prepara e executa a consulta SQL para buscar o usuário no banco de dados usando o email
    $stmt = $conn->prepare("SELECT id, nome, senha FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email); // Vincula o parâmetro do email à consulta preparada
    $stmt->execute(); // Executa a consulta
    $stmt->store_result(); // Armazena o resultado da consulta

    if ($stmt->num_rows > 0) { // Verifica se encontrou algum usuário com o email fornecido
        $stmt->bind_result($id, $nome, $hash_senha); // Vincula as colunas de resultado às variáveis
        $stmt->fetch(); // Obtém o resultado da consulta

        // Verifica se a senha fornecida corresponde ao hash de senha armazenado no banco de dados
        if (password_verify($senha, $hash_senha)) {
            // Se a senha estiver correta, define variáveis de sessão para o usuário
            $_SESSION['id'] = $id; // Define o ID do usuário na sessão
            $_SESSION['nome'] = $nome; // Define o nome do usuário na sessão
            header("Location: dashboard.php"); // Redireciona para a página de dashboard
            exit(); // Finaliza o script
        } else {
            $erro = "Senha incorreta."; // Define uma mensagem de erro se a senha estiver incorreta
        }
    } else {
        $erro = "Usuário não encontrado."; // Define uma mensagem de erro se o usuário não for encontrado
    }

    $stmt->close(); // Fecha a consulta preparada
}

$conn->close(); // Fecha a conexão com o banco de dados
?>


<!DOCTYPE html> <!-- Declara o tipo de documento HTML -->
<html lang="pt-BR"> <!-- Inicia o documento HTML com a linguagem definida como português do Brasil -->
<head>
    <meta charset="UTF-8"> <!-- Define o conjunto de caracteres como UTF-8 para suportar caracteres especiais -->
    <title>Login</title> <!-- Define o título da página -->
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet"> <!-- Inclui o arquivo CSS do Bootstrap -->
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet"> <!-- Inclui o arquivo CSS da fonte Roboto do Google Fonts -->
    <style>
        body {
            background-color: #ffffff; /* Cor de fundo branco */
            height: 100vh; /* Define a altura da página para 100% da altura da viewport */
            display: flex; /* Define o modelo de layout flexível para centralizar o conteúdo verticalmente */
            align-items: center; /* Alinha os itens verticalmente ao centro */
            justify-content: center; /* Alinha os itens horizontalmente ao centro */
            font-family: 'Roboto', sans-serif; /* Aplica a fonte Roboto aos elementos de texto */
        }
        .card {
            background-color: rgba(255, 255, 255, 0.9); /* Fundo branco semi-transparente para o cartão */
        }
        .logo {
            max-height: 150px; /* Define a altura máxima da imagem do logo */
        }
    </style> <!-- Estilos CSS internos para personalização adicional -->
</head>
<body>
    <div class="container"> <!-- Container Bootstrap para agrupar conteúdo responsivo -->
        <div class="row justify-content-center mt-5"> <!-- Linha Bootstrap com conteúdo justificado ao centro e margem superior -->
            <div class="col-md-6 text-center"> <!-- Coluna Bootstrap de tamanho médio (6/12) e alinhamento centralizado -->
                <img src="img/Logo.jpg" alt="Logo do App" class="logo mb-3">  <!-- Imagem do logo do aplicativo -->
                <h1 class="text-dark">Kick-Day Tasks</h1> <!-- Título do aplicativo -->
            </div>
        </div>
        <div class="row justify-content-center mt-3"> <!-- Outra linha Bootstrap com conteúdo justificado ao centro e margem superior -->
            <div class="col-md-6"> <!-- Outra coluna Bootstrap de tamanho médio (6/12) -->
                <div class="card"> <!-- Cartão Bootstrap para envolver o formulário -->
                    <div class="card-body"> <!-- Corpo do cartão Bootstrap -->
                        <h2 class="card-title text-center">Login</h2> <!-- Título do cartão para o formulário de login -->
                        <?php if (isset($erro)) echo "<div class='alert alert-danger'>$erro</div>"; ?> <!-- Exibe mensagem de erro se houver -->
                        <form method="post" action=""> <!-- Formulário de login com método POST -->
                            <div class="form-group"> <!-- Grupo de formulário para o campo de email -->
                                <label for="email">Email:</label> <!-- Rótulo para o campo de email -->
                                <input type="email" class="form-control" id="email" name="email" required> <!-- Campo de entrada de email -->
                            </div>
                            <div class="form-group"> <!-- Grupo de formulário para o campo de senha -->
                                <label for="senha">Senha:</label> <!-- Rótulo para o campo de senha -->
                                <input type="password" class="form-control" id="senha" name="senha" required> <!-- Campo de entrada de senha -->
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Entrar</button> <!-- Botão de submissão do formulário -->
                        </form>
                        <div class="text-center mt-3"> <!-- Div centralizada com margem superior -->
                            <a href="registro.php" class="btn btn-link">Registrar Novo Usuário</a> <!-- Link para a página de registro -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- Fim do container Bootstrap -->
    <!-- Bootstrap JS (Essencial) -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script> <!-- Inclui o arquivo JS
