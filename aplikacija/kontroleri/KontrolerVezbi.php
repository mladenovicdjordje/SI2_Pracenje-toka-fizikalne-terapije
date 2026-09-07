<?php
namespace Kinetika\Aplikacija\Kontroleri;

use Kinetika\Aplikacija\ProveraPristupa;
use Kinetika\Domen\Dto\VezbaDto;
use Kinetika\Domen\PoslovnaLogika\ValidatorUnosa;
use Kinetika\Domen\PoslovnaLogika\VezbaServis;
use Kinetika\Podaci\Repozitorijumi\VezbaRepozitorijum;

class KontrolerVezbi
{
    private VezbaServis $servis;

    public function __construct()
    {
        $this->servis = new VezbaServis(new VezbaRepozitorijum(), new ValidatorUnosa());
    }

    public function lista(): void
    {
        $korisnik = ProveraPristupa::zahtevajUlogu(['admin', 'fizioterapeut']);
        $pretraga = trim((string) ($_GET['q'] ?? ''));
        $grupa = trim((string) ($_GET['grupa'] ?? ''));
        $lista = $this->servis->lista($pretraga !== '' ? $pretraga : null, $grupa !== '' ? $grupa : null);
        $grupe = $this->servis->grupe();
        $poruka = (string) ($_GET['poruka'] ?? '');
        $naslov = 'Katalog vežbi';
        require dirname(__DIR__, 2) . '/prezentacija/stranice/vezbe_lista.php';
    }

    public function forma(): void
    {
        $korisnik = ProveraPristupa::zahtevajUlogu(['admin', 'fizioterapeut']);
        $id = (int) ($_GET['id'] ?? 0);
        $zapis = $id ? $this->servis->nadji($id) : null;
        if ($id && $zapis === null) {
            header('Location: /katalog-vezbi');
            exit;
        }
        $greske = [];
        $podaci = $zapis ? [
            'id_vezbe' => $zapis->idVezbe(),
            'naziv' => $zapis->naziv(),
            'grupa_misica' => $zapis->grupaMisica(),
            'opis' => $zapis->opis() ?? '',
            'kontraindikacije' => $zapis->kontraindikacije() ?? '',
            'aktivna' => $zapis->jeAktivna() ? '1' : '0',
        ] : ['naziv' => '', 'grupa_misica' => '', 'opis' => '', 'kontraindikacije' => '', 'aktivna' => '1'];
        $naslov = $zapis ? 'Izmena vežbe' : 'Nova vežba';
        require dirname(__DIR__, 2) . '/prezentacija/stranice/vezba_forma.php';
    }

    public function sacuvaj(): void
    {
        $korisnik = ProveraPristupa::zahtevajUlogu(['admin', 'fizioterapeut']);
        $dto = VezbaDto::izNiza($_POST);
        $rez = $this->servis->sacuvaj($dto, (int) $korisnik['id_korisnika']);
        if (!$rez['ok']) {
            $greske = $rez['greske'];
            $podaci = $_POST;
            $naslov = $dto->idVezbe ? 'Izmena vežbe' : 'Nova vežba';
            $zapis = $dto->idVezbe ? $this->servis->nadji($dto->idVezbe) : null;
            require dirname(__DIR__, 2) . '/prezentacija/stranice/vezba_forma.php';
            return;
        }
        header('Location: /katalog-vezbi?poruka=' . urlencode('Vežba je sačuvana.'));
        exit;
    }

    public function obrisi(): void
    {
        ProveraPristupa::zahtevajUlogu(['admin', 'fizioterapeut']);
        $id = (int) ($_POST['id'] ?? 0);
        $rez = $this->servis->obrisi($id);
        header('Location: /katalog-vezbi?poruka=' . urlencode($rez['poruka'] ?? 'Gotovo.'));
        exit;
    }
}
