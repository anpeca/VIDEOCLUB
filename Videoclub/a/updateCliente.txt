<?php
session_start();

// Aceptar solo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

// Recoger datos
$id = isset($_POST['id']) && $_POST['id'] !== '' ? (int)$_POST['id'] : null;
$from = isset($_POST['from']) && $_POST['from'] === 'admin' ? 'admin' : 'cliente';

$nombre = trim($_POST['nombre'] ?? '');
$email = trim($_POST['email'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$usuario = trim($_POST['usuario'] ?? '');
$password = $_POST['password'] ?? '';

// Validaciones básicas
$errors = [];
if ($nombre === '') $errors[] = 'El nombre es obligatorio.';
if ($usuario === '') $errors[] = 'El usuario es obligatorio.';
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email inválido.';

if (!empty($errors)) {
    $_SESSION['form_error'] = implode(' ', $errors);
    $_SESSION['form_old'] = ['id'=>$id,'nombre'=>$nombre,'email'=>$email,'telefono'=>$telefono,'usuario'=>$usuario];
    // volver al formulario (si admin con id, mantener ?id=)
    $loc = 'formUpdateCliente.php' . ($id !== null ? '?id=' . $id : '');
    header('Location: ' . $loc);
    exit();
}

// Actualizar en $_SESSION['clientes'] si existe
if ($id !== null && isset($_SESSION['clientes']) && isset($_SESSION['clientes'][$id])) {
    // actualizar array asociativo
    $_SESSION['clientes'][$id]['nombre'] = $nombre;
    $_SESSION['clientes'][$id]['email'] = $email;
    $_SESSION['clientes'][$id]['telefono'] = $telefono;
    $_SESSION['clientes'][$id]['usuario'] = $usuario;
    if ($password !== '') {
        // en prácticas guardamos en claro (para producción usar password_hash)
        $_SESSION['clientes'][$id]['password'] = $password;
    }
}

// Si no había id (o el id no existía), intentar buscar por usuario y actualizar
if (($id === null || !isset($_SESSION['clientes'][$id])) && isset($_SESSION['clientes']) && is_array($_SESSION['clientes'])) {
    foreach ($_SESSION['clientes'] as $k => $v) {
        if (isset($v['usuario']) && $v['usuario'] === $_SESSION['usuario']) {
            // usar este registro
            $_SESSION['clientes'][$k]['nombre'] = $nombre;
            $_SESSION['clientes'][$k]['email'] = $email;
            $_SESSION['clientes'][$k]['telefono'] = $telefono;
            $_SESSION['clientes'][$k]['usuario'] = $usuario;
            if ($password !== '') {
                $_SESSION['clientes'][$k]['password'] = $password;
            }
            $id = $k; // actualizar id encontrado
            break;
        }
    }
}

// Actualizar objeto en sesión si existe cliente_actual
if (isset($_SESSION['cliente_actual']) && is_object($_SESSION['cliente_actual'])) {
    $c = $_SESSION['cliente_actual'];
    $match = false;

    // comparar por id/numero si existe
    if ($id !== null) {
        if (method_exists($c, 'getNumero') && $c->getNumero() == $id) $match = true;
        if (!$match && method_exists($c, 'getId') && $c->getId() == $id) $match = true;
    } else {
        // comparar por usuario
        if (method_exists($c, 'getUser') && $c->getUser() === ($_SESSION['usuario'] ?? '')) $match = true;
    }

    if ($match) {
        // intentar setters si existen
        if (method_exists($c, 'setNombre')) $c->setNombre($nombre);
        if (method_exists($c, 'setEmail')) $c->setEmail($email);
        if (method_exists($c, 'setTelefono')) $c->setTelefono($telefono);
        if (method_exists($c, 'setUser')) $c->setUser($usuario);
        if ($password !== '' && method_exists($c, 'setPassword')) $c->setPassword($password);

        // Si no hay setters, intentar reconstruir la instancia (si existe clase)
        if (!method_exists($c, 'setNombre') && class_exists('\Dwes\ProyectoVideoclub\Cliente')) {
            // recrear objeto conservando cupo y otros campos si es posible
            $cupo = method_exists($c, 'getMaxAlquilerConcurrente') ? $c->getMaxAlquilerConcurrente() : 3;
            $num = method_exists($c, 'getNumero') ? $c->getNumero() : $id;
            // intentar reconstruir con constructor (nombre, id, cupo, user, password)
            $_SESSION['cliente_actual'] = new \Dwes\ProyectoVideoclub\Cliente($nombre, $num ?? 0, $cupo, $usuario, $password ?: ($v['password'] ?? ''));
        }
        // actualizar usuario en sesión (si el user cambió)
        $_SESSION['usuario'] = $usuario;
    }
}

// Preparar mensaje y redirección final
$_SESSION['flash_message'] = 'Cliente actualizado correctamente.';

if ($from === 'admin') {
    header('Location: mainAdmin.php');
    exit();
} else {
    header('Location: mainCliente.php');
    exit();
}
