<?php
namespace Dwes\ProyectoVideoclub;

/**
 * Clase que representa una cinta de vídeo dentro del sistema del videoclub.
 * Hereda de Soporte y añade la propiedad específica "duracion".
 * Se utiliza para crear productos de tipo cinta y mostrar su información.
 * Forma parte del sistema de alquiler junto con Cliente y Videoclub.
 */
class CintaVideo extends Soporte {

    /** @var int Duración de la cinta en minutos */
    private int $duracion;

    /**
     * Constructor de CintaVideo
     *
     * @param string $titulo Título del soporte
     * @param float $precio Precio base sin IVA
     * @param float $precioConIVA Precio con IVA incluido
     * @param int $duracion Duración de la cinta en minutos
     */
    public function __construct(string $titulo, float $precio, float $precioConIVA, int $duracion) {
        parent::__construct($titulo, $precio, $precioConIVA);
        $this->duracion = $duracion;
    }

    /**
     * Devuelve la duración de la cinta
     *
     * @return int Duración en minutos
     */
    public function getDuracion(): int {
        return $this->duracion;
    }

    /**
     * Muestra un resumen del soporte incluyendo la duración
     *
     * @return string Resumen del soporte
     */
    public function muestraResumen(): string {
        return parent::muestraResumen() . " Duración: " . $this->duracion . " minutos";
    }
}
?>
