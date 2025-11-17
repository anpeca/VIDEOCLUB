<?php
session_start();

// Solo admin
if (empty($_SESSION['logueado']) || ($_SESSION['tipo'] ?? '') !== 'admin') {
    header('Location: index.php');
    exit();
}

// Recoger POST y saneado básico
$id = isset($_POST['id']) && $_POST['id'] !== '' ? (int)$_POST['id'] : null;
$nombre = trim($_POST['nombre'] ?? '');
$email = trim($_POST['email'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$usuario = trim($_POST['usuario'] ?? '');
$password = $_POST['password'] ?? '';

// Guardar valores antiguos para relleno en caso de error
$_SESSION['form_old'] = [
    'id' => $id,
    'nombre' => $nombre,
    'email' => $email,
    'telefono' => $telefono,
    'usuario' => $usuario
];

// Validaciones básicas
if ($nombre === '' || $usuario === '' || $password === '') {
    $_SESSION['form_error'] = 'Rellena al menos Nombre, Usuario y Contraseña.';
    header('Location: formCreateCliente.php');
    exit();
}

// Asegurar que la sesión de clientes existe y es array
if (!isset($_SESSION['clientes']) || !is_array($_SESSION['clientes'])) {
    $_SESSION['clientes'] = [];
}

// Normalizar IDs: si no se proporcionó ID, autogenerar mayor que el máximo actual
if ($id === null) {
    $ids = array_keys($_SESSION['clientes']);
    $maxId = 0;
    foreach ($ids as $k) {
        if ((int)$k > $maxId) $maxId = (int)$k;
    }
    $id = $maxId + 1;
} else {
    $id = (int)$id;
    // comprobar unicidad del id
    if (isset($_SESSION['clientes'][$id])) {
        $_SESSION['form_error'] = 'El ID proporcionado ya existe. Elija otro.';
        header('Location: formCreateCliente.php');
        exit();
    }
}

// Comprobar unicidad de usuario
foreach ($_SESSION['clientes'] as $c) {
    if (is_array($c) && isset($c['usuario']) && $c['usuario'] === $usuario) {
        $_SESSION['form_error'] = 'El usuario ya está en uso. Elija otro.';
        header('Location: formCreateCliente.php');
        exit();
    }
    if (is_object($c) && method_exists($c, 'getUser') && $c->getUser() === $usuario) {
        $_SESSION['form_error'] = 'El usuario ya está en uso. Elija otro.';
        header('Location: formCreateCliente.php');
        exit();
    }
}

// Insertar en la sesión como array simple (práctica)
// Guardar hash de la contraseña para mayor seguridad en entornos de práctica
$passHash = password_hash($password, PASSWORD_DEFAULT);

$_SESSION['clientes'][$id] = [
    'id' => $id,
    'nombre' => $nombre,
    'email' => $email,
    'telefono' => $telefono,
    'usuario' => $usuario,
    // Guardar el hash en vez de la contraseña en claro
    'password' => $passHash
];

// Inicializar estructura de alquileres para el nuevo cliente (consistencia)
if (!isset($_SESSION['alquileres']) || !is_array($_SESSION['alquileres'])) {
    $_SESSION['alquileres'] = [];
}
if (!isset($_SESSION['alquileres'][$id])) {
    $_SESSION['alquileres'][$id] = [];
}

// Limpiar datos temporales de formulario
unset($_SESSION['form_old'], $_SESSION['form_error']);

// Mensaje flash para el admin (se muestra en mainAdmin.php si lo detectas)
$_SESSION['admin_msg'] = "Cliente " . htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') . " (ID $id) creado correctamente.";

// Redirigir a mainAdmin para ver el cliente insertado
header('Location: mainAdmin.php');
exit();
