<?php
namespace Kinetika\Domen\Interfejsi;

use Kinetika\Domen\Entiteti\Fizioterapeut;

/**
 * CRC: IFizioterapeutRepozitorijum
 * Odgovornost: ugovor za rad sa terapeutima
 * Saradnici: FizioterapeutRepozitorijum, FizioterapeutServis
 */
interface IFizioterapeutRepozitorijum
{
    public function nadjiPoId(int $id): ?Fizioterapeut;
    public function nadjiSve(?string $pretraga = null, ?int $aktivan = null): array;
    public function emailZauzet(string $email, ?int $idKorisnikaIzuzetak = null): bool;
    public function sacuvajNovog(array $podaci): int;
    public function izmeni(int $idFizioterapeuta, array $podaci): void;
    public function deaktiviraj(int $idFizioterapeuta): void;
    public function obrisi(int $idFizioterapeuta): void;
    public function imaPacijente(int $idFizioterapeuta): bool;
}
