<?php
namespace Dwes\ProyectoVideoclub;
use Dwes\ProyectoVideoclub\Util\MetacriticScraper;

// require_once "Soporte.php";

class Dvd extends Soporte
{

    public string $idiomas;
    private string $formatoPantalla;

    public function __construct(string $titulo, int $numero, float $precio, string $idiomas, string $formatoPantalla)
    {

        parent::__construct($titulo, $numero, $precio);
        $this->idiomas = $idiomas;
        $this->formatoPantalla = $formatoPantalla;
    }

    public function muestraResumen(): string
    {
        return parent::muestraResumen() . " Idiomas: {$this->idiomas}, Formato: {$this->formatoPantalla}";
    }

    public function getPuntuacion(): ?float { $url = $this->getMetacritic(); if ($url === null) return null; return MetacriticScraper::obtenerPuntuacion($url); }
}
?>