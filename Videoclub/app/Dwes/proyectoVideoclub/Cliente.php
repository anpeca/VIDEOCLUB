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
     * Constructor de Cliente
     *
     * @param string $nombre Nombre del cliente
     * @param int $numero Número identificador
     * @param int $maxAlquilerConcurrente Máximo de alquileres simultáneos (por defecto 3)
     */
    public function __construct(string $nombre, int $numero, int $maxAlquilerConcurrente = 3)
    {
        $this->nombre = $nombre;
        $this->numero = $numero;
        $this->maxAlquilerConcurrente = $maxAlquilerConcurrente;
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
     * Muestra un resumen del cliente
     *
     * @return string
     */
    public function muestraResumen(): string
    {
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
