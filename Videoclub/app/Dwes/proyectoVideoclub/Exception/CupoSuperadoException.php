<?php

namespace Dwes\ProyectoVideoclub\Exception;

use Exception;

/**
 * Excepción semántica que indica que un cliente ha intentado
 * alquilar más soportes de los permitidos por su cupo.
 *
 * Esta clase se deja intencionalmente vacía porque su único propósito
 * es servir como tipo específico de excepción que pueda capturarse
 * y distinguirse de otras excepciones genéricas.
 *
 * Hereda de \Exception para mantener compatibilidad con manejadores
 * y con las aserciones de PHPUnit que esperan excepciones basadas en Exception.
 */
class CupoSuperadoException extends Exception {}
