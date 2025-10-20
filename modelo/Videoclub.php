<?php
    require_once "Soporte.php"
    require_once "Cliente.php"
class Videoclub{
    private string $nombre;
    private array $productos=[];
    private int $numProductos;
    private array $socios=[];
    private int $numSocios;

    public function __construct(string $nombre, array $productos, int  $numProductos, array $socios, int $numSocios ){
        $this->nombre=$nombre;
        $this->productos=$productos;
        $this->numProductos=$numProductos;
        $this->socios=$socios;
        $this->numSocios=$numSocios;
    }


    public function listarProductos(){
        foreach($productos as  $producto){
            echo $producto->muestraResumen();
        }
    }
    public function listarSocios(){
        foreach($socios as  $socio){
            echo $socio->muestraResumen();
        }
    }
    
}


?>