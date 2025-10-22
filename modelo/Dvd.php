<?php

require_once "Soporte.php";

class Dvd extends Soporte {

    public string $idiomas;
    private string $formatoPantalla;

    public function __construct(string $titulo, int $numero, float $precio, string $idiomas, string $formatoPantalla)
    {

        parent::__construct($titulo, $numero, $precio);
        $this->idiomas = $idiomas;
        $this->formatoPantalla = $formatoPantalla;
    }

    public function muestraResumen(): string {
        
        return "Título: {$this->getTitulo()}, Nº: {$this->getNumero()}, Precio: {$this->getPrecio()}€ Idiomas: {$this->idiomas}, Formato: {$this->formatoPantalla}";
    }

}
?>
