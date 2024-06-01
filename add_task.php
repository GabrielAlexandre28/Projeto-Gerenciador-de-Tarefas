<?php
session_start();

// Assume que o arquivo de conexão com o banco já foi incluído
include_once 'bd.php';

// Verificar se o método POST foi utilizado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = $_POST['titulo'];
    $descricao = $_POST['descricao'];
    $id_usuario = $_POST['id_usuario'];
    $data_prazo = $_POST['data_prazo'];
    $status = $_POST['status'];

    // Preparar a inserção da tarefa no banco de dados
    $stmt = $conn->prepare("INSERT INTO tarefas (titulo, descricao, id_usuario, data_prazo, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiss", $titulo, $descricao, $id_usuario, $data_prazo, $status);
    $stmt->execute();

    // Fechar a conexão
    $stmt->close();
    $conn->close();

    // Redirecionar de volta para a página da dashboard
    header("Location: dashboard.php"); // Substitua 'dashboard.php' pelo nome correto da sua página de dashboard
    exit();
} else {
    // Caso o acesso a este arquivo não seja via POST, redirecione para a página de login ou principal
    header("Location: login.php"); // ou outra página de sua preferência
    exit();
}
?>
