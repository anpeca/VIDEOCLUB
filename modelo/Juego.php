<?php

require_once "Soporte.php";

class Juego extends Soporte {

    private string $consola;
    private int $minNumJugadores;
    private int $maxNumJugadores;

    public function __construct(string $titulo, float $precio, float $precioConIVA, string $consola, int $minNumJugadores, int $maxNumJugadores) {

        parent::__construct($titulo, $precio, $precioConIVA);
        $this->consola = $consola;
        $this->minNumJugadores = $minNumJugadores;
        $this->maxNumJugadores = $maxNumJugadores;
    }

    public function getConsola(): string {
        return $this->consola;
    }

    public function getMinNumJugadores(): int {
        return $this->minNumJugadores;
    }

    public function getMaxNumJugadores(): int {
        return $this->maxNumJugadores;
    }

    public function muestraJugadoresPosibles(): string {

        if ($this->minNumJugadores === 1 && $this->maxNumJugadores === 1) {
            return "Para un jugador";

        } elseif ($this->minNumJugadores === $this->maxNumJugadores) {
            return "Para " . $this->minNumJugadores . " jugadores";
            
        } else {
            return "De " . $this->minNumJugadores . " a " . $this->maxNumJugadores . " jugadores";
        }
    }

    public function muestraResumen(): string {
        return parent::muestraResumen() . " Consola: " . $this->consola . " Jugadores: " . $this->muestraJugadoresPosibles();
    }

    public function __toString(): string {
        return "Consola: " . $this->consola . " Jugadores: " . $this->muestraJugadoresPosibles();
    }
}
?>