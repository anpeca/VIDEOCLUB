<?php

namespace Dwes\ProyectoVideoclub\Util;

/**
 * Clase alias para excepciones relacionadas con el videoclub.
 *
 * Esta clase existe para proporcionar un tipo semántico específico dentro del
 * namespace Util sin cambiar el comportamiento de la excepción base.
 * Extiende de \Exception para mantener compatibilidad con el manejo estándar
 * de excepciones en PHP y con las aserciones de PHPUnit que esperan excepciones
 * basadas en Exception.
 *
 * la clase permanece vacía y su
 * propósito es únicamente tipificar errores relacionados con el videoclub.
 */
class VideoclubException extends \Exception
{
    // Intencionalmente vacía: sirve como excepción semántica específica.
}
