<?php
namespace Dwes\ProyectoVideoclub;

/* Llegado a este punto, vamos a relacionar los clientes y los soportes mediante la clase Videoclub. Así pues crea la clase que representa el gráfico, teniendo en cuenta que:
productos es un array de Soporte
socios es una array de Cliente
Los métodos públicos de incluir algún soporte, crearán la clase y llamarán al método privado de incluirProducto, el cual es el encargado de introducirlo dentro del array. -->
*/

// require_once "Soporte.php";
// require_once "Cliente.php";
// require_once "CintaVideo.php";
// require_once "Dvd.php";
// require_once "Juego.php";
class Videoclub {

    private string $nombre;
    private array $productos = [];
    private int $numProductos=0;
    private array $socios = [];
    private int $numSocios=0;

    public function __construct(string $nombre) {
        $this->nombre = $nombre;
    }


    public function listarProductos(): void
    {

        foreach ($this->productos as  $producto) {
            echo $producto->muestraResumen();
        }
    }
    public function listarSocios(): void
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


    public function incluirSocio(Cliente $c): Videoclub
    {
        $this->socios[] = $c;
        $this->numSocios++;
        return $this;
    }
    

    public function incluirCintaVideo(string $titulo, float $precio, int $duracion): Videoclub{
        $cintaVideo= new CintaVideo($titulo,$this->numProductos + 1,
        $precio, $duracion);
        $this->incluirProducto($cintaVideo);
        return $this;
    }


    public function incluirDvd(string $titulo,float $precio, string $idiomas, string $pantalla): Videoclub{
        $dvd=new Dvd($titulo, $this->numProductos + 1,$precio,$idiomas,$pantalla);
        $this->incluirProducto($dvd);
        return $this;
    }
    

   public function incluirJuego(string $titulo, float $precio, string $consola, int $minJ, int $maxJ): Videoclub
{
  
    $juego = new Juego($titulo, $this->numProductos + 1, $precio, $consola, $minJ, $maxJ);
    $this->incluirProducto($juego);
    return $this;
}

    public function alquilarSocioProducto(int $numeroCliente, int $numeroSoporte): VideoClub{
        $socioEncontrado= null;
        foreach($this->socios as $socio){
            if($socio->getNumero()=== $numeroCliente){
                $socioEncontrado=$socio;
                break;
            }
        }

         $productoEncontrado = null;
          foreach ($this->productos as $producto) {
             if ($producto->getNumero() === $numeroSoporte) {
            $productoEncontrado = $producto;
            break;
            }
    }

     if ($socioEncontrado && $productoEncontrado) {
        $socioEncontrado->alquilar($productoEncontrado); 
    } else {
        if (!$socioEncontrado) {
            echo "Socio con número $numeroCliente no encontrado.<br>";
        }
        if (!$productoEncontrado) {
            echo "Producto con número $numeroSoporte no encontrado.<br>";
        }
    }


    return $this;

    }

}
?>