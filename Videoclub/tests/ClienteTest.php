<?php
namespace Dwes\ProyectoVideoclub\Tests;

use PHPUnit\Framework\TestCase;
use Dwes\ProyectoVideoclub\Cliente;
use Dwes\ProyectoVideoclub\CintaVideo;
use Dwes\ProyectoVideoclub\Dvd;
use Dwes\ProyectoVideoclub\Juego;
use Exception;

/**
 * Pruebas unitarias para la clase Cliente.
 *
 * Comprueba comportamientos relacionados con el alquiler de soportes:
 * - que se pueda alquilar dentro del cupo permitido,
 * - que no se pueda alquilar el mismo soporte dos veces,
 * - que no se pueda superar el cupo máximo,
 * - que los soportes añadidos tengan identificadores distintos cuando corresponde.
 */
class ClienteTest extends TestCase{

     /**
      * @dataProvider proveedorClientesConCupo
      *
      * Verifica que un cliente puede alquilar hasta su cupo máximo sin errores.
      * Recibe pares [cupo, numSoporte] desde el proveedor de datos.
      */
     public function testAlquilerDentroDelCupo(int $cupo, int $numSoporte){
        // Crear cliente con el cupo indicado
        $cliente = new Cliente('Antonio', 1, $cupo, 'antonio', '1234');

        // Alquilar la cantidad de soportes indicada por $numSoporte
        for($i = 1; $i<=$numSoporte; $i++){
            $soporte = new Dvd("Peli $i", $i, 10, "Español", "16:9");
            $cliente->alquilar($soporte);
        }

        // Comprobar que el array de alquileres contiene el número esperado de elementos
        $this->assertCount($numSoporte,$cliente->getAlquileres());
        // Comprobar que el contador interno coincide con el número de alquileres
        $this->assertEquals($numSoporte,$cliente->getNumSoportesAlquilados());
     }

     /**
      * Proveedor de datos para testAlquilerDentroDelCupo.
      *
      * Devuelve arrays [cupo, numSoporte] para probar varios escenarios.
      */
     public function proveedorClientesConCupo():array{
        return[
            [1,1],
            [2,2],
            [3,3],
        ];
     }

     /**
      * Comprueba que no se puede alquilar el mismo soporte dos veces por el mismo cliente.
      *
      * Se espera una excepción (genérica Exception en la firma del test).
      */
     public function testNoSePuedeAlquilarSoporteDuplicado(){
        $this->expectException(Exception::class);

        $cliente = new Cliente('Antonio', 1, 3, 'antonio', '1234');
        $dvd = new Dvd("Origen", 10, 15, "Español", "16:9");

        // Primer alquiler válido
        $cliente->alquilar($dvd);
        // Segundo intento con el mismo objeto debe lanzar excepción
        $cliente->alquilar($dvd); 
    }

    /**
     * Comprueba que no se puede superar el cupo máximo de alquileres del cliente.
     *
     * Se espera una excepción cuando se intenta alquilar más soportes que el cupo.
     */
    public function testNoSePuedeSuperarElCupo(){
        $this->expectException(Exception::class);

        $cliente = new Cliente('Antonio', 1, 2, 'antonio', '1234');

        // Alquilar hasta el límite
        $cliente->alquilar(new Dvd("Peli 1", 1, 10, "Español", "16:9"));
        $cliente->alquilar(new Dvd("Peli 2", 2, 10, "Español", "16:9"));
        // Intento que supera el cupo: debe lanzar excepción
        $cliente->alquilar(new Dvd("Peli 3", 3, 10, "Español", "16:9")); 
    }

    /**
     * Verifica que al alquilar dos soportes distintos sus identificadores sean diferentes.
     *
     * Se usan dos instancias de Juego con números distintos y se comprueba que
     * los números almacenados en los alquileres no coinciden.
     */
    public function testSoportesConIdsDistintos(){
        $cliente = new Cliente('Antonio', 1, 3, 'antonio', '1234');

        $s1 = new Juego("Juego 1", 1, 40, "PS5", 1, 2);
        $s2 = new Juego("Juego 2", 2, 50, "PS5", 1, 2);

        $cliente->alquilar($s1);
        $cliente->alquilar($s2);

        $alquileres = $cliente->getAlquileres();

        // Asegura que los números de los dos soportes alquilados son distintos
        $this->assertNotEquals(
            $alquileres[0]->getNumero(),
            $alquileres[1]->getNumero()
        );
    }

}
?>
