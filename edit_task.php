<?php
session_start();

include_once 'bd.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $titulo = $_POST['titulo'];
    $descricao = $_POST['descricao'];
    $id_usuario = $_POST['id_usuario'];
    $data_prazo = $_POST['data_prazo'];
    $status = $_POST['status'];

    // Recuperar a data antiga e o usuário antigo
    $stmt_old = $conn->prepare("SELECT data_prazo, id_usuario FROM tarefas WHERE id = ?");
    $stmt_old->bind_param("i", $id);
    $stmt_old->execute();
    $result = $stmt_old->get_result();
    $old_data_prazo = "";
    $old_id_usuario = "";
    if ($row = $result->fetch_assoc()) {
        $old_data_prazo = $row['data_prazo'];
        $old_id_usuario = $row['id_usuario'];
    }
    $stmt_old->close();

    // Verificar se a data_prazo foi alterada
    if(empty($data_prazo)) {
        $data_prazo = $old_data_prazo; // Se vazio, mantenha a data antiga
    }

    // Verificar se o id_usuario foi alterado
    if(empty($id_usuario)) {
        $id_usuario = $old_id_usuario; // Se vazio, mantenha o usuário antigo
    }

    $stmt = $conn->prepare("UPDATE tarefas SET titulo = ?, descricao = ?, id_usuario = ?, data_prazo = ?, status = ? WHERE id = ?");
    $stmt->bind_param("ssissi", $titulo, $descricao, $id_usuario, $data_prazo, $status, $id);
    $stmt->execute();

    $stmt->close();
    $conn->close();

    header("Location: dashboard.php"); // ajuste conforme necessário
    exit();
}
?>
