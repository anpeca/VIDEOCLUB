<?php

namespace Dwes\ProyectoVideoclub;

use Dwes\ProyectoVideoclub\Util\ClienteNoEncontradoException;
use Dwes\ProyectoVideoclub\Util\SoporteNoEncontradoException;
use Dwes\ProyectoVideoclub\Util\SoporteYaAlquiladoException;
use Dwes\ProyectoVideoclub\Util\CupoSuperadoException;

/**
 * Clase Videoclub
 *
 * Gestiona el catálogo de productos (soportes) y los socios (clientes) del videoclub.
 * Permite incluir productos, registrar socios, realizar alquileres y devoluciones.
 */
class Videoclub
{
    /** @var string Nombre del videoclub */
    private string $nombre;

    /** @var Soporte[] Lista de productos disponibles */
    private array $productos = [];

    /** @var int Número total de productos registrados */
    private int $numProductos = 0;

    /** @var Cliente[] Lista de socios registrados */
    private array $socios = [];

    /** @var int Número total de socios registrados */
    private int $numSocios = 0;

    /** @var int Número de productos actualmente alquilados */
    private int $numProductosAlquilados = 0;

    /** @var int Número total de alquileres realizados */
    private int $numTotalAlquileres = 0;

    /**
     * Constructor del videoclub
     *
     * @param string $nombre Nombre del videoclub
     */
    public function __construct(string $nombre)
    {
        $this->nombre = $nombre;
    }

    /**
     * Devuelve el número de productos alquilados actualmente
     *
     * @return int
     */
    public function getNumProductosAlquilados(): int
    {
        return $this->numProductosAlquilados;
    }

    /**
     * Devuelve el número total de alquileres realizados
     *
     * @return int
     */
    public function getNumTotalAlquileres(): int
    {
        return $this->numTotalAlquileres;
    }

    /**
     * Muestra todos los productos registrados
     *
     * @return void
     */
    public function listarProductos(): void
    {
        foreach ($this->productos as $producto) {
            echo $producto->muestraResumen();
        }
    }

    /**
     * Muestra todos los socios registrados
     *
     * @return void
     */
    public function listarSocios(): void
    {
        foreach ($this->socios as $socio) {
            echo $socio->muestraResumen();
        }
    }

    /**
     * Añade un producto al catálogo
     *
     * @param Soporte $s
     * @return void
     */
    private function incluirProducto(Soporte $s): void
    {
        $this->productos[] = $s;
        $this->numProductos++;
    }

    /**
     * Añade un nuevo socio al videoclub
     *
     * @param Cliente $c
     * @return Videoclub
     */
    public function incluirSocio(Cliente $c): Videoclub
    {
        $this->socios[] = $c;
        $this->numSocios++;
        return $this;
    }

    /**
     * Crea y añade una cinta de vídeo al catálogo
     *
     * @param string $titulo
     * @param float $precio
     * @param int $duracion
     * @return Videoclub
     */
    public function incluirCintaVideo(string $titulo, float $precio, int $duracion): Videoclub
    {
        $cintaVideo = new CintaVideo($titulo, $this->numProductos + 1, $precio, $duracion);
        $this->incluirProducto($cintaVideo);
        return $this;
    }

    /**
     * Crea y añade un DVD al catálogo
     *
     * @param string $titulo
     * @param float $precio
     * @param string $idiomas
     * @param string $pantalla
     * @return Videoclub
     */
    public function incluirDvd(string $titulo, float $precio, string $idiomas, string $pantalla): Videoclub
    {
        $dvd = new Dvd($titulo, $this->numProductos + 1, $precio, $idiomas, $pantalla);
        $this->incluirProducto($dvd);
        return $this;
    }

    /**
     * Crea y añade un juego al catálogo
     *
     * @param string $titulo
     * @param float $precio
     * @param string $consola
     * @param int $minJ
     * @param int $maxJ
     * @return Videoclub
     */
    public function incluirJuego(string $titulo, float $precio, string $consola, int $minJ, int $maxJ): Videoclub
    {
        $juego = new Juego($titulo, $this->numProductos + 1, $precio, $consola, $minJ, $maxJ);
        $this->incluirProducto($juego);
        return $this;
    }


    /**
     * Alquila un producto a un socio por número de cliente y número de soporte
     *
     * @param int $numeroCliente Número identificador del cliente
     * @param int $numeroSoporte Número identificador del soporte
     * @return Videoclub
     */
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

            // sincronizar alquiler en $_SESSION
            if (session_status() !== PHP_SESSION_ACTIVE) {
                session_start();
            }
            $clienteId = (int) $socioEncontrado->getNumero();
            $soporteId = (int) $productoEncontrado->getNumero();

            if (!isset($_SESSION['alquileres']) || !is_array($_SESSION['alquileres'])) {
                $_SESSION['alquileres'] = [];
            }
            if (!isset($_SESSION['alquileres'][$clienteId]) || !is_array($_SESSION['alquileres'][$clienteId])) {
                $_SESSION['alquileres'][$clienteId] = [];
            }
            if (!in_array($soporteId, $_SESSION['alquileres'][$clienteId], true)) {
                $_SESSION['alquileres'][$clienteId][] = $soporteId;
            }
            // fin sincronización

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

