<?php
// Configurações do banco de dados
$servername = "localhost"; // Endereço do servidor MySQL
$username = "root"; // Nome de usuário do MySQL
$password = ""; // Senha do MySQL
$dbname = "gerenciador"; // Nome do banco de dados

// Criar a conexão
$conn = new mysqli($servername, $username, $password, $dbname); // Estabelece a conexão com o banco de dados

// Verificar a conexão
if ($conn->connect_error) { // Verifica se houve falha na conexão
    die("Falha na conexão: " . $conn->connect_error); // Interrompe o script e exibe uma mensagem de erro em caso de falha na conexão
}

// Definindo o conjunto de caracteres para evitar problemas com acentuação
$conn->set_charset("utf8"); // Define o conjunto de caracteres como UTF-8 para suportar acentuação e caracteres especiais
?>
