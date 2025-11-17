<?php
/**
 * Autocargador de clases para el proyecto Videoclub
 *
 * Utiliza `spl_autoload_register` para registrar una función que se ejecuta automáticamente
 * cuando se intenta instanciar una clase que no ha sido incluida aún.
 *
 * La función construye la ruta del archivo correspondiente a la clase solicitada,
 * reemplazando los separadores de espacio de nombres (`\`) por barras (`/`),
 * y luego incluye el archivo si existe.
 *
 * Esto permite cargar clases de forma automática sin necesidad de hacer `require_once` manualmente.
 */

spl_autoload_register(function($nombreClase) {
    $ruta = "app/" . $nombreClase . '.php';
    $ruta = str_replace("\\", "/", $ruta); // Convertir \ en /
    if (file_exists($ruta)) {
        require_once $ruta;
    }
});
?>
