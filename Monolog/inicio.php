<?php

/**
 * Punto de entrada del ejemplo con Monolog.
 *
 * Este script inicializa el autoload de Composer, crea una instancia de la
 * clase HolaMonolog y ejecuta sus métodos principales para registrar mensajes
 * de saludo y despedida en función de la hora proporcionada.
 *
 * @package Dwes\Monologos
 */

require_once __DIR__ . '/vendor/autoload.php';

use Dwes\Monologos\HolaMonolog;

// Instancia de la clase con una hora de ejemplo
$hola = new HolaMonolog(10);

// Registro de mensajes
$hola->saludar();
$hola->despedir();
