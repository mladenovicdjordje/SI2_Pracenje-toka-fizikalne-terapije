<?php
namespace Kinetika\Aplikacija;

/**
 * CRC: Ruter
 * Odgovornost: usmerava HTTP zahtev na odgovarajući kontroler
 * Saradnici: kontroleri, javno/index.php
 */
class Ruter
{
    private array $rute = [];

    public function dodaj(string $metod, string $putanja, array $akcija): void
    {
        $this->rute[strtoupper($metod) . ' ' . rtrim($putanja, '/') ?: '/'] = $akcija;
    }

    public function pokreni(string $metod, string $uri): void
    {
        $putanja = parse_url($uri, PHP_URL_PATH) ?: '/';
        if ($putanja !== '/') {
            $putanja = rtrim($putanja, '/');
        }
        $kljuc = strtoupper($metod) . ' ' . $putanja;

        if (!isset($this->rute[$kljuc])) {
            http_response_code(404);
            $korisnik = Sesija::korisnik();
            $poruka = 'Tražena stranica ne postoji.';
            require dirname(__DIR__) . '/prezentacija/stranice/nije_pronadjeno.php';
            return;
        }

        [$klasa, $metoda] = $this->rute[$kljuc];
        $kontroler = new $klasa();
        $kontroler->{$metoda}();
    }
}
