<?php
session_start();

// Protección: solo admin puede acceder
if (empty($_SESSION['logueado']) || $_SESSION['logueado'] !== true || ($_SESSION['tipo'] ?? '') !== 'admin') {
    header('Location: index.php');
    exit();
}

$usuario = $_SESSION['usuario'] ?? 'Administrador';
$soportes = $_SESSION['soportes'] ?? [];
$clientes = $_SESSION['clientes'] ?? [];

if (!is_array($soportes)) $soportes = [];
if (!is_array($clientes) && !is_object($clientes)) $clientes = [];

// Helper para obtener campo usuario desde array u objeto Cliente
function clienteGetUsuario($c)
{
    if (is_array($c)) {
        return $c['usuario'] ?? '';
    }
    if (is_object($c) && method_exists($c, 'getUser')) {
        return $c->getUser() ?? '';
    }
    return '';
}

// Helper para obtener campo nombre desde array u objeto
function clienteGetNombre($c)
{
    if (is_array($c)) {
        return $c['nombre'] ?? '';
    }
    if (is_object($c) && method_exists($c, 'getNombre')) {
        return $c->getNombre();
    }
    return '';
}

function clienteGetEmail($c)
{
    if (is_array($c)) {
        return $c['email'] ?? '';
    }
    if (is_object($c) && method_exists($c, 'getEmail')) {
        return $c->getEmail();
    }
    return '';
}

function clienteGetTelefono($c)
{
    if (is_array($c)) {
        return $c['telefono'] ?? '';
    }
    if (is_object($c) && method_exists($c, 'getTelefono')) {
        return $c->getTelefono();
    }
    return '';
}

function clienteGetId($c)
{
    if (is_array($c)) {
        return $c['id'] ?? ($c['numero'] ?? '');
    }
    if (is_object($c) && method_exists($c, 'getNumero')) {
        return $c->getNumero();
    }
    return '';
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Videoclub - Panel Admin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1000px;
            margin: 30px auto;
            padding: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        .table th,
        .table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .table th {
            background: #f0f0f0;
        }

        .section {
            margin-top: 28px;
        }

        a.button {
            background: #007bff;
            color: #fff;
            padding: 8px 12px;
            border-radius: 6px;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Bienvenido, <?php echo htmlspecialchars($usuario); ?></h1>
        <div><a class="button" href="logout.php">Cerrar sesión</a></div>
    </div>

    <div class="section">
        <h2>Listado de clientes (<?php echo is_array($clientes) ? count($clientes) : '0'; ?>)</h2>
        <?php if (empty($clientes)): ?>
            <p>No hay clientes cargados.</p>
        <?php else: ?>
            <table class="table" aria-describedby="clientes">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clientes as $c): ?>
                        <tr>
                            <td><?php echo htmlspecialchars(clienteGetId($c)); ?></td>
                            <td><?php echo htmlspecialchars(clienteGetUsuario($c)); ?></td>
                            <td><?php echo htmlspecialchars(clienteGetNombre($c)); ?></td>
                            <td><?php echo htmlspecialchars(clienteGetEmail($c)); ?></td>
                            <td><?php echo htmlspecialchars(clienteGetTelefono($c)); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <div class="section">
        <h2>Listado de soportes (<?php echo count($soportes); ?>)</h2>
        <?php if (empty($soportes)): ?>
            <p>No hay soportes cargados.</p>
        <?php else: ?>
            <table class="table" aria-describedby="soportes">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tipo</th>
                        <th>Título</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($soportes as $s): ?>
                        <?php
                        // Soporte puede ser array o objeto; manejamos ambos casos
                        if (is_array($s)) {

                            $sid = $s['id'] ?? '';
                            $tipo = $s['tipo'] ?? '';
                            $titulo = $s['titulo'] ?? '';
                            $precio = isset($s['precio']) ? number_format((float)$s['precio'], 2, ',', '.') . ' €' : '';
                            $cantidad = isset($s['cantidad']) ? (int)$s['cantidad'] : 0;
                        } else {
                            // objeto: intentamos usar getters comunes
                            $sid = method_exists($s, 'getNumero') ? $s->getNumero() : (method_exists($s, 'getId') ? $s->getId() : '');
                            $tipo = (new ReflectionClass($s))->getShortName();
                            $titulo = method_exists($s, 'getTitulo') ? $s->getTitulo() : '';
                            $precio = method_exists($s, 'getPrecio') ? number_format((float)$s->getPrecio(), 2, ',', '.') . ' €' : '';
                            $cantidad = 0;
                            foreach (['getCantidad', 'getUnidades', 'getUnidadesDisponibles', 'getStock'] as $m) {
                                if (method_exists($s, $m)) {
                                    $cantidad = (int)$s->$m();
                                    break;
                                }
                            }
                        }
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($sid); ?></td>
                            <td><?php echo htmlspecialchars($tipo); ?></td>
                            <td><?php echo htmlspecialchars($titulo); ?></td>
                            <td><?php echo htmlspecialchars($precio); ?></td>
                            <td><?php echo htmlspecialchars($cantidad); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

</body>

</html>