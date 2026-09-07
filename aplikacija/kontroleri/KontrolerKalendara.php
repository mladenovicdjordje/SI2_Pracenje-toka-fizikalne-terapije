<?php
namespace Kinetika\Aplikacija\Kontroleri;

use Kinetika\Aplikacija\ProveraPristupa;
use Kinetika\Domen\Dto\SeansaDto;
use Kinetika\Domen\PoslovnaLogika\KalendarServis;
use Kinetika\Domen\PoslovnaLogika\PoslovnaPravila;
use Kinetika\Domen\PoslovnaLogika\SeansaServis;
use Kinetika\Podaci\Repozitorijumi\FizioterapeutRepozitorijum;
use Kinetika\Podaci\Repozitorijumi\PacijentRepozitorijum;
use Kinetika\Podaci\Repozitorijumi\PlanRepozitorijum;
use Kinetika\Podaci\Repozitorijumi\SeansaRepozitorijum;

/**
 * CRC: KontrolerKalendara
 * Odgovornost: nedeljni kalendar, zakazivanje, zahtev, potvrda, otkaz
 * Saradnici: SeansaServis, KalendarServis, ProveraPristupa
 */
class KontrolerKalendara
{
    private SeansaServis $servis;
    private KalendarServis $kalendar;
    private SeansaRepozitorijum $seanse;
    private PlanRepozitorijum $planovi;
    private PacijentRepozitorijum $pacijenti;
    private FizioterapeutRepozitorijum $terapeuti;

    public function __construct()
    {
        $pravila = new PoslovnaPravila();
        $this->kalendar = new KalendarServis($pravila);
        $this->seanse = new SeansaRepozitorijum();
        $this->planovi = new PlanRepozitorijum();
        $this->pacijenti = new PacijentRepozitorijum();
        $this->terapeuti = new FizioterapeutRepozitorijum();
        $this->servis = new SeansaServis($this->seanse, $this->planovi, $this->kalendar, $pravila);
    }

    private function kaoDatum(mixed $vrednost): string
    {
        if ($vrednost instanceof \DateTime) {
            return $vrednost->format('Y-m-d');
        }
        return (string) $vrednost;
    }

    public function prikazi(): void
    {
        $korisnik = ProveraPristupa::zahtevajUlogu(['admin', 'fizioterapeut', 'pacijent']);
        $pon = $this->kaoDatum($this->kalendar->ponedeljakNedelje((string) ($_GET['nedelja'] ?? date('Y-m-d'))));
        $dani = $this->kalendar->daniNedelje($pon);
        $slotovi = $this->kalendar->slotoviDana();
        $idFt = $this->idFtPrikaza($korisnik);
        $idPacFilter = $korisnik['uloga'] === 'pacijent' ? (int) $korisnik['id_pacijenta'] : null;
        $kraj = (new \DateTime(end($dani)))->modify('+1 day')->format('Y-m-d');
        $seanse = $this->seanse->zaOpseg($pon, $kraj, $idFt, $idPacFilter);

        $mapa = [];
        foreach ($seanse as $s) {
            $kljuc = $s->datum() . '|' . $s->sat();
            $mapa[$kljuc][] = $s;
        }

        $terapeuti = $korisnik['uloga'] === 'admin' ? $this->terapeuti->nadjiSve(null, 1) : [];
        $poruka = (string) ($_GET['poruka'] ?? '');
        $naslov = 'Kalendar';
        $prethodna = (new \DateTime($pon))->modify('-7 days')->format('Y-m-d');
        $sledeca = (new \DateTime($pon))->modify('+7 days')->format('Y-m-d');
        require dirname(__DIR__, 2) . '/prezentacija/stranice/kalendar.php';
    }

    public function forma(): void
    {
        $korisnik = ProveraPristupa::zahtevajUlogu(['admin', 'fizioterapeut', 'pacijent']);
        $datum = (string) ($_GET['datum'] ?? '');
        $sat = (string) ($_GET['sat'] ?? '');
        if ($datum === '' || $sat === '') {
            header('Location: /kalendar');
            exit;
        }
        $idFt = $this->idFtPrikaza($korisnik);
        $pacijenti = [];
        $planovi = [];
        if ($korisnik['uloga'] === 'pacijent') {
            $planovi = $this->planovi->filtriraj((int) $korisnik['id_pacijenta'], null, 'aktivan', '');
        } else {
            $pacijenti = $korisnik['uloga'] === 'fizioterapeut'
                ? $this->pacijenti->nadjiSve(null, (int) $korisnik['id_fizioterapeuta'], 1)
                : $this->pacijenti->nadjiSve(null, $idFt, 1);
            $idPac = (int) ($_GET['pac'] ?? 0);
            if ($idPac) {
                $planovi = $this->planovi->filtriraj($idPac, $idFt, 'aktivan', '');
            }
        }
        $greske = [];
        $naslov = $korisnik['uloga'] === 'pacijent' ? 'Zahtev za termin' : 'Zakazivanje seanse';
        require dirname(__DIR__, 2) . '/prezentacija/stranice/seansa_forma.php';
    }

