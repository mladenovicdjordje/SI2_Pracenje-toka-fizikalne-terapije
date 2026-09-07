<?php
$putanja = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$fajl = __DIR__ . $putanja;
if ($putanja !== '/' && is_file($fajl)) {
    return false;
}
require __DIR__ . '/index.php';