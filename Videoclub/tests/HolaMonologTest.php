<?php
declare(strict_types=1);

namespace Dwes\Monologos\Tests;

use PHPUnit\Framework\TestCase;
use Dwes\Monologos\HolaMonolog;

class HolaMonologTest extends TestCase
{
    public function testSaludoManana(): void
    {
        $h = new HolaMonolog(8); // mañana
        $this->assertSame('Buenos días', $h->saludar());
        $this->assertSame('Hasta luego', $h->despedir());
    }

    public function testSaludoTarde(): void
    {
        $h = new HolaMonolog(15); // tarde
        $this->assertSame('Buenas tardes', $h->saludar());
        $this->assertSame('Hasta la tarde', $h->despedir());
    }

    public function testSaludoNoche(): void
    {
        $h = new HolaMonolog(22); // noche
        $this->assertSame('Buenas noches', $h->saludar());
        $this->assertSame('Hasta mañana', $h->despedir());
    }

    public function testLimiteInicioManana(): void
    {
        $h = new HolaMonolog(6);
        $this->assertSame('Buenos días', $h->saludar());
    }

    public function testLimiteFinManana(): void
    {
        $h = new HolaMonolog(11);
        $this->assertSame('Buenos días', $h->saludar());
    }

    public function testLimiteInicioTarde(): void
    {
        $h = new HolaMonolog(12);
        $this->assertSame('Buenas tardes', $h->saludar());
    }

    public function testLimiteFinTarde(): void
    {
        $h = new HolaMonolog(19);
        $this->assertSame('Buenas tardes', $h->saludar());
    }

    public function testLimiteInicioNoche(): void
    {
        $h = new HolaMonolog(20);
        $this->assertSame('Buenas noches', $h->saludar());
    }

    public function testHoraInvalidaWarningButStillReturns(): void
    {
        // Si se pasa una hora inválida, la clase registra una warning,
        // pero los métodos siguen devolviendo el saludo/despedida según la lógica.
        $h = new HolaMonolog(30);
        // hora 30 se considera fuera de 0-24; según la lógica actual, cae en el else -> "Buenas noches"
        $this->assertSame('Buenas noches', $h->saludar());
        $this->assertSame('Hasta mañana', $h->despedir());
    }
}
