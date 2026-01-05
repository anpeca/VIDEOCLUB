<?php
namespace Dwes\ProyectoVideoclub\Tests;

use PHPUnit\Framework\TestCase;
use Dwes\ProyectoVideoclub\Videoclub;
use Dwes\ProyectoVideoclub\Exception\ClienteNoExisteException;

class VideoclubClienteTest extends TestCase
{
    public function testAlquilarConClienteInexistenteLanzaClienteNoExisteException(): void
    {
        $videoclub = new Videoclub('Videoclub de pruebas');

        $this->expectException(ClienteNoExisteException::class);

        $videoclub->alquilar(9999, 1, 1);
    }

    public function testDevolverConClienteInexistenteLanzaClienteNoExisteException(): void
    {
        $videoclub = new Videoclub('Videoclub de pruebas');

        $this->expectException(ClienteNoExisteException::class);

        $videoclub->devolver(9999, 1, 1);
    }
}
