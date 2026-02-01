<?php
namespace Dwes\ProyectoVideoclub\Util;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Psr\Log\LoggerInterface;

class LogFactory{
/**
     * Crea un logger de Monolog con ruta segura y nivel DEBUG
     *
     * @param string $name Nombre del logger
     * @param string|null $logFile Nombre del fichero de log (opcional, por defecto 'videoclub.log')
     * @return LoggerInterface
     */


   public static function crearLogger(string $nombre, ?string $logFile = null): LoggerInterface
    {
        if ($logFile === null) {
            $logFile = 'videoclub.log';
        }

        // Ruta absoluta para el directorio logs
        $logDir = __DIR__ . '/../logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }

        // Crear el logger
        $logger = new Logger($nombre);
        $logger->pushHandler(new StreamHandler($logDir . '/' . $logFile, Logger::DEBUG));

        return $logger;
    }


}

?>