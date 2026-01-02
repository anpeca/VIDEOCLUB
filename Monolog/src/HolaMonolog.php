<?php

namespace Dwes\Monologos;

use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Processor\IntrospectionProcessor;

class HolaMonolog
{
    private Logger $miLog;
    private int $hora;

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

    public function saludar(): void
    {
        if ($this->hora >= 6 && $this->hora < 12) {
            $mensaje = 'Buenos días';
        } elseif ($this->hora >= 12 && $this->hora < 20) {
            $mensaje = 'Buenas tardes';
        } else {
            $mensaje = 'Buenas noches';
        }

        $this->miLog->info($mensaje);
    }

    public function despedir(): void
    {
        if ($this->hora < 12) {
            $mensaje = 'Hasta luego';
        } elseif ($this->hora < 20) {
            $mensaje = 'Hasta la tarde';
        } else {
            $mensaje = 'Hasta mañana';
        }

        $this->miLog->info($mensaje);
    }
}
