<?php
namespace Dwes\ProyectoVideoclub;
use Dwes\ProyectoVideoclub\Util\MetacriticScraper;

/**
 * CintaVideo representa un soporte de tipo cinta con duración en minutos.
 *
 * - Duración almacenada en minutos accesible mediante getDuracion().
 * - muestraResumen() añade la información de duración al resumen base.
 * - getPuntuacion() delega en MetacriticScraper si existe una URL de Metacritic.
 */
class CintaVideo extends Soporte {

    /**
     * Duración de la cinta en minutos.
     *
     * @var int
     */
    private int $duracion;

    /**
     * Constructor.
     *
     * @param string $titulo    Título del soporte.
     * @param int    $numero    Número identificador del soporte.
     * @param float  $precio    Precio del soporte.
     * @param int    $duracion  Duración en minutos.
     * @param string|null $metacritic URL opcional para obtener puntuación en Metacritic.
     */
    public function __construct(string $titulo, int $numero, float $precio, int $duracion, ?string $metacritic = null) {
        parent::__construct($titulo, $numero, $precio, $metacritic);
        $this->duracion = $duracion;
    }

    /**
     * Devuelve la duración en minutos.
     *
     * @return int
     */
    public function getDuracion(): int {
        return $this->duracion;
    }

    /**
     * Muestra un resumen del soporte incluyendo la duración.
     *
     * Se delega en parent::muestraResumen() para mantener el formato base y
     * se añade la información específica de la cinta (duración en minutos).
     *
     * @return string
     */
    public function muestraResumen(): string {
        return parent::muestraResumen() . " Duración: " . $this->duracion . " minutos";
    }

    /**
     * Obtiene la puntuación desde Metacritic si se proporcionó una URL.
     *
     * - Si no hay URL de Metacritic, devuelve null.
     * - Si existe, delega en MetacriticScraper::obtenerPuntuacion para obtener
     *   la puntuación como float o null si no se puede obtener.
     *
     * @return float|null
     */
    public function getPuntuacion(): ?float {
        $url = $this->getMetacritic();
        if ($url === null) return null;
        return MetacriticScraper::obtenerPuntuacion($url);
    }
}
