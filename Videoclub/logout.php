<?php
session_start();
// Mensaje opcional
$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

// Mensaje de logout para mostrar en index
session_start();
$_SESSION['logout_message'] = 'Has cerrado la sesión correctamente.';
header('Location: index.php');
exit();
