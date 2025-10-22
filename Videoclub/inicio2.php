<?php
require_once "autoload.php";

use Dwes\ProyectoVideoclub\CintaVideo;
use Dwes\ProyectoVideoclub\Dvd;
use Dwes\ProyectoVideoclub\Juego;
use Dwes\ProyectoVideoclub\Cliente;
use Dwes\ProyectoVideoclub\Util\SoporteYaAlquiladoException;
use Dwes\ProyectoVideoclub\Util\CupoSuperadoException;
use Dwes\ProyectoVideoclub\Util\SoporteNoEncontradoException;

// Crear clientes
$cliente1 = new Cliente("Bruce Wayne", 23);
$cliente2 = new Cliente("Clark Kent", 33);

echo "<br>El identificador del cliente 1 es: " . $cliente1->getNumero();
echo "<br>El identificador del cliente 2 es: " . $cliente2->getNumero();

// Instanciar soportes
$soporte1 = new CintaVideo("Los cazafantasmas", 23, 3.5, 107);
$soporte2 = new Juego("The Last of Us Part II", 26, 49.99, "PS4", 1, 1);  
$soporte3 = new Dvd("Origen", 24, 15, "es,en,fr", "16:9");
$soporte4 = new Dvd("El Imperio Contraataca", 4, 3, "es,en", "16:9");

// Agrupar alquileres iniciales
try {
    $cliente1->alquilar($soporte1);
    $cliente1->alquilar($soporte2);
    $cliente1->alquilar($soporte3);
} catch (\Exception $e) {
    echo "Error al alquilar: " . $e->getMessage() . "<br>";
}

// Intentar alquilar de nuevo un soporte ya alquilado
try {
    $cliente1->alquilar($soporte1);
} catch (\Exception $e) {
    echo "Reintento fallido: " . $e->getMessage() . "<br>";
}

// Intentar alquilar superando el cupo
try {
    $cliente1->alquilar($soporte4);
} catch (\Exception $e) {
    echo "Cupo excedido: " . $e->getMessage() . "<br>";
}

// Devoluciones
try {
    $cliente1->devolver(4); // no lo tiene
    $cliente1->devolver(2); // sí lo tiene
} catch (\Exception $e) {
    echo "Error al devolver: " . $e->getMessage() . "<br>";
}

// Alquilar otro soporte tras liberar uno
try {
    $cliente1->alquilar($soporte4);
} catch (\Exception $e) {
    echo "Error al alquilar tras devolución: " . $e->getMessage() . "<br>";
}

// Listar alquileres
$cliente1->listaAlquileres();

// Cliente sin alquileres intenta devolver
try {
    $cliente2->devolver(2);
} catch (\Exception $e) {
    echo "Clark Kent no tiene ese soporte: " . $e->getMessage() . "<br>";
}
?>
