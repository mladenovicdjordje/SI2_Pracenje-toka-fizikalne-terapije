<?php
namespace Kinetika\Domen\Interfejsi;

use Kinetika\Domen\Entiteti\Pacijent;

/**
 * CRC: IPacijentRepozitorijum
 * Odgovornost: ugovor za rad sa pacijentima
 * Saradnici: PacijentRepozitorijum, PacijentServis
 */
interface IPacijentRepozitorijum
{
    public function nadjiPoId(int $id): ?Pacijent;
    public function nadjiSve(?string $pretraga = null, ?int $idFizioterapeuta = null, ?int $aktivan = null): array;
    public function emailZauzet(string $email, ?int $idKorisnikaIzuzetak = null): bool;
    public function sacuvajNovog(array $podaci): int;
    public function izmeni(int $idPacijenta, array $podaci): void;
    public function deaktiviraj(int $idPacijenta): void;
    public function obrisi(int $idPacijenta): void;
    public function imaIstoriju(int $idPacijenta): bool;
}
