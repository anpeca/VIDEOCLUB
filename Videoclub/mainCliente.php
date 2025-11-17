<?php
// Incluir autoload (ruta robusta subiendo directorios)
$autoload = __DIR__ . '/vendor/autoload.php';
if (!file_exists($autoload)) {
    $dir = __DIR__;
    for ($i = 0; $i < 8; $i++) {
        $try = $dir . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
        if (file_exists($try)) {
            $autoload = $try;
            break;
        }
        $dir = dirname($dir);
    }
}
if (file_exists($autoload)) {
    require_once $autoload;
}

// Iniciar sesión (autoload ya cargado)
session_start();

/**
 * Fábrica ligera: crea instancia de Soporte (Juego|Dvd|CintaVideo) desde un array.
 * Devuelve el mismo objeto si ya recibe un objeto.
 */
function creaSoporteDesdeArray($item) {
    if (is_object($item)) return $item;
    if (!is_array($item)) return null;

    $tipo = strtolower($item['tipo'] ?? '');
    $id = isset($item['id']) ? (int)$item['id'] : (int)($item['numero'] ?? 0);
    $titulo = $item['titulo'] ?? ($item['titulo'] ?? '');
    $precio = isset($item['precio']) ? (float)$item['precio'] : 0.0;

    switch ($tipo) {
        case 'juego':
        case 'game':
            $consola = $item['consola'] ?? '';
            $min = isset($item['minNumJugadores']) ? (int)$item['minNumJugadores'] : (int)($item['min'] ?? 1);
            $max = isset($item['maxNumJugadores']) ? (int)$item['maxNumJugadores'] : (int)($item['max'] ?? $min);
            return new \Dwes\ProyectoVideoclub\Juego($titulo, $id, $precio, $consola, $min, $max);
        case 'dvd':
            $idiomas = $item['idiomas'] ?? '';
            $formato = $item['formatoPantalla'] ?? ($item['formato'] ?? '');
            return new \Dwes\ProyectoVideoclub\Dvd($titulo, $id, $precio, $idiomas, $formato);
        default:
            // Cinta de vídeo por defecto
            $dur = isset($item['duracion']) ? (int)$item['duracion'] : (int)($item['dur'] ?? 0);
            return new \Dwes\ProyectoVideoclub\CintaVideo($titulo, $id, $precio, $dur);
    }
}

// Reconstruir Cliente desde array en sesión (opción B) o usar objeto en sesión
$cliente = null;
if (!empty($_SESSION['cliente_actual_array']) && is_array($_SESSION['cliente_actual_array'])) {
    $arr = $_SESSION['cliente_actual_array'];
    $cliente = new \Dwes\ProyectoVideoclub\Cliente(
        $arr['nombre'] ?? '',
        isset($arr['id']) ? (int)$arr['id'] : 0,
        3,
        $arr['usuario'] ?? null,
        $arr['password'] ?? null
    );
} elseif (isset($_SESSION['cliente_actual']) && is_object($_SESSION['cliente_actual']) && $_SESSION['cliente_actual'] instanceof \Dwes\ProyectoVideoclub\Cliente) {
    $cliente = $_SESSION['cliente_actual'];
}

// Protección de acceso: usuario logueado, tipo cliente y cliente válido
if (empty($_SESSION['usuario']) || ($_SESSION['tipo'] ?? '') !== 'cliente' || !$cliente) {
    die("Error - debe <a href='index.php'>identificarse</a> como cliente.<br />");
}

// Obtener alquileres desde la sesión (estructura recomendada)
// $_SESSION['alquileres'][<clienteId>] = [ idSoporte, ... ]
$clientId = (int)$cliente->getNumero();
$alquileres = [];

