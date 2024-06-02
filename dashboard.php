<?php
session_start(); // Inicia a sessão PHP para usar variáveis de sessão.

if (!isset($_SESSION['id'])) {
    header("Location: login.php"); // Verifica se o usuário está autenticado. Se não estiver, redireciona para a página de login.
    exit(); // Encerra a execução do script.
}

include_once 'bd.php'; // Inclui o arquivo de conexão com o banco de dados.

$nome = $_SESSION['nome']; // Armazena o nome do usuário da sessão na variável $nome.

// Consultar todas as tarefas
$stmt = $conn->prepare("SELECT t.id, t.titulo, t.descricao, t.data_prazo, t.status, u.nome AS usuario_nome 
                        FROM tarefas t
                        LEFT JOIN usuarios u ON t.id_usuario = u.id
                        ORDER BY t.data_prazo ASC"); 
// Prepara a consulta SQL para selecionar todas as tarefas e seus respectivos usuários, ordenadas por data de prazo.

$stmt->execute(); // Executa a consulta preparada.
$stmt->bind_result($taskId, $taskTitle, $taskDescription, $taskDate, $taskStatus, $userName); 
// Liga as variáveis aos resultados da consulta.

$tasks = []; // Inicializa um array vazio para armazenar as tarefas.
while ($stmt->fetch()) {
    $tasks[] = [
        'id' => $taskId, // Armazena o ID da tarefa.
        'titulo' => $taskTitle, // Armazena o título da tarefa.
        'descricao' => $taskDescription, // Armazena a descrição da tarefa.
        'data_prazo' => $taskDate, // Armazena a data de prazo da tarefa.
        'status' => $taskStatus, // Armazena o status da tarefa.
        'usuario_nome' => $userName // Armazena o nome do usuário associado à tarefa.
    ];
}

$stmt->close(); // Fecha a consulta.

// Atualizar status de tarefas atrasadas
foreach ($tasks as $index => $task) {
    $timezone = new DateTimeZone('America/Sao_Paulo'); // Define o fuso horário conforme necessário.
    $dataPrazo = new DateTime($task['data_prazo'], $timezone); // Cria um objeto DateTime para a data de prazo com o fuso horário específico.
    $dataAtual = new DateTime('now', $timezone); // Cria um objeto DateTime para a data e hora atual com o fuso horário específico.

    // Adiciona 24 horas ao prazo final para ajustar a verificação de atraso.
    $dataPrazo->modify('+1 day'); 

    // Verifica se a data de prazo ajustada é menor que a data atual e o status não é 'concluída'.
    if ($dataPrazo < $dataAtual && $task['status'] !== 'concluida') {
        $tasks[$index]['status'] = 'Atrasada'; // Atualiza o status da tarefa para 'Atrasada'.
        
        // Prepara a consulta de atualização do status da tarefa no banco de dados.
        $stmtUpdate = $conn->prepare("UPDATE tarefas SET status = 'Atrasada' WHERE id = ?");
        $stmtUpdate->bind_param("i", $task['id']); // Associa o ID da tarefa à consulta.
        $stmtUpdate->execute(); // Executa a consulta de atualização.
        $stmtUpdate->close(); // Fecha a consulta para liberar recursos.
    }
}

//Usuários para Dropdown
$stmtUsers = $conn->prepare("(
    SELECT NULL as id, NULL as nome
    UNION
    SELECT id, nome FROM usuarios
) ORDER BY id ASC"); 
// Prepara a consulta SQL para selecionar todos os usuários com uma entrada vazia adicional.
$stmtUsers->execute(); // Executa a consulta.
$stmtUsers->bind_result($userId, $userName); // Liga as variáveis aos resultados da consulta.

$users = []; // Inicializa um array vazio para armazenar os usuários.
while ($stmtUsers->fetch()) {
    $users[] = [
        'id' => $userId, // Armazena o ID do usuário.
        'nome' => $userName // Armazena o nome do usuário.
    ];
}

$stmtUsers->close(); // Fecha a consulta.
$conn->close(); // Fecha a conexão com o banco de dados.

?>


<!DOCTYPE html>
<html lang="pt-BR"> <!-- Define o tipo do documento e a linguagem como português do Brasil -->
<head>
    <meta charset="UTF-8"> <!-- Define o conjunto de caracteres como UTF-8 -->
    <title>Kick-Day Tasks</title> <!-- Define o título da página -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet"> <!-- Inclui o CSS do Bootstrap -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet"> <!-- Inclui a fonte Roboto do Google Fonts -->
    <style>
        body {
            background-color: #ffffff; /* Define a cor de fundo do corpo */
            font-family: 'Roboto', sans-serif; /* Define a fonte do corpo */
        }
        .container {
            margin-top: 50px; /* Define a margem superior do container */
        }
        .table {
            margin-top: 20px; /* Define a margem superior da tabela */
        }
        .modal-dialog {
            max-width: 800px; /* Define a largura máxima do diálogo modal */
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px; /* Define a margem inferior do cabeçalho */
        }
        .header img {
            width: 50px; /* Define a largura da imagem do logo */
        }
        .header h1 {
            margin: 0; /* Remove a margem do título */
        }
    </style>
</head>
<body>
    <div class="container"> <!-- Início do container principal -->
    <div class="header"> <!-- Início do cabeçalho -->
    <img src="img/Logo.jpg" alt="Logo"> <!-- Exibe o logo -->
    <h1>Kick-Day Tasks</h1> <!-- Título da página -->
    <div>
        <?php echo htmlspecialchars($nome); ?> | <!-- Exibe o nome do usuário de forma segura -->
        <a href="logout.php" class="btn btn-danger">Sair</a> <!-- Link para logout com botão -->
    </div>
</div>

        <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addTaskModal">Adicionar Tarefa</button> <!-- Botão para adicionar tarefa que abre o modal -->
        
        <table class="table table-striped"> <!-- Início da tabela com listras -->
            <thead>
                <tr>
                    <th>Título</th> <!-- Cabeçalho da coluna Título -->
                    <th>Descrição</th> <!-- Cabeçalho da coluna Descrição -->
                    <th>Responsavel</th> <!-- Cabeçalho da coluna Responsável -->
                    <th>Prazo de Entrega</th> <!-- Cabeçalho da coluna Prazo de Entrega -->
                    <th>Status</th> <!-- Cabeçalho da coluna Status -->
                    <th>Ações</th> <!-- Cabeçalho da coluna Ações -->
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tasks as $task): ?> <!-- Loop para exibir cada tarefa -->
                    <tr>
                        <td><?php echo htmlspecialchars($task['titulo']); ?></td> <!-- Exibe o título da tarefa de forma segura -->
                        <td><?php echo htmlspecialchars($task['descricao']); ?></td> <!-- Exibe a descrição da tarefa de forma segura -->
                        <td><?php echo htmlspecialchars($task['usuario_nome']); ?></td> <!-- Exibe o nome do responsável de forma segura -->
                        <td><?php echo htmlspecialchars($task['data_prazo']); ?></td> <!-- Exibe o prazo de entrega de forma segura -->
                        <td><?php echo htmlspecialchars($task['status']); ?></td> <!-- Exibe o status da tarefa de forma segura -->
                        <td>
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editTaskModal" data-id="<?php echo $task['id']; ?>" data-titulo="<?php echo $task['titulo']; ?>" data-descricao="<?php echo $task['descricao']; ?>" data-status="<?php echo $task['status']; ?>">Editar</button> <!-- Botão para editar tarefa, abrindo modal com dados da tarefa -->
                            <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteTaskModal" data-id="<?php echo $task['id']; ?>">Excluir</button> <!-- Botão para excluir tarefa, abrindo modal de confirmação -->
                        </td>
                    </tr>
                <?php endforeach; ?> <!-- Fim do loop -->
            </tbody>
        </table>
    </div>

    <!-- Modal Adicionar Tarefa -->
