<?php
include_once "../modelo/CintaVideo.php";
include_once "../modelo/Dvd.php";
include_once "../modelo/Juego.php";
include_once "../modelo/Cliente.php";

// Instanciamos clientes
$cliente1 = new Cliente("Bruce Wayne", 23);
$cliente2 = new Cliente("Clark Kent", 33);

echo "<h2>Identificadores de clientes</h2>";
echo "<p>Cliente 1: " . $cliente1->getNumero() . "</p>";
echo "<p>Cliente 2: " . $cliente2->getNumero() . "</p>";

// Instanciamos soportes
$soporte1 = new CintaVideo("Los cazafantasmas", 23, 3.5, 107);
$soporte2 = new Juego("The Last of Us Part II", 26, 49.99, "PS4", 1, 1);  
$soporte3 = new Dvd("Origen", 24, 15, "es,en,fr", "16:9");
$soporte4 = new Dvd("El Imperio Contraataca", 4, 3, "es,en", "16:9");

echo "<h2>Alquileres realizados por Bruce Wayne</h2>";
$cliente1->alquilar($soporte1);
$cliente1->alquilar($soporte2);
$cliente1->alquilar($soporte3);

echo "<h2>Intentos de alquiler inválidos</h2>";
$cliente1->alquilar($soporte1); // repetido
$cliente1->alquilar($soporte4); // cupo superado

echo "<h2>Devoluciones</h2>";
$cliente1->devolver(4); // no alquilado
$cliente1->devolver(2); // válido

echo "<h2>Nuevo intento de alquiler</h2>";
$cliente1->alquilar($soporte4); // ahora debería funcionar

echo "<h2>Soportes alquilados por Bruce Wayne</h2>";
$cliente1->listaAlquileres();

echo "<h2>Acciones del cliente Clark Kent</h2>";
$cliente2->devolver(2);
$cliente2->listaAlquileres();
?>
