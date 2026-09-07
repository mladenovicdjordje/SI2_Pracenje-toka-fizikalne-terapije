<?php
declare(strict_types=1);

$koren = dirname(__DIR__);
require $koren . '/aplikacija/UcitavacKlasa.php';
(new UcitavacKlasa($koren))->registruj();
require $koren . '/sql/seed_nalozi.php';

try {
    kinetika_popuni_naloge();
    $baza = \Kinetika\Podaci\Konekcija::uzmi();
    $redovi = $baza->query('SELECT email, uloga FROM korisnik ORDER BY id_korisnika')->fetchAll();
    echo "Nalozi u bazi:\n";
    foreach ($redovi as $red) {
        echo ' - ' . $red['uloga'] . '  ' . $red['email'] . PHP_EOL;
    }
    if (!$redovi) {
        echo "Tabela je i dalje prazna.\n";
        exit(1);
    }
} catch (Throwable $e) {
    fwrite(STDERR, 'SEED GREŠKA: ' . $e->getMessage() . PHP_EOL);
    exit(1);
}
