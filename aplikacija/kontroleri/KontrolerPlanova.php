<?php
namespace Kinetika\Aplikacija\Kontroleri;

use Kinetika\Aplikacija\Datum;
use Kinetika\Aplikacija\ProveraPristupa;
use Kinetika\Domen\Dto\PlanDto;
use Kinetika\Domen\PoslovnaLogika\PlanServis;
use Kinetika\Domen\PoslovnaLogika\PoslovnaPravila;
use Kinetika\Domen\PoslovnaLogika\ValidatorUnosa;
use Kinetika\Podaci\Repozitorijumi\DeoTelaRepozitorijum;
use Kinetika\Podaci\Repozitorijumi\FizioterapeutRepozitorijum;
use Kinetika\Podaci\Repozitorijumi\PacijentRepozitorijum;
use Kinetika\Podaci\Repozitorijumi\PlanRepozitorijum;
use Kinetika\Podaci\Repozitorijumi\PlanVezbaRepozitorijum;
use Kinetika\Podaci\Repozitorijumi\PovredaRepozitorijum;
use Kinetika\Podaci\Repozitorijumi\VezbaRepozitorijum;

/**
 * CRC: KontrolerPlanova
 * Odgovornost: lista, unos i izmena planova terapije
 * Saradnici: PlanServis, ProveraPristupa
 */
class KontrolerPlanova
{
    private function servis(): PlanServis
    {
        return new PlanServis(
            new PlanRepozitorijum(),
            new PovredaRepozitorijum(),
            new PlanVezbaRepozitorijum(),
            new ValidatorUnosa(),
            new PoslovnaPravila()
        );
    }

    public function lista(): void
    {
        $korisnik = ProveraPristupa::zahtevajUlogu(['admin', 'fizioterapeut', 'pacijent']);
        $pojam = trim((string) ($_GET['q'] ?? ''));
        $status = (string) ($_GET['status'] ?? 'svi');
        $idPac = isset($_GET['pac']) && $_GET['pac'] !== '' ? (int) $_GET['pac'] : null;

        $idFt = null;
        if ($korisnik['uloga'] === 'fizioterapeut') {
            $idFt = (int) $korisnik['id_fizioterapeuta'];
        } elseif ($korisnik['uloga'] === 'pacijent') {
            $idPac = (int) $korisnik['id_pacijenta'];
        }

        $lista = (new PlanRepozitorijum())->filtriraj($idPac, $idFt, $status, $pojam);
        $pacijenti = [];
        if ($korisnik['uloga'] === 'admin') {
            $pacijenti = (new PacijentRepozitorijum())->nadjiSve();
        } elseif ($korisnik['uloga'] === 'fizioterapeut') {
            $pacijenti = (new PacijentRepozitorijum())->nadjiSve(null, $idFt);
        }
        $poruka = (string) ($_GET['poruka'] ?? '');
        $naslov = 'Plan terapije';
        require dirname(__DIR__, 2) . '/prezentacija/stranice/planovi_lista.php';
    }

    public function forma(): void
    {
        $korisnik = ProveraPristupa::zahtevajUlogu(['admin', 'fizioterapeut']);
        $id = (int) ($_GET['id'] ?? 0);
        $plan = $id ? (new PlanRepozitorijum())->nadjiPoId($id) : null;
        if ($id && $plan === null) {
            header('Location: /planovi?poruka=' . rawurlencode('Plan nije pronađen.'));
            exit;
        }
        if ($plan && $korisnik['uloga'] === 'fizioterapeut'
            && $plan->idFizioterapeuta() !== (int) $korisnik['id_fizioterapeuta']) {
            http_response_code(403);
            $poruka = 'Nemate pravo na ovaj plan.';
            require dirname(__DIR__, 2) . '/prezentacija/stranice/zabranjeno.php';
            return;
        }

        $povreda = $plan ? (new PovredaRepozitorijum())->nadjiPoId($plan->idPovrede()) : null;
        $podaci = $this->podaciForme($plan, $povreda, $korisnik);
        $deloviTela = (new DeoTelaRepozitorijum())->svi();
        $terapeuti = (new FizioterapeutRepozitorijum())->nadjiSve(null, 1);
        $pacijenti = $korisnik['uloga'] === 'fizioterapeut'
            ? (new PacijentRepozitorijum())->nadjiSve(null, (int) $korisnik['id_fizioterapeuta'])
            : (new PacijentRepozitorijum())->nadjiSve();
        $vezbeKatalog = $id ? (new VezbaRepozitorijum())->nadjiSve() : [];
        $vezbePlana = $id ? (new PlanVezbaRepozitorijum())->zaPlan($id) : [];
        $terapeuti = $korisnik['uloga'] === 'admin'
            ? (new FizioterapeutRepozitorijum())->nadjiSve(null, 1)
            : [];
        $greske = [];
        $poruka = (string) ($_GET['poruka'] ?? '');
        $naslov = $plan ? 'Izmena plana' : 'Novi plan terapije';
        require dirname(__DIR__, 2) . '/prezentacija/stranice/plan_forma.php';
    }

