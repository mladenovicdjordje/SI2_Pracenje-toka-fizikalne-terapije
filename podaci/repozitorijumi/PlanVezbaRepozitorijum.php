<?php
namespace Kinetika\Podaci\Repozitorijumi;

use Kinetika\Domen\Entiteti\PlanVezba;
use PDO;

/**
 * CRC: PlanVezbaRepozitorijum
 * Odgovornost: vežbe vezane za plan
 * Saradnici: BazniRepozitorijum, PlanServis
 */
class PlanVezbaRepozitorijum extends BazniRepozitorijum
{
    public function zaPlan(int $idPlana): array
    {
        $upit = $this->baza->prepare(
            'SELECT pv.*, v.naziv AS naziv_vezbe, d.naziv AS naziv_dela
             FROM plan_vezba pv
             JOIN vezba v ON v.id_vezbe = pv.id_vezbe
             JOIN deo_tela d ON d.id_dela_tela = pv.id_dela_tela
             WHERE pv.id_plana = :id
             ORDER BY pv.id_plan_vezba'
        );
        $upit->execute(['id' => $idPlana]);
        $lista = [];
        foreach ($upit->fetchAll(PDO::FETCH_ASSOC) as $red) {
            $lista[] = $this->izReda($red);
        }
        return $lista;
    }

    public function upisi(int $idPlana, int $idVezbe, int $idDelaTela, int $serije, int $ponavljanja, ?string $napomena): int
    {
        $this->baza->prepare(
            'INSERT INTO plan_vezba (id_plana, id_vezbe, id_dela_tela, serije, ponavljanja, napomena)
             VALUES (:plan, :vezba, :deo, :ser, :pon, :nap)'
        )->execute([
            'plan' => $idPlana,
            'vezba' => $idVezbe,
            'deo' => $idDelaTela,
            'ser' => $serije,
            'pon' => $ponavljanja,
            'nap' => $napomena,
        ]);
        return (int) $this->baza->lastInsertId();
    }

    public function obrisi(int $idPlanVezba): void
    {
        $this->baza->prepare('DELETE FROM plan_vezba WHERE id_plan_vezba = :id')->execute(['id' => $idPlanVezba]);
    }

    public function vecPostoji(int $idPlana, int $idVezbe, int $idDelaTela): bool
    {
        $upit = $this->baza->prepare(
            'SELECT COUNT(*) FROM plan_vezba
             WHERE id_plana = :plan AND id_vezbe = :vezba AND id_dela_tela = :deo'
        );
        $upit->execute(['plan' => $idPlana, 'vezba' => $idVezbe, 'deo' => $idDelaTela]);
        return (int) $upit->fetchColumn() > 0;
    }

    private function izReda(array $red): PlanVezba
    {
        return new PlanVezba(
            (int) $red['id_plan_vezba'],
            (int) $red['id_plana'],
            (int) $red['id_vezbe'],
            (int) $red['id_dela_tela'],
            (int) $red['serije'],
            (int) $red['ponavljanja'],
            $red['napomena'] !== null ? (string) $red['napomena'] : null,
            (string) ($red['naziv_vezbe'] ?? ''),
            (string) ($red['naziv_dela'] ?? '')
        );
    }
}