    /**
     * Alquila varios productos a un socio
     *
     * @param int $numSocio Número identificador del socio
     * @param int[] $numerosProductos Array de números de productos a alquilar
     * @return Videoclub
     * @throws ClienteNoEncontradoException
     * @throws SoporteNoEncontradoException
     * @throws SoporteYaAlquiladoException
     * @throws CupoSuperadoException
     */
    public function alquilarSocioProductos(int $numSocio, array $numerosProductos): Videoclub
    {
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

        $productosParaAlquilar = [];

        foreach ($numerosProductos as $numProducto) {
            $productoEncontrado = null;
            foreach ($this->productos as $producto) {
                if ($producto->getNumero() === $numProducto) {
                    $productoEncontrado = $producto;
                    break;
                }
            }

            if (!$productoEncontrado) {
                throw new SoporteNoEncontradoException("Producto con número {$numProducto} no encontrado");
            }

            if ($socioEncontrado->tieneAlquilado($productoEncontrado)) {
                throw new SoporteYaAlquiladoException("El producto {$numProducto} ya está alquilado por el socio {$numSocio}");
            }

            $productosParaAlquilar[] = $productoEncontrado;
        }

        $alquileresActuales = count($socioEncontrado->getSoportesAlquilados());
        $totalDespuesDeAlquilar = $alquileresActuales + count($productosParaAlquilar);

        if ($totalDespuesDeAlquilar > $socioEncontrado->getMaxAlquilerConcurrente()) {
            throw new CupoSuperadoException("El socio {$numSocio} no tiene cupo suficiente para alquilar " . count($numerosProductos) . " productos.");
        }

        foreach ($productosParaAlquilar as $producto) {
            try {
                $socioEncontrado->alquilar($producto);
                echo "Alquiler exitoso: {$producto->getTitulo()}<br>";

                // sincronizar cada alquiler en la sesión
                if (session_status() !== PHP_SESSION_ACTIVE) session_start();
                $clienteId = (int)$socioEncontrado->getNumero();
                $soporteId = (int)$producto->getNumero();

                if (!isset($_SESSION['alquileres']) || !is_array($_SESSION['alquileres'])) {
                    $_SESSION['alquileres'] = [];
                }
                if (!isset($_SESSION['alquileres'][$clienteId]) || !is_array($_SESSION['alquileres'][$clienteId])) {
                    $_SESSION['alquileres'][$clienteId] = [];
                }
                if (!in_array($soporteId, $_SESSION['alquileres'][$clienteId], true)) {
                    $_SESSION['alquileres'][$clienteId][] = $soporteId;
                }
                // fin sincronización

            } catch (\Exception $e) {
                echo "Error inesperado al alquilar {$producto->getTitulo()}: {$e->getMessage()}<br>";
            }
        }

        return $this;
    }

    /**
     * Devuelve un producto alquilado por un socio
     *
     * @param int $numSocio Número identificador del socio
     * @param int $numeroProducto Número identificador del producto
     * @return Videoclub
     */
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
            echo "Error: Cliente no encontrado.<br>";
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

            // mantener la sesión coherente: eliminar id de alquiler del cliente
            if (session_status() !== PHP_SESSION_ACTIVE) session_start();
            $clienteId = (int)$socioEncontrado->getNumero();
            $numeroProductoInt = (int)$numeroProducto;
            if (!empty($_SESSION['alquileres'][$clienteId]) && is_array($_SESSION['alquileres'][$clienteId])) {
                $pos = array_search($numeroProductoInt, $_SESSION['alquileres'][$clienteId], true);
                if ($pos !== false) {
                    unset($_SESSION['alquileres'][$clienteId][$pos]);
                    $_SESSION['alquileres'][$clienteId] = array_values($_SESSION['alquileres'][$clienteId]);
                }
            }
            // fin sincronización

        } catch (\Exception $e) {
            echo "Error al devolver: " . $e->getMessage() . "<br>";
        }

        return $this;
    }

    /**
     * Devuelve varios productos alquilados por un socio
     *
     * @param int $numSocio Número identificador del socio
     * @param int[] $numerosProductos Array de números de productos a devolver
     * @return Videoclub
     */
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

                // sincronizar eliminación en sesión
                if (session_status() !== PHP_SESSION_ACTIVE) session_start();
                $clienteId = (int)$socioEncontrado->getNumero();
                $numeroProductoInt = (int)$numeroProducto;
                if (!empty($_SESSION['alquileres'][$clienteId]) && is_array($_SESSION['alquileres'][$clienteId])) {
                    $pos = array_search($numeroProductoInt, $_SESSION['alquileres'][$clienteId], true);
                    if ($pos !== false) {
                        unset($_SESSION['alquileres'][$clienteId][$pos]);
                        $_SESSION['alquileres'][$clienteId] = array_values($_SESSION['alquileres'][$clienteId]);
                    }
                }
                // fin sincronización

            } catch (\Exception $e) {
                echo "Error al devolver el producto {$numeroProducto}: " . $e->getMessage() . "<br>";
            }
        }

        return $this;
    }
}
