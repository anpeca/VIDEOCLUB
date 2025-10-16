<?php

require_once "Soporte.php";

class Dvd extends Soporte {

    private String $idiomas;
    private String $formatoPantalla;

    public function __construct(string $titulo, float $precio, float $precioConIVA, string $idiomas, string $formatoPantalla) {

        parent::__construct($titulo, $precio, $precioConIVA);
        $this->idiomas = $idiomas;
        $this->formatoPantalla = $formatoPantalla;
    }

    public function getIdiomas(): string {
        return $this->idiomas;
    }

    public function getFormatoPantalla(): string {
        return $this->formatoPantalla;
    }


    public function muestraResumen(): string {
        return parent::muestraResumen() . " Idiomas: " . $this->idiomas . " Formato de pantalla: " . $this->formatoPantalla;
    }

    public function __toString(): string {
        return "Idiomas: " . $this->idiomas . " Formato de pantalla: " . $this->formatoPantalla;
    }
}
