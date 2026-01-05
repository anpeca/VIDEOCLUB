<?php

namespace Dwes\ProyectoVideoclub\Util;

use Dwes\ProyectoVideoclub\Exception\ClienteNoExisteException;

/**
 * Clase alias para mantener compatibilidad con código o tests antiguos que
 * importaban la excepción desde el namespace Util.
 *
 * la clase simplemente extiende
 * ClienteNoExisteException del namespace Exception para que cualquier lugar
 * que haga `use Dwes\ProyectoVideoclub\Util\ClienteNoEncontradoException`
 * siga funcionando sin cambios en el resto del proyecto.
 *
 * Mantener este alias evita tener que actualizar multitud de `use` en tests
 * o código legado y preserva la semántica: sigue siendo la misma excepción
 * concreta, solo con un nombre/ubicación alternativa.
 */
class ClienteNoEncontradoException extends ClienteNoExisteException {}