    public function sacuvaj(): void
    {
        $korisnik = ProveraPristupa::zahtevajUlogu(['admin', 'fizioterapeut']);
        $ulaz = $_POST;
        if ($korisnik['uloga'] === 'fizioterapeut') {
            $ulaz['id_fizioterapeuta'] = (int) $korisnik['id_fizioterapeuta'];
            $pac = (new PacijentRepozitorijum())->nadjiPoId((int) ($ulaz['id_pacijenta'] ?? 0));
            if ($pac && $pac->idFizioterapeuta() !== (int) $korisnik['id_fizioterapeuta']) {
                http_response_code(403);
                $poruka = 'Pacijent nije vaš.';
                require dirname(__DIR__, 2) . '/prezentacija/stranice/zabranjeno.php';
                return;
            }
        } else {
            $ulaz['id_fizioterapeuta'] = (int) ($ulaz['id_fizioterapeuta'] ?? 0);
            if ($ulaz['id_fizioterapeuta'] < 1) {
                $pac = (new PacijentRepozitorijum())->nadjiPoId((int) ($ulaz['id_pacijenta'] ?? 0));
                $ulaz['id_fizioterapeuta'] = $pac ? $pac->idFizioterapeuta() : 0;
            }
        }

        $dto = PlanDto::izNiza($ulaz);
        $rez = $this->servis()->sacuvaj($dto);
        if (!$rez['ok']) {
            $plan = $dto->idPlana ? (new PlanRepozitorijum())->nadjiPoId((int) $dto->idPlana) : null;
            $povreda = $plan ? (new PovredaRepozitorijum())->nadjiPoId($plan->idPovrede()) : null;
            $podaci = $ulaz;
            $deloviTela = (new DeoTelaRepozitorijum())->svi();
        $terapeuti = (new FizioterapeutRepozitorijum())->nadjiSve(null, 1);
            $pacijenti = $korisnik['uloga'] === 'fizioterapeut'
                ? (new PacijentRepozitorijum())->nadjiSve(null, (int) $korisnik['id_fizioterapeuta'])
                : (new PacijentRepozitorijum())->nadjiSve();
            $vezbeKatalog = $dto->idPlana ? (new VezbaRepozitorijum())->nadjiSve() : [];
            $vezbePlana = $dto->idPlana ? (new PlanVezbaRepozitorijum())->zaPlan((int) $dto->idPlana) : [];
            $terapeuti = $korisnik['uloga'] === 'admin'
                ? (new FizioterapeutRepozitorijum())->nadjiSve(null, 1)
                : [];
            $greske = $rez['greske'];
            $poruka = (string) ($greske['opste'] ?? '');
            $naslov = $dto->idPlana ? 'Izmena plana' : 'Novi plan terapije';
            require dirname(__DIR__, 2) . '/prezentacija/stranice/plan_forma.php';
            return;
        }
            header('Location: /planovi?poruka=' . rawurlencode('Plan je sačuvan.'));
        exit;
    }

    public function obrisi(): void
    {
        $korisnik = ProveraPristupa::zahtevajUlogu(['admin', 'fizioterapeut']);
        $id = (int) ($_POST['id'] ?? 0);
        $plan = (new PlanRepozitorijum())->nadjiPoId($id);
        if ($plan && $korisnik['uloga'] === 'fizioterapeut'
            && $plan->idFizioterapeuta() !== (int) $korisnik['id_fizioterapeuta']) {
            http_response_code(403);
            $poruka = 'Nemate pravo da uklonite ovaj plan.';
            require dirname(__DIR__, 2) . '/prezentacija/stranice/zabranjeno.php';
            return;
        }
        $rez = $this->servis()->ukloni($id);
        header('Location: /planovi?poruka=' . rawurlencode($rez['poruka'] ?? 'Gotovo.'));
        exit;
    }

