<?php
namespace Dwes\ProyectoVideoclub;

/**
 * Representa un soporte del videoclub (DVD, BluRay, Digital, etc.).
 *
 * Ahora es una clase abstracta que obliga a las subclases a implementar
 * getPuntuacion(), que devolverá la puntuación obtenida de Metacritic
 * o null si no está disponible.
 *
 * @package Dwes\ProyectoVideoclub
 */
abstract class Soporte
{
    public string $titulo;
    protected int $numero;
    private float $precio;
    private const IVA = 21.0;

    /**
     * URL de Metacritic asociada al soporte (opcional).
     *
     * @var string|null
     */
    private ?string $metacritic = null;

    public function __construct(string $titulo, int $numero, float $precio, ?string $metacritic = null)
    {
        $this->titulo = $titulo;
        $this->numero = $numero;
        $this->precio = $precio;

        if ($metacritic !== null) {
            $this->setMetacritic($metacritic);
        }
    }

    public function getTitulo(): string
    {
        return $this->titulo;
    }

    public function getPrecio(): float
    {
        return $this->precio;
    }

    public function getPrecioConIVA(): float
    {
        return $this->precio * (1 + self::IVA / 100);
    }

    public function getNumero(): int
    {
        return $this->numero;
    }

    public function getIVA(): float
    {
        return self::IVA;
    }

    public function getMetacritic(): ?string
    {
        return $this->metacritic;
    }

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
