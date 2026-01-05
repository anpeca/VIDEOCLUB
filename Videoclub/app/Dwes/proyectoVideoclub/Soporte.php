<?php
namespace Dwes\ProyectoVideoclub;

/**
 * Representa un soporte del videoclub (DVD, BluRay, Digital, etc.).
 *
 * Clase abstracta que obliga a las subclases a implementar getPuntuacion(),
 * que devolverá la puntuación obtenida de Metacritic o null si no está disponible.
 *
 * @package Dwes\ProyectoVideoclub
 */
abstract class Soporte
{
    /** Título del soporte (público para acceso directo en algunos tests) */
    public string $titulo;

    /** Número identificador del soporte (protegido para permitir acceso en subclases) */
    protected int $numero;

    /** Precio base del soporte (sin IVA) */
    private float $precio;

    /** Porcentaje de IVA aplicado al precio */
    private const IVA = 21.0;

    /** Indicador de si el soporte está alquilado */
    private bool $alquilado = false;

    /**
     * URL de Metacritic asociada al soporte (opcional).
     *
     * @var string|null
     */
    private ?string $metacritic = null;

    /**
     * Constructor.
     *
     * @param string      $titulo     Título del soporte.
     * @param int         $numero     Número identificador.
     * @param float       $precio     Precio base sin IVA.
     * @param string|null $metacritic URL opcional de Metacritic.
     */
    public function __construct(string $titulo, int $numero, float $precio, ?string $metacritic = null)
    {
        $this->titulo = $titulo;
        $this->numero = $numero;
        $this->precio = $precio;

        if ($metacritic !== null) {
            $this->setMetacritic($metacritic);
        }
    }

    /** Devuelve el título del soporte */
    public function getTitulo(): string
    {
        return $this->titulo;
    }

    /** Devuelve el precio base (sin IVA) */
    public function getPrecio(): float
    {
        return $this->precio;
    }

    /** Devuelve el precio con IVA aplicado */
    public function getPrecioConIVA(): float
    {
        return $this->precio * (1 + self::IVA / 100);
    }

    /** Devuelve el número identificador del soporte */
    public function getNumero(): int
    {
        return $this->numero;
    }

    /** Devuelve el porcentaje de IVA aplicado */
    public function getIVA(): float
    {
        return self::IVA;
    }

    /** Indica si el soporte está actualmente alquilado */
    public function isAlquilado(): bool { return $this->alquilado; }

    /** Devuelve la URL de Metacritic asociada o null si no existe */
    public function getMetacritic(): ?string
    {
        return $this->metacritic;
    }

    /**
     * Establece la URL de Metacritic asociada al soporte.
     *
     * Valida que la URL sea correcta con filter_var; si se pasa null se elimina.
     *
     * @param string|null $url URL válida o null para eliminarla.
     * @throws \InvalidArgumentException Si la URL no es válida.
     */
    public function setMetacritic(?string $url): void
    {
        if ($url === null) {
            $this->metacritic = null;
            return;
        }

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('URL de Metacritic no válida: ' . $url);
        }

        $this->metacritic = $url;
    }

    /** Marca el soporte como alquilado o devuelto según el valor booleano */
    public function setAlquilado(bool $valor): void { $this->alquilado = $valor; }

    /**
     * Construye un resumen textual básico del soporte.
     *
     * Incluye título, número y precio; añade la URL de Metacritic si está presente.
     *
     * @return string Resumen legible del soporte.
     */
    public function muestraResumen(): string
    {
        $base = "Título: {$this->titulo}, Nº: {$this->numero}, Precio: {$this->precio}€";
        if ($this->metacritic !== null) {
            $base .= ", Metacritic: {$this->metacritic}";
        }
        return $base;
    }

    /**
     * Devuelve la puntuación en Metacritic para este soporte.
     *
     * Implementación obligatoria en cada subclase. Debe devolver:
     *  - float con la puntuación (por ejemplo 78.0) si se obtiene correctamente
     *  - null si no hay URL o no se puede obtener la puntuación
     *
     * NOTA: la lógica de scraping se implementará en las subclases (o en una utilidad
     * compartida que llamen desde aquí).
     *
     * @return float|null
     */
    abstract public function getPuntuacion(): ?float;
}
