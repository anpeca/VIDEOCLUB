<?php
spl_autoload_register(function($nombreClase) {
    $ruta = "app/" . $nombreClase . '.php';
    $ruta = str_replace("\\", "/", $ruta); // Convertir \ en /
    if (file_exists($ruta)) {
        require_once $ruta;
    }
});
?>