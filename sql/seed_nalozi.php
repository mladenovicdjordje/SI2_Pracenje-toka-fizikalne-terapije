<?php
/**
 * CRC: seed_nalozi
 * Odgovornost: puni bazu podacima iz sql/baza.sql
 * Saradnici: Konekcija
 *
 * Svi nalozi: 12345678
 * Admin: djordje@kinetika.rs
 */
declare(strict_types=1);

use Kinetika\Podaci\Konekcija;

function kinetika_popuni_naloge(): void
{
    $baza = Konekcija::uzmi();
    $putanja = __DIR__ . '/baza.sql';
    if (!is_file($putanja)) {
        throw new RuntimeException('Nedostaje sql/baza.sql.');
    }

    $baza->beginTransaction();
    try {
        $baza->exec('SET FOREIGN_KEY_CHECKS = 0');
        foreach (['seansa', 'plan_vezba', 'vezba', 'terapijski_plan', 'povreda', 'pacijent', 'fizioterapeut', 'korisnik'] as $t) {
            $baza->exec('DELETE FROM ' . $t);
        }

        $sql = file_get_contents($putanja);
        foreach (array_filter(array_map('trim', explode(';', $sql))) as $naredba) {
            if (stripos($naredba, 'INSERT INTO') === false) {
                continue;
            }
            $baza->exec($naredba);
        }

        $baza->exec('SET FOREIGN_KEY_CHECKS = 1');
        $hash = password_hash('12345678', PASSWORD_DEFAULT);
        $baza->prepare('UPDATE korisnik SET lozinka_hash = :h')->execute(['h' => $hash]);
        $baza->commit();
    } catch (Throwable $e) {
        if ($baza->inTransaction()) {
            $baza->rollBack();
        }
        throw $e;
    }
}