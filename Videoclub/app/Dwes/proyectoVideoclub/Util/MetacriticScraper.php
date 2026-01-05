<?php
namespace Dwes\ProyectoVideoclub\Util;

/**
 * Utilidad simple para obtener la puntuación (metascore) de una página de Metacritic.
 * Devuelve float (ej. 78.0) o null si no se encuentra.
 */
class MetacriticScraper
{
    /**
     * Obtiene la puntuación desde la URL de Metacritic.
     *
     * @param string $url URL completa de Metacritic
     * @return float|null
     */
    public static function obtenerPuntuacion(string $url): ?float
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        // Descargar HTML (cURL con user agent)
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; Scraper/1.0)');
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $html = curl_exec($ch);
        curl_close($ch);

        if (!$html) return null;

        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        if (!@$dom->loadHTML($html)) {
            libxml_clear_errors();
            return null;
        }
        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);

        // Selector robusto: Metacritic suele mostrar el metascore en un elemento con clase 'metascore_w'
        // Buscamos el primer elemento que contenga un número entero entre 0 y 100.
        $nodes = $xpath->query("//*[contains(@class,'metascore') or contains(@class,'metascore_w') or contains(@class,'score')]");
        foreach ($nodes as $n) {
            $text = trim($n->textContent);
            if (preg_match('/\b([0-9]{1,3})\b/', $text, $m)) {
                $val = (float)$m[1];
                if ($val >= 0 && $val <= 100) {
                    return $val;
                }
            }
        }

        // Fallback: buscar cualquier número 0-100 en la página
        if (preg_match('/\b([0-9]{1,3})\b/', strip_tags($html), $m)) {
            $val = (float)$m[1];
            if ($val >= 0 && $val <= 100) return $val;
        }

        return null;
    }
}
