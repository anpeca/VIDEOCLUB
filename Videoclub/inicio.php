<?php
// require_once "app/Dwes/ProyectoVideoclub/Soporte.php";
// require_once "app/Dwes/ProyectoVideoclub/CintaVideo.php";
// require_once "app/Dwes/ProyectoVideoclub/Dvd.php";
// require_once "app/Dwes/ProyectoVideoclub/Juego.php";
require_once "autoload.php";
use Dwes\ProyectoVideoclub\Videoclub;

use Dwes\ProyectoVideoclub\Soporte;
use Dwes\ProyectoVideoclub\CintaVideo;
use Dwes\ProyectoVideoclub\Dvd;
use Dwes\ProyectoVideoclub\Juego;

$miJuego = new Juego("The Last of Us Part II", 26, 49.99, "PS4", 1, 1); 
echo "<strong>" . $miJuego->titulo . "</strong>"; 
echo "<br>Precio: " . $miJuego->getPrecio() . " euros"; 
echo "<br>Precio IVA incluido: " . $miJuego->getPrecioConIva() . " euros";
$miJuego->muestraResumen();
?>