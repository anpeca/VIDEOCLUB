<?php

namespace Dwes\ProyectoVideoclub;

use Dwes\ProyectoVideoclub\Util\ClienteNoEncontradoException;
use Dwes\ProyectoVideoclub\Util\SoporteNoEncontradoException;
use Dwes\ProyectoVideoclub\Util\SoporteYaAlquiladoException;
use Dwes\ProyectoVideoclub\Util\CupoSuperadoException;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Exception;
use Dwes\ProyectoVideoclub\Util\LogFactory;

class Videoclub
{
    private string $nombre;
    private array $productos = [];
    private int $numProductos = 0;
    private array $socios = [];
    private int $numSocios = 0;
    private Logger $log;

    public function __construct(string $nombre)
    {
        $this->nombre = $nombre;

        $this->log = LogFactory::crearLogger('VideoclubLogger', 'videoclub.log'); $this->log->debug("Videoclub '{$this->nombre}' creado.");

        // Configurar Monolog
        // $logDir = __DIR__ . '/../../logs';
        // if (!is_dir($logDir)) {
        //     mkdir($logDir, 0777, true);
        // }

        // $this->log = new Logger('VideoclubLogger');
        // $this->log->pushHandler(new StreamHandler($logDir . '/videoclub.log', Logger::DEBUG));
        // $this->log->debug("Videoclub '{$this->nombre}' creado.");
    }

    public function listarProductos(): void
    {
        foreach ($this->productos as $producto) {
            $linea = method_exists($producto, 'muestraResumen') ? $producto->muestraResumen() : json_encode($producto);
            $this->log->info($linea);
            // echo $linea . "<br>"; // opcional para depuración
        }
    }

    public function listarSocios(): void
    {
        foreach ($this->socios as $socio) {
            $linea = method_exists($socio, 'muestraResumen') ? $socio->muestraResumen() : json_encode($socio);
            $this->log->info($linea);
            // echo $linea . "<br>"; // opcional
        }
    }

    private function incluirProducto(Soporte $s): void
    {
        $this->productos[] = $s;
        $this->numProductos++;
        $this->log->debug("Producto incluido: " . (method_exists($s, 'getTitulo') ? $s->getTitulo() : 'desconocido'));
    }

    public function incluirSocio(Cliente $c): Videoclub
    {
        $this->socios[] = $c;
        $this->numSocios++;
        $this->log->debug("Socio incluido: " . $c->getNombre());
        return $this;
    }

    /**
     * Incluir una cinta de vídeo.
     *
     * @param string|null $metacriticUrl URL de Metacritic asociada al soporte (puede ser null)
     * @param string $titulo
     * @param float $precio
     * @param int $duracion
     * @return Videoclub
     */
    public function incluirCintaVideo(?string $metacriticUrl, string $titulo, float $precio, int $duracion): Videoclub
    {
        $cintaVideo = new CintaVideo($titulo, $this->numProductos + 1, $precio, $duracion);
        // Asignar metacritic si se proporciona
        if (method_exists($cintaVideo, 'setMetacritic')) {
            $cintaVideo->setMetacritic($metacriticUrl);
        }
        $this->incluirProducto($cintaVideo);
        return $this;
    }

    /**
     * Incluir un DVD.
     *
     * @param string|null $metacriticUrl URL de Metacritic asociada al soporte (puede ser null)
     * @param string $titulo
     * @param float $precio
     * @param string $idiomas
     * @param string $pantalla
     * @return Videoclub
     */
    public function incluirDvd(?string $metacriticUrl, string $titulo, float $precio, string $idiomas, string $pantalla): Videoclub
    {
        $dvd = new Dvd($titulo, $this->numProductos + 1, $precio, $idiomas, $pantalla);
        if (method_exists($dvd, 'setMetacritic')) {
            $dvd->setMetacritic($metacriticUrl);
        }
        $this->incluirProducto($dvd);
        return $this;
    }

    /**
     * Incluir un juego.
     *
     * @param string|null $metacriticUrl URL de Metacritic asociada al soporte (puede ser null)
     * @param string $titulo
     * @param float $precio
     * @param string $consola
     * @param int $minJ
     * @param int $maxJ
     * @return Videoclub
     */
    public function incluirJuego(?string $metacriticUrl, string $titulo, float $precio, string $consola, int $minJ, int $maxJ): Videoclub
    {
        $juego = new Juego($titulo, $this->numProductos + 1, $precio, $consola, $minJ, $maxJ);
        if (method_exists($juego, 'setMetacritic')) {
            $juego->setMetacritic($metacriticUrl);
        }
        $this->incluirProducto($juego);
        return $this;
    }

    public function alquilarSocioProducto(int $numeroCliente, int $numeroSoporte): Videoclub
    {
        $socio = null;
        foreach ($this->socios as $c) {
            if ($c->getNumero() === $numeroCliente) {
                $socio = $c;
                break;
            }
        }

        $producto = null;
        foreach ($this->productos as $p) {
            if ($p->getNumero() === $numeroSoporte) {
                $producto = $p;
                break;
            }
        }

        if (!$socio) {
            $this->log->warning("Cliente no encontrado: {$numeroCliente}");
            throw new ClienteNoEncontradoException("Cliente con número {$numeroCliente} no encontrado");
        }
        if (!$producto) {
            $this->log->warning("Producto no encontrado: {$numeroSoporte}");
            throw new SoporteNoEncontradoException("Producto con número {$numeroSoporte} no encontrado");
        }

        try {
            $socio->alquilar($producto);
            $this->log->info("Alquiler realizado: Cliente {$socio->getNombre()} - Producto {$producto->getTitulo()}");
        } catch (SoporteYaAlquiladoException | CupoSuperadoException $e) {
            $this->log->warning("Error al alquilar: {$e->getMessage()}");
            throw $e;
        }

        return $this;
    }

    public function devolverSocioProducto(int $numeroCliente, int $numeroSoporte): Videoclub
    {
        $socio = null;
        foreach ($this->socios as $c) {
            if ($c->getNumero() === $numeroCliente) {
                $socio = $c;
                break;
            }
        }

        $producto = null;
        foreach ($this->productos as $p) {
            if ($p->getNumero() === $numeroSoporte) {
                $producto = $p;
                break;
            }
        }

        if (!$socio) {
            $this->log->warning("Cliente no encontrado al devolver: {$numeroCliente}");
            throw new ClienteNoEncontradoException("Cliente con número {$numeroCliente} no encontrado");
        }
        if (!$producto) {
            $this->log->warning("Producto no encontrado al devolver: {$numeroSoporte}");
            throw new SoporteNoEncontradoException("Producto con número {$numeroSoporte} no encontrado");
        }

        try {
            $socio->devolver($numeroSoporte);
            $producto->alquilado = false;
            $this->log->info("Devolución realizada: Cliente {$socio->getNombre()} - Producto {$producto->getTitulo()}");
        } catch (SoporteNoEncontradoException $e) {
            $this->log->warning("Error al devolver: {$e->getMessage()}");
            throw $e;
        }

        return $this;
    }

    /**
     * Devuelve el objeto Cliente con el número indicado o null si no existe.
     *
     * @param int $numero
     * @return Cliente|null
     */
    public function obtenerSocioPorNumero(int $numero): ?Cliente
    {
        foreach ($this->socios as $socio) {
            if ($socio->getNumero() === $numero) {
                return $socio;
            }
        }
        return null;
    }
}
