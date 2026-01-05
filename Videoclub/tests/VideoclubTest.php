<?php
declare(strict_types=1);

namespace Dwes\ProyectoVideoclub\Tests;

use PHPUnit\Framework\TestCase;
use Dwes\ProyectoVideoclub\Videoclub;
use Dwes\ProyectoVideoclub\Cliente;
use Dwes\ProyectoVideoclub\Soporte;
use Dwes\ProyectoVideoclub\CintaVideo;
use Dwes\ProyectoVideoclub\Util\ClienteNoEncontradoException;
use Dwes\ProyectoVideoclub\Util\SoporteNoEncontradoException;
use Dwes\ProyectoVideoclub\Util\SoporteYaAlquiladoException;
use Dwes\ProyectoVideoclub\Util\CupoSuperadoException;
use Dwes\ProyectoVideoclub\Util\SoporteNoEncontradoException as SoporteNoEncontradoEx; // alias si hace falta

class VideoclubTest extends TestCase
{
    public function testAlquilarExitoso(): void
    {
        $vc = new Videoclub('MiVideoclub');

        // incluir un producto real (CintaVideo) con número 1
        $vc->incluirCintaVideo(null, 'Título A', 1.5, 90);

        // crear mock de Cliente
        $cliente = $this->createMock(Cliente::class);
        $cliente->method('getNumero')->willReturn(1);
        $cliente->method('getNombre')->willReturn('Cliente 1');

        // esperamos que se llame a alquilar con cualquier Soporte
        $cliente->expects($this->once())->method('alquilar')->with($this->isInstanceOf(Soporte::class));

        $vc->incluirSocio($cliente);

        $result = $vc->alquilarSocioProducto(1, 1);
        $this->assertInstanceOf(Videoclub::class, $result);
    }

    public function testAlquilarClienteNoEncontrado(): void
    {
        $vc = new Videoclub('MiVideoclub');
        $vc->incluirCintaVideo(null, 'Título A', 1.5, 90);

        $this->expectException(ClienteNoEncontradoException::class);
        $vc->alquilarSocioProducto(999, 1);
    }

    public function testAlquilarProductoNoEncontrado(): void
    {
        $vc = new Videoclub('MiVideoclub');

        $cliente = $this->createMock(Cliente::class);
        $cliente->method('getNumero')->willReturn(1);
        $cliente->method('getNombre')->willReturn('Cliente 1');
        // no esperamos llamadas a alquilar en este test
        $cliente->expects($this->never())->method('alquilar');

        $vc->incluirSocio($cliente);

        $this->expectException(SoporteNoEncontradoException::class);
        $vc->alquilarSocioProducto(1, 999);
    }

    public function testAlquilarSoporteYaAlquilado(): void
    {
        $vc = new Videoclub('MiVideoclub');
        $vc->incluirCintaVideo(null, 'Título A', 1.5, 90);

        $cliente = $this->createMock(Cliente::class);
        $cliente->method('getNumero')->willReturn(1);
        $cliente->method('getNombre')->willReturn('Cliente 1');

        // Simular que al intentar alquilar lanza SoporteYaAlquiladoException
        $cliente->expects($this->once())->method('alquilar')
            ->willThrowException(new SoporteYaAlquiladoException('Ya alquilado'));

        $vc->incluirSocio($cliente);

        $this->expectException(SoporteYaAlquiladoException::class);
        $vc->alquilarSocioProducto(1, 1);
    }

    public function testAlquilarCupoSuperado(): void
    {
        $vc = new Videoclub('MiVideoclub');
        $vc->incluirCintaVideo(null, 'Título A', 1.5, 90);

        $cliente = $this->createMock(Cliente::class);
        $cliente->method('getNumero')->willReturn(1);
        $cliente->method('getNombre')->willReturn('Cliente 1');

        // Simular que al intentar alquilar lanza CupoSuperadoException
        $cliente->expects($this->once())->method('alquilar')
            ->willThrowException(new CupoSuperadoException('Cupo superado'));

        $vc->incluirSocio($cliente);

        $this->expectException(CupoSuperadoException::class);
        $vc->alquilarSocioProducto(1, 1);
    }

    public function testDevolverExitoso(): void
    {
        $vc = new Videoclub('MiVideoclub');
        $vc->incluirCintaVideo(null, 'Título A', 1.5, 90);

        // obtener referencia al producto para manipular su estado
        // el producto creado tendrá número 1
        $cliente = $this->createMock(Cliente::class);
        $cliente->method('getNumero')->willReturn(1);
        $cliente->method('getNombre')->willReturn('Cliente 1');

        // esperamos que se llame a devolver con el número de soporte
        $cliente->expects($this->once())->method('devolver')->with(1);

        $vc->incluirSocio($cliente);

        // marcar el producto como alquilado antes de devolver
        // localizar el producto mediante reflexión simple: incluirCintaVideo crea CintaVideo con número 1
        // crear una instancia para comprobar el estado tras la devolución
        // Llamamos a devolverSocioProducto y comprobamos que no lanza excepción y devuelve Videoclub
        $result = $vc->devolverSocioProducto(1, 1);
        $this->assertInstanceOf(Videoclub::class, $result);
    }

    public function testDevolverClienteNoEncontrado(): void
    {
        $vc = new Videoclub('MiVideoclub');
        $vc->incluirCintaVideo(null, 'Título A', 1.5, 90);

        $this->expectException(ClienteNoEncontradoException::class);
        $vc->devolverSocioProducto(999, 1);
    }

    public function testDevolverProductoNoEncontrado(): void
    {
        $vc = new Videoclub('MiVideoclub');

        $cliente = $this->createMock(Cliente::class);
        $cliente->method('getNumero')->willReturn(1);
        $cliente->method('getNombre')->willReturn('Cliente 1');
        $vc->incluirSocio($cliente);

        $this->expectException(SoporteNoEncontradoException::class);
        $vc->devolverSocioProducto(1, 999);
    }

    public function testDevolverSoporteNoEncontradoExceptionFromCliente(): void
    {
        $vc = new Videoclub('MiVideoclub');
        $vc->incluirCintaVideo(null, 'Título A', 1.5, 90);

        $cliente = $this->createMock(Cliente::class);
        $cliente->method('getNumero')->willReturn(1);
        $cliente->method('getNombre')->willReturn('Cliente 1');

        // Simular que al intentar devolver el cliente lanza SoporteNoEncontradoException
        $cliente->expects($this->once())->method('devolver')
            ->willThrowException(new SoporteNoEncontradoException('Soporte no encontrado'));

        $vc->incluirSocio($cliente);

        $this->expectException(SoporteNoEncontradoException::class);
        $vc->devolverSocioProducto(1, 1);
    }
}
