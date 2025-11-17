<?php
/**
 * Script de prueba: inicio1.php
 *
 * Este archivo ejecuta pruebas individuales sobre las clases Soporte, CintaVideo, Dvd y Juego.
 * Se instancian objetos de cada tipo y se muestra su información básica:
 * - Título
 * - Precio base
 * - Precio con IVA
 * - Resumen del soporte
 *
 * Este script es útil para verificar el correcto funcionamiento de los métodos
 * de acceso y resumen de cada clase, así como la herencia desde Soporte.
 */

require_once "vendor/autoload.php";

use Dwes\ProyectoVideoclub\Soporte;
use Dwes\ProyectoVideoclub\CintaVideo;
use Dwes\ProyectoVideoclub\Dvd;
use Dwes\ProyectoVideoclub\Juego;

echo "<h2>Prueba de soportes individuales</h2>";

echo "<hr><h3>Soporte genérico</h3>";
$soporte1 = new Soporte("Tenet", 22, 3);
echo "<strong>Título:</strong> " . $soporte1->getTitulo() . "<br>";
echo "<strong>Precio base:</strong> " . $soporte1->getPrecio() . " €<br>";
echo "<strong>Precio con IVA:</strong> " . $soporte1->getPrecioConIVA() . " €<br>";
echo "<strong>Resumen:</strong> " . $soporte1->muestraResumen() . "<br>";

echo "<hr><h3>Cinta de vídeo</h3>";
$miCinta = new CintaVideo("Los cazafantasmas", 23, 3.5, 107);
echo "<strong>Título:</strong> " . $miCinta->getTitulo() . "<br>";
echo "<strong>Precio base:</strong> " . $miCinta->getPrecio() . " €<br>";
echo "<strong>Precio con IVA:</strong> " . $miCinta->getPrecioConIVA() . " €<br>";
echo "<strong>Resumen:</strong> " . $miCinta->muestraResumen() . "<br>";

echo "<hr><h3>DVD</h3>";
$miDvd = new Dvd("Origen", 24, 15, "es,en,fr", "16:9");
echo "<strong>Título:</strong> " . $miDvd->getTitulo() . "<br>";
echo "<strong>Precio base:</strong> " . $miDvd->getPrecio() . " €<br>";
echo "<strong>Precio con IVA:</strong> " . $miDvd->getPrecioConIVA() . " €<br>";
echo "<strong>Resumen:</strong> " . $miDvd->muestraResumen() . "<br>";

echo "<hr><h3>Juego</h3>";
$miJuego = new Juego("The Last of Us Part II", 26, 49.99, "PS4", 1, 1);
echo "<strong>Título:</strong> " . $miJuego->getTitulo() . "<br>";
echo "<strong>Precio base:</strong> " . $miJuego->getPrecio() . " €<br>";
echo "<strong>Precio con IVA:</strong> " . $miJuego->getPrecioConIVA() . " €<br>";
echo "<strong>Resumen:</strong> " . $miJuego->muestraResumen() . "<br>";
?>