    public function dodajVezbu(): void
    {
        $korisnik = ProveraPristupa::zahtevajUlogu(['admin', 'fizioterapeut']);
        $idPlana = (int) ($_POST['id_plana'] ?? 0);
        $this->proveriPlan($korisnik, $idPlana);
        $rez = $this->servis()->dodajVezbu(
            $idPlana,
            (int) ($_POST['id_vezbe'] ?? 0),
            (int) ($_POST['id_dela_tela'] ?? 0),
            (int) ($_POST['serije'] ?? 3),
            (int) ($_POST['ponavljanja'] ?? 10),
            trim((string) ($_POST['napomena'] ?? '')) ?: null
        );
        header('Location: /planovi/vezbe?id=' . $idPlana . '&poruka=' . rawurlencode($rez['poruka'] ?? 'Vežba je dodata.'));
    }

    public function ukloniVezbu(): void
    {
        $korisnik = ProveraPristupa::zahtevajUlogu(['admin', 'fizioterapeut']);
        $idPlana = (int) ($_POST['id_plana'] ?? 0);
        $this->proveriPlan($korisnik, $idPlana);
        (new PlanVezbaRepozitorijum())->obrisi((int) ($_POST['id_plan_vezba'] ?? 0));
        header('Location: /planovi/vezbe?id=' . $idPlana . '&poruka=' . rawurlencode('Vežba je uklonjena iz plana.'));
    }

    public function planVezbanja(): void
    {
        $korisnik = ProveraPristupa::zahtevajUlogu(['pacijent', 'fizioterapeut', 'admin']);
        $idPac = $korisnik['uloga'] === 'pacijent' ? (int) $korisnik['id_pacijenta'] : ((int) ($_GET['pac'] ?? 0) ?: null);
        $idFt = $korisnik['uloga'] === 'fizioterapeut' ? (int) $korisnik['id_fizioterapeuta'] : null;
        if ($korisnik['uloga'] !== 'pacijent' && !$idPac) {
            header('Location: /planovi');
            exit;
        }
        $planovi = (new PlanRepozitorijum())->filtriraj($idPac, $idFt, 'aktivan', '');
        $stavke = [];
        $repoPv = new PlanVezbaRepozitorijum();
                $stavke = [];
        $repoPv = new PlanVezbaRepozitorijum();
        $repoVezba = new VezbaRepozitorijum();
        foreach ($planovi as $plan) {
            $vezbe = [];
            foreach ($repoPv->zaPlan($plan->idPlana()) as $pv) {
                $katalog = $repoVezba->nadjiPoId($pv->idVezbe());
                $vezbe[] = [
                    'naziv' => $pv->nazivVezbe(),
                    'deo_tela' => $pv->nazivDelaTela(),
                    'serije' => $pv->serije(),
                    'ponavljanja' => $pv->ponavljanja(),
                    'opis' => $katalog?->opis() ?? '',
                    'kontraindikacije' => $katalog?->kontraindikacije() ?? '',
                ];
            }
            $stavke[$plan->idPlana()] = [
                'plan' => $plan,
                'vezbe' => $vezbe,
            ];
        }
        $naslov = 'Plan vežbanja';
        require dirname(__DIR__, 2) . '/prezentacija/stranice/plan_vezbanja.php';
    }

    private function proveriPlan(array $korisnik, int $idPlana): void
    {
        $plan = (new PlanRepozitorijum())->nadjiPoId($idPlana);
        if ($plan === null) {
            header('Location: /planovi?poruka=' . rawurlencode('Plan ne postoji.'));
            exit;
        }
        if ($korisnik['uloga'] === 'fizioterapeut'
            && $plan->idFizioterapeuta() !== (int) $korisnik['id_fizioterapeuta']) {
            http_response_code(403);
            $poruka = 'Nemate pravo na ovaj plan.';
            require dirname(__DIR__, 2) . '/prezentacija/stranice/zabranjeno.php';
            exit;
        }
    }

