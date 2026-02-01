<?php
namespace Dwes\ProyectoVideoclub;

use Dwes\ProyectoVideoclub\Util\SoporteYaAlquiladoException;
use Dwes\ProyectoVideoclub\Util\CupoSuperadoException;
use Dwes\ProyectoVideoclub\Util\SoporteNoEncontradoException;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Dwes\ProyectoVideoclub\Util\LogFactory;

class Cliente
{
    private string $nombre;
    private int $numero;
    private int $maxAlquilerConcurrente;
    private int $numSoportesAlquilados = 0;
    private array $soportesAlquilados = [];
    private ?string $user = null;
    private ?string $passwordHash = null;
    private Logger $log;

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

        $this->log = LogFactory::crearLogger('ClienteLogger', 'cliente.log');
        $this->log->debug('Cliente creado: '. $this->nombre);


        // Configurar Monolog
        // $logDir = __DIR__ . '/../../logs';
        // if (!is_dir($logDir)) {
        //     mkdir($logDir, 0777, true);
        // }
        // $this->log = new Logger('VideoclubLogger');
        // $this->log->pushHandler(new StreamHandler($logDir . '/videoclub.log', Logger::DEBUG));

        // $this->log->debug("Cliente creado: {$this->nombre}");
    }

    public function getNumero(): int
    {
        return $this->numero;
    }

    public function setNumero(int $numero): void
    {
        $this->numero = $numero;
    }

    public function getNumSoportesAlquilados(): int
    {
        return $this->numSoportesAlquilados;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): void
    {
        $this->nombre = $nombre;
    }

    public function getUser(): ?string
    {
        return $this->user;
    }

    public function setUser(string $user): void
    {
        $this->user = $user;
    }

    public function setPassword(string $plain): void
    {
        $this->passwordHash = password_hash($plain, PASSWORD_DEFAULT);
    }

    public function verifyPassword(string $plain): bool
    {
        if ($this->passwordHash === null) return false;
        return password_verify($plain, $this->passwordHash);
    }

    public function muestraResumen(): string
    {
        return "Cliente: {$this->nombre} - Total alquileres activos: " . count($this->soportesAlquilados);
    }

    public function tieneAlquilado(Soporte $s): bool
    {
        foreach ($this->soportesAlquilados as $soporte) {
            if ($soporte === $s) return true;
        }
        return false;
    }

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

        $s->alquilado = true;
        $this->soportesAlquilados[] = $s;
        $this->numSoportesAlquilados++;
        $this->log->info("Alquiler realizado", [
            'cliente' => $this->nombre,
            'soporte' => method_exists($s, 'getTitulo') ? $s->getTitulo() : 'desconocido'
        ]);
        return $this;
    }

    public function devolver(int $numSoporte): Cliente
    {
        foreach ($this->soportesAlquilados as $key => $soporte) {
            if ($soporte->getNumero() === $numSoporte) {
                $soporte->alquilado = false;
                unset($this->soportesAlquilados[$key]);
                $this->soportesAlquilados = array_values($this->soportesAlquilados);
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

    public function listaAlquileres(): void
    {
        $this->log->info("Lista de alquileres del cliente {$this->nombre}");
        foreach ($this->soportesAlquilados as $soporte) {
            $linea = method_exists($soporte, 'muestraResumen') ? $soporte->muestraResumen() : json_encode($soporte);
            $this->log->info($linea);
            // echo $linea . "<br>"; // opcional para depuración
        }
    }

    public function getAlquileres(): array
    {
        return $this->soportesAlquilados;
    }

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
