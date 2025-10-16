<?php
class Soporte {

    private string $titulo;
    private float $precio;
    private float $precioConIVA;
    private int $numero;
    private const IVA = 21;

    public function __construct(string $titulo, float $precio, float $precioConIVA) {
        $this->titulo = $titulo;
        $this->precio = $precio;
        $this->precioConIVA = $precioConIVA;
    }

    public function getTitulo(): string {
        return $this->titulo;
    }

    public function getPrecio(): float {
        return $this->precio;
    }

    public function getPrecioConIVA(): float {
        return $this->precioConIVA;
    }

    public function getNumero(): int {
        return $this->numero;
    }

    public function getIVA(): float {
        return self::IVA;
    }

    public function muestraResumen(): string {
        return "Título: " . $this->titulo . " con precio: " . $this->precio . " el precio total es de: " . $this->precioConIVA;
    }
}
?>
