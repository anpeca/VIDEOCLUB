<?php
spl_autoload_register(function ($class) {
    $prefix = 'Dwes\\ProyectoVideoclub\\';
    $baseDir = __DIR__ . '/ProyectoVideoclub/'; // ← carpeta real donde están tus clases

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});
