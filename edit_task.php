<?php
session_start(); // Inicia a sessão PHP

include_once 'bd.php'; // Inclui o arquivo com as configurações de conexão com o banco de dados

if ($_SERVER['REQUEST_METHOD'] == 'POST') { // Verifica se a requisição é do tipo POST
    $id = $_POST['id']; // Recupera o ID enviado via POST
    $titulo = $_POST['titulo']; // Recupera o título enviado via POST
    $descricao = $_POST['descricao']; // Recupera a descrição enviada via POST
    $id_usuario = $_POST['id_usuario']; // Recupera o ID do usuário enviado via POST
    $data_prazo = $_POST['data_prazo']; // Recupera a data de prazo enviada via POST
    $status = $_POST['status']; // Recupera o status enviado via POST

    // Recuperar a data antiga e o usuário antigo
    $stmt_old = $conn->prepare("SELECT data_prazo, id_usuario FROM tarefas WHERE id = ?"); // Prepara uma consulta SQL para recuperar a data de prazo e o ID do usuário da tarefa antiga
    $stmt_old->bind_param("i", $id); // Liga o parâmetro da consulta ao valor da variável $id
    $stmt_old->execute(); // Executa a consulta preparada
    $result = $stmt_old->get_result(); // Obtém o resultado da consulta
    $old_data_prazo = ""; // Inicializa a variável $old_data_prazo
    $old_id_usuario = ""; // Inicializa a variável $old_id_usuario
    if ($row = $result->fetch_assoc()) { // Verifica se há uma linha de resultado
        $old_data_prazo = $row['data_prazo']; // Atribui o valor da data de prazo antiga à variável $old_data_prazo
        $old_id_usuario = $row['id_usuario']; // Atribui o valor do ID do usuário antigo à variável $old_id_usuario
    }
    $stmt_old->close(); // Fecha o statement após a utilização

    // Verificar se a data_prazo foi alterada
    if(empty($data_prazo)) { // Verifica se a nova data de prazo está vazia
        $data_prazo = $old_data_prazo; // Se estiver vazia, mantém a data de prazo antiga
    }

    // Verificar se o id_usuario foi alterado
    if(empty($id_usuario)) { // Verifica se o novo ID do usuário está vazio
        $id_usuario = $old_id_usuario; // Se estiver vazio, mantém o ID do usuário antigo
    }

    $stmt = $conn->prepare("UPDATE tarefas SET titulo = ?, descricao = ?, id_usuario = ?, data_prazo = ?, status = ? WHERE id = ?"); // Prepara uma consulta SQL para atualizar os dados da tarefa
    $stmt->bind_param("ssissi", $titulo, $descricao, $id_usuario, $data_prazo, $status, $id); // Liga os parâmetros da consulta aos valores das variáveis correspondentes
    $stmt->execute(); // Executa a consulta preparada

    $stmt->close(); // Fecha o statement após a utilização
    $conn->close(); // Fecha a conexão com o banco de dados após a utilização

    header("Location: dashboard.php"); // Redireciona para a página de dashboard após a atualização dos dados
    exit(); // Finaliza o script PHP após o redirecionamento
}
?>
