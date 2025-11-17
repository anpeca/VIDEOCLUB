<?php
/**
 * Script de prueba: inicio2.php
 *
 * Este archivo realiza pruebas sobre la clase Cliente y su interacción con los soportes.
 * Se crean dos clientes y varios productos (CintaVideo, Dvd, Juego), y se simulan operaciones de alquiler y devolución.
 *
 * Se validan los siguientes casos:
 * - Alquileres exitosos
 * - Intento de alquilar un soporte ya alquilado
 * - Superación del cupo de alquileres
 * - Devolución de soportes (válida e inválida)
 * - Alquiler tras devolución
 * - Listado de alquileres actuales
 * - Intento de devolución por un cliente sin alquileres
 *
 * Este script permite comprobar el funcionamiento de las excepciones personalizadas
 * y la lógica de control de cupo y estado de alquiler en la clase Cliente.
 */

require_once "autoload.php";

use Dwes\ProyectoVideoclub\CintaVideo;
use Dwes\ProyectoVideoclub\Dvd;
use Dwes\ProyectoVideoclub\Juego;
use Dwes\ProyectoVideoclub\Cliente;
use Dwes\ProyectoVideoclub\Util\SoporteYaAlquiladoException;
use Dwes\ProyectoVideoclub\Util\CupoSuperadoException;
use Dwes\ProyectoVideoclub\Util\SoporteNoEncontradoException;

echo "<h2>Pruebas con la clase Cliente</h2>";

echo "<hr><h3>Creación de clientes</h3>";
$cliente1 = new Cliente("Bruce Wayne", 23);
$cliente2 = new Cliente("Clark Kent", 33);

echo "<strong>Cliente 1:</strong> " . $cliente1->getNumero() . " - Bruce Wayne<br>";
echo "<strong>Cliente 2:</strong> " . $cliente2->getNumero() . " - Clark Kent<br>";

echo "<hr><h3>Instanciación de soportes</h3>";
$soporte1 = new CintaVideo("Los cazafantasmas", 23, 3.5, 107);
$soporte2 = new Juego("The Last of Us Part II", 26, 49.99, "PS4", 1, 1);  
$soporte3 = new Dvd("Origen", 24, 15, "es,en,fr", "16:9");
$soporte4 = new Dvd("El Imperio Contraataca", 4, 3, "es,en", "16:9");

echo "<ul>";
echo "<li>" . $soporte1->muestraResumen() . "</li>";
echo "<li>" . $soporte2->muestraResumen() . "</li>";
echo "<li>" . $soporte3->muestraResumen() . "</li>";
echo "<li>" . $soporte4->muestraResumen() . "</li>";
echo "</ul>";

echo "<hr><h3>Alquileres iniciales</h3>";
try {
    $cliente1->alquilar($soporte1);
    $cliente1->alquilar($soporte2);
    $cliente1->alquilar($soporte3);
    echo "Alquileres realizados correctamente.<br>";
} catch (\Exception $e) {
    echo "Error al alquilar: " . $e->getMessage() . "<br>";
}

echo "<hr><h3>Reintento de alquiler ya existente</h3>";
try {
    $cliente1->alquilar($soporte1);
} catch (\Exception $e) {
    echo "Reintento fallido: " . $e->getMessage() . "<br>";
}

echo "<hr><h3>Intento de alquiler superando el cupo</h3>";
try {
    $cliente1->alquilar($soporte4);
} catch (\Exception $e) {
    echo "Cupo excedido: " . $e->getMessage() . "<br>";
}

echo "<hr><h3>Devoluciones</h3>";
try {
    $cliente1->devolver(4); // no lo tiene
    $cliente1->devolver(2); // sí lo tiene
    echo "Devoluciones procesadas.<br>";
} catch (\Exception $e) {
    echo "Error al devolver: " . $e->getMessage() . "<br>";
}

echo "<hr><h3>Alquiler tras devolución</h3>";
try {
    $cliente1->alquilar($soporte4);
    echo "Alquiler realizado tras devolución.<br>";
} catch (\Exception $e) {
    echo "Error al alquilar tras devolución: " . $e->getMessage() . "<br>";
}

echo "<hr><h3>Listado de alquileres actuales</h3>";
$cliente1->listaAlquileres();

echo "<hr><h3>Clark Kent intenta devolver sin alquileres</h3>";
try {
    $cliente2->devolver(2);
} catch (\Exception $e) {
    echo "Clark Kent no tiene ese soporte: " . $e->getMessage() . "<br>";
}
?>
