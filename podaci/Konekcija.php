<?php
namespace Kinetika\Podaci;

use PDO;
use PDOException;
use RuntimeException;

/**
 * CRC: Konekcija
 * Odgovornost: jedinstveni PDO pristup bazi; čita string iz eksternog ini fajla
 * Saradnici: svi repozitorijumi
 */
class Konekcija
{
    private static ?PDO $pdo = null;

    public static function uzmi(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        $putanja = dirname(__DIR__) . '/konfiguracija/konekcija.ini';
        if (!is_file($putanja)) {
            throw new RuntimeException('Nedostaje konfiguracija/konekcija.ini.');
        }

        $ini = parse_ini_file($putanja, true);
        if ($ini === false || !isset($ini['baza'])) {
            throw new RuntimeException('Neispravan format konekcija.ini.');
        }

        $b = $ini['baza'];
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            $b['host'],
            $b['port'] ?? '3306',
            $b['naziv'],
            $b['charset'] ?? 'utf8mb4'
        );

        try {
            self::$pdo = new PDO($dsn, $b['korisnik'], $b['lozinka'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            throw new RuntimeException('Neuspešna konekcija na bazu: ' . $e->getMessage());
        }

        return self::$pdo;
    }

    public function zapocniTransakciju(): void
    {
        self::uzmi()->beginTransaction();
    }

    public function potvrdi(): void
    {
        $pdo = self::uzmi();
        if ($pdo->inTransaction()) {
            $pdo->commit();
        }
    }

    public function ponisti(): void
    {
        $pdo = self::uzmi();
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
    }
}
