<?php

namespace Dwes\ProyectoVideoclub\Exception;

use Exception;

/**
 * Excepción lanzada cuando no se encuentra un soporte (película, DVD, juego, etc.)
 * en el videoclub.
 *
 * Esta clase se mantiene intencionalmente vacía: su propósito es servir como
 * tipo semántico específico que permita capturar y distinguir este caso frente
 * a otras excepciones genéricas. Al extender de \Exception, es compatible con
 * las aserciones de PHPUnit que esperan excepciones basadas en Exception.
 */
class SoporteNoEncontradoException extends Exception {}
