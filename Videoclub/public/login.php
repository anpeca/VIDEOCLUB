<?php
session_start();

// Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

// Usuarios válidos (en producción usar fuente segura)
$usuarios_validos = [
    'admin'   => 'admin',
    'usuario' => 'usuario'
];

$usuario  = trim($_POST['usuario'] ?? '');
$password = $_POST['password'] ?? '';

$_SESSION['last_user'] = $usuario; // rellenar form si error

// Comprobación rápida contra usuarios "globales" (admin / usuario)
if (isset($usuarios_validos[$usuario]) && $usuarios_validos[$usuario] === $password) {
    // Login correcto: inicializar sesión base
    $_SESSION['usuario']  = $usuario;
    $_SESSION['logueado'] = true;
    $_SESSION['tipo']     = ($usuario === 'admin') ? 'admin' : 'cliente';

    // Si es admin, cargar datos de prueba (arrays asociativos) en la sesión
    if ($usuario === 'admin') {
        $_SESSION['soportes'] = [
            1 => ['id' => 1, 'tipo' => 'CintaVideo', 'titulo' => 'Tenet',  'precio' => 3.00,  'cantidad' => 2],
            2 => ['id' => 2, 'tipo' => 'CintaVideo', 'titulo' => 'Los cazafantasmas', 'precio' => 3.50, 'cantidad' => 1],
            3 => ['id' => 3, 'tipo' => 'Dvd',       'titulo' => 'Origen', 'precio' => 4.50,  'cantidad' => 5],
            4 => ['id' => 4, 'tipo' => 'Dvd',       'titulo' => 'El Imperio Contraataca', 'precio' => 3.00, 'cantidad' => 3],
            5 => ['id' => 5, 'tipo' => 'Juego',     'titulo' => 'The Last of Us Part II', 'precio' => 49.99, 'cantidad' => 1],
        ];

        // Clientes de prueba con usuario/password para poder iniciar sesión como cliente
        $_SESSION['clientes'] = [
            101 => [
                'id' => 101,
                'nombre' => 'María Pérez',
                'email' => 'maria@example.com',
                'telefono' => '600111222',
                'usuario' => 'maria',
                'password' => 'maria123' // sólo para pruebas
            ],
            102 => [
                'id' => 102,
                'nombre' => 'Juan López',
                'email' => 'juan@example.com',
                'telefono' => '600222333',
                'usuario' => 'juan',
                'password' => 'juan123'
            ],
            103 => [
                'id' => 103,
                'nombre' => 'Ana Rodríguez',
                'email' => 'ana@example.com',
                'telefono' => '600333444',
                'usuario' => 'ana',
                'password' => 'ana123'
            ],
        ];

        // redirigir al panel de administrador
        header('Location: mainAdmin.php');
        exit();
    }

    // Usuario "global" cliente: redirige a main.php (flujo por defecto)
    header('Location: main.php');
    exit();
}

// Si no coincide con usuarios globales, intentamos autenticar contra clientes de prueba en sesión
if (!empty($_SESSION['clientes']) && is_array($_SESSION['clientes'])) {
    foreach ($_SESSION['clientes'] as $clArr) {
        if (isset($clArr['usuario'], $clArr['password']) && $clArr['usuario'] === $usuario && $clArr['password'] === $password) {
            // Creamos una instancia de Cliente y la guardamos en sesión como cliente_actual
            require_once 'autoload.php';
            $nombre = $clArr['nombre'] ?? '';
            $id     = isset($clArr['id']) ? (int)$clArr['id'] : 0;
            $user   = $clArr['usuario'];
            $pass   = $clArr['password'];

            // Usamos el constructor que acepta user/plainPassword opcionales
            $clienteObj = new \Dwes\ProyectoVideoclub\Cliente($nombre, $id, 3, $user, $pass);

            // Guardamos en sesión la instancia del cliente y marcamos sesión como cliente
            $_SESSION['cliente_actual'] = $clienteObj;
            $_SESSION['usuario']  = $user;
            $_SESSION['logueado'] = true;
            $_SESSION['tipo']     = 'cliente';

            header('Location: mainCliente.php');
            exit();
        }
    }
}

// Credenciales incorrectas
$_SESSION['login_error'] = 'Usuario o contraseña incorrectos';
header('Location: index.php');
exit();
