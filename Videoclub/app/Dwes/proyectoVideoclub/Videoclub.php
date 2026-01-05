<?php

namespace Dwes\ProyectoVideoclub;

use Dwes\ProyectoVideoclub\Exception\ClienteNoExisteException;
use Dwes\ProyectoVideoclub\Exception\SoporteNoEncontradoException;
use Dwes\ProyectoVideoclub\Exception\SoporteYaAlquiladoException;
use Dwes\ProyectoVideoclub\Exception\CupoSuperadoException;


use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Exception;
use Dwes\ProyectoVideoclub\Util\LogFactory;

/**
 * Clase principal que representa un videoclub.
 *
 * - Mantiene listas internas de productos (Soporte) y socios (Cliente).
 * - Proporciona métodos para incluir productos y socios, listar elementos,
 *   y operaciones de alquiler/devolución delegando en Cliente y Soporte.
 * - Registra eventos relevantes mediante un logger obtenido de LogFactory.
 *
 * Nota: el código funcional original se conserva tal cual; los comentarios
 * internos explican responsabilidades y puntos clave de la implementación.
 */
class Videoclub
{
    /** Nombre del videoclub */
    private string $nombre;

    /** Array de productos (instancias de Soporte) */
    private array $productos = [];

    /** Contador de productos incluidos (se usa para asignar números) */
    private int $numProductos = 0;

    /** Array de socios (instancias de Cliente) */
    private array $socios = [];

    /** Contador de socios incluidos */
    private int $numSocios = 0;

    /** Logger para registrar eventos del videoclub */
    private Logger $log;

    /**
     * Constructor.
     *
     * Inicializa el nombre y configura el logger mediante LogFactory.
     */
    public function __construct(string $nombre)
    {
        $this->nombre = $nombre;

        $this->log = LogFactory::crearLogger('VideoclubLogger', 'videoclub.log');
        $this->log->debug("Videoclub '{$this->nombre}' creado.");

        // Configurar Monolog
        // $logDir = __DIR__ . '/../../logs';
        // if (!is_dir($logDir)) {
        //     mkdir($logDir, 0777, true);
        // }

        // $this->log = new Logger('VideoclubLogger');
        // $this->log->pushHandler(new StreamHandler($logDir . '/videoclub.log', Logger::DEBUG));
        // $this->log->debug("Videoclub '{$this->nombre}' creado.");
    }

    /**
     * Registra en el log el resumen de cada producto.
     *
     * Usa muestraResumen() si está disponible en el objeto producto.
     */
    public function listarProductos(): void
    {
        foreach ($this->productos as $producto) {
            $linea = method_exists($producto, 'muestraResumen') ? $producto->muestraResumen() : json_encode($producto);
            $this->log->info($linea);
            // echo $linea . "<br>"; // opcional para depuración
        }
    }

    /**
     * Registra en el log el resumen de cada socio.
     *
     * Usa muestraResumen() si está disponible en el objeto socio.
     */
    public function listarSocios(): void
    {
        foreach ($this->socios as $socio) {
            $linea = method_exists($socio, 'muestraResumen') ? $socio->muestraResumen() : json_encode($socio);
            $this->log->info($linea);
            // echo $linea . "<br>"; // opcional
        }
    }

    /**
     * Añade un producto (Soporte) al inventario interno.
     *
     * Incrementa el contador de productos y registra la inclusión en el log.
     */
    private function incluirProducto(Soporte $s): void
    {
        $this->productos[] = $s;
        $this->numProductos++;
        $this->log->debug("Producto incluido: " . (method_exists($s, 'getTitulo') ? $s->getTitulo() : 'desconocido'));
    }

    /**
     * Añade un socio (Cliente) al videoclub.
     *
     * Devuelve $this para permitir encadenado en llamadas de inclusión.
     */
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
     * - Crea una instancia de CintaVideo con número secuencial.
     * - Si se proporciona URL de Metacritic, la asigna mediante setMetacritic().
     * - Añade el producto al inventario interno.
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
     * - Crea una instancia de Dvd con número secuencial.
     * - Asigna Metacritic si se proporciona y añade el producto.
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
     * - Crea una instancia de Juego con número secuencial.
     * - Asigna Metacritic si se proporciona y añade el producto.
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

    /**
     * Alquila un producto a un socio identificado por sus números.
     *
     * - Busca el socio y el producto por número.
     * - Lanza ClienteNoExisteException o SoporteNoEncontradoException si no existen.
     * - Delegar la lógica de alquiler al método alquilar() del Cliente.
     * - Captura y re-lanza excepciones específicas de negocio para logging.
     *
     * @throws ClienteNoExisteException
     * @throws SoporteNoEncontradoException
     * @throws SoporteYaAlquiladoException
     * @throws CupoSuperadoException
     */
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
            throw new ClienteNoExisteException("Cliente con número {$numeroCliente} no encontrado");
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

    /**
     * Devuelve un producto de un socio.
     *
     * - Busca socio y producto por número.
     * - Llama a devolver() del Cliente y marca el producto como no alquilado.
     * - Maneja SoporteNoEncontradoException lanzada por el cliente.
     *
     * @throws ClienteNoExisteException
     * @throws SoporteNoEncontradoException
     */
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
            throw new ClienteNoExisteException("Cliente con número {$numeroCliente} no encontrado");
        }
        if (!$producto) {
            $this->log->warning("Producto no encontrado al devolver: {$numeroSoporte}");
            throw new SoporteNoEncontradoException("Producto con número {$numeroSoporte} no encontrado");
        }

        try {
            $socio->devolver($numeroSoporte);
            $producto->setAlquilado(false);
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
     * Útil para consultas directas desde código o tests.
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

    /**
     * Método de compatibilidad con tests que esperan la firma alquilar($cliente, $producto, $soporte).
     *
     * Reutiliza la lógica interna existente delegando en alquilarSocioProducto.
     */
    public function alquilar(int $numeroCliente, int $numeroProducto, int $numeroSoporte): void
    {
        // Reutilizamos la lógica existente: el método interno usa cliente y soporte por número.
        $this->alquilarSocioProducto($numeroCliente, $numeroSoporte);
    }

    /**
     * Método de compatibilidad con tests que esperan la firma devolver($cliente, $producto, $soporte).
     *
     * Reutiliza la lógica interna existente delegando en devolverSocioProducto.
     */
    public function devolver(int $numeroCliente, int $numeroProducto, int $numeroSoporte): void
    {
        $this->devolverSocioProducto($numeroCliente, $numeroSoporte);
    }
}
