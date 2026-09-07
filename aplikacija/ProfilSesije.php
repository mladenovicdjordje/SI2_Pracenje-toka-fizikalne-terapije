<?php
namespace Kinetika\Aplikacija;

use Kinetika\Podaci\Konekcija;
use PDO;

/**
 * CRC: ProfilSesije
 * Odgovornost: dopunjuje sesiju imenom, ulogom i vezama (FT / pacijent)
 * Saradnici: Sesija, Konekcija, KontrolerPrijave
 */
class ProfilSesije
{
    public static function sastavi(int $idKorisnika): ?array
    {
        $baza = Konekcija::uzmi();
        $upit = $baza->prepare(
            'SELECT id_korisnika, email, uloga, aktivan FROM korisnik WHERE id_korisnika = :id LIMIT 1'
        );
        $upit->execute(['id' => $idKorisnika]);
        $korisnik = $upit->fetch(PDO::FETCH_ASSOC);
        if (!$korisnik || !(int) $korisnik['aktivan']) {
            return null;
        }

        $korisnik['ime'] = 'Kinetika';
        $korisnik['prezime'] = '';
        $korisnik['id_fizioterapeuta'] = null;
        $korisnik['id_pacijenta'] = null;
        $korisnik['prikaz_ime'] = 'Administrator';

        if ($korisnik['uloga'] === 'fizioterapeut') {
            $ft = $baza->prepare(
                'SELECT id_fizioterapeuta, ime, prezime FROM fizioterapeut WHERE id_korisnika = :id LIMIT 1'
            );
            $ft->execute(['id' => $idKorisnika]);
            $red = $ft->fetch(PDO::FETCH_ASSOC);
            if ($red) {
                $korisnik['id_fizioterapeuta'] = (int) $red['id_fizioterapeuta'];
                $korisnik['ime'] = $red['ime'];
                $korisnik['prezime'] = $red['prezime'];
                $korisnik['prikaz_ime'] = trim($red['ime'] . ' ' . $red['prezime']);
            }
        }

        if ($korisnik['uloga'] === 'pacijent') {
            $pac = $baza->prepare(
                'SELECT id_pacijenta, id_fizioterapeuta, ime, prezime FROM pacijent WHERE id_korisnika = :id LIMIT 1'
            );
            $pac->execute(['id' => $idKorisnika]);
            $red = $pac->fetch(PDO::FETCH_ASSOC);
            if ($red) {
                $korisnik['id_pacijenta'] = (int) $red['id_pacijenta'];
                $korisnik['id_fizioterapeuta'] = (int) $red['id_fizioterapeuta'];
                $korisnik['ime'] = $red['ime'];
                $korisnik['prezime'] = $red['prezime'];
                $korisnik['prikaz_ime'] = trim($red['ime'] . ' ' . $red['prezime']);
            }
        }

        if ($korisnik['uloga'] === 'admin') {
            $korisnik['prikaz_ime'] = 'Uprava ordinacije';
        }

        return $korisnik;
    }
}
