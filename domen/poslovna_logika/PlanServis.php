<?php
namespace Kinetika\Domen\PoslovnaLogika;

use Kinetika\Domen\Dto\PlanDto;
use Kinetika\Podaci\Konekcija;
use Kinetika\Podaci\Repozitorijumi\PlanRepozitorijum;
use Kinetika\Podaci\Repozitorijumi\PlanVezbaRepozitorijum;
use Kinetika\Podaci\Repozitorijumi\PovredaRepozitorijum;

/**
 * CRC: PlanServis
 * Odgovornost: validacija i pravila za plan terapije i povredu
 * Saradnici: PlanRepozitorijum, PovredaRepozitorijum, PoslovnaPravila
 *
 * Overloading: validiraj($dto) novi plan, validiraj($dto, true) izmena.
 */
class PlanServis
{
    public function __construct(
        private PlanRepozitorijum $planovi,
        private PovredaRepozitorijum $povrede,
        private PlanVezbaRepozitorijum $planVezbe,
        private ValidatorUnosa $validator,
        private PoslovnaPravila $pravila
    ) {
    }

    public function validiraj(PlanDto $dto, bool $izmena = false): array
    {
        $greske = [];
        if ($g = $this->validator->obavezno($dto->cilj, 'Cilj')) {
            $greske['cilj'] = $g;
        }
        if ($dto->idPacijenta < 1) {
            $greske['id_pacijenta'] = 'Izaberite pacijenta.';
        }
        if ($dto->idDelaTela < 1) {
            $greske['id_dela_tela'] = 'Izaberite deo tela.';
        }
        if ($g = $this->validator->obavezno($dto->tipPovrede, 'Tip povrede')) {
            $greske['tip_povrede'] = $g;
        }
        if ($dto->datumPovrede === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dto->datumPovrede)) {
            $greske['datum_povrede'] = 'Datum povrede nije ispravan.';
        }
        if ($dto->datumOd === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dto->datumOd)) {
            $greske['datum_od'] = 'Datum pocetka nije ispravan.';
        }
        if ($dto->datumDo !== null && $dto->datumOd !== '' && $dto->datumDo < $dto->datumOd) {
            $greske['datum_do'] = 'Datum zavrsetka ne moze biti pre pocetka.';
        }
        if ($dto->predvidjenBrojSeansi < 1) {
            $greske['predvidjen_broj_seansi'] = 'Broj seansi mora biti bar 1.';
        }
        if (!in_array($dto->tezina, ['blaga', 'umerena', 'teska'], true)) {
            $greske['tezina'] = 'Tezina nije ispravna.';
        }
        if (!in_array($dto->status, ['nacrt', 'aktivan', 'zavrsen', 'prekinut'], true)) {
            $greske['status'] = 'Status nije ispravan.';
        }
        if (!in_array($dto->nacinNaplate, ['po_planu', 'po_seansi', 'oba'], true)) {
            $greske['nacin_naplate'] = 'Nacin naplate nije ispravan.';
        }
        if ($dto->cena < 0) {
            $greske['cena'] = 'Cena ne moze biti negativna.';
        }

        if ($izmena && $dto->idPlana && in_array($dto->status, ['zavrsen', 'prekinut'], true)) {
            $otvorene = $this->planovi->brojOtvorenihSeansi($dto->idPlana);
            if ($otvorene > 0) {
                $greske['status'] = 'Plan se ne moze zatvoriti dok ima nezavrsenih seansi (' . $otvorene . ').';
            }
        }

        return $greske;
    }

    public function sacuvaj(PlanDto $dto): array
    {
        $izmena = $dto->idPlana !== null;
        $greske = $this->validiraj($dto, $izmena);
        if ($greske) {
            return ['ok' => false, 'greske' => $greske];
        }

        $pdo = Konekcija::uzmi();
        $pdo->beginTransaction();
        try {
            $podaciPovrede = [
                'id_pacijenta' => $dto->idPacijenta,
                'id_dela_tela' => $dto->idDelaTela,
                'tip' => $dto->tipPovrede,
                'datum_povrede' => $dto->datumPovrede,
                'tezina' => $dto->tezina,
                'opis' => $dto->opisPovrede,
            ];
            if ($izmena) {
                $plan = $this->planovi->nadjiPoId((int) $dto->idPlana);
                if ($plan === null) {
                    $pdo->rollBack();
                    return ['ok' => false, 'greske' => ['opste' => 'Plan ne postoji.']];
                }
                $this->povrede->izmeni($plan->idPovrede(), $podaciPovrede);
                $this->planovi->izmeni((int) $dto->idPlana, [
                    'cilj' => $dto->cilj,
                    'datum_od' => $dto->datumOd,
                    'datum_do' => $dto->datumDo,
                    'predvidjen_broj_seansi' => $dto->predvidjenBrojSeansi,
                    'status' => $dto->status,
                    'nacin_naplate' => $dto->nacinNaplate,
                    'cena' => $dto->cena,
                    'napomena' => $dto->napomena,
                ]);
                $id = (int) $dto->idPlana;
            } else {
                $idPovrede = $this->povrede->upisi($podaciPovrede);
                $id = $this->planovi->upisi([
                    'id_povrede' => $idPovrede,
                    'id_pacijenta' => $dto->idPacijenta,
                    'id_fizioterapeuta' => $dto->idFizioterapeuta,
                    'cilj' => $dto->cilj,
                    'datum_od' => $dto->datumOd,
                    'datum_do' => $dto->datumDo,
                    'predvidjen_broj_seansi' => $dto->predvidjenBrojSeansi,
                    'status' => $dto->status,
                    'nacin_naplate' => $dto->nacinNaplate,
                    'cena' => $dto->cena,
                    'napomena' => $dto->napomena,
                ]);
            }
            $pdo->commit();
            return ['ok' => true, 'id' => $id];
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            return ['ok' => false, 'greske' => ['opste' => 'Upis nije uspeo.']];
        }
    }

    public function ukloni(int $idPlana): array
    {
        $plan = $this->planovi->nadjiPoId($idPlana);
        if ($plan === null) {
            return ['ok' => false, 'poruka' => 'Plan ne postoji.'];
        }
        if ($this->planovi->imaSeanse($idPlana)) {
            $this->planovi->izmeni($idPlana, [
                'cilj' => $plan->cilj(),
                'datum_od' => $plan->datumOd(),
                'datum_do' => $plan->datumDo(),
                'predvidjen_broj_seansi' => $plan->predvidjenBrojSeansi(),
                'status' => 'prekinut',
                'nacin_naplate' => $plan->nacinNaplate(),
                'cena' => $plan->cena(),
                'napomena' => $plan->napomena(),
            ]);
            return ['ok' => true, 'poruka' => 'Plan ima seanse, status je prekinut umesto brisanja.'];
        }
        $pdo = Konekcija::uzmi();
        $pdo->beginTransaction();
        try {
            $this->planovi->obrisi($idPlana);
            $pdo->commit();
            return ['ok' => true, 'poruka' => 'Plan je obrisan.'];
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            return ['ok' => false, 'poruka' => 'Plan nije obrisan.'];
        }
    }

    public function dodajVezbu(int $idPlana, int $idVezbe, int $idDelaTela, int $serije, int $ponavljanja, ?string $napomena): array
    {
        if ($idVezbe < 1) {
            return ['ok' => false, 'poruka' => 'Izaberite vežbu iz kataloga.'];
        }
        if ($idDelaTela < 1) {
            return ['ok' => false, 'poruka' => 'Izaberite deo tela za vežbu.'];
        }
        if ($serije < 1 || $ponavljanja < 1) {
            return ['ok' => false, 'poruka' => 'Serije i ponavljanja moraju biti bar 1.'];
        }
        if ($this->planVezbe->vecPostoji($idPlana, $idVezbe, $idDelaTela)) {
            return ['ok' => false, 'poruka' => 'Ova vežba je već na tom delu tela u planu.'];
        }
        $this->planVezbe->upisi($idPlana, $idVezbe, $idDelaTela, $serije, $ponavljanja, $napomena);
        return ['ok' => true, 'poruka' => 'Vežba je dodata u plan.'];
    }
}
