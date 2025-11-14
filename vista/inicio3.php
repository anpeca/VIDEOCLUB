<?php
include_once "../modelo/Videoclub.php";

$vc = new Videoclub("Severo 8A");

echo "<h2>Productos añadidos al videoclub</h2>";

$vc->incluirJuego("God of War", 19.99, "PS4", 1, 1);
$vc->incluirJuego("The Last of Us Part II", 49.99, "PS4", 1, 1);
$vc->incluirDvd("Torrente", 4.5, "es", "16:9");
$vc->incluirDvd("Origen", 4.5, "es,en,fr", "16:9");
$vc->incluirDvd("El Imperio Contraataca", 3, "es,en", "16:9");
$vc->incluirCintaVideo("Los cazafantasmas", 3.5, 107);
$vc->incluirCintaVideo("El nombre de la Rosa", 1.5, 140);

echo "<h3>Listado de productos</h3>";
$vc->listarProductos();

echo "<h2>Socios registrados</h2>";

$vc->incluirSocio(new Cliente("Amancio Ortega", 1, 2));
$vc->incluirSocio(new Cliente("Pablo Picasso", 2, 2));

echo "<h3>Alquileres realizados por Amancio Ortega (socio 1)</h3>";
$vc->alquilarSocioProducto(1, 2);
$vc->alquilarSocioProducto(1, 3);

echo "<h3>Intentos de alquiler no válidos</h3>";
echo "<p>Intento de alquilar el mismo soporte (2) otra vez:</p>";
$vc->alquilarSocioProducto(1, 2);

echo "<p>Intento de alquilar un soporte adicional (6) superando el cupo:</p>";
$vc->alquilarSocioProducto(1, 6);

echo "<h2>Listado de socios y sus alquileres</h2>";
$vc->listarSocios();
?>

