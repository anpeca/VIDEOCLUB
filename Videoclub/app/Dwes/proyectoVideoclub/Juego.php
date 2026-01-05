<?php
namespace Dwes\ProyectoVideoclub;
use Dwes\ProyectoVideoclub\Util\MetacriticScraper;

/**
 * Representa un juego como tipo de Soporte.
 *
 * - Contiene información sobre la consola y el rango de jugadores.
 * - Proporciona métodos para obtener esos datos y para construir resúmenes legibles.
 * - getPuntuacion() delega en MetacriticScraper si se ha configurado una URL de Metacritic.
 */
class Juego extends Soporte {

    /** Consola para la que está diseñado el juego (por ejemplo "PS5", "Switch") */
    private string $consola;

    /** Número mínimo de jugadores */
    private int $minNumJugadores;

    /** Número máximo de jugadores */
    private int $maxNumJugadores;

    /**
     * Constructor.
     *
     * @param string      $titulo            Título del juego.
     * @param int         $numero            Identificador del soporte.
     * @param float       $precio            Precio del soporte.
     * @param string      $consola           Consola objetivo.
     * @param int         $minNumJugadores   Mínimo de jugadores.
     * @param int         $maxNumJugadores   Máximo de jugadores.
     * @param string|null $metacritic        URL opcional de Metacritic para obtener puntuación.
     */
    public function __construct(string $titulo, int $numero, float $precio, string $consola, int $minNumJugadores, int $maxNumJugadores, ?string $metacritic = null) {
        parent::__construct($titulo, $numero, $precio, $metacritic);
        $this->consola = $consola;
        $this->minNumJugadores = $minNumJugadores;
        $this->maxNumJugadores = $maxNumJugadores;
    }

    /** Devuelve la consola del juego */
    public function getConsola(): string {
        return $this->consola;
    }

    /** Devuelve el número mínimo de jugadores */
    public function getMinNumJugadores(): int {
        return $this->minNumJugadores;
    }

    /** Devuelve el número máximo de jugadores */
    public function getMaxNumJugadores(): int {
        return $this->maxNumJugadores;
    }

    /**
     * Devuelve una cadena legible que describe el rango de jugadores.
     *
     * - "Para un jugador" si min y max son 1.
     * - "Para N jugadores" si min == max != 1.
     * - "De X a Y jugadores" en el resto de casos.
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
     * Construye un resumen del soporte incluyendo información específica del juego.
     *
     * Se apoya en parent::muestraResumen() y añade consola y jugadores.
     */
    public function muestraResumen(): string {
        return parent::muestraResumen() . " Consola: " . $this->consola . " Jugadores: " . $this->muestraJugadoresPosibles();
    }

    /**
     * Representación en cadena del juego.
     *
     * Devuelve una línea concisa con consola y rango de jugadores.
     */
    public function __toString(): string {
        return "Consola: " . $this->consola . " Jugadores: " . $this->muestraJugadoresPosibles();
    }

    /**
     * Obtiene la puntuación desde Metacritic si se proporcionó una URL.
     *
     * - Devuelve null si no hay URL de Metacritic.
     * - Si existe, delega en MetacriticScraper::obtenerPuntuacion.
     *
     * @return float|null
     */
    public function getPuntuacion(): ?float {
        $url = $this->getMetacritic();
        if ($url === null) return null;
        return MetacriticScraper::obtenerPuntuacion($url);
    }
}
