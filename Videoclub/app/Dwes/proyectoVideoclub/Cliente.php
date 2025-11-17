<?php

namespace Dwes\ProyectoVideoclub;

use Dwes\ProyectoVideoclub\Util\SoporteYaAlquiladoException;
use Dwes\ProyectoVideoclub\Util\CupoSuperadoException;
use Dwes\ProyectoVideoclub\Util\SoporteNoEncontradoException;

/**
 * Clase Cliente
 *
 * Representa a un cliente del videoclub, con capacidad para alquilar y devolver soportes.
 * Cada cliente tiene un número identificador, un nombre, un límite de alquileres simultáneos
 * y un historial de soportes alquilados.
 */
class Cliente
{
    /** @var string Nombre del cliente */
    private string $nombre;

    /** @var int Número identificador del cliente */
    private int $numero;

    /** @var int Máximo de alquileres simultáneos permitidos */
    private int $maxAlquilerConcurrente;

    /** @var int Total de soportes alquilados (histórico) */
    private int $numSoportesAlquilados = 0;

    /** @var Soporte[] Array de soportes actualmente alquilados */
    private array $soportesAlquilados = [];

    /**
     * Usuario asociado al cliente (login)
     *
     * @var string|null
     */
    private ?string $user = null;

    /**
     * Hash de la contraseña (no almacenar la contraseña en claro)
     *
     * @var string|null
     */
    private ?string $passwordHash = null;

    /**
     * Constructor de Cliente
     *
     * @param string $nombre Nombre del cliente
     * @param int $numero Número identificador
     * @param int $maxAlquilerConcurrente Máximo de alquileres simultáneos (por defecto 3)
     * @param string|null $user Nombre de usuario opcional para login
     * @param string|null $plainPassword Contraseña en claro opcional (se almacenará como hash)
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
    }

    /**
     * Obtiene el número identificador del cliente
     *
     * @return int
     */
    public function getNumero(): int
    {
        return $this->numero;
    }

    /**
     * Establece el número identificador del cliente
     *
     * @param int $numero
     */
    public function setNumero(int $numero): void
    {
        $this->numero = $numero;
    }

    /**
     * Obtiene el máximo de alquileres simultáneos permitidos
     *
     * @return int
     */
    public function getMaxAlquilerConcurrente(): int
    {
        return $this->maxAlquilerConcurrente;
    }

    /**
     * Devuelve el array de soportes actualmente alquilados
     *
     * @return Soporte[]
     */
    public function getSoportesAlquilados(): array
    {
        return $this->soportesAlquilados;
    }

    /**
     * Devuelve el número total de soportes alquilados (histórico)
     *
     * @return int
     */
    public function getNumSoportesAlquilados(): int
    {
        return $this->numSoportesAlquilados;
    }

    /**
     * Obtiene el nombre del cliente
     *
     * @return string
     */
    public function getNombre(): string
    {
        return $this->nombre;
    }

    /**
     * Establece el nombre del cliente
     *
     * @param string $nombre
     */
    public function setNombre(string $nombre): void
    {
        $this->nombre = $nombre;
    }

    /**
     * Obtiene el usuario de login asociado al cliente
     *
     * @return string|null
     */
    public function getUser(): ?string
    {
        return $this->user;
    }

    /**
     * Establece el usuario de login asociado al cliente
     *
     * @param string $user
     */
    public function setUser(string $user): void
    {
        $this->user = $user;
    }

    /**
     * Establece la contraseña en texto plano (se guarda como hash internamente)
     *
     * @param string $plain
     * @return void
     */
    public function setPassword(string $plain): void
    {
        // Usar password_hash para seguridad; PASSWORD_DEFAULT es adecuado para prácticas
        $this->passwordHash = password_hash($plain, PASSWORD_DEFAULT);
    }

    /**
     * Verifica una contraseña en texto plano contra el hash almacenado
     *
     * @param string $plain
     * @return bool
     */
    public function verifyPassword(string $plain): bool
    {
        if ($this->passwordHash === null) {
            return false;
        }
        return password_verify($plain, $this->passwordHash);
    }

    /**
     * Devuelve true si el cliente tiene contraseña configurada (útil para comprobaciones)
     *
     * @return bool
     */
    public function hasPassword(): bool
    {
        return $this->passwordHash !== null;
    }

    /**
     * Muestra un resumen del cliente
     *
     * @return string
     */
    public function muestraResumen(): string
    {
        // Incluimos nombre y número de soportes activos en el resumen
        return "Cliente: " . $this->nombre . " - Total alquileres: " . count($this->soportesAlquilados);
    }

    /**
     * Comprueba si el cliente ya tiene alquilado un soporte específico
     *
     * @param Soporte $s
     * @return bool
     */
    public function tieneAlquilado(Soporte $s): bool
    {
        foreach ($this->soportesAlquilados as $soporte) {
            if ($soporte === $s) {
                return true;
            }
        }
        return false;
    }

    /**
     * Alquila un soporte al cliente si no lo tiene ya y no ha superado el cupo
     *
     * @param Soporte $s
     * @return Cliente
     * @throws SoporteYaAlquiladoException Si el soporte ya está alquilado por el cliente
     * @throws CupoSuperadoException Si el cliente ha superado el cupo de alquileres
     */
    public function alquilar(Soporte $s): Cliente
    {
        $s->alquilado = true;

        if ($this->tieneAlquilado($s)) {
            throw new SoporteYaAlquiladoException("El soporte ya está alquilado por este cliente.");
        }

        if (count($this->soportesAlquilados) >= $this->maxAlquilerConcurrente) {
            throw new CupoSuperadoException("Ha superado el cupo de alquileres.");
        }

        $this->soportesAlquilados[] = $s;
        $this->numSoportesAlquilados++;
        return $this;
    }

    /**
     * Devuelve un soporte alquilado por número
     *
     * @param int $numSoporte Número identificador del soporte
     * @return Cliente
     * @throws SoporteNoEncontradoException Si el cliente no tiene ese soporte alquilado
     */
    public function devolver(int $numSoporte): Cliente
    {
        foreach ($this->soportesAlquilados as $key => $soporte) {
            if ($soporte->getNumero() === $numSoporte) {
                $soporte->alquilado = false;
                unset($this->soportesAlquilados[$key]);
                $this->soportesAlquilados = array_values($this->soportesAlquilados);
                return $this;
            }
        }
        throw new SoporteNoEncontradoException("El cliente no tenía alquilado este soporte.");
    }

    /**
     * Muestra por pantalla los soportes actualmente alquilados por el cliente
     *
     * @return void
     */
    public function listaAlquileres(): void
    {
        echo "Hay " . $this->getNumSoportesAlquilados() . " soportes alquilados:<br>";
        foreach ($this->soportesAlquilados as $soporte) {
            echo $soporte->muestraResumen() . "<br>";
        }
    }
}
?>
