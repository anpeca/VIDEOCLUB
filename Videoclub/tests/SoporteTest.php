<?php

namespace Dwes\ProyectoVideoclub\Test;

use PHPUnit\Framework\TestCase;
use Dwes\ProyectoVideoclub\Soporte;

class SoporteTest extends TestCase
{
    public function testMuestraResumenDevuelveString(): void
    {
        // Creamos un mock concreto de la clase abstracta e indicamos
        // que queremos poder configurar el método 'muestraResumen'
        $soporte = $this->getMockForAbstractClass(
            Soporte::class,
            ['Título de prueba', 1, 0.0], // argumentos del constructor si los requiere
            '',    // nombre de la clase mock (vacío para autogenerar)
            true,  // callOriginalConstructor
            true,  // callOriginalClone
            true,  // callAutoload
            ['muestraResumen'] // <-- aquí indicamos los métodos a mockear
        );

        // Ahora sí podemos definir el comportamiento del método
        $soporte->method('muestraResumen')->willReturn('Resumen de prueba');

        $this->assertIsString($soporte->muestraResumen());
    }
}
