<?php
namespace Dwes\ProyectoVideoclub;

/**
 * Clase Soporte
 *
 * Representa un soporte genérico dentro del sistema del videoclub.
 * Es la clase base para otros tipos de productos como Dvd, Juego o CintaVideo.
 * Contiene información común como título, número identificador, precio y estado de alquiler.
 */
class Soporte {

    /** @var string Título del soporte */
    public string $titulo;

    /** @var int Número identificador del soporte */
    protected int $numero;

    /** @var float Precio base del soporte sin IVA */
    private float $precio;

    /** @var float Porcentaje de IVA aplicado (constante) */
    private const IVA = 21.0;

    /** @var bool Indica si el soporte está actualmente alquilado */
    public bool $alquilado = false;

    /**
     * Constructor de Soporte
     *
     * @param string $titulo Título del soporte
     * @param int $numero Número identificador del soporte
     * @param float $precio Precio base sin IVA
     */
    public function __construct(string $titulo, int $numero, float $precio) {
        $this->titulo = $titulo;
        $this->numero = $numero;
        $this->precio = $precio;
    }

    /**
     * Devuelve el título del soporte
     *
     * @return string
     */
    public function getTitulo(): string {
        return $this->titulo;
    }

    /**
     * Devuelve el precio base del soporte sin IVA
     *
     * @return float
     */
    public function getPrecio(): float {
        return $this->precio;
    }

    /**
     * Calcula y devuelve el precio con IVA incluido
     *
     * @return float
     */
    public function getPrecioConIVA(): float {
        return $this->precio * (1 + self::IVA / 100);
    }

    /**
     * Devuelve el número identificador del soporte
     *
     * @return int
     */
    public function getNumero(): int {
        return $this->numero;
    }

    /**
     * Devuelve el porcentaje de IVA aplicado
     *
     * @return float
     */
    public function getIVA(): float {
        return self::IVA;
    }

    /**
     * Muestra un resumen del soporte con título, número y precio
     *
     * @return string
     */
    public function muestraResumen(): string {
        return "Título: {$this->titulo}, Nº: {$this->numero}, Precio: {$this->precio}€";
    }
}
?>
