<?php
namespace Dwes\ProyectoVideoclub;

use Dwes\ProyectoVideoclub\Util\MetacriticScraper;

/**
 * Representa un DVD como tipo de Soporte.
 *
 * - Contiene información sobre idiomas, formato de pantalla y duración en minutos.
 * - Proporciona un resumen textual que incluye estos datos y delega en la clase base
 *   para la parte común del resumen.
 * - Permite obtener una puntuación externa mediante Metacritic si se ha proporcionado
 *   una URL en la propiedad metacritic heredada.
 */
class Dvd extends Soporte
{
    /** Idiomas disponibles en el DVD (por ejemplo "ES,EN") */
    public string $idiomas;

    /** Formato de pantalla (por ejemplo "16:9") */
    private string $formatoPantalla;

    /** Duración en minutos */
    private int $duracion;

    /**
     * Constructor.
     *
     * @param string $titulo         Título del DVD.
     * @param int    $numero         Número identificador del soporte.
     * @param float  $precio         Precio del soporte.
     * @param string $idiomas        Idiomas disponibles.
     * @param string $formatoPantalla Formato de pantalla.
     * @param int    $duracion       Duración en minutos (opcional, por defecto 0).
     */
    public function __construct(string $titulo, int $numero, float $precio, string $idiomas, string $formatoPantalla, int $duracion = 0)
    {
        parent::__construct($titulo, $numero, $precio);
        $this->idiomas = $idiomas;
        $this->formatoPantalla = $formatoPantalla;
        $this->duracion = $duracion;
    }

    /**
     * Devuelve la duración en minutos.
     *
     * @return int Duración en minutos.
     */
    public function getDuracion(): int
    {
        return $this->duracion;
    }

    /**
     * Construye y devuelve un resumen textual del DVD.
     *
     * - Se apoya en parent::muestraResumen() para la parte común.
     * - Añade idiomas y formato de pantalla.
     * - Si la duración es mayor que 0, incluye la duración en minutos en el formato:
     *   "Título (NN min) - resto_del_resumen".
     *
     * @return string Resumen del DVD.
     */
    public function muestraResumen(): string
    {
        $base = parent::muestraResumen() . " Idiomas: {$this->idiomas}, Formato: {$this->formatoPantalla}";
        if ($this->duracion > 0) {
            $base = sprintf('%s (%d min) - %s', $this->getTitulo(), $this->duracion, substr($base, strlen($this->getTitulo())));
            // alternativa simple:
            // $base = parent::muestraResumen() . " ({$this->duracion} min) Idiomas: {$this->idiomas}, Formato: {$this->formatoPantalla}";
        }
        return $base;
    }

    /**
     * Obtiene la puntuación desde Metacritic si existe una URL configurada.
     *
     * - Si no hay URL de Metacritic, devuelve null.
     * - Si existe, delega en MetacriticScraper::obtenerPuntuacion para recuperar
     *   la puntuación como float o null si no se puede obtener.
     *
     * @return float|null Puntuación o null si no está disponible.
     */
    public function getPuntuacion(): ?float
    {
        $url = $this->getMetacritic();
        if ($url === null) return null;
        return MetacriticScraper::obtenerPuntuacion($url);
    }
}