    public function sacuvaj(): void
    {
        $korisnik = ProveraPristupa::zahtevajUlogu(['admin', 'fizioterapeut', 'pacijent']);
        $ulaz = $_POST;
        if ($korisnik['uloga'] === 'pacijent') {
            $ulaz['id_pacijenta'] = (int) $korisnik['id_pacijenta'];
            $ulaz['id_fizioterapeuta'] = (int) $korisnik['id_fizioterapeuta'];
            $dto = SeansaDto::izNiza($ulaz);
            $rez = $this->servis->zahtevaj($dto);
        } else {
            if ($korisnik['uloga'] === 'fizioterapeut') {
                $ulaz['id_fizioterapeuta'] = (int) $korisnik['id_fizioterapeuta'];
            }
            $dto = SeansaDto::izNiza($ulaz);
            $rez = $this->servis->zakazi($dto);
        }
        if (!$rez['ok']) {
            $datum = (string) ($ulaz['datum'] ?? '');
            $sat = (string) ($ulaz['sat'] ?? '');
            $idFt = (int) ($ulaz['id_fizioterapeuta'] ?? 0);
            $pacijenti = $korisnik['uloga'] === 'pacijent' ? [] : (
                $korisnik['uloga'] === 'fizioterapeut'
                    ? $this->pacijenti->nadjiSve(null, (int) $korisnik['id_fizioterapeuta'], 1)
                    : $this->pacijenti->nadjiSve(null, $idFt, 1)
            );
            $planovi = $this->planovi->filtriraj((int) ($ulaz['id_pacijenta'] ?? 0) ?: null, $idFt ?: null, 'aktivan', '');
            $greske = $rez['greske'];
            $naslov = $korisnik['uloga'] === 'pacijent' ? 'Zahtev za termin' : 'Zakazivanje seanse';
            require dirname(__DIR__, 2) . '/prezentacija/stranice/seansa_forma.php';
            return;
        }
        $pon = $this->kaoDatum($this->kalendar->ponedeljakNedelje((string) ($ulaz['datum'] ?? date('Y-m-d'))));
        header('Location: /kalendar?nedelja=' . $pon . '&poruka=' . rawurlencode($rez['poruka']));
        exit;
    }

    public function potvrdi(): void
    {
        $korisnik = ProveraPristupa::zahtevajUlogu(['admin', 'fizioterapeut']);
        $id = (int) ($_POST['id'] ?? 0);
        $seansa = $this->seanse->nadjiPoId($id);
        if ($seansa && $korisnik['uloga'] === 'fizioterapeut'
            && $seansa->idFizioterapeuta() !== (int) $korisnik['id_fizioterapeuta']) {
            http_response_code(403);
            $poruka = 'Nemate pravo na ovaj termin.';
            require dirname(__DIR__, 2) . '/prezentacija/stranice/zabranjeno.php';
            return;
        }
        $rez = $this->servis->potvrdi($id);
        $pon = $seansa ? $this->kaoDatum($this->kalendar->ponedeljakNedelje($seansa->datum())) : date('Y-m-d');
        header('Location: /kalendar?nedelja=' . $pon . '&poruka=' . rawurlencode($rez['poruka']));
        exit;
    }

    public function otkazi(): void
    {
        $korisnik = ProveraPristupa::zahtevajUlogu(['admin', 'fizioterapeut', 'pacijent']);
        $id = (int) ($_POST['id'] ?? 0);
        $seansa = $this->seanse->nadjiPoId($id);
        if ($seansa === null) {
            header('Location: /kalendar?poruka=' . rawurlencode('Termin ne postoji.'));
            exit;
        }
        if ($korisnik['uloga'] === 'fizioterapeut' && $seansa->idFizioterapeuta() !== (int) $korisnik['id_fizioterapeuta']) {
            http_response_code(403);
            $poruka = 'Nemate pravo na ovaj termin.';
            require dirname(__DIR__, 2) . '/prezentacija/stranice/zabranjeno.php';
            return;
        }
        if ($korisnik['uloga'] === 'pacijent' && $seansa->idPacijenta() !== (int) $korisnik['id_pacijenta']) {
            http_response_code(403);
            $poruka = 'Nemate pravo na ovaj termin.';
            require dirname(__DIR__, 2) . '/prezentacija/stranice/zabranjeno.php';
            return;
        }
        $rez = $this->servis->otkazi($id, $korisnik['uloga']);
        $pon = $this->kaoDatum($this->kalendar->ponedeljakNedelje($seansa->datum()));
        header('Location: /kalendar?nedelja=' . $pon . '&poruka=' . rawurlencode($rez['poruka'] ?? ''));

        exit;
    }

    public function odrzana(): void
    {
        $korisnik = ProveraPristupa::zahtevajUlogu(['admin', 'fizioterapeut']);
        $id = (int) ($_POST['id'] ?? 0);
        $seansa = $this->seanse->nadjiPoId($id);
        if ($seansa && $korisnik['uloga'] === 'fizioterapeut'
            && $seansa->idFizioterapeuta() !== (int) $korisnik['id_fizioterapeuta']) {
            http_response_code(403);
            $poruka = 'Nemate pravo na ovaj termin.';
            require dirname(__DIR__, 2) . '/prezentacija/stranice/zabranjeno.php';
            return;
        }
        $bol = isset($_POST['nivo_bola']) && $_POST['nivo_bola'] !== '' ? (int) $_POST['nivo_bola'] : null;
        $rez = $this->servis->oznaciOdrzanu($id, $bol, $korisnik['uloga']);
        $pon = $seansa ? $this->kaoDatum($this->kalendar->ponedeljakNedelje($seansa->datum())) : date('Y-m-d');
        header('Location: /kalendar?nedelja=' . $pon . '&poruka=' . rawurlencode($rez['poruka']));
        exit;
    }

    private function idFtPrikaza(array $korisnik): ?int
    {
        if ($korisnik['uloga'] === 'fizioterapeut' || $korisnik['uloga'] === 'pacijent') {
            return (int) $korisnik['id_fizioterapeuta'];
        }
        $izbor = (int) ($_GET['ft'] ?? $_POST['id_fizioterapeuta'] ?? 0);
        return $izbor > 0 ? $izbor : null;
    }
}