<?php
namespace Dwes\ProyectoVideoclub;
use Dwes\ProyectoVideoclub\Util\MetacriticScraper;

// require_once "Soporte.php";
class CintaVideo extends Soporte {

    private int $duracion;

    public function __construct(string $titulo, float $precio, float $precioConIVA, int $duracion) {
        parent::__construct($titulo, $precio, $precioConIVA);
        $this->duracion = $duracion;
    }

    public function getDuracion(): int {
        return $this->duracion;
    }

    public function muestraResumen(): string {
        return parent::muestraResumen() . " Duración: " . $this->duracion . " minutos";
    }

    public function getPuntuacion(): ?float { $url = $this->getMetacritic(); if ($url === null) return null; return MetacriticScraper::obtenerPuntuacion($url); }
}
?>