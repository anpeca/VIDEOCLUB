<?php

namespace Dwes\ProyectoVideoclub;

use Dwes\ProyectoVideoclub\Util\ClienteNoEncontradoException;
use Dwes\ProyectoVideoclub\Util\SoporteNoEncontradoException;
use Dwes\ProyectoVideoclub\Util\SoporteYaAlquiladoException;
use Dwes\ProyectoVideoclub\Util\CupoSuperadoException;

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
class Videoclub
{

    private string $nombre;
    private array $productos = [];
    private int $numProductos = 0;
    private array $socios = [];
    private int $numSocios = 0;

    private int $numProductosAlquilados = 0;
    private int $numTotalAlquileres = 0;


    public function __construct(string $nombre)
    {
        $this->nombre = $nombre;
    }

    public function getNumProductosAlquilados(): int
    {
        return $this->numProductosAlquilados;
    }

    public function getNumTotalAlquileres(): int
    {
        return $this->numTotalAlquileres;
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


    public function incluirCintaVideo(string $titulo, float $precio, int $duracion): Videoclub
    {
        $cintaVideo = new CintaVideo(
            $titulo,
            $this->numProductos + 1,
            $precio,
            $duracion
        );
        $this->incluirProducto($cintaVideo);
        return $this;
    }


    public function incluirDvd(string $titulo, float $precio, string $idiomas, string $pantalla): Videoclub
    {
        $dvd = new Dvd($titulo, $this->numProductos + 1, $precio, $idiomas, $pantalla);
        $this->incluirProducto($dvd);
        return $this;
    }


    public function incluirJuego(string $titulo, float $precio, string $consola, int $minJ, int $maxJ): Videoclub
    {

        $juego = new Juego($titulo, $this->numProductos + 1, $precio, $consola, $minJ, $maxJ);
        $this->incluirProducto($juego);
        return $this;
    }


    public function alquilarSocioProducto(int $numeroCliente, int $numeroSoporte): Videoclub
    {

        $socioEncontrado = null;
        foreach ($this->socios as $socio) {
            if ($socio->getNumero() === $numeroCliente) {
                $socioEncontrado = $socio;
                break;
            }
        }

        if (!$socioEncontrado) {
            echo "Error: Cliente no encontrado.<br>";
            return $this;
        }

        $productoEncontrado = null;
        foreach ($this->productos as $producto) {
            if ($producto->getNumero() === $numeroSoporte) {
                $productoEncontrado = $producto;
                break;
            }
        }

        if (!$productoEncontrado) {
            echo "Error: Soporte no encontrado.<br>";
            return $this;
        }

        try {
            $socioEncontrado->alquilar($productoEncontrado);
            echo "Soporte alquilado con éxito.<br>";

            if (!$productoEncontrado->alquilado) {
                $productoEncontrado->alquilado = true;
                $this->numProductosAlquilados++;
            }
            $this->numTotalAlquileres++;
        } catch (SoporteYaAlquiladoException | CupoSuperadoException $e) {
            echo "Error: " . $e->getMessage() . "<br>";
        }

        return $this;
    }

    // Método 336
    public function alquilarSocioProductos(int $numSocio, array $numerosProductos): Videoclub
    {
        // Buscar el socio
        $socioEncontrado = null;
        foreach ($this->socios as $socio) {
            if ($socio->getNumero() === $numSocio) {
                $socioEncontrado = $socio;
                break;
            }
        }

        if (!$socioEncontrado) {
            throw new ClienteNoEncontradoException("Cliente con número {$numSocio} no encontrado");
        }

        // Verificar que todos los productos existen y están disponibles
        $productosParaAlquilar = [];

        foreach ($numerosProductos as $numProducto) {
            $productoEncontrado = null;

            // Buscar el producto
            foreach ($this->productos as $producto) {
                if ($producto->getNumero() === $numProducto) {
                    $productoEncontrado = $producto;
                    break;
                }
            }

            if (!$productoEncontrado) {
                throw new SoporteNoEncontradoException("Producto con número {$numProducto} no encontrado");
            }

            // Verificar si el socio ya tiene alquilado este producto
            if ($socioEncontrado->tieneAlquilado($productoEncontrado)) {
                throw new SoporteYaAlquiladoException("El producto {$numProducto} ya está alquilado por el socio {$numSocio}");
            }

            $productosParaAlquilar[] = $productoEncontrado;
        }

        // Verificar que el socio tiene cupo suficiente para todos los productos
        $alquileresActuales = count($socioEncontrado->getSoportesAlquilados());
        $totalDespuesDeAlquilar = $alquileresActuales + count($productosParaAlquilar);

        if ($totalDespuesDeAlquilar > $socioEncontrado->getMaxAlquilerConcurrente()) {
            throw new CupoSuperadoException("El socio {$numSocio} no tiene cupo suficiente para alquilar " . count($numerosProductos) . " productos. Cupo actual: {$alquileresActuales}, máximo: {$socioEncontrado->getMaxAlquilerConcurrente()}");
        }

        // Alquilar todos los productos (si llegamos aquí, todo está OK)
        foreach ($productosParaAlquilar as $producto) {
            try {
                $socioEncontrado->alquilar($producto);
                echo "Alquiler exitoso: {$producto->getTitulo()} alquilado a {$socioEncontrado->getNombre()}<br>";
            } catch (\Exception $e) {
                // Esto no debería pasar ya que verificamos todo antes
                echo "Error inesperado al alquilar {$producto->getTitulo()}: {$e->getMessage()}<br>";
            }
        }

        return $this;
    }


    public function devolverSocioProducto(int $numSocio, int $numeroProducto): Videoclub
    {
        $socioEncontrado = null;
        foreach ($this->socios as $socio) {
            if ($socio->getNumero() === $numSocio) {
                $socioEncontrado = $socio;
                break;
            }
        }

        if (!$socioEncontrado) {
            echo "❌ Error: Cliente no encontrado.<br>";
            return $this;
        }

        $productoEncontrado = null;
        foreach ($this->productos as $producto) {
            if ($producto->getNumero() === $numeroProducto) {
                $productoEncontrado = $producto;
                break;
            }
        }

        if (!$productoEncontrado) {
            echo "Error: Producto no encontrado.<br>";
            return $this;
        }

        try {
            $socioEncontrado->devolver($numeroProducto);
            $productoEncontrado->alquilado = false;
            echo "Producto devuelto con éxito.<br>";
        } catch (\Exception $e) {
            echo "Error al devolver: " . $e->getMessage() . "<br>";
        }

        return $this;
    }


    public function devolverSocioProductos(int $numSocio, array $numerosProductos): Videoclub
    {
        $socioEncontrado = null;
        foreach ($this->socios as $socio) {
            if ($socio->getNumero() === $numSocio) {
                $socioEncontrado = $socio;
                break;
            }
        }

        if (!$socioEncontrado) {
            echo "Error: Cliente no encontrado.<br>";
            return $this;
        }

        foreach ($numerosProductos as $numeroProducto) {
            $productoEncontrado = null;
            foreach ($this->productos as $producto) {
                if ($producto->getNumero() === $numeroProducto) {
                    $productoEncontrado = $producto;
                    break;
                }
            }

            if (!$productoEncontrado) {
                echo "Error: Producto con número {$numeroProducto} no encontrado.<br>";
                continue;
            }

            try {
                $socioEncontrado->devolver($numeroProducto);
                $productoEncontrado->alquilado = false;
                echo "Producto {$numeroProducto} devuelto con éxito.<br>";
            } catch (\Exception $e) {
                echo "Error al devolver el producto {$numeroProducto}: " . $e->getMessage() . "<br>";
            }
        }

        return $this;
    }
}
