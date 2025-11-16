<?php
/**
 * Script de prueba: inicio3.php
 *
 * Este archivo simula la gestión completa del videoclub "Severo 8A".
 * Se prueban las funcionalidades principales de la clase Videoclub:
 *
 * - Inclusión de productos: se añaden juegos, DVDs y cintas de vídeo al catálogo.
 * - Inclusión de socios: se registran clientes con distintos cupos de alquiler.
 * - Alquileres: se realizan alquileres válidos y se prueban casos de error como:
 *   - Intentar alquilar un producto ya alquilado por el mismo socio.
 *   - Superar el cupo máximo de alquileres permitidos.
 * - Listado de productos y socios: se muestra el estado actual del videoclub.
 *
 * Este script permite verificar la integración entre las clases Videoclub, Cliente y Soporte,
 * así como la gestión de excepciones y el control de estado de los productos.
 */

require_once "autoload.php";

use Dwes\ProyectoVideoclub\Videoclub;
use Dwes\ProyectoVideoclub\Cliente;

echo "<h2>Gestión del Videoclub Severo 8A</h2>";

$vc = new Videoclub("Severo 8A");

echo "<hr><h3>Inclusión de productos</h3>";
$vc->incluirJuego("God of War", 19.99, "PS4", 1, 1)
   ->incluirJuego("The Last of Us Part II", 49.99, "PS4", 1, 1)
   ->incluirDvd("Torrente", 4.5, "es", "16:9")
   ->incluirDvd("Origen", 4.5, "es,en,fr", "16:9")
   ->incluirDvd("El Imperio Contraataca", 3, "es,en", "16:9")
   ->incluirCintaVideo("Los cazafantasmas", 3.5, 107)
   ->incluirCintaVideo("El nombre de la Rosa", 1.5, 140);

echo "<strong>Listado de productos:</strong><br>";
$vc->listarProductos();

echo "<hr><h3>Inclusión de socios</h3>";
$vc->incluirSocio(new Cliente("Amancio Ortega", 1))
   ->incluirSocio(new Cliente("Pablo Picasso", 2, 2));

echo "<hr><h3>Alquileres de productos</h3>";
$vc->alquilarSocioProducto(1, 2); // God of War
$vc->alquilarSocioProducto(1, 3); // Torrente

echo "<br><em>Intento de alquilar nuevamente el soporte 2 (ya alquilado):</em><br>";
$vc->alquilarSocioProducto(1, 2); // Repetido

echo "<br><em>Intento de alquilar el soporte 6 superando el cupo:</em><br>";
$vc->alquilarSocioProducto(1, 6); // Excede cupo

echo "<hr><h3>Listado de socios</h3>";
$vc->listarSocios();
?>
