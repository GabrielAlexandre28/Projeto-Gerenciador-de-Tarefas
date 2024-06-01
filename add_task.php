<?php
session_start(); // Inicia a sessão PHP

// Assume que o arquivo de conexão com o banco já foi incluído
include_once 'bd.php'; // Inclui o arquivo com as configurações de conexão com o banco de dados

// Verificar se o método POST foi utilizado
if ($_SERVER['REQUEST_METHOD'] == 'POST') { // Verifica se a requisição é do tipo POST
    $titulo = $_POST['titulo']; // Recupera o título enviado via POST
    $descricao = $_POST['descricao']; // Recupera a descrição enviada via POST
    $id_usuario = $_POST['id_usuario']; // Recupera o ID do usuário enviado via POST
    $data_prazo = $_POST['data_prazo']; // Recupera a data de prazo enviada via POST
    $status = $_POST['status']; // Recupera o status enviado via POST

    // Preparar a inserção da tarefa no banco de dados
    $stmt = $conn->prepare("INSERT INTO tarefas (titulo, descricao, id_usuario, data_prazo, status) VALUES (?, ?, ?, ?, ?)"); // Prepara uma consulta SQL para inserir uma nova tarefa
    $stmt->bind_param("ssiss", $titulo, $descricao, $id_usuario, $data_prazo, $status); // Liga os parâmetros da consulta aos valores das variáveis correspondentes
    $stmt->execute(); // Executa a consulta preparada para inserir a nova tarefa

    // Fechar a conexão
    $stmt->close(); // Fecha o statement após a utilização
    $conn->close(); // Fecha a conexão com o banco de dados após a utilização

    // Redirecionar de volta para a página da dashboard
    header("Location: dashboard.php"); // Redireciona para a página de dashboard após a inserção da nova tarefa
    exit(); // Finaliza o script PHP após o redirecionamento
} else {
    // Caso o acesso a este arquivo não seja via POST, redirecione para a página de login ou principal
    header("Location: login.php"); // Redireciona para a página de login se a requisição não for do tipo POST
    exit(); // Finaliza o script PHP após o redirecionamento
}
?>
