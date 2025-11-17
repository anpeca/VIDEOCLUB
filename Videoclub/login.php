<?php
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);


// Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

// Usuarios válidos "globales" (en producción usar fuente segura)
$usuarios_validos = [
    'admin'   => 'admin',
    'usuario' => 'usuario'
];

$usuario  = trim($_POST['usuario'] ?? '');
$password = $_POST['password'] ?? '';

$_SESSION['last_user'] = $usuario; // rellenar form si error

// Ruta al autoload (ajusta si tu vendor/autoload.php está en otra ubicación)
$autoloadPath = __DIR__ . '/vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
}
// -----------------------------------------------------------------------------
// NOTA PARA PRUEBAS LOCALES:
// El enunciado pedía que los datos de soportes y clientes se carguen cuando el
// administrador entra (admin/admin). Para facilitar pruebas y permitir iniciar
// sesión con "maria/maria123" sin entrar antes como admin, este bloque
// autoinicializa clientes de prueba cuando no existen en la sesión.
// Si quieres ceñirte estrictamente a la práctica, comenta o borra este bloque.
// -----------------------------------------------------------------------------
if (!isset($_SESSION['clientes']) || !is_array($_SESSION['clientes'])) {
    // Autoinicialización solo para desarrollo/pruebas
    $_SESSION['clientes'] = [
        101 => ['id' => 101, 'nombre' => 'María Pérez',   'email' => 'maria@example.com', 'telefono' => '600111222', 'usuario' => 'maria', 'password' => 'maria123'],
        102 => ['id' => 102, 'nombre' => 'Juan López',    'email' => 'juan@example.com',  'telefono' => '600222333', 'usuario' => 'juan',  'password' => 'juan123'],
        103 => ['id' => 103, 'nombre' => 'Ana Rodríguez', 'email' => 'ana@example.com',   'telefono' => '600333444', 'usuario' => 'ana',   'password' => 'ana123'],
    ];
}

// -----------------------------------------------------------------------------
// 1) Primero: intentar autenticar contra clientes en sesión (si existen)
// -----------------------------------------------------------------------------
if (!empty($_SESSION['clientes']) && is_array($_SESSION['clientes'])) {
    foreach ($_SESSION['clientes'] as $clArr) {
        if (isset($clArr['usuario'], $clArr['password']) && $clArr['usuario'] === $usuario && $clArr['password'] === $password) {
            // Crear instancia Cliente y guardarla en sesión (mainCliente.php la usará)
            if (!class_exists('\Dwes\ProyectoVideoclub\Cliente')) {
                // Si la clase no está cargada, intentar autoload alternativo o error claro
                // require_once __DIR__ . '/path/to/autoload.php';
                // Aquí asumimos que el autoload ya se cargó arriba
            }
            $nombre = $clArr['nombre'] ?? '';
            $id     = isset($clArr['id']) ? (int)$clArr['id'] : 0;
            $user   = $clArr['usuario'];    
            $pass   = $clArr['password'];

            // Instanciar Cliente (el constructor puede crear el hash del password internamente)
            // Guardar sólo los datos del cliente en la sesión (no el objeto) — opción B
            $_SESSION['cliente_actual_array'] = [
                'id'       => $id,
                'nombre'   => $nombre,
                'usuario'  => $user,
                'password' => $pass,
                // añade otros campos si los necesitas (email, telefono...)
            ];

            $_SESSION['usuario']  = $user;
            $_SESSION['logueado'] = true;
            $_SESSION['tipo']     = 'cliente';

            header('Location: mainCliente.php');
            exit();
        }
    }
}

// -----------------------------------------------------------------------------
// 2) Si no coincide con clientes, comprobar usuarios "globales" (admin/usuario)
// -----------------------------------------------------------------------------
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

        // Redirigir al panel de administrador
        header('Location: mainAdmin.php');
        exit();
    }

    // Usuario "global" cliente: redirige a main.php (flujo por defecto para usuario/usuario)
    header('Location: main.php');
    exit();
}

// -----------------------------------------------------------------------------
// Credenciales incorrectas
// -----------------------------------------------------------------------------
$_SESSION['login_error'] = 'Usuario o contraseña incorrectos';
header('Location: index.php');
exit();
