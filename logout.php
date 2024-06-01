<?php
session_start(); // Inicia ou continua a sessão atual

// Limpa todas as variáveis de sessão
$_SESSION = array();

// Verifica se o cookie de sessão existe e o destrói
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    // Define um cookie de sessão vazio e define o tempo de expiração para uma hora atrás
    setcookie(session_name(), '', time() - 3600,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destrói a sessão
session_destroy();

// Redireciona o usuário para a página de login
header("Location: login.php");
exit;
?>
