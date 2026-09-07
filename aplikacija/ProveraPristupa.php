<?php
namespace Kinetika\Aplikacija;

/**
 * CRC: ProveraPristupa
 * Odgovornost: na svakoj zaštićenoj strani proverava otvorenu sesiju i postojećeg korisnika
 * Saradnici: Sesija, Ruter, kontroleri
 */
class ProveraPristupa
{
    public static function zahtevajPrijavu(): array
    {
        if (!Sesija::jePrijavljen()) {
            header('Location: /prijava');
            exit;
        }
        return Sesija::korisnik();
    }

    public static function zahtevajUlogu(array $dozvoljene): array
    {
        $korisnik = self::zahtevajPrijavu();
        if (!in_array($korisnik['uloga'], $dozvoljene, true)) {
            http_response_code(403);
            $poruka = 'Nemate pravo da otvorite ovu stranicu.';
            require dirname(__DIR__) . '/prezentacija/stranice/zabranjeno.php';
            exit;
        }
        return $korisnik;
    }
}
