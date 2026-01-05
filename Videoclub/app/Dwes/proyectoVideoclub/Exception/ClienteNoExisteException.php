<?php

namespace Dwes\ProyectoVideoclub\Exception;

use Exception;

/**
 * Excepción lanzada cuando no se encuentra un cliente en el videoclub.
 */
class ClienteNoExisteException extends Exception
{
    // Clase intencionalmente vacía: sirve como excepción semántica específica.
    // Extiende \Exception para ser compatible con expectException(Exception::class)
    // en tests y con cualquier manejo genérico de excepciones.
}
