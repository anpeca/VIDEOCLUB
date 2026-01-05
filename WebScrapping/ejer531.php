<?php
$url = 'http://www.seleccionbaloncesto.es/Inicio.aspx?tabid=4';

// Configuración
ini_set('user_agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');

$html = @file_get_contents($url);
if ($html === false) {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
        CURLOPT_TIMEOUT => 20,
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    $html = curl_exec($ch);
    curl_close($ch);
}

if (!$html) {
    fwrite(STDERR, "Error: No se pudo descargar la página\n");
    exit(1);
}

$dom = new DOMDocument();
libxml_use_internal_errors(true);
$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
libxml_clear_errors();

$xpath = new DOMXPath($dom);

// DEPURACIÓN: Ver qué tablas encontramos
echo "=== BUSCANDO TABLAS ===\n";
$tables = $xpath->query("//table");
echo "Total de tablas encontradas: " . $tables->length . "\n";

// Buscar la tabla específica de jugadores
$jugadoresTable = null;

foreach ($tables as $i => $table) {
    // Ver el contenido de cada tabla
    $tableHTML = $dom->saveHTML($table);
    
    // Buscar si contiene datos de jugadores
    if (strpos($tableHTML, 'Great Osobor') !== false || 
        strpos($tableHTML, 'Álvaro Cárdenas') !== false) {
        $jugadoresTable = $table;
        echo "¡Tabla de jugadores encontrada en índice $i!\n";
        break;
    }
}

if (!$jugadoresTable) {
    echo "No se encontró la tabla de jugadores\n";
    
    // Intentar otro método: buscar por el título
    $titulos = $xpath->query("//h3[contains(@class, 'cab')]");
    foreach ($titulos as $titulo) {
        $texto = trim($titulo->textContent);
        echo "Título encontrado: $texto\n";
        if (strpos($texto, 'VENTANA NOVIEMBRE') !== false) {
            echo "Encontrado título de jugadores\n";
            // Buscar la siguiente tabla
            $tablasDespues = $xpath->query("./following::table", $titulo);
            if ($tablasDespues->length > 0) {
                $jugadoresTable = $tablasDespues->item(0);
                break;
            }
        }
    }
}

if (!$jugadoresTable) {
    echo "ERROR: No se pudo encontrar la tabla de jugadores\n";
    exit(1);
}

// Ahora extraemos las filas de la tabla
$rows = $xpath->query(".//tr", $jugadoresTable);
echo "Filas en la tabla de jugadores: " . $rows->length . "\n";

// Funciones auxiliares
function toCentimeters(string $text): ?int {
    $t = trim(mb_strtolower($text));
    $t = str_replace(['cm','centímetros','centimetros','metros','m',' '], ['','','','','',''], $t);
    
    // 1,96 o 1.96 -> 196
    if (preg_match('/^(\d)[\.,](\d{2})$/', $t, $m)) {
        return (int) round(((float)($m[1] . '.' . $m[2])) * 100);
    }
    
    // 196 o 196. -> 196
    if (preg_match('/\b(\d{3})\b/', $t, $m)) {
        $val = (int)$m[1];
        if ($val >= 140 && $val <= 230) return $val;
    }
    
    return null;
}

function extractAge(string $fecha): ?int {
    $now = new DateTime();
    $fecha = trim($fecha);
    
    // Solo número (edad directa)
    if (preg_match('/^\s*(\d{1,2})\s*$/', $fecha, $m)) {
        $age = (int)$m[1];
        if ($age >= 16 && $age <= 45) return $age;
    }
    
    // dd/mm/yyyy o dd-mm-yyyy
    if (preg_match('/(\d{2})[\/\-](\d{2})[\/\-](\d{4})/', $fecha, $d)) {
        $birth = DateTime::createFromFormat('d-m-Y', "{$d[1]}-{$d[2]}-{$d[3]}") 
               ?: DateTime::createFromFormat('d/m/Y', "{$d[1]}/{$d[2]}/{$d[3]}");
        if ($birth) return $now->diff($birth)->y;
    }
    
    // solo año
    if (preg_match('/\b(19|20)\d{2}\b/', $fecha, $y)) {
        $birth = DateTime::createFromFormat('Y-m-d', $y[0] . '-01-01');
        if ($birth) return $now->diff($birth)->y;
    }
    
    return null;
}

$alturas = [];
$edades = [];
$contador = 0;

echo "\n=== PROCESANDO JUGADORES ===\n";

foreach ($rows as $r) {
    // Verificar que $r es un DOMElement
    if (!($r instanceof DOMElement)) {
        continue;
    }
    
    // Usar XPath para obtener las celdas de esta fila
    $cols = $xpath->query(".//td", $r);
    
    if ($cols->length < 3) {
        // Podría ser la fila de encabezado
        $ths = $xpath->query(".//th", $r);
        if ($ths->length > 0) {
            echo "Encontrada fila de encabezado, saltando...\n";
        }
        continue;
    }
    
    // En esta tabla específica, las columnas son:
    // 0: Nombre, 1: Altura, 2: Edad, 3: Club, 4: Puesto
    $nombre = trim($cols->item(0)->textContent);
    $alturaText = trim($cols->item(1)->textContent);
    $edadText = trim($cols->item(2)->textContent);
    
    // Saltar si es encabezado o fila vacía
    if (empty($nombre) || strpos($nombre, 'Nombre') !== false) {
        continue;
    }
    
    echo "Jugador $contador: $nombre\n";
    echo "  Altura: $alturaText\n";
    echo "  Edad: $edadText\n";
    
    $alt = toCentimeters($alturaText);
    if ($alt !== null) {
        $alturas[] = $alt;
        echo "  Altura convertida: $alt cm\n";
    } else {
        echo "  Altura no válida: $alturaText\n";
    }
    
    $age = extractAge($edadText);
    if ($age !== null) {
        $edades[] = $age;
        echo "  Edad convertida: $age años\n";
    } else {
        echo "  Edad no válida: $edadText\n";
    }
    
    $contador++;
    echo "\n";
}

// Resultados
echo "\n=== RESULTADOS FINALES ===\n";
echo "Jugadores procesados: $contador\n";
echo "Alturas válidas: " . count($alturas) . "\n";
echo "Edades válidas: " . count($edades) . "\n";

if (count($alturas) > 0) {
    $avgAlt = array_sum($alturas) / count($alturas);
    echo "Altura media: " . round($avgAlt, 2) . " cm\n";
} else {
    echo "Altura media: No encontrada\n";
}

if (count($edades) > 0) {
    $avgAge = array_sum($edades) / count($edades);
    echo "Edad media: " . round($avgAge, 2) . " años\n";
} else {
    echo "Edad media: No encontrada\n";
}

// Mostrar lista para verificación
if (count($alturas) > 0) {
    echo "\nAlturas individuales (cm):\n";
    foreach ($alturas as $i => $alt) {
        echo "  Jugador " . ($i+1) . ": $alt cm\n";
    }
}

if (count($edades) > 0) {
    echo "\nEdades individuales (años):\n";
    foreach ($edades as $i => $edad) {
        echo "  Jugador " . ($i+1) . ": $edad años\n";
    }
}