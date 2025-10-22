<?php
namespace Dwes\ProyectoVideoclub;

/**
 * Clase Juego
 *
 * Representa un soporte de tipo videojuego dentro del sistema del videoclub.
 * Hereda de la clase Soporte y añade propiedades específicas como consola y número de jugadores.
 */
class Juego extends Soporte {

    /** @var string Consola para la que está disponible el juego (ej: PS4, Xbox) */
    private string $consola;

    /** @var int Número mínimo de jugadores que pueden participar */
    private int $minNumJugadores;

    /** @var int Número máximo de jugadores que pueden participar */
    private int $maxNumJugadores;

    /**
     * Constructor de Juego
     *
     * @param string $titulo Título del juego
     * @param int $numero Número identificador del soporte
     * @param float $precio Precio base del juego
     * @param string $consola Consola compatible con el juego
     * @param int $minNumJugadores Número mínimo de jugadores
     * @param int $maxNumJugadores Número máximo de jugadores
     */
    public function __construct(string $titulo, int $numero, float $precio, string $consola, int $minNumJugadores, int $maxNumJugadores) {
        parent::__construct($titulo, $numero, $precio); 
        $this->consola = $consola;
        $this->minNumJugadores = $minNumJugadores;
        $this->maxNumJugadores = $maxNumJugadores;
    }

    /**
     * Devuelve la consola compatible con el juego
     *
     * @return string
     */
    public function getConsola(): string {
        return $this->consola;
    }

    /**
     * Devuelve el número mínimo de jugadores
     *
     * @return int
     */
    public function getMinNumJugadores(): int {
        return $this->minNumJugadores;
    }

    /**
     * Devuelve el número máximo de jugadores
     *
     * @return int
     */
    public function getMaxNumJugadores(): int {
        return $this->maxNumJugadores;
    }

    /**
     * Devuelve una descripción del rango de jugadores posibles
     *
     * @return string
     */
    public function muestraJugadoresPosibles(): string {
        if ($this->minNumJugadores === 1 && $this->maxNumJugadores === 1) {
            return "Para un jugador";
        } elseif ($this->minNumJugadores === $this->maxNumJugadores) {
            return "Para " . $this->minNumJugadores . " jugadores";
        } else {
            return "De " . $this->minNumJugadores . " a " . $this->maxNumJugadores . " jugadores";
        }
    }

    /**
     * Muestra un resumen del juego incluyendo consola y jugadores
     *
     * @return string
     */
    public function muestraResumen(): string {
        return parent::muestraResumen() . " Consola: " . $this->consola . " Jugadores: " . $this->muestraJugadoresPosibles();
    }

    /**
     * Representación textual del juego
     *
     * @return string
     */
    public function __toString(): string {
        return "Consola: " . $this->consola . " Jugadores: " . $this->muestraJugadoresPosibles();
    }
}
?>
