<?php
namespace Kinetika\Aplikacija\Kontroleri;

use Kinetika\Aplikacija\ProfilSesije;
use Kinetika\Aplikacija\Sesija;
use Kinetika\Domen\PoslovnaLogika\PrijavaServis;
use Kinetika\Podaci\Repozitorijumi\KorisnikRepozitorijum;

/**
 * CRC: KontrolerPrijave
 * Odgovornost: prikaz i obrada login forme, odjava
 * Saradnici: PrijavaServis, Sesija, ProfilSesije
 */
class KontrolerPrijave
{
    public function prikazi(): void
    {
        if (Sesija::jePrijavljen()) {
            header('Location: /pocetna');
            exit;
        }
        $greska = $_GET['greska'] ?? '';
        require dirname(__DIR__, 2) . '/prezentacija/stranice/prijava.php';
    }

    public function obrada(): void
    {
        $email = trim((string) ($_POST['email'] ?? ''));
        $lozinka = (string) ($_POST['lozinka'] ?? '');

        $servis = new PrijavaServis(new KorisnikRepozitorijum());
        $rezultat = $servis->proveri($email, $lozinka);

        if (!$rezultat['ok']) {
            $greska = $rezultat['poruka'];
            require dirname(__DIR__, 2) . '/prezentacija/stranice/prijava.php';
            return;
        }

        $profil = ProfilSesije::sastavi($rezultat['korisnik']->idKorisnika());
        if ($profil === null) {
            $greska = 'Nalog nije moguće otvoriti.';
            require dirname(__DIR__, 2) . '/prezentacija/stranice/prijava.php';
            return;
        }

        Sesija::prijavi($profil);
        header('Location: /pocetna');
        exit;
    }

    public function odjava(): void
    {
        Sesija::odjavi();
        header('Location: /prijava');
        exit;
    }

    public function preusmeri(): void
    {
        if (Sesija::jePrijavljen()) {
            header('Location: /pocetna');
        } else {
            header('Location: /prijava');
        }
        exit;
    }
}
