<?php
namespace Kinetika\Aplikacija;

/**
 * CRC: Sesija
 * Odgovornost: otvaranje PHP sesije i čuvanje prijavljenog korisnika
 * Saradnici: KontrolerPrijave, ProveraPristupa
 */
class Sesija
{
    public static function otvori(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public static function prijavi(array $korisnik): void
    {
        self::otvori();
        session_regenerate_id(true);
        $_SESSION['korisnik'] = $korisnik;
    }

    public static function odjavi(): void
    {
        self::otvori();
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }

    public static function korisnik(): ?array
    {
        self::otvori();
        return $_SESSION['korisnik'] ?? null;
    }

    public static function jePrijavljen(): bool
    {
        $k = self::korisnik();
        return is_array($k) && !empty($k['id_korisnika']);
    }

    public static function staviPoruku(string $tekst, string $tip = 'ok'): void
    {
        self::otvori();
        $_SESSION['poruka'] = ['tekst' => $tekst, 'tip' => $tip];
    }

    public static function uzmiPoruku(): ?array
    {
        self::otvori();
        $p = $_SESSION['poruka'] ?? null;
        unset($_SESSION['poruka']);
        return $p;
    }
}
