<?php
namespace Kinetika\Podaci\Repozitorijumi;

use Kinetika\Domen\Entiteti\TerapijskiPlan;
use PDO;

/**
 * CRC: PlanRepozitorijum
 * Odgovornost: SQL pristup planu, koristi pogled za listu
 * Saradnici: BazniRepozitorijum, PlanServis
 */
class PlanRepozitorijum extends BazniRepozitorijum
{
    public function filtriraj(?int $idPacijenta, ?int $idFizioterapeuta, string $status, string $pojam): array
    {
        $sql = 'SELECT * FROM v_pregled_toka_terapije WHERE 1 = 1';
        $par = [];
        if ($idPacijenta) {
            $sql .= ' AND id_pacijenta = :pac';
            $par['pac'] = $idPacijenta;
        }
        if ($idFizioterapeuta) {
            $sql .= ' AND id_fizioterapeuta = :ft';
            $par['ft'] = $idFizioterapeuta;
        }
        if ($status !== '' && $status !== 'svi') {
            $sql .= ' AND status_plana = :st';
            $par['st'] = $status;
        }
        if ($pojam !== '') {
            $sql .= ' AND (ime_pacijenta LIKE :q1 OR prezime_pacijenta LIKE :q2 OR tip_povrede LIKE :q3 OR deo_tela LIKE :q4)';
            $par['q1'] = $par['q2'] = $par['q3'] = $par['q4'] = '%' . $pojam . '%';
        }
        $sql .= ' ORDER BY id_plana DESC';
        $upit = $this->baza->prepare($sql);
        $upit->execute($par);
        $lista = [];
        foreach ($upit->fetchAll(PDO::FETCH_ASSOC) as $red) {
            $lista[] = $this->izPogleda($red);
        }
        return $lista;
    }

    public function nadjiPoId(int $id): ?TerapijskiPlan
    {
        $upit = $this->baza->prepare('SELECT * FROM v_pregled_toka_terapije WHERE id_plana = :id LIMIT 1');
        $upit->execute(['id' => $id]);
        $red = $upit->fetch(PDO::FETCH_ASSOC);
        if (!$red) {
            return null;
        }
        $det = $this->baza->prepare('SELECT * FROM terapijski_plan WHERE id_plana = :id');
        $det->execute(['id' => $id]);
        $p = $det->fetch(PDO::FETCH_ASSOC);
        return $this->izPogleda($red, $p ?: []);
    }

    public function upisi(array $podaci): int
    {
        $this->baza->prepare(
            'INSERT INTO terapijski_plan
                (id_povrede, id_pacijenta, id_fizioterapeuta, cilj, datum_od, datum_do,
                 predvidjen_broj_seansi, status, nacin_naplate, cena, napomena)
             VALUES
                (:pov, :pac, :ft, :cilj, :od, :do, :broj, :status, :nacin, :cena, :nap)'
        )->execute([
            'pov' => $podaci['id_povrede'],
            'pac' => $podaci['id_pacijenta'],
            'ft' => $podaci['id_fizioterapeuta'],
            'cilj' => $podaci['cilj'],
            'od' => $podaci['datum_od'],
            'do' => $podaci['datum_do'],
            'broj' => $podaci['predvidjen_broj_seansi'],
            'status' => $podaci['status'],
            'nacin' => $podaci['nacin_naplate'],
            'cena' => $podaci['cena'],
            'nap' => $podaci['napomena'],
        ]);
        return (int) $this->baza->lastInsertId();
    }

    public function izmeni(int $id, array $podaci): void
    {
        $this->baza->prepare(
            'UPDATE terapijski_plan
             SET cilj = :cilj, datum_od = :od, datum_do = :do,
                 predvidjen_broj_seansi = :broj, status = :status,
                 nacin_naplate = :nacin, cena = :cena, napomena = :nap
             WHERE id_plana = :id'
        )->execute([
            'cilj' => $podaci['cilj'],
            'od' => $podaci['datum_od'],
            'do' => $podaci['datum_do'],
            'broj' => $podaci['predvidjen_broj_seansi'],
            'status' => $podaci['status'],
            'nacin' => $podaci['nacin_naplate'],
            'cena' => $podaci['cena'],
            'nap' => $podaci['napomena'],
            'id' => $id,
        ]);
    }

    public function obrisi(int $id): void
    {
        $this->baza->prepare('DELETE FROM terapijski_plan WHERE id_plana = :id')->execute(['id' => $id]);
    }

    public function imaSeanse(int $idPlana): bool
    {
        $upit = $this->baza->prepare('SELECT COUNT(*) FROM seansa WHERE id_plana = :id');
        $upit->execute(['id' => $idPlana]);
        return (int) $upit->fetchColumn() > 0;
    }

    public function brojOtvorenihSeansi(int $idPlana): int
    {
        $upit = $this->baza->prepare(
            "SELECT COUNT(*) FROM seansa
             WHERE id_plana = :id AND status IN ('zahtevana', 'zakazana')"
        );
        $upit->execute(['id' => $idPlana]);
        return (int) $upit->fetchColumn();
    }

        public function promeniStatus(int $idPlana, string $status): void
    {
        $this->baza->prepare(
            'UPDATE terapijski_plan SET status = :st WHERE id_plana = :id'
        )->execute([
            'st' => $status,
            'id' => $idPlana,
        ]);
    }

    private function izPogleda(array $red, array $det = []): TerapijskiPlan
    {
        return new TerapijskiPlan(
            (int) $red['id_plana'],
            (int) ($det['id_povrede'] ?? 0),
            (int) $red['id_pacijenta'],
            (int) $red['id_fizioterapeuta'],
            (string) ($det['cilj'] ?? ''),
            (string) ($det['datum_od'] ?? ''),
            $det['datum_do'] ?? null,
            (int) ($red['predvidjen_broj_seansi'] ?? $det['predvidjen_broj_seansi'] ?? 0),
            (string) ($red['status_plana'] ?? $det['status'] ?? 'aktivan'),
            (string) ($red['nacin_naplate'] ?? $det['nacin_naplate'] ?? 'po_seansi'),
            (float) ($det['cena'] ?? 0),
            $det['napomena'] ?? null,
            trim(($red['ime_pacijenta'] ?? '') . ' ' . ($red['prezime_pacijenta'] ?? '')),
            trim(($red['ime_terapeuta'] ?? '') . ' ' . ($red['prezime_terapeuta'] ?? '')),
            (string) ($red['deo_tela'] ?? ''),
            (string) ($red['tip_povrede'] ?? ''),
            (int) ($red['broj_odrzanih'] ?? 0)
        );
    }
}
