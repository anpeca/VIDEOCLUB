<?php
namespace Dwes\ProyectoVideoclub\Test;

use PHPUnit\Framework\TestCase;
use Dwes\ProyectoVideoclub\Soporte;

class SoporteTest extends TestCase{

    public function testMuestraResumeDevuelveString(){
        $soporte = new Soporte("Matrix", 1, 3.5);

        $resultado = $soporte->muestraResumen();

        $this->assertIsString($resultado);
        $this->assertStringContainsString("Matrix",$resultado);
        $this->assertStringContainsString("3.5",$resultado);
    }
}

?>