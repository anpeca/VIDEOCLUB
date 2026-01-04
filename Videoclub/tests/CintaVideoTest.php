<?php

namespace Dwes\ProyectoVideoclub\Tests;

use PHPUnit\Framework\TestCase;
use Dwes\ProyectoVideoclub\CintaVideo;

class CintaVideoTest extends TestCase
{
    public function testMuestraResumenIncluyeDuracion()
    {
        $cinta = new CintaVideo("Los Cazafantasmas", 2, 4.0, 107);

        $resultado = $cinta->muestraResumen();

        $this->assertIsString($resultado);
        $this->assertStringContainsString("Los Cazafantasmas", $resultado);
        $this->assertStringContainsString("107", $resultado);
    }
}
