<?php

require_once __DIR__ . '/vendor/autoload.php';

use Dwes\Monologos\HolaMonolog;

$hola = new HolaMonolog(10);

$hola->saludar();
$hola->despedir();