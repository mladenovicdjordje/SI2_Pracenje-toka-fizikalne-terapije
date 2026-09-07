<?php
namespace Kinetika\Podaci\Repozitorijumi;

use PDO;

/**
 * CRC: IzvestajRepozitorijum
 * Odgovornost: cita pogled i tabele za izvestaje
 * Saradnici: BazniRepozitorijum, KontrolerIzvestaja
 */
class IzvestajRepozitorijum extends BazniRepozitorijum
{
    public function tokTerapije(?int $idPac = null, ?int $idFt = null, string $status = ''): array
    {
        $sql = 'SELECT * FROM v_pregled_toka_terapije WHERE 1=1';
        $par = [];
        if ($idPac) {
            $sql .= ' AND id_pacijenta = :pac';
            $par['pac'] = $idPac;
        }
        if ($idFt) {
            $sql .= ' AND id_fizioterapeuta = :ft';
            $par['ft'] = $idFt;
        }
        if ($status !== '' && $status !== 'svi') {
            $sql .= ' AND status_plana = :st';
            $par['st'] = $status;
        }
        $sql .= ' ORDER BY datum_od DESC';
        $upit = $this->baza->prepare($sql);
        $upit->execute($par);
        return $upit->fetchAll(PDO::FETCH_ASSOC);
    }

    public function seanse(?int $idPac = null, ?int $idFt = null, string $status = '', string $od = '', string $do = ''): array
    {
        $sql = 'SELECT s.*, CONCAT(pac.ime, \' \', pac.prezime) AS ime_pacijenta,
                       CONCAT(ft.ime, \' \', ft.prezime) AS ime_terapeuta
                FROM seansa s
                JOIN pacijent pac ON pac.id_pacijenta = s.id_pacijenta
                JOIN fizioterapeut ft ON ft.id_fizioterapeuta = s.id_fizioterapeuta
                WHERE 1=1';
        $par = [];
        if ($idPac) {
            $sql .= ' AND s.id_pacijenta = :pac';
            $par['pac'] = $idPac;
        }
        if ($idFt) {
            $sql .= ' AND s.id_fizioterapeuta = :ft';
            $par['ft'] = $idFt;
        }
        if ($status !== '' && $status !== 'svi') {
            $sql .= ' AND s.status = :st';
            $par['st'] = $status;
        }
        if ($od !== '') {
            $sql .= ' AND s.pocetak >= :od';
            $par['od'] = $od . ' 00:00:00';
        }
        if ($do !== '') {
            $sql .= ' AND s.pocetak < DATE_ADD(:do, INTERVAL 1 DAY)';
            $par['do'] = $do;
        }
        $sql .= ' ORDER BY s.pocetak DESC';
        $upit = $this->baza->prepare($sql);
        $upit->execute($par);
        return $upit->fetchAll(PDO::FETCH_ASSOC);
    }
}
