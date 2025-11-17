<?php
session_start();

// Solo admin
if (empty($_SESSION['logueado']) || ($_SESSION['tipo'] ?? '') !== 'admin') {
    header('Location: index.php');
    exit();
}

// Mensaje flash desde createCliente.php (opcional)
$admin_msg = $_SESSION['admin_msg'] ?? null;
if ($admin_msg) {
    unset($_SESSION['admin_msg']);
}

// Obtener mensajes/valores previos
$error = $_SESSION['form_error'] ?? null;
$old = $_SESSION['form_old'] ?? [];
unset($_SESSION['form_error'], $_SESSION['form_old']);

// Generar token CSRF simple para el formulario
if (empty($_SESSION['csrf_token_create_cliente'])) {
    $_SESSION['csrf_token_create_cliente'] = bin2hex(random_bytes(16));
}
$csrf = $_SESSION['csrf_token_create_cliente'];
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

        .flash {
            background: #e9ffe9;
            border: 1px solid #b5e6b5;
            padding: 10px;
            margin-bottom: 12px;
            border-radius: 4px;
            color: #155724;
        }

        a.cancel {
            margin-left:10px;
            color:#333;
            text-decoration:none;
            vertical-align:middle;
        }
    </style>
</head>

<body>
    <h1>Alta de nuevo cliente</h1>

    <?php if ($admin_msg): ?>
        <div class="flash"><?php echo htmlspecialchars($admin_msg); ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="post" action="createCliente.php" novalidate>
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf); ?>">

        <div class="form-group">
            <label for="id">ID cliente (número) — déjalo vacío para autogenerar:</label>
            <input id="id" name="id" type="number" min="1" value="<?php echo htmlspecialchars($old['id'] ?? ''); ?>" aria-describedby="idHelp">
            <small id="idHelp" style="color:#666">Si no indicas ID se generará uno automáticamente.</small>
        </div>

        <div class="form-group">
            <label for="nombre">Nombre completo</label>
            <input id="nombre" name="nombre" type="text" required minlength="3" value="<?php echo htmlspecialchars($old['nombre'] ?? ''); ?>">
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
            <input id="usuario" name="usuario" type="text" required minlength="3" value="<?php echo htmlspecialchars($old['usuario'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label for="password">Contraseña</label>
            <input id="password" name="password" type="password" required minlength="6" aria-describedby="pwdHelp">
            <small id="pwdHelp" style="color:#666">Mínimo 6 caracteres recomendado.</small>
        </div>

        <button type="submit">Crear cliente</button>
        <a class="cancel" href="mainAdmin.php">Cancelar</a>
    </form>
</body>

</html>
