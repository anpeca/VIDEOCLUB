<?php
namespace Dwes\ProyectoVideoclub;

use Dwes\ProyectoVideoclub\Exception\SoporteYaAlquiladoException;
use Dwes\ProyectoVideoclub\Exception\CupoSuperadoException;
use Dwes\ProyectoVideoclub\Exception\SoporteNoEncontradoException;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Dwes\ProyectoVideoclub\Util\LogFactory;

/**
 * Representa un cliente del videoclub.
 *
 * - Gestiona el estado de los alquileres del cliente.
 * - Lanza excepciones específicas cuando se producen condiciones de error
 *   (soporte ya alquilado, cupo superado, soporte no encontrado).
 * - Registra eventos relevantes mediante un logger obtenido de LogFactory.
 *
 * Nota: el código original se mantiene intacto; aquí se añaden comentarios
 * para documentar el propósito de propiedades y métodos sin modificar la lógica.
 */
class Cliente
{
    /** Nombre del cliente */
    private string $nombre;

    /** Número identificador del cliente */
    private int $numero;

    /** Máximo de alquileres concurrentes permitidos para este cliente */
    private int $maxAlquilerConcurrente;

    /** Contador de soportes actualmente alquilados (valor entero) */
    private int $numSoportesAlquilados = 0;

    /** Array de objetos Soporte que el cliente tiene alquilados */
    private array $soportesAlquilados = [];

    /** Usuario asociado (opcional) */
    private ?string $user = null;

    /** Hash de la contraseña (si se establece) */
    private ?string $passwordHash = null;

    /** Logger para registrar eventos relacionados con el cliente */
    private Logger $log;

    /**
     * Constructor.
     *
     * @param string      $nombre                 Nombre del cliente.
     * @param int         $numero                 Número identificador.
     * @param int         $maxAlquilerConcurrente Cupo máximo de alquileres.
     * @param string|null $user                   Usuario opcional.
     * @param string|null $plainPassword          Contraseña en texto plano (se hashéa).
     */
    public function __construct(string $nombre, int $numero, int $maxAlquilerConcurrente = 3, ?string $user = null, ?string $plainPassword = null)
    {
        $this->nombre = $nombre;
        $this->numero = $numero;
        $this->maxAlquilerConcurrente = $maxAlquilerConcurrente;

        if ($user !== null) {
            $this->user = $user;
        }
        if ($plainPassword !== null) {
            $this->setPassword($plainPassword);
        }

        // Se obtiene un logger a través de la factoría de logs
        $this->log = LogFactory::crearLogger('ClienteLogger', 'cliente.log');
        $this->log->debug('Cliente creado: '. $this->nombre);
    }

    /** Devuelve el número identificador del cliente */
    public function getNumero(): int
    {
        return $this->numero;
    }

    /** Establece el número identificador del cliente */
    public function setNumero(int $numero): void
    {
        $this->numero = $numero;
    }

    /** Devuelve el número de soportes actualmente alquilados (contador) */
    public function getNumSoportesAlquilados(): int
    {
        return $this->numSoportesAlquilados;
    }

    /** Devuelve el nombre del cliente */
    public function getNombre(): string
    {
        return $this->nombre;
    }

    /** Establece el nombre del cliente */
    public function setNombre(string $nombre): void
    {
        $this->nombre = $nombre;
    }

    /** Devuelve el usuario asociado (si existe) */
    public function getUser(): ?string
    {
        return $this->user;
    }

    /** Establece el usuario asociado */
    public function setUser(string $user): void
    {
        $this->user = $user;
    }

    /**
     * Almacena el hash de la contraseña a partir del texto plano.
     *
     * Se utiliza password_hash con el algoritmo por defecto.
     */
    public function setPassword(string $plain): void
    {
        $this->passwordHash = password_hash($plain, PASSWORD_DEFAULT);
    }

    /**
     * Verifica una contraseña en texto plano contra el hash almacenado.
     *
     * @return bool True si coincide, false en caso contrario o si no hay hash.
     */
    public function verifyPassword(string $plain): bool
    {
        if ($this->passwordHash === null) return false;
        return password_verify($plain, $this->passwordHash);
    }

    /**
     * Muestra un resumen textual del cliente y sus alquileres activos.
     *
     * @return string Resumen legible.
     */
    public function muestraResumen(): string
    {
        return "Cliente: {$this->nombre} - Total alquileres activos: " . count($this->soportesAlquilados);
    }

    /**
     * Comprueba si el cliente ya tiene alquilado un soporte concreto.
     *
     * Se compara por referencia de objeto.
     */
    public function tieneAlquilado(Soporte $s): bool
    {
        foreach ($this->soportesAlquilados as $soporte) {
            if ($soporte === $s) return true;
        }
        return false;
    }

