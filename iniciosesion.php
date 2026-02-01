<?php
session_start();

// Definir usuarios válidos
$usuarios_validos = [
    'admin' => 'admin',
    'usuario' => 'usuario'
];

// Procesar el formulario si se envió
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Validar credenciales
    if (isset($usuarios_validos[$usuario]) && $usuarios_validos[$usuario] === $password) {
        // Credenciales correctas
        $_SESSION['usuario'] = $usuario;
        $_SESSION['logueado'] = true;
        header('Location: inicio.php');
        exit();
    } else {
        // Credenciales incorrectas
        $error = 'Usuario o contraseña incorrectos';
    }
}

// Si ya está logueado, redirigir a main.php
if (isset($_SESSION['logueado']) && $_SESSION['logueado'] === true) {
    header('Location: inicio.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Videoclub - Login</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            max-width: 400px; 
            margin: 50px auto; 
            padding: 20px;
            background-color: #f5f5f5;
        }
        .login-container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }
        .form-group { 
            margin-bottom: 20px; 
        }
        label { 
            display: block; 
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }
        input[type="text"], 
        input[type="password"] { 
            width: 100%; 
            padding: 12px; 
            border: 1px solid #ddd; 
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 16px;
        }
        input[type="text"]:focus, 
        input[type="password"]:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 5px rgba(0,123,255,0.3);
        }
        button { 
            background: #007bff; 
            color: white; 
            padding: 12px 20px; 
            border: none; 
            border-radius: 4px; 
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            font-weight: bold;
        }
        button:hover { 
            background: #0056b3; 
        }
        .error { 
            color: #d9534f; 
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
            text-align: center;
        }
        .success {
            color: #155724;
            margin-bottom: 20px;
            padding: 10px;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 4px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Videoclub - Acceso</h2>
        
        <?php if (isset($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['logout_message'])): ?>
            <div class="success"><?php echo htmlspecialchars($_SESSION['logout_message']); 
            unset($_SESSION['logout_message']); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="usuario">Usuario:</label>
                <input type="text" id="usuario" name="usuario" value="<?php echo isset($_POST['usuario']) ? htmlspecialchars($_POST['usuario']) : ''; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit">Iniciar Sesión</button>
        </form>
        
        <div style="margin-top: 20px; text-align: center; font-size: 14px; color: #666;">
            <p><strong>Usuarios de prueba:</strong></p>
            <p>admin / admin</p>
            <p>usuario / usuario</p>
        </div>
    </div>
</body>
</html>