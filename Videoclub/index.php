<?php
session_start();

// Si ya está logueado, redirigir a main.php
if (!empty($_SESSION['logueado']) && $_SESSION['logueado'] === true) {
    header('Location: main.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Videoclub - Login</title>
    <style>
        /* Mantén aquí tu CSS (copiado del original) */
        body {
            font-family: Arial, sans-serif;
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5
        }

        .login-container {
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1)
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 30px
        }

        .form-group {
            margin-bottom: 20px
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 700;
            color: #555
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box
        }

        button {
            background: #007bff;
            color: #fff;
            padding: 12px;
            border: 0;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            font-weight: 700
        }

        .error {
            color: #d9534f;
            margin-bottom: 20px;
            padding: 10px;
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
            text-align: center
        }
    </style>
</head>

<body>
    <div class="login-container">
        <h2>Videoclub - Acceso</h2>

        <?php if (!empty($_SESSION['login_error'])): ?>
            <div class="error"><?php echo htmlspecialchars($_SESSION['login_error']);
                                unset($_SESSION['login_error']); ?></div>
        <?php endif; ?>

        <form method="post" action="login.php">
            <div class="form-group">
                <label for="usuario">Usuario:</label>
                <input id="usuario" name="usuario" type="text" required value="<?php echo isset($_SESSION['last_user']) ? htmlspecialchars($_SESSION['last_user']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input id="password" name="password" type="password" required>
            </div>

            <button type="submit">Iniciar Sesión</button>
        </form>

        <div style="margin-top:20px;text-align:center;font-size:14px;color:#666">
            <p><strong>Usuarios de prueba:</strong></p>
            <p>admin / admin</p>
            <p>usuario / usuario</p>
        </div>
    </div>
</body>

</html>