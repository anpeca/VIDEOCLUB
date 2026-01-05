<?php

namespace Dwes\Monologos;

use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Processor\IntrospectionProcessor;



/**
 * Clase de ejemplo que demuestra el uso de Monolog para registrar mensajes
 * en función de una hora recibida. Configura distintos handlers y processors
 * para gestionar logs rotativos y salida de errores con introspección.
 *
 * Esta clase permite generar mensajes de saludo y despedida, registrando
 * la información en los distintos canales configurados.
 *
 * @package Dwes\Monologos
 */
class HolaMonolog
{
    /**
     * Instancia del logger principal utilizado para registrar mensajes.
     *
     * @var Logger
     */
    private Logger $miLog;

    /**
     * Hora utilizada para determinar el mensaje a registrar.
     *
     * @var int
     */
    private int $hora;

    /**
     * Constructor de la clase.
     *
     * Configura el logger con:
     * - Un handler de archivos rotativos (7 días de retención).
     * - Un handler hacia stderr con un processor de introspección.
     * - Validación de la hora recibida.
     *
     * @param int $hora Hora del día (0–24) utilizada para generar mensajes.
     */
    public function __construct(int $hora)
    {
        $this->hora = $hora;

        $this->miLog = new Logger('mi_logger');

        // Handler de archivos rotativos
        $fileHandler = new RotatingFileHandler(
            __DIR__ . '/../logs/app.log',
            7,
            Logger::WARNING
        );

        // Handler a salida de error + introspection
        $errorHandler = new StreamHandler(
            'php://stderr',
            Logger::DEBUG
        );
        $errorHandler->pushProcessor(new IntrospectionProcessor());

        $this->miLog->pushHandler($fileHandler);
        $this->miLog->pushHandler($errorHandler);

        // Validación de hora
        if ($hora < 0 || $hora > 24) {
            $this->miLog->warning('Hora inválida: ' . $hora);
        }
    }

    /**
     * Registra un mensaje de saludo en función de la hora configurada.
     *
     * - 06:00–11:59 → "Buenos días"
     * - 12:00–19:59 → "Buenas tardes"
     * - 20:00–05:59 → "Buenas noches"
     *
     * @return string Mensaje de saludo
     */
    public function saludar(): string
    {
        if ($this->hora >= 6 && $this->hora < 12) {
            $mensaje = 'Buenos días';
        } elseif ($this->hora >= 12 && $this->hora < 20) {
            $mensaje = 'Buenas tardes';
        } else {
            $mensaje = 'Buenas noches';
        }

        $this->miLog->info($mensaje);
        return $mensaje;
    }

    /**
     * Registra un mensaje de despedida en función de la hora configurada.
     *
     * - Antes de 12:00 → "Hasta luego"
     * - 12:00–19:59 → "Hasta la tarde"
     * - 20:00–23:59 → "Hasta mañana"
     *
     * @return string Mensaje de despedida
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

        $this->miLog->info($mensaje);
        return $mensaje;
    }
}
