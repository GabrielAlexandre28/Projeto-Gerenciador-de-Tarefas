<?php
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

include_once 'bd.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];

    $stmt = $conn->prepare("DELETE FROM tarefas WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        // Sucesso
        $stmt->close();
        $conn->close();
        header("Location: dashboard.php"); // Ajuste conforme necessário
        exit();
    } else {
        echo "Erro ao excluir: " . $stmt->error;
    }
} else {
    // Não é POST
    echo "Método de requisição inválido.";
}
?>
