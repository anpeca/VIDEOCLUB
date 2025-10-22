<?php
namespace Dwes\ProyectoVideoclub;

/**
 * Clase Dvd
 *
 * Representa un soporte de tipo DVD dentro del sistema del videoclub.
 * Hereda de la clase Soporte y añade propiedades específicas como idiomas disponibles
 * y formato de pantalla.
 */
class Dvd extends Soporte
{
    /** @var string Idiomas disponibles en el DVD (separados por comas) */
    public string $idiomas;

    /** @var string Formato de pantalla del DVD (por ejemplo, 16:9) */
    private string $formatoPantalla;

    /**
     * Constructor de Dvd
     *
     * @param string $titulo Título del DVD
     * @param int $numero Número identificador del soporte
     * @param float $precio Precio base del DVD
     * @param string $idiomas Idiomas disponibles (ej: "es,en,fr")
     * @param string $formatoPantalla Formato de pantalla (ej: "16:9")
     */
    public function __construct(string $titulo, int $numero, float $precio, string $idiomas, string $formatoPantalla)
    {
        parent::__construct($titulo, $numero, $precio);
        $this->idiomas = $idiomas;
        $this->formatoPantalla = $formatoPantalla;
    }

    /**
     * Muestra un resumen del DVD incluyendo idiomas y formato de pantalla
     *
     * @return string Resumen del soporte
     */
    public function muestraResumen(): string
    {
        return parent::muestraResumen() . " Idiomas: {$this->idiomas}, Formato: {$this->formatoPantalla}";
    }
}
?>
