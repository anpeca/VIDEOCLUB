<?php


class Soporte{
    private String $titulo;
    private float $precio;
    private float $precioConIVA;
    private const $IVA=21;

    public function __construct(string $nombre, float $precio, float $precioConIVA ){
        $this->nombre=$nombre;
        $this->precio=$precio;
        $this->precioConIVA=$precioConIVA;
    }

    public function getTitulo():string{
        return $this->titulo;
    }

    
    public function getPrecio():float{
        return $this->precio;
    }
    
    public function getPrecioConIVA():float{
        return $this->PrecioConIVA;
    }
    
    public function getIVA():float{
        return self::IVA;
    }

    public function muestraResumen():string{
        return "Titulo: ".$titulo." con precio: ".$precio." el precio total es de: "-$precioConIVA;
    }

}



?>