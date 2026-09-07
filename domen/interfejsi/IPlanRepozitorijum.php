<?php
namespace Kinetika\Domen\Interfejsi;

use Kinetika\Domen\Entiteti\TerapijskiPlan;

/**
 * CRC: IPlanRepozitorijum
 * Odgovornost: ugovor za planove, povrede i vezbe u planu
 * Saradnici: PlanRepozitorijum, PlanServis
 */
interface IPlanRepozitorijum
{
    public function lista(?string $pretraga = null, ?string $status = null, ?int $idPacijenta = null, ?int $idFizioterapeuta = null): array;
    public function nadjiPoId(int $id): ?TerapijskiPlan;
    public function deloviTela(): array;
    public function sacuvajNovi(array $podaci): int;
    public function izmeni(int $idPlana, array $podaci): void;
    public function promeniStatus(int $idPlana, string $status): void;
    public function obrisi(int $idPlana): void;
    public function imaSeanse(int $idPlana, bool $samoOtvorene = false): bool;
    public function vezbePlana(int $idPlana): array;
    public function dodajVezbu(array $podaci): int;
    public function ukloniVezbu(int $idPlanVezba): void;
}
