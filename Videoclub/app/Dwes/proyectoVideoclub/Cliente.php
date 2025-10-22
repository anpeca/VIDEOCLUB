<?php
namespace Dwes\ProyectoVideoclub;

use Dwes\ProyectoVideoclub\Util\SoporteYaAlquiladoException;
use Dwes\ProyectoVideoclub\Util\CupoSuperadoException;
use Dwes\ProyectoVideoclub\Util\SoporteNoEncontradoException;   
/*
<!-- Crear la clase Cliente. El constructor recibirá el nombre, numero y maxAlquilerConcurrente, este último pudiendo ser opcional y tomando como valor por defecto 3. Tras ello, añade getter/setter únicamente a numero, y un getter a numSoportesAlquilados (este campo va a almacenar un contador del total de alquileres que ha realizado). El array de soportes alquilados contedrá clases que hereden de Soporte. Finalmente, añade el método muestraResumen que muestre el nombre y la cantidad de alquileres (tamaño del array soportesAlquilados). 
 
Dentro de Cliente, añade las siguiente operaciones:

tieneAlquilado(Soporte $s): bool → Recorre el array de soportes y comprueba si está el soporte
alquilar(Soporte $s): bool -→ Debe comprobar si el soporte está alquilado y si no ha superado el cupo de alquileres. Al alquilar, incrementará el numSoportesAlquilados y almacenará el soporte en el array. Para cada caso debe mostrar un mensaje informando de lo ocurrido.-->
*/
class Cliente {

    private string $nombre;
    private int $numero;
    private int $maxAlquilerConcurrente;
    private int $numSoportesAlquilados = 0;
    private array $soportesAlquilados = [];

    public function __construct(string $nombre, int $numero, int $maxAlquilerConcurrente = 3) {
        $this->nombre = $nombre;
        $this->numero = $numero;
        $this->maxAlquilerConcurrente = $maxAlquilerConcurrente;
    }

    public function getNumero(): int {
        return $this->numero;
    }

    public function setNumero(int $numero): void {
        $this->numero = $numero;
    }

    public function getMaxAlquilerConcurrente(): int {
        return $this->maxAlquilerConcurrente;
    }

    public function getNumSoportesAlquilados(): int {
        return $this->numSoportesAlquilados;
    }

    public function muestraResumen(): string {
        return "Cliente: " . $this->nombre . " - Total alquileres: " . count($this->soportesAlquilados);
    }

    public function tieneAlquilado(Soporte $s): bool {
        
        foreach ($this->soportesAlquilados as $soporte) {
            if ($soporte === $s) {
                return true;
            }
        }
        return false;
    }

    public function alquilar(Soporte $s): Cliente {
        
        $s->alquilado = true;

        if ($this->tieneAlquilado($s)) {
            throw new SoporteYaAlquiladoException("El soporte ya está alquilado por este cliente.");
        }

        if (count($this->soportesAlquilados) >= $this->maxAlquilerConcurrente) {
            throw new CupoSuperadoException("Ha superado el cupo de alquileres.");
        }

        $this->soportesAlquilados[] = $s;
        $this->numSoportesAlquilados++;
        return $this;
    }

 
    public function devolver(int $numSoporte): Cliente {

        foreach ($this->soportesAlquilados as $key => $soporte) {

            if ($soporte->getNumero() === $numSoporte) {

                $soporte->alquilado = false; 
                unset($this->soportesAlquilados[$key]);
                $this->soportesAlquilados = array_values($this->soportesAlquilados);
                return $this;
            }
        }
        throw new SoporteNoEncontradoException("El cliente no tenía alquilado este soporte.");
    }




    public function listaAlquileres(): void{

        echo "Hay ".$this->getNumSoportesAlquilados(). " Soportes alquilados:<br>";

        for($i = 0; $i < sizeof($this -> soportesAlquilados); $i++){

            echo $this->soportesAlquilados[$i]."\n";
        }
    }
}
?>