<?php
session_start();
// proteger la página
if (empty($_SESSION['logueado']) || $_SESSION['logueado'] !== true) {
    header('Location: index.php');
    exit();
}

$usuario = $_SESSION['usuario'] ?? 'Invitado';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Videoclub - Inicio</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 40px auto;
            padding: 20px
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center
        }

        a.button {
            background: #007bff;
            color: #fff;
            padding: 8px 12px;
            border-radius: 6px;
            text-decoration: none
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Bienvenido, <?php echo htmlspecialchars($usuario); ?></h1>
        <div>
            <a class="button" href="logout.php">Cerrar sesión</a>
        </div>
    </div>

    <p>Aquí iría el contenido principal del Videoclub según el tipo de usuario:
        <?php echo ($_SESSION['tipo'] === 'admin') ? ' (vista administrador)' : ' (vista cliente)'; ?></p>

    <!-- Ejemplo: mostrar opciones según tipo -->
    <?php if ($_SESSION['tipo'] === 'admin'): ?>
        <h2>Panel de administrador</h2>
        <p>Opciones para administrar soportes, clientes, etc.</p>
    <?php else: ?>
        <h2>Zona cliente</h2>
        <p>Listado de soportes, alquileres, etc.</p>
    <?php endif; ?>

</body>

</html>