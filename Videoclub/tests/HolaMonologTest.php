<?php

declare(strict_types=1);

namespace Dwes\Monologos\Tests;

use PHPUnit\Framework\TestCase;
use Dwes\Monologos\HolaMonolog;

/**
 * Pruebas unitarias para HolaMonolog.
 *
 * Verifican comportamiento de saludo y despedida según la hora,
 * validación de rangos de hora, y almacenamiento de los últimos saludos.
 */
class HolaMonologTest extends TestCase
{
    /**
     * Crea un mock de Logger compatible con PSR-3.
     *
     * Se configura para aceptar llamadas a info() y warning() ya que
     * la clase HolaMonolog registra eventos en esos niveles.
     *
     * @return \Psr\Log\LoggerInterface Mock del logger
     */
    private function createLoggerMock()
    {
        $logger = $this->createMock(\Psr\Log\LoggerInterface::class);
        // Permitimos llamadas a info() sin restricciones
        $logger->expects($this->any())->method('info');
        // Permitimos llamadas a warning() sin restricciones (compatibilidad)
        $logger->expects($this->any())->method('warning');
        return $logger;
    }

    /**
     * Comprueba saludo y despedida para la franja de mañana.
     *
     * Hora 8 debe devolver "Buenos días" y despedida "Hasta luego".
     */
    public function testSaludoManana(): void
    {
        $logger = $this->createLoggerMock();
        $h = new HolaMonolog(8, $logger); // mañana
        $this->assertSame('Buenos días', $h->saludar());
        $this->assertSame('Hasta luego', $h->despedir());
    }

    /**
     * Comprueba saludo y despedida para la franja de tarde.
     *
     * Hora 15 debe devolver "Buenas tardes" y despedida "Hasta la tarde".
     */
    public function testSaludoTarde(): void
    {
        $logger = $this->createLoggerMock();
        $h = new HolaMonolog(15, $logger); // tarde
        $this->assertSame('Buenas tardes', $h->saludar());
        $this->assertSame('Hasta la tarde', $h->despedir());
    }

    /**
     * Comprueba saludo y despedida para la franja de noche.
     *
     * Hora 22 debe devolver "Buenas noches" y despedida "Hasta mañana".
     */
    public function testSaludoNoche(): void
    {
        $logger = $this->createLoggerMock();
        $h = new HolaMonolog(22, $logger); // noche
        $this->assertSame('Buenas noches', $h->saludar());
        $this->assertSame('Hasta mañana', $h->despedir());
    }

    /**
     * Límite inferior de la franja de mañana.
     *
     * Hora 6 se considera inicio de "Buenos días".
     */
    public function testLimiteInicioManana(): void
    {
        $logger = $this->createLoggerMock();
        $h = new HolaMonolog(6, $logger);
        $this->assertSame('Buenos días', $h->saludar());
    }

    /**
     * Límite superior de la franja de mañana.
     *
     * Hora 11 sigue perteneciendo a "Buenos días".
     */
    public function testLimiteFinManana(): void
    {
        $logger = $this->createLoggerMock();
        $h = new HolaMonolog(11, $logger);
        $this->assertSame('Buenos días', $h->saludar());
    }

    /**
     * Límite inicio de la franja de tarde.
     *
     * Hora 12 debe devolver "Buenas tardes".
     */
    public function testLimiteInicioTarde(): void
    {
        $logger = $this->createLoggerMock();
        $h = new HolaMonolog(12, $logger);
        $this->assertSame('Buenas tardes', $h->saludar());
    }

    /**
     * Límite fin de la franja de tarde.
     *
     * Hora 19 sigue perteneciendo a "Buenas tardes".
     */
    public function testLimiteFinTarde(): void
    {
        $logger = $this->createLoggerMock();
        $h = new HolaMonolog(19, $logger);
        $this->assertSame('Buenas tardes', $h->saludar());
    }

    /**
     * Límite inicio de la franja de noche.
     *
     * Hora 20 debe devolver "Buenas noches".
     */
    public function testLimiteInicioNoche(): void
    {
        $logger = $this->createLoggerMock();
        $h = new HolaMonolog(20, $logger);
        $this->assertSame('Buenas noches', $h->saludar());
    }

    /**
     * Validación: hora negativa debe lanzar InvalidArgumentException.
     */
    public function testHoraNegativaLanzaInvalidArgumentException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new HolaMonolog(-1, $this->createLoggerMock());
    }

    /**
     * Validación: hora mayor de 24 debe lanzar InvalidArgumentException.
     */
    public function testHoraMayorDe24LanzaInvalidArgumentException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new HolaMonolog(30, $this->createLoggerMock());
    }

    /**
     * Comprueba que se almacenan menos de tres saludos correctamente.
     *
     * Se realizan dos saludos con horas distintas y se verifica el orden:
     * el más reciente debe aparecer en la posición 0 del array devuelto.
     */
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

    /**
     * Comprueba que solo se mantienen los últimos tres saludos y en orden correcto.
     *
     * Se realizan cuatro saludos; el más antiguo debe descartarse y los tres
     * más recientes deben aparecer en orden desde el más reciente al más antiguo.
     */
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
     * Prueba parametrizada que ejecuta secuencias de horas y compara
     * el resultado con el array esperado de últimos saludos.
     *
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

        // Comparar el array completo de últimos saludos con el esperado
        $this->assertSame($esperado, $h->getUltimosSaludos());
    }

    /**
     * Proveedor de datos para testProviderUltimosSaludos.
     *
     * Cada caso contiene un array de horas a aplicar y el array esperado
     * de últimos saludos (más reciente primero).
     */
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
