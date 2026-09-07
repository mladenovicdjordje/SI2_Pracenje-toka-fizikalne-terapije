<?php
namespace Kinetika\Podaci\Repozitorijumi;

use Kinetika\Domen\Entiteti\Povreda;
use PDO;

/**
 * CRC: PovredaRepozitorijum
 * Odgovornost: SQL pristup tabeli povreda
 * Saradnici: BazniRepozitorijum, PlanServis
 */
class PovredaRepozitorijum extends BazniRepozitorijum
{
    public function nadjiPoId(int $id): ?Povreda
    {
        $upit = $this->baza->prepare(
            'SELECT p.*, d.naziv AS naziv_dela
             FROM povreda p
             JOIN deo_tela d ON d.id_dela_tela = p.id_dela_tela
             WHERE p.id_povrede = :id LIMIT 1'
        );
        $upit->execute(['id' => $id]);
        $red = $upit->fetch(PDO::FETCH_ASSOC);
        return $red ? $this->izReda($red) : null;
    }

    public function upisi(array $podaci): int
    {
        $this->baza->prepare(
            'INSERT INTO povreda (id_pacijenta, id_dela_tela, tip, datum_povrede, tezina, opis)
             VALUES (:pac, :deo, :tip, :datum, :tezina, :opis)'
        )->execute([
            'pac' => $podaci['id_pacijenta'],
            'deo' => $podaci['id_dela_tela'],
            'tip' => $podaci['tip'],
            'datum' => $podaci['datum_povrede'],
            'tezina' => $podaci['tezina'],
            'opis' => $podaci['opis'],
        ]);
        return (int) $this->baza->lastInsertId();
    }

    public function izmeni(int $id, array $podaci): void
    {
        $this->baza->prepare(
            'UPDATE povreda
             SET id_dela_tela = :deo, tip = :tip, datum_povrede = :datum, tezina = :tezina, opis = :opis
             WHERE id_povrede = :id'
        )->execute([
            'deo' => $podaci['id_dela_tela'],
            'tip' => $podaci['tip'],
            'datum' => $podaci['datum_povrede'],
            'tezina' => $podaci['tezina'],
            'opis' => $podaci['opis'],
            'id' => $id,
        ]);
    }

    private function izReda(array $red): Povreda
    {
        return new Povreda(
            (int) $red['id_povrede'],
            (int) $red['id_pacijenta'],
            (int) $red['id_dela_tela'],
            (string) $red['tip'],
            (string) $red['datum_povrede'],
            (string) $red['tezina'],
            $red['opis'] !== null ? (string) $red['opis'] : null,
            (string) ($red['naziv_dela'] ?? '')
        );
    }
}