<div class="modal fade" id="addTaskModal" tabindex="-1" aria-labelledby="addTaskModalLabel" aria-hidden="true"> <!-- Início do modal de adicionar tarefa -->
    <div class="modal-dialog"> <!-- Define o diálogo do modal -->
        <div class="modal-content"> <!-- Conteúdo do modal -->
            <form method="post" action="add_task.php"> <!-- Formulário para adicionar tarefa -->
                <div class="modal-header">
                    <h5 class="modal-title" id="addTaskModalLabel">Adicionar Tarefa</h5> <!-- Título do modal -->
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> <!-- Botão para fechar o modal -->
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="titulo">Título</label>
                        <input type="text" class="form-control" id="titulo" name="titulo" required> <!-- Campo para título da tarefa -->
                    </div>
                    <div class="form-group">
                        <label for="descricao">Descrição</label>
                        <textarea class="form-control" id="descricao" name="descricao" rows="3" required></textarea> <!-- Campo para descrição da tarefa -->
                    </div>
                    <div class="form-group">
                        <label for="id_usuario">Usuário</label>
                        <select class="form-control" id="id_usuario" name="id_usuario" required> <!-- Campo para selecionar o usuário -->
                            <?php foreach ($users as $user): ?> <!-- Loop para exibir cada usuário -->
                                <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['nome']); ?></option> <!-- Opções do campo de seleção com nome dos usuários -->
                            <?php endforeach; ?> <!-- Fim do loop -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="data_prazo">Data e Hora</label>
                        <input type="datetime-local" class="form-control" id="data_prazo" name="data_prazo" required> <!-- Campo para data e hora -->
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select class="form-control" id="status" name="status" required> <!-- Campo para selecionar o status -->
                            <option value="pendente">Pendente</option> <!-- Opção Pendente -->
                            <option value="em_progresso">Em Progresso</option> <!-- Opção Em Progresso -->
                            <option value="concluida">Concluída</option> <!-- Opção Concluída -->
                            <option value="Atrasada">Atrasada</option> <!-- Opção Atrasada -->
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button> <!-- Botão para fechar o modal -->
                    <button type="submit" class="btn btn-primary">Salvar</button> <!-- Botão para salvar a tarefa -->
                </div>
            </form>
        </div>
    </div>
