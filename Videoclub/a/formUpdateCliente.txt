<?php
session_start();

// Determinar origen: admin (edita a otro) o cliente (se edita a sí mismo)
$from = 'cliente'; // valor por defecto
$clientData = null;
$id = null;

// Si viene id por GET, lo edita el admin
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];
    $clients = $_SESSION['clientes'] ?? [];
    if (isset($clients[$id])) {
        $clientData = $clients[$id];
        $from = 'admin';
    } else {
        // Si no existe, mostrar mensaje simple
        die('Cliente no encontrado.');
    }
} else {
    // intentar cargar desde la sesión cliente_actual
    if (isset($_SESSION['cliente_actual']) && is_object($_SESSION['cliente_actual'])) {
        // intentamos leer campos desde el objeto (getters) o desde array
        $c = $_SESSION['cliente_actual'];
        $clientData = [
            'id' => method_exists($c, 'getNumero') ? $c->getNumero() : (method_exists($c, 'getId') ? $c->getId() : null),
            'nombre' => method_exists($c, 'getNombre') ? $c->getNombre() : '',
            'email' => method_exists($c, 'getEmail') ? $c->getEmail() : '',
            'telefono' => method_exists($c, 'getTelefono') ? $c->getTelefono() : '',
            'usuario' => method_exists($c, 'getUser') ? $c->getUser() : '',
        ];
        $from = 'cliente';
    } elseif (!empty($_SESSION['usuario']) && !empty($_SESSION['clientes']) && is_array($_SESSION['clientes'])) {
        // si no hay objeto, intentar buscar por usuario en la lista
        foreach ($_SESSION['clientes'] as $k => $v) {
            if (isset($v['usuario']) && $v['usuario'] === $_SESSION['usuario']) {
                $id = $k;
                $clientData = $v;
                $from = 'cliente';
                break;
            }
        }
    }
}

// Si no hay datos, no permitimos continuar
if (!$clientData) {
    die('No hay datos de cliente disponibles para editar.');
}

// Campos con valores por defecto
$idField = $id !== null ? (int)$id : ($clientData['id'] ?? '');
$nombre = htmlspecialchars($clientData['nombre'] ?? '', ENT_QUOTES, 'UTF-8');
$email  = htmlspecialchars($clientData['email'] ?? '', ENT_QUOTES, 'UTF-8');
$telefono = htmlspecialchars($clientData['telefono'] ?? '', ENT_QUOTES, 'UTF-8');
$usuario = htmlspecialchars($clientData['usuario'] ?? '', ENT_QUOTES, 'UTF-8');

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Actualizar Cliente</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <style>
        body { font-family: Arial, sans-serif; max-width: 700px; margin: 30px auto; padding: 20px; background:#f7f7f7; }
        form { background:#fff; padding:20px; border-radius:6px; box-shadow:0 2px 8px rgba(0,0,0,0.05); }
        label { display:block; margin-top:12px; font-weight:600; }
        input[type="text"], input[type="email"], input[type="password"] { width:100%; padding:8px; box-sizing:border-box; margin-top:6px; }
        .actions { margin-top:16px; }
        .btn { padding:10px 14px; background:#007bff; color:#fff; border:none; border-radius:4px; cursor:pointer; text-decoration:none; }
        .btn-secondary { background:#6c757d; margin-left:8px; }
    </style>
</head>
<body>
    <h1>Editar cliente</h1>
    <form method="post" action="updateCliente.php" novalidate>
        <input type="hidden" name="id" value="<?php echo $idField; ?>">
        <input type="hidden" name="from" value="<?php echo $from === 'admin' ? 'admin' : 'cliente'; ?>">

        <label for="nombre">Nombre</label>
        <input id="nombre" name="nombre" type="text" required value="<?php echo $nombre; ?>">

        <label for="email">Email</label>
        <input id="email" name="email" type="email" required value="<?php echo $email; ?>">

        <label for="telefono">Teléfono</label>
        <input id="telefono" name="telefono" type="text" value="<?php echo $telefono; ?>">

        <label for="usuario">Usuario</label>
        <input id="usuario" name="usuario" type="text" required value="<?php echo $usuario; ?>">

        <label for="password">Contraseña nueva (dejar vacío para no cambiarla)</label>
        <input id="password" name="password" type="password" placeholder="Dejar en blanco para mantener la actual">

        <div class="actions">
            <button type="submit" class="btn">Guardar cambios</button>
            <?php if ($from === 'admin'): ?>
                <a class="btn btn-secondary" href="mainAdmin.php">Volver al listado</a>
            <?php else: ?>
                <a class="btn btn-secondary" href="mainCliente.php">Volver</a>
            <?php endif; ?>
        </div>
    </form>
</body>
</html>
