<!-- Llegado a este punto, vamos a relacionar los clientes y los soportes mediante la clase Videoclub. Así pues crea la clase que representa el gráfico, teniendo en cuenta que:
productos es un array de Soporte
socios es una array de Cliente
Los métodos públicos de incluir algún soporte, crearán la clase y llamarán al método privado de incluirProducto, el cual es el encargado de introducirlo dentro del array. -->
<?php
require_once "Soporte.php";
require_once "Cliente.php";
class Videoclub 
    private string $nombre;
    private array $productos = [];
    private int $numProductos;
    private array $socios = [];
    private int $numSocios;

    public function __construct(string $nombre, array $productos, int  $numProductos, array $socios, int $numSocios)
    {
        $this->nombre = $nombre;
        $this->productos = $productos;
        $this->numProductos = $numProductos;
        $this->socios = $socios;
        $this->numSocios = $numSocios;
    }


    public function listarProductos()
    {

        foreach ($this->productos as  $producto) {
            echo $producto->muestraResumen();
        }
    }
    public function listarSocios()
    {
        foreach ($this->socios as  $socio) {
            echo $socio->muestraResumen();
        }
    }

    private function incluirProducto(Soporte $s): void
    {
        $this->productos[] = $s;
        $this->numProductos++;
    }

    public function incluirSocio(Cliente $c): void
    {
        $this->socios[] = $c;
        $this->numSocios++;
    }
}
?>