</div>

    <!-- Modal Editar Tarefa -->
    <div class="modal fade" id="editTaskModal" tabindex="-1" aria-labelledby="editTaskModalLabel" aria-hidden="true"> <!-- Início do modal de editar tarefa -->
        <div class="modal-dialog"> <!-- Define o diálogo do modal -->
            <div class="modal-content"> <!-- Conteúdo do modal -->
                <form method="post" action="edit_task.php"> <!-- Formulário para editar tarefa -->
                    <div class="modal-header">
                        <h5 class="modal-title" id="editTaskModalLabel">Editar Tarefa</h5> <!-- Título do modal -->
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> <!-- Botão para fechar o modal -->
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="editTaskId" name="id"> <!-- Campo oculto para o ID da tarefa -->
                        <div class="form-group">
                            <label for="editTitulo">Título</label>
                            <input type="text" class="form-control" id="editTitulo" name="titulo" required> <!-- Campo para título da tarefa -->
                        </div>
                        <div class="form-group">
                            <label for="editDescricao">Descrição</label>
                            <textarea class="form-control" id="editDescricao" name="descricao" rows="3" required></textarea> <!-- Campo para descrição da tarefa -->
                        </div>
                        <div class="form-group">
                            <label for="editIdUsuario">Usuário</label>
                            <select class="form-control" id="editIdUsuario" name="id_usuario" > <!-- Campo para selecionar o usuário -->
                                <?php foreach ($users as $user): ?> <!-- Loop para exibir cada usuário -->
                                    <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['nome']); ?></option> <!-- Opções do campo de seleção com nome dos usuários -->
                                <?php endforeach; ?> <!-- Fim do loop -->
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="editDataPrazo">Data e Hora</label>
                            <input type="datetime-local" class="form-control" id="editDataPrazo" name="data_prazo"> <!-- Campo para data e hora -->
                        </div>
                        <div class="form-group">
                            <label for="editStatus">Status</label>
                            <select class="form-control" id="editStatus" name="status" required> <!-- Campo para selecionar o status -->
                                <option value="pendente">Pendente</option> <!-- Opção Pendente -->
                                <option value="em_progresso">Em Progresso</option> <!-- Opção Em Progresso -->
                                <option value="concluida">Concluída</option> <!-- Opção Concluída -->
                                <option value="Atrasada">Atrasdada</option> <!-- Opção Atrasada -->
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button> <!-- Botão para fechar o modal -->
                        <button type="submit" class="btn btn-primary">Salvar</button> <!-- Botão para salvar as alterações -->
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Excluir Tarefa -->
    <div class="modal fade" id="deleteTaskModal" tabindex="-1" aria-labelledby="deleteTaskModalLabel" aria-hidden="true"> <!-- Início do modal de excluir tarefa -->
        <div class="modal-dialog"> <!-- Define o diálogo do modal -->
            <div class="modal-content"> <!-- Conteúdo do modal -->
                <form method="post" action="delete_task.php"> <!-- Formulário para excluir tarefa -->
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteTaskModalLabel">Excluir Tarefa</h5> <!-- Título do modal -->
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> <!-- Botão para fechar o modal -->
                    </div>
                    <div class="modal-body">
                        <p>Tem certeza que deseja excluir esta tarefa?</p> <!-- Mensagem de confirmação -->
                        <input type="hidden" id="deleteTaskId" name="id"> <!-- Campo oculto para o ID da tarefa -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button> <!-- Botão para fechar o modal -->
                        <button type="submit" class="btn btn-danger">Excluir</button> <!-- Botão para excluir a tarefa -->
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script> <!-- Inclui o JS do Bootstrap -->
    <script>
        document.addEventListener('DOMContentLoaded', function () { //Função executada quando o DOM estiver carregado
            var editTaskModal = document.getElementById('editTaskModal'); //Obtém o modal de edição
            editTaskModal.addEventListener('show.bs.modal', function (event) { //Evento para quando o modal for exibido
                var button = event.relatedTarget; //Botão que abriu o modal
                var id = button.getAttribute('data-id'); //Obtém o ID da tarefa
                var titulo = button.getAttribute('data-titulo'); //Obtém o título da tarefa
                var descricao = button.getAttribute('data-descricao'); //Obtém a descrição da tarefa
                var status = button.getAttribute('data-status'); //Obtém o status da tarefa
                var usuarioId = button.getAttribute('data-usuario-id'); //Obtém o ID do usuário
                
                var modalTitle = editTaskModal.querySelector('.modal-title'); //Obtém o título do modal
                var modalBodyInputId = editTaskModal.querySelector('#editTaskId'); //Campo de ID no modal
                var modalBodyInputTitulo = editTaskModal.querySelector('#editTitulo'); //Campo de título no modal
                var modalBodyInputDescricao = editTaskModal.querySelector('#editDescricao'); //Campo de descrição no modal 
                var modalBodyInputUsuario = editTaskModal.querySelector('#editIdUsuario'); //Campo de usuário no modal 
                var modalBodyInputDataPrazo = editTaskModal.querySelector('#editDataPrazo'); //Campo de data no modal 
                var modalBodyInputStatus = editTaskModal.querySelector('#editStatus'); //Campo de status no modal
                
                modalBodyInputId.value = id; //Define o valor do ID no modal
                modalBodyInputTitulo.value = titulo; //Define o valor do título no modal
                modalBodyInputDescricao.value = descricao; //Define o valor da descrição no modal 
                modalBodyInputDataPrazo.value = ''; //Limpa o valor da data para permitir nova seleção
                modalBodyInputStatus.value = status; //Define o valor do status no modal
                
                // Selecionar o usuário correspondente à tarefa
                var options = modalBodyInputUsuario.options;
                for (var i = 0; i < options.length; i++) {
                    if (options[i].value == usuarioId) {
                        options[i].selected = true;
                        break;
                    }
                }
            });

            var deleteTaskModal = document.getElementById('deleteTaskModal'); //Obtém o modal de exclusão
            deleteTaskModal.addEventListener('show.bs.modal', function (event) { //Evento para quando o modal for exibido
                var button = event.relatedTarget; //Botão que abriu o modal
                var id = button.getAttribute('data-id'); //Obtém o ID da tarefa
                
                var modalBodyInputId = deleteTaskModal.querySelector('#deleteTaskId'); //Campo de ID no modal
                
                modalBodyInputId.value = id; //Define o valor do ID no modal
            });
        });
    </script>
    </div>
</body>
</html>

</body>
</html>

                                

