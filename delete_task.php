<?php
session_start(); // Inicia a sessão PHP

include_once 'bd.php'; // Inclui o arquivo com as configurações de conexão com o banco de dados

if ($_SERVER['REQUEST_METHOD'] == 'POST') { // Verifica se a requisição é do tipo POST
    $id = $_POST['id']; // Recupera o ID enviado via POST

    $stmt = $conn->prepare("DELETE FROM tarefas WHERE id = ?"); // Prepara uma consulta SQL para excluir a tarefa com base no ID
    $stmt->bind_param("i", $id); // Liga o parâmetro da consulta ao valor da variável $id
    if ($stmt->execute()) { // Executa a consulta preparada e verifica se foi bem-sucedida
        // Sucesso
        $stmt->close(); // Fecha o statement após a utilização
        $conn->close(); // Fecha a conexão com o banco de dados após a utilização
        header("Location: dashboard.php"); // Redireciona para a página de dashboard após a exclusão da tarefa
        exit(); // Finaliza o script PHP após o redirecionamento
    } else {
        echo "Erro ao excluir: " . $stmt->error; // Se houver erro na execução da consulta, exibe a mensagem de erro
    }
} else {
    // Não é POST
    echo "Método de requisição inválido."; // Se a requisição não for do tipo POST, exibe uma mensagem de erro
}
?>
