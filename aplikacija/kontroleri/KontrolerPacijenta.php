<?php
namespace Kinetika\Aplikacija\Kontroleri;

use Kinetika\Aplikacija\ProveraPristupa;
use Kinetika\Domen\Dto\PacijentDto;
use Kinetika\Domen\PoslovnaLogika\PacijentServis;
use Kinetika\Domen\PoslovnaLogika\ValidatorUnosa;
use Kinetika\Podaci\Repozitorijumi\FizioterapeutRepozitorijum;
use Kinetika\Podaci\Repozitorijumi\PacijentRepozitorijum;

/**
 * CRC: KontrolerPacijenta
 * Odgovornost: liste i forme pacijenata za admina i terapeuta
 * Saradnici: PacijentServis, ProveraPristupa
 */
class KontrolerPacijenta
{
    private PacijentServis $servis;
    private FizioterapeutRepozitorijum $terapeuti;

    public function __construct()
    {
        $this->terapeuti = new FizioterapeutRepozitorijum();
        $this->servis = new PacijentServis(new PacijentRepozitorijum(), $this->terapeuti, new ValidatorUnosa());
    }

    public function lista(): void
    {
        $korisnik = ProveraPristupa::zahtevajUlogu(['admin', 'fizioterapeut']);
        $pretraga = trim((string) ($_GET['q'] ?? ''));
        $status = $_GET['status'] ?? '';
        $aktivan = $status === '1' ? 1 : ($status === '0' ? 0 : null);
        $ftFilter = $korisnik['uloga'] === 'fizioterapeut'
            ? (int) $korisnik['id_fizioterapeuta']
            : ((int) ($_GET['ft'] ?? 0) ?: null);
        $lista = $this->servis->lista($pretraga !== '' ? $pretraga : null, $ftFilter, $aktivan);
        $terapeuti = $korisnik['uloga'] === 'admin' ? $this->terapeuti->nadjiSve() : [];
        $poruka = (string) ($_GET['poruka'] ?? '');
        $naslov = 'Pacijenti';
        require dirname(__DIR__, 2) . '/prezentacija/stranice/pacijenti_lista.php';
    }

    public function forma(): void
    {
        $korisnik = ProveraPristupa::zahtevajUlogu(['admin', 'fizioterapeut']);
        $id = (int) ($_GET['id'] ?? 0);
        $zapis = $id ? $this->servis->nadji($id) : null;
        if ($id && $zapis === null) {
            header('Location: /pacijenti');
            exit;
        }
        if ($zapis && $korisnik['uloga'] === 'fizioterapeut'
            && $zapis->idFizioterapeuta() !== (int) $korisnik['id_fizioterapeuta']) {
            header('Location: /zabranjeno');
            http_response_code(403);
            $poruka = 'Nemate pravo da otvorite ovog pacijenta.';
            require dirname(__DIR__, 2) . '/prezentacija/stranice/zabranjeno.php';
            exit;
        }
        $greske = [];
        $podaci = $zapis ? [
            'id_pacijenta' => $zapis->idPacijenta(),
            'ime' => $zapis->ime(),
            'prezime' => $zapis->prezime(),
            'email' => $zapis->email(),
            'telefon' => $zapis->telefon() ?? '',
            'id_fizioterapeuta' => $zapis->idFizioterapeuta(),
            'aktivan' => $zapis->jeAktivan() ? '1' : '0',
        ] : [
            'ime' => '', 'prezime' => '', 'email' => '', 'telefon' => '',
            'id_fizioterapeuta' => $korisnik['uloga'] === 'fizioterapeut'
                ? (int) $korisnik['id_fizioterapeuta'] : 0,
            'aktivan' => '1',
        ];
        $terapeuti = $this->terapeuti->nadjiSve(null, 1);
        $naslov = $zapis ? 'Izmena pacijenta' : 'Novi pacijent';
        require dirname(__DIR__, 2) . '/prezentacija/stranice/pacijent_forma.php';
    }

    public function sacuvaj(): void
    {
        $korisnik = ProveraPristupa::zahtevajUlogu(['admin', 'fizioterapeut']);
        $ulaz = $_POST;
        if ($korisnik['uloga'] === 'fizioterapeut') {
            $ulaz['id_fizioterapeuta'] = (int) $korisnik['id_fizioterapeuta'];
        }
        $dto = PacijentDto::izNiza($ulaz);
        if ($dto->idPacijenta && $korisnik['uloga'] === 'fizioterapeut') {
            $postojeci = $this->servis->nadji($dto->idPacijenta);
            if (!$postojeci || $postojeci->idFizioterapeuta() !== (int) $korisnik['id_fizioterapeuta']) {
                http_response_code(403);
                $poruka = 'Nemate pravo da menjate ovog pacijenta.';
                require dirname(__DIR__, 2) . '/prezentacija/stranice/zabranjeno.php';
                exit;
            }
        }
        $rez = $this->servis->sacuvaj($dto);
        if (!$rez['ok']) {
            $greske = $rez['greske'];
            $podaci = $ulaz;
            $terapeuti = $this->terapeuti->nadjiSve(null, 1);
            $naslov = $dto->idPacijenta ? 'Izmena pacijenta' : 'Novi pacijent';
            $zapis = $dto->idPacijenta ? $this->servis->nadji($dto->idPacijenta) : null;
            require dirname(__DIR__, 2) . '/prezentacija/stranice/pacijent_forma.php';
            return;
        }
        header('Location: /pacijenti?poruka=' . urlencode('Podaci su sacuvani.'));
        exit;
    }

    public function obrisi(): void
    {
        $korisnik = ProveraPristupa::zahtevajUlogu(['admin', 'fizioterapeut']);
        $id = (int) ($_POST['id'] ?? 0);
        $postojeci = $this->servis->nadji($id);
        if ($korisnik['uloga'] === 'fizioterapeut' && $postojeci
            && $postojeci->idFizioterapeuta() !== (int) $korisnik['id_fizioterapeuta']) {
            http_response_code(403);
            $poruka = 'Nemate pravo da obrisete ovog pacijenta.';
            require dirname(__DIR__, 2) . '/prezentacija/stranice/zabranjeno.php';
            exit;
        }
        $rez = $this->servis->obrisi($id);
        header('Location: /pacijenti?poruka=' . urlencode($rez['poruka'] ?? 'Gotovo.'));
        exit;
    }
}
