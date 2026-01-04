<?php

namespace Dwes\ProyectoVideoclub\Tests;

use PHPUnit\Framework\TestCase;
use Dwes\ProyectoVideoclub\Dvd;

class DvdTest extends TestCase
{
    public function testMuestraResumenIncluyeIdiomasYPantalla()
    {
        $dvd = new Dvd("Origen", 3, 5.0, "ES,EN", "16:9");

        $resultado = $dvd->muestraResumen();

        $this->assertIsString($resultado);
        $this->assertStringContainsString("Origen", $resultado);
        $this->assertStringContainsString("ES,EN", $resultado);
        $this->assertStringContainsString("16:9", $resultado);
    }
}
