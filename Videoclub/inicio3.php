<?php
// require_once "app/Dwes/proyectoVideoclub/Videoclub.php";
// require_once "app/Dwes/proyectoVideoclub/Cliente.php";

require_once __DIR__ . '/vendor/autoload.php';
use Dwes\ProyectoVideoclub\Videoclub;
use Dwes\ProyectoVideoclub\Cliente;

$vc = new Videoclub("Severo 8A");

// voy a incluir unos cuantos soportes de prueba
// Primer parámetro: URL de Metacritic (string) o null si no la hay

// Juegos (ejemplo de URLs; reemplaza por las correctas si las encuentras)
$vc->incluirJuego('https://www.metacritic.com/game/ps4/god-of-war', "God of War", 19.99, "PS4", 1, 1); 
$vc->incluirJuego('https://www.metacritic.com/game/ps4/the-last-of-us-part-ii', "The Last of Us Part II", 49.99, "PS4", 1, 1);

// DVDs (si no hay página en Metacritic para la película, usa null)
$vc->incluirDvd("https://www.metacritic.com/movie/inception", "Origen", 4.5, "es,en,fr", "16:9");
$vc->incluirDvd("https://www.metacritic.com/movie/star-wars-episode-v---the-empire-strikes-back", "El Imperio Contraataca", 3, "es,en","16:9");

// Cintas de vídeo / películas
$vc->incluirCintaVideo('https://www.metacritic.com/movie/ghostbusters', "Los cazafantasmas", 3.5, 107); 
$vc->incluirCintaVideo("https://www.metacritic.com/movie/the-name-of-the-rose", "El nombre de la Rosa", 1.5, 140);

// listo los productos 
$vc->listarProductos(); 

// voy a crear algunos socios 
$vc->incluirSocio(new Cliente("Amancio Ortega",1));
$vc->incluirSocio(new Cliente("Pablo Picasso", 2,2)); 

$vc->alquilarSocioProducto(1,2); 
$vc->alquilarSocioProducto(1,3); 
// alquilo otra vez el soporte 2 al socio 1. 
// no debe dejarme porque ya lo tiene alquilado 
try {
    $vc->alquilarSocioProducto(1,2);
} catch (\Throwable $e) {
    // ya registrado en logs; opcionalmente mostrar por pantalla
    echo "Error al intentar alquilar de nuevo: " . $e->getMessage() . PHP_EOL;
}
// alquilo el soporte 6 al socio 1. 
// no se puede porque el socio 1 tiene 2 alquileres como máximo 
try {
    $vc->alquilarSocioProducto(1,6);
} catch (\Throwable $e) {
    echo "Error al intentar alquilar soporte 6: " . $e->getMessage() . PHP_EOL;
}

// listo los socios 
$vc->listarSocios();

// -----------------------------
// Nuevo bloque: mostrar alquileres de un socio con puntuación Metacritic
// -----------------------------

// Número de socio a consultar (ajusta si quieres otro)
$socioNumero = 1;

// Obtener socio desde Videoclub (usa el método obtenerSocioPorNumero que añadimos)
$socio = $vc->obtenerSocioPorNumero($socioNumero);

if ($socio === null) {
    echo "Socio con número {$socioNumero} no encontrado." . PHP_EOL;
    exit;
}

echo PHP_EOL . "Alquileres del socio {$socio->getNombre()} (nº {$socio->getNumero()}):" . PHP_EOL;

$alquileres = $socio->getAlquileres(); // devuelve array de Soporte

if (empty($alquileres)) {
    echo "No tiene alquileres activos." . PHP_EOL;
} else {
    foreach ($alquileres as $soporte) {
        $titulo = method_exists($soporte, 'getTitulo') ? $soporte->getTitulo() : '(sin título)';
        $puntuacionText = 'sin puntuación';
        try {
            // getPuntuacion puede hacer scraping; puede tardar o lanzar excepciones
            if (method_exists($soporte, 'getPuntuacion')) {
                $p = $soporte->getPuntuacion();
                if ($p !== null) {
                    $puntuacionText = round($p, 1) . '/100';
                }
            }
        } catch (\Throwable $e) {
            // registrar o ignorar; mostramos sin puntuación
            $puntuacionText = 'sin puntuación';
        }
        echo "- {$titulo}  —  Puntuación Metacritic: {$puntuacionText}" . PHP_EOL;
    }
}