    /**
     * Realiza el alquiler de un soporte para este cliente.
     *
     * - Lanza SoporteYaAlquiladoException si el cliente ya tiene ese soporte.
     * - Lanza CupoSuperadoException si se supera el cupo máximo.
     * - Marca el soporte como alquilado, lo añade al array y actualiza contadores.
     *
     * @return Cliente Devuelve $this para permitir encadenado.
     * @throws SoporteYaAlquiladoException
     * @throws CupoSuperadoException
     */
    public function alquilar(Soporte $s): Cliente
    {
        if ($this->tieneAlquilado($s)) {
            $this->log->warning("Intento de alquilar soporte ya alquilado", [
                'cliente' => $this->nombre,
                'soporte' => method_exists($s, 'getTitulo') ? $s->getTitulo() : 'desconocido'
            ]);
            throw new SoporteYaAlquiladoException("El soporte ya está alquilado por este cliente.");
        }

        if (count($this->soportesAlquilados) >= $this->maxAlquilerConcurrente) {
            $this->log->warning("Cupo de alquileres superado", [
                'cliente' => $this->nombre,
                'actual' => count($this->soportesAlquilados),
                'maximo' => $this->maxAlquilerConcurrente
            ]);
            throw new CupoSuperadoException("Ha superado el cupo de alquileres.");
        }

        // Marca el soporte como alquilado mediante su API pública
        $s->setAlquilado(true);
        $this->soportesAlquilados[] = $s;
        $this->numSoportesAlquilados++;
        $this->log->info("Alquiler realizado", [
            'cliente' => $this->nombre,
            'soporte' => method_exists($s, 'getTitulo') ? $s->getTitulo() : 'desconocido'
        ]);
        return $this;
    }

    /**
     * Devuelve un soporte identificado por su número.
     *
     * - Busca en el array de soportes alquilados y, si lo encuentra,
     *   lo marca como no alquilado, lo elimina del array y actualiza contadores.
     * - Si no lo encuentra, lanza SoporteNoEncontradoException.
     *
     * @param int $numSoporte Número del soporte a devolver.
     * @return Cliente
     * @throws SoporteNoEncontradoException
     */
    public function devolver(int $numSoporte): Cliente
    {
        foreach ($this->soportesAlquilados as $key => $soporte) {
            if ($soporte->getNumero() === $numSoporte) {
                // Se accede directamente a la propiedad 'alquilado' del soporte
                // para marcarlo como devuelto (manteniendo el código original).
                $soporte->alquilado = false;
                unset($this->soportesAlquilados[$key]);
                $this->soportesAlquilados = array_values($this->soportesAlquilados);
                // Decrementar contador de soportes alquilados
                $this->numSoportesAlquilados = max(0, $this->numSoportesAlquilados - 1);
                $this->log->info("Devolución realizada", [
                    'cliente' => $this->nombre,
                    'soporte_numero' => $numSoporte
                ]);
                return $this;
            }
        }
        $this->log->warning("Intento de devolución de soporte no alquilado", [
            'cliente' => $this->nombre,
            'soporte_numero' => $numSoporte
        ]);
        throw new SoporteNoEncontradoException("El cliente no tenía alquilado este soporte.");
    }

    /**
     * Registra en el log la lista de alquileres actuales del cliente.
     *
     * Para cada soporte intenta usar muestraResumen() si está disponible.
     */
    public function listaAlquileres(): void
    {
        $this->log->info("Lista de alquileres del cliente {$this->nombre}");
        foreach ($this->soportesAlquilados as $soporte) {
            $linea = method_exists($soporte, 'muestraResumen') ? $soporte->muestraResumen() : json_encode($soporte);
            $this->log->info($linea);
        }
    }

    /**
     * Devuelve el array de soportes actualmente alquilados por el cliente.
     *
     * @return Soporte[] Array de objetos Soporte
     */
    public function getAlquileres(): array
    {
        return $this->soportesAlquilados;
    }

    /**
     * Inicializa el array de soportes alquilados a partir de un array externo.
     *
     * - Actualiza el contador y, si los objetos tienen la propiedad 'alquilado',
     *   la marca a true (manteniendo el comportamiento original).
     *
     * @param array $soportesArray Array de objetos Soporte
     */
    public function setSoportesAlquiladosFromArray(array $soportesArray): void
    {
        $this->soportesAlquilados = array_values($soportesArray);
        $this->numSoportesAlquilados = count($this->soportesAlquilados);
        foreach ($this->soportesAlquilados as $s) {
            if (is_object($s) && property_exists($s, 'alquilado')) {
                $s->alquilado = true;
            }
        }
    }
}
