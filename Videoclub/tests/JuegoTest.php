<?php

namespace Dwes\ProyectoVideoclub\Tests;

use PHPUnit\Framework\TestCase;
use Dwes\ProyectoVideoclub\Juego;

class JuegoTest extends TestCase
{
    public function testMuestraResumenIncluyeConsolaYJugadores()
    {
        $juego = new Juego("The Last of Us", 4, 49.99, "PS4", 1, 1);

        $resultado = $juego->muestraResumen();

        $this->assertIsString($resultado);
        $this->assertStringContainsString("The Last of Us", $resultado);
        $this->assertStringContainsString("PS4", $resultado);
        $this->assertStringContainsString("1", $resultado);
    }
}
