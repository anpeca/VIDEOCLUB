<?php

namespace Dwes\ProyectoVideoclub\Tests;

use PHPUnit\Framework\TestCase;
use Dwes\ProyectoVideoclub\Dvd;

/**
 * Pruebas unitarias para la clase Dvd.
 *
 * Verifica que muestraResumen() incluye idiomas, formato de pantalla y duración,
 * y que el getter getDuracion() devuelve el valor esperado.
 */
class DvdTest extends TestCase
{
    /**
     * Comprueba que el resumen del DVD contiene:
     *  - el título proporcionado,
     *  - la cadena de idiomas,
     *  - el formato de pantalla,
     *  - la duración cuando se pasa como último parámetro.
     */
    public function testMuestraResumenIncluyeIdiomasYPantallaYDuracion()
    {
        // Crear una instancia de Dvd pasando la duración como último parámetro (120 minutos)
        $dvd = new Dvd("Origen", 3, 5.0, "ES,EN", "16:9", 120);

        // Obtener el resumen generado por la instancia
        $resultado = $dvd->muestraResumen();

        // Aserciones básicas sobre el tipo y contenido del resumen:
        // 1) El resultado debe ser una cadena
        $this->assertIsString($resultado);
        // 2) Debe contener el título "Origen"
        $this->assertStringContainsString("Origen", $resultado);
        // 3) Debe contener la lista de idiomas "ES,EN"
        $this->assertStringContainsString("ES,EN", $resultado);
        // 4) Debe contener el formato de pantalla "16:9"
        $this->assertStringContainsString("16:9", $resultado);

        // Comprobaciones adicionales sobre la duración:
        // 5) El getter getDuracion() debe devolver exactamente 120 (tipo int)
        $this->assertSame(120, $dvd->getDuracion());
        // 6) El resumen debe incluir el número 120 como texto
        $this->assertStringContainsString('120', $resultado);
        // 7) El resumen debe incluir la abreviatura 'min' indicando minutos
        $this->assertStringContainsString('min', $resultado);
    }
}