// Si hay IDs de alquileres en sesión, mapearlos a objetos/arrays para la vista
if (!empty($_SESSION['alquileres'][$clientId]) && is_array($_SESSION['alquileres'][$clientId])) {
    foreach ($_SESSION['alquileres'][$clientId] as $sid) {
        // buscar soporte por id en $_SESSION['soportes']
        if (!empty($_SESSION['soportes'][$sid]) && is_array($_SESSION['soportes'][$sid])) {
            $sArr = $_SESSION['soportes'][$sid];
            $alquileres[] = creaSoporteDesdeArray($sArr);
            continue;
        }
        // alternativa: buscar en valores si la estructura no usa claves por id
        if (!empty($_SESSION['soportes']) && is_array($_SESSION['soportes'])) {
            foreach ($_SESSION['soportes'] as $sItem) {
                $sItemId = isset($sItem['id']) ? (int)$sItem['id'] : (int)($sItem['numero'] ?? 0);
                if ($sItemId === (int)$sid) {
                    $alquileres[] = creaSoporteDesdeArray($sItem);
                    break;
                }
            }
        }
    }
} else {
    // fallback: si el objeto Cliente realmente mantiene soportes (opción A), usar getAlquileres()
    if (method_exists($cliente, 'getAlquileres')) {
        $alquileres = $cliente->getAlquileres();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Alquileres - Videoclub</title>
    <style>
        body { font-family: Arial, sans-serif; margin:20px; background:#f5f5f5; }
        .container { max-width:1200px; margin:0 auto; background:#fff; padding:20px; border-radius:8px; box-shadow:0 2px 10px rgba(0,0,0,0.1); }
        h1 { color:#333; border-bottom:2px solid #007bff; padding-bottom:10px; }
        .alquiler-card { border:1px solid #ddd; border-radius:5px; padding:15px; margin:10px 0; background:#f9f9f9; }
        .info-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:10px; margin:10px 0; }
        .label { font-weight:bold; color:#555; }
        .logout-link { float:right; background:#dc3545; color:#fff; padding:8px 15px; text-decoration:none; border-radius:4px; }
        .soporte-type { display:inline-block; background:#007bff; color:#fff; padding:3px 8px; border-radius:3px; font-size:0.8em; margin-left:10px; }
        .no-alquileres { text-align:center; padding:40px; color:#666; font-style:italic; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-info">
            <h1>Bienvenido, <?= htmlspecialchars($cliente->getNombre()) ?></h1>
            <p>Número de cliente: <?= htmlspecialchars($cliente->getNumero()) ?> |
               Usuario: <?= htmlspecialchars(method_exists($cliente, 'getUser') ? $cliente->getUser() : ($_SESSION['usuario'] ?? '')) ?></p>
            <a href="logout.php" class="logout-link">Cerrar Sesión</a>
            <div style="clear:both"></div>
        </div>

        <h2>Mis Alquileres Activos</h2>

        <?php
        $maxAlq = method_exists($cliente, 'getMaxAlquilerConcurrente') ? $cliente->getMaxAlquilerConcurrente() : 3;
        if (count($alquileres) > 0): ?>
            <p>Tienes <strong><?= count($alquileres) ?></strong> soporte(s) alquilado(s) de un máximo de <?= $maxAlq ?></p>

            <?php foreach ($alquileres as $soporte): ?>
                <div class="alquiler-card">
                    <h3>
                        <?= htmlspecialchars(method_exists($soporte, 'getTitulo') ? $soporte->getTitulo() : ($soporte->titulo ?? 'Soporte')) ?>
                        <span class="soporte-type"><?= htmlspecialchars(is_object($soporte) ? (new ReflectionClass($soporte))->getShortName() : ($soporte['tipo'] ?? 'Soporte')) ?></span>
                    </h3>

                    <div class="info-grid">
                        <div>
                            <span class="label">Número:</span>
                            <?= htmlspecialchars(method_exists($soporte, 'getNumero') ? $soporte->getNumero() : ($soporte->numero ?? '')) ?>
                        </div>
                        <div>
                            <span class="label">Precio:</span>
                            <?= htmlspecialchars(method_exists($soporte, 'getPrecio') ? $soporte->getPrecio() : ($soporte->precio ?? '')) ?> €
                        </div>
                        <div>
                            <span class="label">Precio con IVA:</span>
                            <?= htmlspecialchars(method_exists($soporte, 'getPrecioConIva') ? $soporte->getPrecioConIva() : (method_exists($soporte, 'getPrecioConIVA') ? $soporte->getPrecioConIVA() : ($soporte->precio ?? ''))) ?> €
                        </div>
                    </div>

                    <?php
                    $className = is_object($soporte) ? get_class($soporte) : strtolower($soporte['tipo'] ?? '');
                    if ($className === 'Dwes\ProyectoVideoclub\CintaVideo' || strtolower($className) === 'cintavideo'): ?>
                        <div class="info-grid"><div><span class="label">Duración:</span> <?= htmlspecialchars($soporte->duracion ?? $soporte['duracion'] ?? '') ?> minutos</div></div>
                    <?php elseif ($className === 'Dwes\ProyectoVideoclub\Dvd' || strtolower($className) === 'dvd'): ?>
                        <div class="info-grid">
                            <div><span class="label">Idiomas:</span> <?= htmlspecialchars($soporte->idiomas ?? $soporte['idiomas'] ?? '') ?></div>
                            <div><span class="label">Formato Pantalla:</span> <?= htmlspecialchars($soporte->formatoPantalla ?? $soporte['formatoPantalla'] ?? $soporte['formato'] ?? '') ?></div>
                        </div>
                    <?php elseif ($className === 'Dwes\ProyectoVideoclub\Juego' || strtolower($className) === 'juego'): ?>
                        <div class="info-grid">
                            <div><span class="label">Consola:</span> <?= htmlspecialchars(method_exists($soporte, 'getConsola') ? $soporte->getConsola() : ($soporte->consola ?? $soporte['consola'] ?? '')) ?></div>
                            <div><span class="label">Jugadores:</span> <?= htmlspecialchars(method_exists($soporte, 'muestraJugadoresPosibles') ? $soporte->muestraJugadoresPosibles() : '') ?></div>
                        </div>
                    <?php endif; ?>

                    <div style="margin-top:10px;border-top:1px solid #ddd;padding-top:10px;">
                        <span class="label">Resumen:</span><br>
                        <?php
                        if (method_exists($soporte, 'muestraResumen')) {
                            $resumen = $soporte->muestraResumen();
                        } else {
                            $resumen = method_exists($soporte, 'getTitulo') ? $soporte->getTitulo() : ($soporte->titulo ?? $soporte['titulo'] ?? 'Soporte');
                        }
                        echo nl2br(htmlspecialchars($resumen));
                        ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-alquileres">
                <h3>No tienes alquileres activos</h3>
                <p>Actualmente no tienes ningún soporte alquilado.</p>
                <p>Visita nuestro catálogo para realizar nuevos alquileres.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
