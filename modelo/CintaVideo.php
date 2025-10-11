<?php
    require_once "Soporte.php"
class CintaVideo extends Soporte{
    private int $duracion;

     public function __construct(string $titulo, float $precio, float $precioConIVA,int $duracion ){
        parent::__construct($titulo,$precio,$precioConIVA);
         $this->duracion=$duracion;
    }

    public function getDuracion():int{
        return $this->duracion;
    }

     public function muestraResumen():string{
        return "Titulo: ".$titulo." con precio: ".$precio." el precio total es de: "-$precioConIVA." con una duración de: ".$duracion;
    }

}

?>