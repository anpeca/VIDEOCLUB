<?php

session_start();

include_once "autoload.php";

//verificamos que el usuario sea cliente:

if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] !== 'cliente' || !isset($_SESSION['cliente_actual'])) {
die("Error - debe <a href='index.php'>identificarse</a> como cliente.<br />");
}


$cliente = $_SESSION['cliente_actual'];
$alquileres = $cliente->getAlquileres();

?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Alquileres - Videoclub</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }
        .alquiler-card {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin: 10px 0;
            background-color: #f9f9f9;
        }
        .alquiler-card h3 {
            margin-top: 0;
            color: #007bff;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
            margin: 10px 0;
        }
        .info-item {
            padding: 5px 0;
        }
        .label {
            font-weight: bold;
            color: #555;
        }
        .no-alquileres {
            text-align: center;
            padding: 40px;
            color: #666;
            font-style: italic;
        }
        .header-info {
            background-color: #e9ecef;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .logout-link {
            float: right;
            background-color: #dc3545;
            color: white;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 4px;
        }
        .logout-link:hover {
            background-color: #c82333;
        }
        .soporte-type {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 0.8em;
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-info">
            <h1>Bienvenido, <?= htmlspecialchars($cliente->getNombre()) ?></h1>
            <p>Número de cliente: <?= htmlspecialchars($cliente->getNumero()) ?> | 
               Usuario: <?= htmlspecialchars($cliente->getUser()) ?></p>
            <a href="logout.php" class="logout-link">Cerrar Sesión</a>
            <div style="clear: both;"></div>
        </div>

        <h2>Mis Alquileres Activos</h2>
        
        <?php if (count($alquileres) > 0): ?>
            <p>Tienes <strong><?= count($alquileres) ?></strong> soporte(s) alquilado(s) de un máximo de <?= $cliente->maxAlquilerConcurrente ?? 3 ?></p>
            
            <?php foreach ($alquileres as $soporte): ?>
                <div class="alquiler-card">
                    <h3>
                        <?= htmlspecialchars($soporte->titulo) ?>
                        <span class="soporte-type">
                            <?= htmlspecialchars(get_class($soporte)) ?>
                        </span>
                    </h3>
                    
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="label">Número:</span> 
                            <?= htmlspecialchars($soporte->numero) ?>
                        </div>
                        <div class="info-item">
                            <span class="label">Precio:</span> 
                            <?= htmlspecialchars($soporte->getPrecio()) ?> €
                        </div>
                        <div class="info-item">
                            <span class="label">Precio con IVA:</span> 
                            <?= htmlspecialchars($soporte->getPrecioConIva()) ?> €
                        </div>
                    </div>

                    <!-- Información específica según el tipo de soporte -->
                    <?php if (get_class($soporte) === 'Dwes\ProyectoVideoclub\CintaVideo'): ?>
                        <div class="info-grid">
                            <div class="info-item">
                                <span class="label">Duración:</span> 
                                <?= htmlspecialchars($soporte->duracion) ?> minutos
                            </div>
                        </div>
                    <?php elseif (get_class($soporte) === 'Dwes\ProyectoVideoclub\Dvd'): ?>
                        <div class="info-grid">
                            <div class="info-item">
                                <span class="label">Idiomas:</span> 
                                <?= htmlspecialchars($soporte->idiomas) ?>
                            </div>
                            <div class="info-item">
                                <span class="label">Formato Pantalla:</span> 
                                <?= htmlspecialchars($soporte->formatoPantalla) ?>
                            </div>
                        </div>
                    <?php elseif (get_class($soporte) === 'Dwes\ProyectoVideoclub\Juego'): ?>
                        <div class="info-grid">
                            <div class="info-item">
                                <span class="label">Consola:</span> 
                                <?= htmlspecialchars($soporte->consola) ?>
                            </div>
                            <div class="info-item">
                                <span class="label">Jugadores:</span> 
                                <?= htmlspecialchars($soporte->muestraJugadoresPosibles()) ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Resumen completo del soporte -->
                    <div class="info-item" style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #ddd;">
                        <span class="label">Resumen:</span><br>
                        <?php 
                        // Llamamos al método muestraResumen pero capturamos la salida
                        ob_start();
                        $soporte->muestraResumen();
                        $resumen = ob_get_clean();
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