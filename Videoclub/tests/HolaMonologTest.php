<?php

declare(strict_types=1);

namespace Dwes\Monologos\Tests;

use PHPUnit\Framework\TestCase;
use Dwes\Monologos\HolaMonolog;

class HolaMonologTest extends TestCase
{
    private function createLoggerMock()
    {
        $logger = $this->createMock(\Psr\Log\LoggerInterface::class);
        $logger->expects($this->any())->method('info');
        // No es obligatorio que exista 'warning' ahora que lanzamos excepción en constructor,
        // pero lo dejamos para compatibilidad con implementaciones previas.
        $logger->expects($this->any())->method('warning');
        return $logger;
    }

    public function testSaludoManana(): void
    {
        $logger = $this->createLoggerMock();
        $h = new HolaMonolog(8, $logger); // mañana
        $this->assertSame('Buenos días', $h->saludar());
        $this->assertSame('Hasta luego', $h->despedir());
    }

    public function testSaludoTarde(): void
    {
        $logger = $this->createLoggerMock();
        $h = new HolaMonolog(15, $logger); // tarde
        $this->assertSame('Buenas tardes', $h->saludar());
        $this->assertSame('Hasta la tarde', $h->despedir());
    }

    public function testSaludoNoche(): void
    {
        $logger = $this->createLoggerMock();
        $h = new HolaMonolog(22, $logger); // noche
        $this->assertSame('Buenas noches', $h->saludar());
        $this->assertSame('Hasta mañana', $h->despedir());
    }

    public function testLimiteInicioManana(): void
    {
        $logger = $this->createLoggerMock();
        $h = new HolaMonolog(6, $logger);
        $this->assertSame('Buenos días', $h->saludar());
    }

    public function testLimiteFinManana(): void
    {
        $logger = $this->createLoggerMock();
        $h = new HolaMonolog(11, $logger);
        $this->assertSame('Buenos días', $h->saludar());
    }

    public function testLimiteInicioTarde(): void
    {
        $logger = $this->createLoggerMock();
        $h = new HolaMonolog(12, $logger);
        $this->assertSame('Buenas tardes', $h->saludar());
    }

    public function testLimiteFinTarde(): void
    {
        $logger = $this->createLoggerMock();
        $h = new HolaMonolog(19, $logger);
        $this->assertSame('Buenas tardes', $h->saludar());
    }

    public function testLimiteInicioNoche(): void
    {
        $logger = $this->createLoggerMock();
        $h = new HolaMonolog(20, $logger);
        $this->assertSame('Buenas noches', $h->saludar());
    }

    public function testHoraNegativaLanzaInvalidArgumentException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new HolaMonolog(-1, $this->createLoggerMock());
    }

    public function testHoraMayorDe24LanzaInvalidArgumentException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new HolaMonolog(30, $this->createLoggerMock());
    }

    public function testAlmacenaMenosDeTresSaludos(): void
    {
        $logger = $this->createLoggerMock();
        $h = new HolaMonolog(8, $logger); // Buenos días
        $h->saludar(); // 1
        $h->setHora(15);
        $h->saludar(); // 2

        $ultimos = $h->getUltimosSaludos();
        $this->assertCount(2, $ultimos);
        $this->assertSame('Buenas tardes', $ultimos[0]); // más reciente
        $this->assertSame('Buenos días', $ultimos[1]);
    }

    public function testAlmacenaUltimosTresSaludos(): void
    {
        $logger = $this->createLoggerMock();
        $h = new HolaMonolog(8, $logger); // Buenos días
        $h->saludar(); // 1 -> Buenos días

        $h->setHora(15);
        $h->saludar(); // 2 -> Buenas tardes

        $h->setHora(22);
        $h->saludar(); // 3 -> Buenas noches

        $h->setHora(9);
        $h->saludar(); // 4 -> Buenos días (debe desplazar el más antiguo)

        $ultimos = $h->getUltimosSaludos();
        $this->assertCount(3, $ultimos);
        $this->assertSame('Buenos días', $ultimos[0]); // saludo más reciente (hora 9)
        $this->assertSame('Buenas noches', $ultimos[1]); // anterior (hora 22)
        $this->assertSame('Buenas tardes', $ultimos[2]); // anterior (hora 15)
    }

    /**
     * @dataProvider providerSaludos
     */
    public function testProviderUltimosSaludos(array $horas, array $esperado): void
    {
        $logger = $this->createLoggerMock();
        // Inicializamos con la primera hora válida del conjunto para evitar excepción
        $initial = $horas[0] ?? 8;
        $h = new HolaMonolog($initial, $logger);

        // Ejecutar saludos según las horas del proveedor
        foreach ($horas as $hora) {
            $h->setHora($hora);
            $h->saludar();
        }

        $this->assertSame($esperado, $h->getUltimosSaludos());
    }

    public function providerSaludos(): array
    {
        return [
            'Un saludo' => [
                // horas a usar en cada llamada a saludar()
                [9],
                // esperado: último saludo (más reciente primero)
                ['Buenos días']
            ],
            'Tres saludos' => [
                // 3 llamadas: mañana, tarde, noche
                [8, 15, 22],
                // esperado: más reciente primero
                ['Buenas noches', 'Buenas tardes', 'Buenos días']
            ],
            'Cuatro saludos' => [
                // 4 llamadas: se debe mantener solo los últimos 3
                [8, 15, 22, 9],
                // esperado: saludo más reciente (hora 9), luego 22 y 15
                ['Buenos días', 'Buenas noches', 'Buenas tardes']
            ],
        ];
    }
}
