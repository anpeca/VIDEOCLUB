<?php

namespace Dwes\ProyectoVideoclub\Tests;

use PHPUnit\Framework\TestCase;
use Dwes\ProyectoVideoclub\CintaVideo;

/**
 * Pruebas unitarias para la clase CintaVideo.
 *
 * Comprueba que el método muestraResumen() incluye el título y la duración.
 */
class CintaVideoTest extends TestCase
{
    /**
     * Verifica que muestraResumen() devuelve una cadena que contiene:
     *  - el título proporcionado al construir la CintaVideo
     *  - la duración en minutos (como número)
     */
    public function testMuestraResumenIncluyeDuracion()
    {
        // Crear una instancia de CintaVideo con título, número, precio y duración
        $cinta = new CintaVideo("Los Cazafantasmas", 2, 4.0, 107);

        // Obtener el resumen generado por la instancia
        $resultado = $cinta->muestraResumen();

        // Aserciones:
        // 1) El resultado debe ser una cadena
        $this->assertIsString($resultado);
        // 2) Debe contener el título "Los Cazafantasmas"
        $this->assertStringContainsString("Los Cazafantasmas", $resultado);
        // 3) Debe contener la duración "107"
        $this->assertStringContainsString("107", $resultado);
    }
}
