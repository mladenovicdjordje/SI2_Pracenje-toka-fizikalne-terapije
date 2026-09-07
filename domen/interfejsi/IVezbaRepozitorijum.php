<?php
namespace Kinetika\Domen\Interfejsi;

use Kinetika\Domen\Entiteti\Vezba;

/**
 * CRC: IVezbaRepozitorijum
 * Odgovornost: ugovor za katalog vezbi
 * Saradnici: VezbaRepozitorijum, VezbaServis
 */
interface IVezbaRepozitorijum
{
    public function nadjiPoId(int $id): ?Vezba;
    public function nadjiSve(?string $pretraga = null, ?string $grupa = null): array;
    public function grupe(): array;
    public function nazivZauzet(string $naziv, ?int $idIzuzetak = null): bool;
    public function sacuvaj(array $podaci): int;
    public function izmeni(int $idVezbe, array $podaci): void;
    public function obrisi(int $idVezbe): void;
    public function koristiSeUPlanu(int $idVezbe): bool;
}