    private function podaciForme($plan, $povreda, array $korisnik): array
    {
        if ($plan && $povreda) {
            return [
                'id_plana' => $plan->idPlana(),
                'id_povrede' => $plan->idPovrede(),
                'id_pacijenta' => $plan->idPacijenta(),
                'id_fizioterapeuta' => $plan->idFizioterapeuta(),
                'id_dela_tela' => $povreda->idDelaTela(),
                'tip_povrede' => $povreda->tip(),
                'datum_povrede' => Datum::prikaz($povreda->datumPovrede()),
                'tezina' => $povreda->tezina(),
                'opis_povrede' => $povreda->opis() ?? '',
                'cilj' => $plan->cilj(),
                'datum_od' => Datum::prikaz($plan->datumOd()),
                'datum_do' => Datum::prikaz($plan->datumDo()),
                'predvidjen_broj_seansi' => $plan->predvidjenBrojSeansi(),
                'status' => $plan->status(),
                'nacin_naplate' => $plan->nacinNaplate(),
                'cena' => $plan->cena(),
                'napomena' => $plan->napomena() ?? '',
            ];
        }
        return [
            'id_plana' => '',
            'id_povrede' => '',
            'id_pacijenta' => '',
            'id_fizioterapeuta' => '',
            'id_dela_tela' => '',
            'tip_povrede' => '',
            'datum_povrede' => date('d/m/Y'),
            'tezina' => 'umerena',
            'opis_povrede' => '',
            'cilj' => '',
            'datum_od' => date('d/m/Y'),
            'datum_do' => '',
            'predvidjen_broj_seansi' => 8,
            'status' => 'aktivan',
            'nacin_naplate' => 'po_seansi',
            'cena' => (new PoslovnaPravila())->cenaSeanse(),
            'napomena' => '',
        ];
    }

    public function vezbe(): void
    {
        $korisnik = ProveraPristupa::zahtevajUlogu(['admin', 'fizioterapeut', 'pacijent']);
        $id = (int) ($_GET['id'] ?? 0);
        $planEnt = (new PlanRepozitorijum())->nadjiPoId($id);
        if ($planEnt === null) {
            header('Location: /planovi?poruka=' . rawurlencode('Plan nije pronađen.'));
            exit;
        }
        if ($korisnik['uloga'] === 'fizioterapeut'
            && $planEnt->idFizioterapeuta() !== (int) $korisnik['id_fizioterapeuta']) {
            http_response_code(403);
            $poruka = 'Nemate pravo na ovaj plan.';
            require dirname(__DIR__, 2) . '/prezentacija/stranice/zabranjeno.php';
            return;
        }
        if ($korisnik['uloga'] === 'pacijent'
            && $planEnt->idPacijenta() !== (int) $korisnik['id_pacijenta']) {
            http_response_code(403);
            $poruka = 'Nemate pravo na ovaj plan.';
            require dirname(__DIR__, 2) . '/prezentacija/stranice/zabranjeno.php';
            return;
        }
        $plan = [
            'id_plana' => $planEnt->idPlana(),
            'ime_pacijenta' => $planEnt->imePacijenta(),
            'prezime_pacijenta' => '',
            'tip_povrede' => $planEnt->tipPovrede(),
            'deo_tela' => $planEnt->deoTela(),
        ];
        $stavke = [];
        foreach ((new PlanVezbaRepozitorijum())->zaPlan($id) as $s) {
            $stavke[] = [
                'id_plan_vezba' => $s->idPlanVezba(),
                'naziv' => $s->nazivVezbe(),
                'deo_tela' => $s->nazivDelaTela(),
                'serije' => $s->serije(),
                'ponavljanja' => $s->ponavljanja(),
            ];
        }
        $katalog = [];
        foreach ((new VezbaRepozitorijum())->nadjiSve() as $v) {
            $katalog[] = ['id_vezbe' => $v->idVezbe(), 'naziv' => $v->naziv()];
        }
        $delovi = [];
        foreach ((new DeoTelaRepozitorijum())->svi() as $d) {
            $delovi[] = ['id_dela_tela' => $d->idDelaTela(), 'naziv' => $d->naziv(), 'grupa' => $d->grupa()];
        }
        $poruka = (string) ($_GET['poruka'] ?? '');
        $samoPregled = $korisnik['uloga'] === 'pacijent';
        $naslov = 'Vežbe u planu';
        require dirname(__DIR__, 2) . '/prezentacija/stranice/plan_vezbe.php';
    }
}
