<?php
declare(strict_types=1);

use Kinetika\Aplikacija\Ruter;
use Kinetika\Aplikacija\Sesija;
use Kinetika\Aplikacija\Kontroleri\KontrolerPrijave;
use Kinetika\Aplikacija\Kontroleri\KontrolerPocetne;
use Kinetika\Aplikacija\Kontroleri\KontrolerFizioterapeuta;
use Kinetika\Aplikacija\Kontroleri\KontrolerPacijenta;
use Kinetika\Aplikacija\Kontroleri\KontrolerVezbi;
use Kinetika\Aplikacija\Kontroleri\KontrolerPlanova;
use Kinetika\Aplikacija\Kontroleri\KontrolerKalendara;
use Kinetika\Aplikacija\Kontroleri\KontrolerIzvestaja;

$koren = dirname(__DIR__);

require $koren . '/aplikacija/UcitavacKlasa.php';
(new UcitavacKlasa($koren))->registruj();

Sesija::otvori();

require $koren . '/sql/seed_nalozi.php';
try {
    kinetika_popuni_naloge();
} catch (Throwable $e) {
    error_log('Kinetika seed: ' . $e->getMessage());
}

$ruter = new Ruter();
$ruter->dodaj('GET', '/', [KontrolerPrijave::class, 'preusmeri']);
$ruter->dodaj('GET', '/prijava', [KontrolerPrijave::class, 'prikazi']);
$ruter->dodaj('POST', '/prijava', [KontrolerPrijave::class, 'obrada']);
$ruter->dodaj('GET', '/odjava', [KontrolerPrijave::class, 'odjava']);
$ruter->dodaj('GET', '/pocetna', [KontrolerPocetne::class, 'prikazi']);

$ruter->dodaj('GET', '/fizioterapeuti', [KontrolerFizioterapeuta::class, 'lista']);
$ruter->dodaj('GET', '/fizioterapeuti/novi', [KontrolerFizioterapeuta::class, 'forma']);
$ruter->dodaj('GET', '/fizioterapeuti/izmena', [KontrolerFizioterapeuta::class, 'forma']);
$ruter->dodaj('POST', '/fizioterapeuti/sacuvaj', [KontrolerFizioterapeuta::class, 'sacuvaj']);
$ruter->dodaj('POST', '/fizioterapeuti/obrisi', [KontrolerFizioterapeuta::class, 'obrisi']);

$ruter->dodaj('GET', '/pacijenti', [KontrolerPacijenta::class, 'lista']);
$ruter->dodaj('GET', '/pacijenti/novi', [KontrolerPacijenta::class, 'forma']);
$ruter->dodaj('GET', '/pacijenti/izmena', [KontrolerPacijenta::class, 'forma']);
$ruter->dodaj('POST', '/pacijenti/sacuvaj', [KontrolerPacijenta::class, 'sacuvaj']);
$ruter->dodaj('POST', '/pacijenti/obrisi', [KontrolerPacijenta::class, 'obrisi']);

$ruter->dodaj('GET', '/katalog-vezbi', [KontrolerVezbi::class, 'lista']);
$ruter->dodaj('GET', '/katalog-vezbi/nova', [KontrolerVezbi::class, 'forma']);
$ruter->dodaj('GET', '/katalog-vezbi/izmena', [KontrolerVezbi::class, 'forma']);
$ruter->dodaj('POST', '/katalog-vezbi/sacuvaj', [KontrolerVezbi::class, 'sacuvaj']);
$ruter->dodaj('POST', '/katalog-vezbi/obrisi', [KontrolerVezbi::class, 'obrisi']);

$ruter->dodaj('GET', '/planovi', [KontrolerPlanova::class, 'lista']);
$ruter->dodaj('GET', '/planovi/novi', [KontrolerPlanova::class, 'forma']);
$ruter->dodaj('GET', '/planovi/izmena', [KontrolerPlanova::class, 'forma']);
$ruter->dodaj('POST', '/planovi/sacuvaj', [KontrolerPlanova::class, 'sacuvaj']);
$ruter->dodaj('POST', '/planovi/obrisi', [KontrolerPlanova::class, 'obrisi']);
$ruter->dodaj('GET', '/planovi/vezbe', [KontrolerPlanova::class, 'vezbe']);
$ruter->dodaj('POST', '/planovi/vezbe/dodaj', [KontrolerPlanova::class, 'dodajVezbu']);
$ruter->dodaj('POST', '/planovi/vezbe/ukloni', [KontrolerPlanova::class, 'ukloniVezbu']);
$ruter->dodaj('GET', '/plan-vezbanja', [KontrolerPlanova::class, 'planVezbanja']);
$ruter->dodaj('GET', '/kalendar', [KontrolerKalendara::class, 'prikazi']);
$ruter->dodaj('GET', '/kalendar/zakazi', [KontrolerKalendara::class, 'forma']);
$ruter->dodaj('POST', '/kalendar/sacuvaj', [KontrolerKalendara::class, 'sacuvaj']);
$ruter->dodaj('POST', '/kalendar/potvrdi', [KontrolerKalendara::class, 'potvrdi']);
$ruter->dodaj('POST', '/kalendar/odbij', [KontrolerKalendara::class, 'odbij']);
$ruter->dodaj('POST', '/kalendar/otkazi', [KontrolerKalendara::class, 'otkazi']);
$ruter->dodaj('POST', '/kalendar/odrzana', [KontrolerKalendara::class, 'odrzana']);
$ruter->dodaj('GET', '/izvestaji', [KontrolerIzvestaja::class, 'prikazi']);
$ruter->dodaj('GET', '/izvestaji/stampa', [KontrolerIzvestaja::class, 'stampa']);

$ruter->pokreni($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');
