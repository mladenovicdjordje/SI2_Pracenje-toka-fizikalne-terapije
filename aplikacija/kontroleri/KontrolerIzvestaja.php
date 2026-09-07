<?php
namespace Kinetika\Aplikacija\Kontroleri;

use Kinetika\Aplikacija\ProveraPristupa;
use Kinetika\Podaci\Repozitorijumi\FizioterapeutRepozitorijum;
use Kinetika\Podaci\Repozitorijumi\PacijentRepozitorijum;
use Kinetika\Podaci\Repozitorijumi\PlanRepozitorijum;
use Kinetika\Podaci\Repozitorijumi\PlanVezbaRepozitorijum;
use Kinetika\Podaci\Repozitorijumi\SeansaRepozitorijum;
use Kinetika\Podaci\Repozitorijumi\VezbaRepozitorijum;

/**
 * CRC: KontrolerIzvestaja
 * Odgovornost: tabelarni izvestaj toka terapije i parametarska stampa
 * Saradnici: PlanRepozitorijum, SeansaRepozitorijum, PlanVezbaRepozitorijum
 */
class KontrolerIzvestaja
{
    public function prikazi(): void
    {
        $podaci = $this->pripremi();
        extract($podaci, EXTR_OVERWRITE);
        $naslov = 'Izveštaji';
        require dirname(__DIR__, 2) . '/prezentacija/stranice/izvestaji.php';
    }

    public function stampa(): void
    {
        $podaci = $this->pripremi();
        extract($podaci, EXTR_OVERWRITE);
        $naslov = 'Stampa izvestaja';
        require dirname(__DIR__, 2) . '/prezentacija/stranice/izvestaj_stampa.php';
    }

    private function pripremi(): array
    {
        $korisnik = ProveraPristupa::zahtevajUlogu(['admin', 'fizioterapeut', 'pacijent']);
        $idPac = isset($_GET['pac']) && $_GET['pac'] !== '' ? (int) $_GET['pac'] : null;
        $idFt = isset($_GET['ft']) && $_GET['ft'] !== '' ? (int) $_GET['ft'] : null;
        $status = (string) ($_GET['status'] ?? '');
        $pojam = trim((string) ($_GET['q'] ?? ''));

        if ($korisnik['uloga'] === 'pacijent') {
            $idPac = (int) $korisnik['id_pacijenta'];
            $idFt = null;
        } elseif ($korisnik['uloga'] === 'fizioterapeut') {
            $idFt = (int) $korisnik['id_fizioterapeuta'];
        }

        $lista = (new PlanRepozitorijum())->filtriraj($idPac, $idFt, $status, $pojam);
        $pacijenti = [];
        $terapeuti = [];
        if ($korisnik['uloga'] === 'admin') {
            $pacijenti = (new PacijentRepozitorijum())->nadjiSve();
            $terapeuti = (new FizioterapeutRepozitorijum())->nadjiSve(null, 1);
        } elseif ($korisnik['uloga'] === 'fizioterapeut') {
            $pacijenti = (new PacijentRepozitorijum())->nadjiSve(null, (int) $korisnik['id_fizioterapeuta']);
        }

        $seansePoPlanu = [];
        $vezbePoPlanu = [];
        $repoSeansa = new SeansaRepozitorijum();
        $repoPv = new PlanVezbaRepozitorijum();
        $repoVezba = new VezbaRepozitorijum();
        foreach ($lista as $plan) {
            $id = $plan->idPlana();
            $seansePoPlanu[$id] = $repoSeansa->zaPlan($id);
            $vezbePoPlanu[$id] = [];
            foreach ($repoPv->zaPlan($id) as $pv) {
                $vezba = $repoVezba->nadjiPoId($pv->idVezbe());
                $vezbePoPlanu[$id][] = [
                    'naziv' => $pv->nazivVezbe(),
                    'deo_tela' => $pv->nazivDelaTela(),
                    'serije' => $pv->serije(),
                    'ponavljanja' => $pv->ponavljanja(),
                    'opis' => $vezba?->opis() ?? '',
                ];
            }
        }

        return compact(
            'korisnik', 'lista', 'pacijenti', 'terapeuti',
            'idPac', 'idFt', 'status', 'pojam',
            'seansePoPlanu', 'vezbePoPlanu'
        );
    }
}