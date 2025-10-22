<?php
namespace Dwes\ProyectoVideoclub;

class Soporte
{
    public string $titulo;
    protected int $numero;
    private float $precio;
    private const  IVA = 21.0;

    public function __construct(string $titulo, int $numero, float $precio)
    {

        $this->titulo = $titulo;
        $this->numero = $numero;
        $this->precio = $precio;
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

    public function muestraResumen(): string
    {
        return "Título: {$this->titulo}, Nº: {$this->numero}, Precio: {$this->precio}€";
    }
}