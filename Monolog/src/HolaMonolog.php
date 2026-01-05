<?php

namespace Dwes\Monologos;

use Psr\Log\LoggerInterface;
use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Processor\IntrospectionProcessor;

/**
 * HolaMonolog con inyección de logger opcional y almacenamiento de últimos saludos.
 */
class HolaMonolog
{
    /** @var mixed Logger real (Monolog\Logger) o cualquier LoggerInterface (mock) */
    private $miLog;

    private int $hora;

    /** @var string[] */
    private array $ultimosSaludos = [];

    public function __construct(int $hora, ?LoggerInterface $logger = null)
    {
        // Validación estricta de la hora: lanzar excepción si fuera de rango
        if ($hora < 0 || $hora > 24) {
            throw new \InvalidArgumentException('Hora inválida: ' . $hora);
        }

        $this->hora = $hora;

        if ($logger !== null) {
            // Si nos pasan un logger (por ejemplo en tests), lo usamos tal cual.
            $this->miLog = $logger;
        } else {
            // Creamos y configuramos un Monolog\Logger real
            $monolog = new Logger('mi_logger');

            $fileHandler = new RotatingFileHandler(
                __DIR__ . '/../logs/app.log',
                7,
                Logger::WARNING
            );

            $errorHandler = new StreamHandler('php://stderr', Logger::DEBUG);
            $errorHandler->pushProcessor(new IntrospectionProcessor());

            // pushHandler es un método de Monolog\Logger, así que lo llamamos sobre $monolog
            $monolog->pushHandler($fileHandler);
            $monolog->pushHandler($errorHandler);

            $this->miLog = $monolog;
        }
    }

    public function setHora(int $hora): void
    {
        // Si quieres que setHora también valide y lance excepción, descomenta la validación:
        if ($hora < 0 || $hora > 24) {
            throw new \InvalidArgumentException('Hora inválida: ' . $hora);
        }
        $this->hora = $hora;
    }

    /**
     * Devuelve los últimos saludos almacenados, más reciente primero.
     *
     * @return string[]
     */
    public function getUltimosSaludos(): array
    {
        return $this->ultimosSaludos;
    }

    /**
     * Registra y devuelve el saludo según la hora.
     *
     * @return string
     */
    public function saludar(): string
    {
        $mensaje = $this->determinarSaludo();

        if (is_object($this->miLog) && method_exists($this->miLog, 'info')) {
            $this->miLog->info($mensaje);
        }

        $this->pushSaludo($mensaje);
        return $mensaje;
    }

    /**
     * Registra y devuelve la despedida según la hora.
     *
     * @return string
     */
    public function despedir(): string
    {
        if ($this->hora < 12) {
            $mensaje = 'Hasta luego';
        } elseif ($this->hora < 20) {
            $mensaje = 'Hasta la tarde';
        } else {
            $mensaje = 'Hasta mañana';
        }

        if (is_object($this->miLog) && method_exists($this->miLog, 'info')) {
            $this->miLog->info($mensaje);
        }

        return $mensaje;
    }

    /**
     * Añade un saludo al buffer manteniendo solo los últimos 3.
     */
    private function pushSaludo(string $mensaje): void
    {
        array_unshift($this->ultimosSaludos, $mensaje); // más reciente al inicio
        if (count($this->ultimosSaludos) > 3) {
            $this->ultimosSaludos = array_slice($this->ultimosSaludos, 0, 3);
        }
    }

    /**
     * Lógica separada para determinar el saludo.
     */
    private function determinarSaludo(): string
    {
        if ($this->hora >= 6 && $this->hora < 12) {
            return 'Buenos días';
        } elseif ($this->hora >= 12 && $this->hora < 20) {
            return 'Buenas tardes';
        } else {
            return 'Buenas noches';
        }
    }
}
