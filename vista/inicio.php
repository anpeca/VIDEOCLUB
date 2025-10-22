<?php

    include "../modelo/Soporte.php";

    $soporte1 = new Soporte("Tenet", 22, 7.5); 
    echo "<strong>" . $soporte1->getTitulo() . "</strong>"; 
    echo "<br>Precio: " . $soporte1->getPrecio() . " euros"; 
    echo "<br>Precio IVA incluido: " . number_format($soporte1->getPrecioConIVA(), 2) . " euros";
    echo "<br>Número de soporte: " . $soporte1->getNumero();
    echo "<br>" . $soporte1->muestraResumen();
?>

<?php
    include "../modelo/CintaVideo.php";

    echo "<p>";
    $miCinta = new CintaVideo("Los cazafantasmas", 23, 3.5, 107);
    echo "<strong>" . $miCinta->getTitulo() . "</strong>"; 
    echo "<br>Precio: " . $miCinta->getPrecio() . " euros"; 
    echo "<br>Precio IVA incluido: " . number_format($miCinta->getPrecioConIVA(), 2) . " euros";
    echo "<br>" . $miCinta->muestraResumen();
?>

<?php
    include "../modelo/Dvd.php";

    echo "<p>";
    $miDvd = new Dvd("Origen", 24, 15, "es,en,fr", "16:9"); 
    echo "<strong>" . $miDvd->getTitulo() . "</strong>";
    echo "<br>Precio: " . $miDvd->getPrecio() . " euros"; 
    echo "<br>Precio IVA incluido: " . number_format($miDvd->getPrecioConIva(), 2) . " euros";
    echo "<br>" . $miDvd->muestraResumen();
?>

<?php
    include "../modelo/Juego.php";

    echo"<p>";
    $miJuego = new Juego("The Last of Us Part II", 26, 49.99, "PS4", 1, 1); 
    echo "<strong>" . $miJuego->getTitulo() . "</strong>"; 
    echo "<br>Precio: " . $miJuego->getPrecio() . " euros"; 
    echo "<br>Precio IVA incluido: " . number_format($miJuego->getPrecioConIva(), 2) . " euros";
    echo "<br>" . $miJuego->muestraResumen();
?>


