<?php
session_start();

// Solo admin
if (empty($_SESSION['logueado']) || ($_SESSION['tipo'] ?? '') !== 'admin') {
    header('Location: index.php');
    exit();
}

// Obtener mensajes/valores previos
$error = $_SESSION['form_error'] ?? null;
$old = $_SESSION['form_old'] ?? [];
unset($_SESSION['form_error'], $_SESSION['form_old']);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Alta Cliente - Videoclub</title>
    <style>
        /* estilo sencillo */
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 30px auto;
            padding: 20px
        }

        .form-group {
            margin-bottom: 12px
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: 700
        }

        input[type="text"],
        input[type="number"],
        input[type="password"],
        input[type="email"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px
        }

        button {
            background: #007bff;
            color: #fff;
            padding: 10px 14px;
            border: none;
            border-radius: 4px;
            cursor: pointer
        }

        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 12px
        }
    </style>
</head>

<body>
    <h1>Alta de nuevo cliente</h1>

    <?php if ($error): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="post" action="createCliente.php" novalidate>
        <div class="form-group">
            <label for="id">ID cliente (número) — déjalo vacío para autogenerar:</label>
            <input id="id" name="id" type="number" min="1" value="<?php echo htmlspecialchars($old['id'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label for="nombre">Nombre completo</label>
            <input id="nombre" name="nombre" type="text" required value="<?php echo htmlspecialchars($old['nombre'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input id="email" name="email" type="email" value="<?php echo htmlspecialchars($old['email'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label for="telefono">Teléfono</label>
            <input id="telefono" name="telefono" type="text" value="<?php echo htmlspecialchars($old['telefono'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label for="usuario">Usuario (login)</label>
            <input id="usuario" name="usuario" type="text" required value="<?php echo htmlspecialchars($old['usuario'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label for="password">Contraseña</label>
            <input id="password" name="password" type="password" required>
        </div>

        <button type="submit">Crear cliente</button>
        <a href="mainAdmin.php" style="margin-left:10px;">Cancelar</a>
    </form>
</body>

</html>