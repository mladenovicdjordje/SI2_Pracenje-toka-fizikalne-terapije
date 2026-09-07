<?php
namespace Kinetika\Podaci\Repozitorijumi;

use Kinetika\Domen\Entiteti\Seansa;
use PDO;
use Throwable;

/**
 * CRC: SeansaRepozitorijum
 * Odgovornost: SQL za seanse i poziv stored procedure za potvrdu
 * Saradnici: BazniRepozitorijum, SeansaServis
 */
class SeansaRepozitorijum extends BazniRepozitorijum
{
    public function zaOpseg(string $od, string $do, ?int $idFt = null, ?int $idPac = null): array
    {
        $sql = 'SELECT s.*,
                       CONCAT(pac.ime, \' \', pac.prezime) AS ime_pacijenta,
                       CONCAT(ft.ime, \' \', ft.prezime) AS ime_terapeuta
                FROM seansa s
                JOIN pacijent pac ON pac.id_pacijenta = s.id_pacijenta
                JOIN fizioterapeut ft ON ft.id_fizioterapeuta = s.id_fizioterapeuta
                WHERE s.pocetak >= :od AND s.pocetak < :do
                  AND s.status IN (\'zahtevana\', \'zakazana\', \'odrzana\')';
        $par = ['od' => $od . ' 00:00:00', 'do' => $do . ' 00:00:00'];
        if ($idFt) {
            $sql .= ' AND s.id_fizioterapeuta = :ft';
            $par['ft'] = $idFt;
        }
        if ($idPac) {
            $sql .= ' AND s.id_pacijenta = :pac';
            $par['pac'] = $idPac;
        }
        $sql .= ' ORDER BY s.pocetak';
        $upit = $this->baza->prepare($sql);
        $upit->execute($par);
        $lista = [];
        foreach ($upit->fetchAll(PDO::FETCH_ASSOC) as $red) {
            $lista[] = $this->izReda($red);
        }
        return $lista;
    }

        public function zaPlan(int $idPlana): array
    {
        $upit = $this->baza->prepare(
            'SELECT s.*,
                    CONCAT(pac.ime, \' \', pac.prezime) AS ime_pacijenta,
                    CONCAT(ft.ime, \' \', ft.prezime) AS ime_terapeuta
             FROM seansa s
             JOIN pacijent pac ON pac.id_pacijenta = s.id_pacijenta
             JOIN fizioterapeut ft ON ft.id_fizioterapeuta = s.id_fizioterapeuta
             WHERE s.id_plana = :id
             ORDER BY s.pocetak'
        );
        $upit->execute(['id' => $idPlana]);
        $lista = [];
        foreach ($upit->fetchAll(PDO::FETCH_ASSOC) as $red) {
            $lista[] = $this->izReda($red);
        }
        return $lista;
    }

    public function nadjiPoId(int $id): ?Seansa
    {
        $upit = $this->baza->prepare(
            'SELECT s.*,
                    CONCAT(pac.ime, \' \', pac.prezime) AS ime_pacijenta,
                    CONCAT(ft.ime, \' \', ft.prezime) AS ime_terapeuta
             FROM seansa s
             JOIN pacijent pac ON pac.id_pacijenta = s.id_pacijenta
             JOIN fizioterapeut ft ON ft.id_fizioterapeuta = s.id_fizioterapeuta
             WHERE s.id_seanse = :id LIMIT 1'
        );
        $upit->execute(['id' => $id]);
        $red = $upit->fetch(PDO::FETCH_ASSOC);
        return $red ? $this->izReda($red) : null;
    }

    public function upisi(array $podaci): int
    {
        $this->baza->prepare(
            'INSERT INTO seansa
                (id_plana, id_pacijenta, id_fizioterapeuta, pocetak, trajanje_min, status, cena, napomena)
             VALUES
                (:plan, :pac, :ft, :pocetak, :trajanje, :status, :cena, :napomena)'
        )->execute([
            'plan' => $podaci['id_plana'],
            'pac' => $podaci['id_pacijenta'],
            'ft' => $podaci['id_fizioterapeuta'],
            'pocetak' => $podaci['pocetak'],
            'trajanje' => $podaci['trajanje_min'],
            'status' => $podaci['status'],
            'cena' => $podaci['cena'],
            'napomena' => $podaci['napomena'],
        ]);
        return (int) $this->baza->lastInsertId();
    }

    public function promeniStatus(int $id, string $status, ?string $ulogaOtkaza = null, ?int $nivoBola = null, ?string $napomena = null): void
    {
        $sql = 'UPDATE seansa SET status = :status';
        $par = ['status' => $status, 'id' => $id];
        if ($ulogaOtkaza !== null) {
            $sql .= ', otkazao_uloga = :uloga';
            $par['uloga'] = $ulogaOtkaza;
        }
        if ($nivoBola !== null) {
            $sql .= ', nivo_bola = :bol';
            $par['bol'] = $nivoBola;
        }
        if ($napomena !== null) {
            $sql .= ', napomena = :nap';
            $par['nap'] = $napomena;
        }
        $sql .= ' WHERE id_seanse = :id';
        $this->baza->prepare($sql)->execute($par);
    }

    public function imaPreklapanje(int $idFt, string $pocetak, int $trajanje, ?int $iskljuciId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM seansa
                WHERE id_fizioterapeuta = :ft
                  AND status IN (\'zahtevana\', \'zakazana\', \'odrzana\')
                  AND pocetak < DATE_ADD(:pocetak, INTERVAL :trajanje MINUTE)
                  AND DATE_ADD(pocetak, INTERVAL trajanje_min MINUTE) > :pocetak2';
        $par = [
            'ft' => $idFt,
            'pocetak' => $pocetak,
            'trajanje' => $trajanje,
            'pocetak2' => $pocetak,
        ];
        if ($iskljuciId) {
            $sql .= ' AND id_seanse <> :id';
            $par['id'] = $iskljuciId;
        }
        $upit = $this->baza->prepare($sql);
        $upit->execute($par);
        return (int) $upit->fetchColumn() > 0;
    }

    public function brojUPlanu(int $idPlana): int
    {
        $upit = $this->baza->prepare(
            "SELECT COUNT(*) FROM seansa
             WHERE id_plana = :id
               AND status IN ('zahtevana', 'zakazana', 'odrzana')"
        );
        $upit->execute(['id' => $idPlana]);
        return (int) $upit->fetchColumn();
    }

        public function brojOdrzanihUPlanu(int $idPlana): int
    {
        $upit = $this->baza->prepare(
            "SELECT COUNT(*) FROM seansa
             WHERE id_plana = :id AND status = 'odrzana'"
        );
        $upit->execute(['id' => $idPlana]);
        return (int) $upit->fetchColumn();
    }
    
    public function brojUDanu(int $idFt, string $datum): int
    {
        $upit = $this->baza->prepare(
            'SELECT COUNT(*) FROM seansa
             WHERE id_fizioterapeuta = :ft
               AND DATE(pocetak) = :datum
               AND status IN (\'zahtevana\', \'zakazana\', \'odrzana\')'
        );
        $upit->execute(['ft' => $idFt, 'datum' => $datum]);
        return (int) $upit->fetchColumn();
    }

    public function potvrdiZahtev(int $idSeanse): void
    {
        $this->baza->prepare('CALL sp_potvrdi_zahtev_seanse(:id)')->execute(['id' => $idSeanse]);
    }

    private function izReda(array $red): Seansa
    {
        return new Seansa(
            (int) $red['id_seanse'],
            (int) $red['id_plana'],
            (int) $red['id_pacijenta'],
            (int) $red['id_fizioterapeuta'],
            (string) $red['pocetak'],
            (int) $red['trajanje_min'],
            (string) $red['status'],
            $red['nivo_bola'] !== null ? (int) $red['nivo_bola'] : null,
            (float) $red['cena'],
            $red['napomena'] !== null ? (string) $red['napomena'] : null,
            (string) ($red['ime_pacijenta'] ?? ''),
            (string) ($red['ime_terapeuta'] ?? '')
        );
    }
}