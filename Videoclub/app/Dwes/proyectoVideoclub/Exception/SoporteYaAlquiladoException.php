<?php

namespace Dwes\ProyectoVideoclub\Exception;

use Exception;

/**
 * Excepción lanzada cuando se intenta alquilar un soporte que ya está alquilado.
 *
 * Esta clase actúa como un tipo semántico específico para distinguir este
 * caso de error frente a otras excepciones genéricas. 
 *
 * Al extender de \Exception, es compatible con las aserciones de PHPUnit que
 * esperan excepciones basadas en Exception y con cualquier manejo genérico
 * de excepciones en la aplicación.
 */
class SoporteYaAlquiladoException extends Exception {}
