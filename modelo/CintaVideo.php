<?php
require_once "Soporte.php";
class CintaVideo extends Soporte {

    private int $duracion;

    public function __construct(string $titulo, int $numero, float $precio, int $duracion) {
    parent::__construct($titulo, $numero, $precio);
    $this->duracion = $duracion;
    }


    public function getDuracion(): int {
        return $this->duracion;
    }

    public function muestraResumen(): string {
        return parent::muestraResumen() . " Duración: " . $this->duracion . " minutos";
    }
}
?>
