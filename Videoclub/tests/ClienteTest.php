<?php
namespace Dwes\ProyectoVideoclub\Tests;

use PHPUnit\Framework\TestCase;
use Dwes\ProyectoVideoclub\Cliente;
use Dwes\ProyectoVideoclub\CintaVideo;
use Dwes\ProyectoVideoclub\Dvd;
use Dwes\ProyectoVideoclub\Juego;
use Exception;

class ClienteTest extends TestCase{

     /**
     * @dataProvider proveedorClientesConCupo
     */

     public function testAlquilerDentroDelCupo(int $cupo, int $numSoporte){
        $cliente = new Cliente('Antonio', 1, $cupo, 'antonio', '1234');

        for($i = 1; $i<=$numSoporte; $i++){
            $soporte = new Dvd("Peli $i", $i, 10, "Español", "16:9");
            $cliente->alquilar($soporte);

        }

        $this->assertCount($numSoporte,$cliente->getAlquileres());
        $this->assertEquals($numSoporte,$cliente->getNumSoportesAlquilados());
     }

     public function proveedorClientesConCupo():array{
        return[
            [1,1],
            [2,2],
            [3,3],
        ];
     }


         public function testNoSePuedeAlquilarSoporteDuplicado(){
        $this->expectException(Exception::class);

        $cliente = new Cliente('Antonio', 1, 3, 'antonio', '1234');
        $dvd = new Dvd("Origen", 10, 15, "Español", "16:9");

        $cliente->alquilar($dvd);
        $cliente->alquilar($dvd); 
    }


        public function testNoSePuedeSuperarElCupo(){
        $this->expectException(Exception::class);

        $cliente = new Cliente('Antonio', 1, 2, 'antonio', '1234');

        $cliente->alquilar(new Dvd("Peli 1", 1, 10, "Español", "16:9"));
        $cliente->alquilar(new Dvd("Peli 2", 2, 10, "Español", "16:9"));
        $cliente->alquilar(new Dvd("Peli 3", 3, 10, "Español", "16:9")); 
    }

        public function testSoportesConIdsDistintos(){
        $cliente = new Cliente('Antonio', 1, 3, 'antonio', '1234');

        $s1 = new Juego("Juego 1", 1, 40, "PS5", 1, 2);
        $s2 = new Juego("Juego 2", 2, 50, "PS5", 1, 2);

        $cliente->alquilar($s1);
        $cliente->alquilar($s2);

        $alquileres = $cliente->getAlquileres();

        $this->assertNotEquals(
            $alquileres[0]->getNumero(),
            $alquileres[1]->getNumero()
        );
    }

    

}







?>