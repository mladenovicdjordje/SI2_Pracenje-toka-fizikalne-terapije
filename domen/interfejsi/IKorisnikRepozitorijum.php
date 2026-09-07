<?php
namespace Kinetika\Domen\Interfejsi;

use Kinetika\Domen\Entiteti\Korisnik;

/**
 * CRC: IKorisnikRepozitorijum
 * Odgovornost: ugovor za čitanje korisničkih naloga
 * Saradnici: KorisnikRepozitorijum, KontrolerPrijave
 */
interface IKorisnikRepozitorijum
{
    public function nadjiPoEmailu(string $email): ?Korisnik;

    public function nadjiPoId(int $idKorisnika): ?Korisnik;

    public function emailZauzet(string $email, ?int $izuzmiId = null): bool;

    public function upisi(string $email, string $lozinkaHash, string $uloga): int;

    public function izmeniNalog(int $idKorisnika, string $email, ?string $lozinkaHash, bool $aktivan): void;

    public function deaktiviraj(int $idKorisnika): void;

    public function obrisi(int $idKorisnika): void;
}
