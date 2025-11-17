<?php
// removeCliente.php
session_start();

// Protección: solo admin puede borrar
if (!isset($_SESSION['usuario']) || ($_SESSION['tipo'] ?? '') !== 'admin') {
    http_response_code(403);
    die('Acceso denegado. Debes identificarte como administrador.');
}

// Aceptar solo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // redirigir silenciando al listado
    header('Location: mainAdmin.php');
    exit();
}

// Comprobar id recibido
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id <= 0) {
    $_SESSION['admin_msg'] = 'ID de cliente no válido.';
    header('Location: mainAdmin.php');
    exit();
}

// Comprobar existencia del array clientes en sesión
if (!isset($_SESSION['clientes']) || !is_array($_SESSION['clientes'])) {
    $_SESSION['admin_msg'] = 'No hay clientes cargados.';
    header('Location: mainAdmin.php');
    exit();
}

// Borrar cliente si existe
if (isset($_SESSION['clientes'][$id])) {
    unset($_SESSION['clientes'][$id]);
    // Reindexar si quieres mantener índices consecutivos:
    // $_SESSION['clientes'] = array_values($_SESSION['clientes']);
    $_SESSION['admin_msg'] = "Cliente con ID $id eliminado correctamente.";
} else {
    $_SESSION['admin_msg'] = "No existe cliente con ID $id.";
}

// Volver al listado
header('Location: mainAdmin.php');
exit();
