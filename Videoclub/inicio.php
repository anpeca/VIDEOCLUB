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

// El código de prueba sigue igual...
$soporte1 = new Soporte("Tenet", 22, 3);
$miCinta = new CintaVideo("Los cazafantasmas", 23, 3.5, 107);
$miDvd = new Dvd("Origen", 24, 15, "es,en,fr", "16:9");
$miJuego = new Juego("The Last of Us Part II", 26, 49.99, "PS4", 1, 1);
?>