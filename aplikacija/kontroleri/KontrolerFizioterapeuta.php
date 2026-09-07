<?php
namespace Kinetika\Aplikacija\Kontroleri;

use Kinetika\Aplikacija\ProveraPristupa;
use Kinetika\Domen\Dto\FizioterapeutDto;
use Kinetika\Domen\PoslovnaLogika\FizioterapeutServis;
use Kinetika\Domen\PoslovnaLogika\ValidatorUnosa;
use Kinetika\Podaci\Repozitorijumi\FizioterapeutRepozitorijum;

/**
 * CRC: KontrolerFizioterapeuta
 * Odgovornost: lista, unos, izmena i uklanjanje terapeuta
 * Saradnici: FizioterapeutServis, ProveraPristupa
 */
class KontrolerFizioterapeuta
{
    private function servis(): FizioterapeutServis
    {
        return new FizioterapeutServis(new FizioterapeutRepozitorijum(), new ValidatorUnosa());
    }

    public function lista(): void
    {
        $korisnik = ProveraPristupa::zahtevajUlogu(['admin']);
        $pretraga = trim((string) ($_GET['q'] ?? ''));
        $status = (string) ($_GET['status'] ?? '');
        $aktivan = $status === '1' ? 1 : ($status === '0' ? 0 : null);
        $repo = new FizioterapeutRepozitorijum();
        $lista = $repo->nadjiSve($pretraga !== '' ? $pretraga : null, $aktivan);
        $brojevi = [];
        foreach ($lista as $ft) {
            $brojevi[$ft->idFizioterapeuta()] = $repo->brojPacijenata($ft->idFizioterapeuta());
        }
        $poruka = (string) ($_GET['poruka'] ?? '');
        $naslov = 'Fizioterapeuti';
        require dirname(__DIR__, 2) . '/prezentacija/stranice/fizioterapeuti_lista.php';
    }

    public function forma(): void
    {
        $korisnik = ProveraPristupa::zahtevajUlogu(['admin']);
        $id = (int) ($_GET['id'] ?? 0);
        $zapis = $id ? $this->servis()->nadji($id) : null;
        if ($id && $zapis === null) {
            http_response_code(404);
            $poruka = 'Fizioterapeut nije pronađen.';
            require dirname(__DIR__, 2) . '/prezentacija/stranice/nije_pronadjeno.php';
            return;
        }
        $podaci = $zapis ? [
            'id_fizioterapeuta' => $zapis->idFizioterapeuta(),
            'ime' => $zapis->ime(),
            'prezime' => $zapis->prezime(),
            'email' => $zapis->email(),
            'telefon' => $zapis->telefon() ?? '',
            'aktivan' => $zapis->jeAktivan() ? '1' : '0',
        ] : [
            'id_fizioterapeuta' => 0,
            'ime' => '',
            'prezime' => '',
            'email' => '',
            'telefon' => '',
            'aktivan' => '1',
        ];
        $greske = [];
        $naslov = $zapis ? 'Izmena terapeuta' : 'Novi fizioterapeut';
        require dirname(__DIR__, 2) . '/prezentacija/stranice/fizioterapeut_forma.php';
    }

    public function sacuvaj(): void
    {
        ProveraPristupa::zahtevajUlogu(['admin']);
        $dto = FizioterapeutDto::izNiza($_POST);
        $rez = $this->servis()->sacuvaj($dto);
        if (!$rez['ok']) {
            $korisnik = ProveraPristupa::zahtevajUlogu(['admin']);
            $greske = $rez['greske'];
            $podaci = [
                'id_fizioterapeuta' => $dto->idFizioterapeuta ?? 0,
                'ime' => $dto->ime,
                'prezime' => $dto->prezime,
                'email' => $dto->email,
                'telefon' => $dto->telefon ?? '',
                'aktivan' => $dto->aktivan ? '1' : '0',
            ];
            $zapis = $dto->idFizioterapeuta ? $this->servis()->nadji($dto->idFizioterapeuta) : null;
            $naslov = $dto->idFizioterapeuta ? 'Izmena terapeuta' : 'Novi fizioterapeut';
            require dirname(__DIR__, 2) . '/prezentacija/stranice/fizioterapeut_forma.php';
            return;
        }
        header('Location: /fizioterapeuti?poruka=' . rawurlencode('Podaci su sačuvani.'));
        exit;
    }

    public function obrisi(): void
    {
        ProveraPristupa::zahtevajUlogu(['admin']);
        $id = (int) ($_POST['id'] ?? $_POST['id_fizioterapeuta'] ?? 0);
        $rez = $this->servis()->obrisi($id);
        header('Location: /fizioterapeuti?poruka=' . rawurlencode($rez['poruka'] ?? 'Gotovo.'));
        exit;
    }
}
