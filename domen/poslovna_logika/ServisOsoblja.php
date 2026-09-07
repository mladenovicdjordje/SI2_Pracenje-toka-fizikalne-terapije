<?php
namespace Kinetika\Domen\PoslovnaLogika;

use Kinetika\Domen\Dto\OsobaDto;
use Kinetika\Domen\Interfejsi\IFizioterapeutRepozitorijum;
use Kinetika\Domen\Interfejsi\IKorisnikRepozitorijum;
use Kinetika\Domen\Interfejsi\IPacijentRepozitorijum;
use InvalidArgumentException;
use RuntimeException;

/**
 * CRC: ServisOsoblja
 * Odgovornost: validacija i transakcioni unos terapeuta i pacijenata
 * Saradnici: IKorisnikRepozitorijum, IFizioterapeutRepozitorijum, IPacijentRepozitorijum
 */
class ServisOsoblja
{
    public function __construct(
        private IKorisnikRepozitorijum $korisnici,
        private IFizioterapeutRepozitorijum $terapeuti,
        private IPacijentRepozitorijum $pacijenti
    ) {
    }

    public function validiraj(OsobaDto $osoba, bool $noviNalog): array
    {
        $greske = [];
        if (trim($osoba->ime) === '') {
            $greske[] = 'Ime je obavezno.';
        }
        if (trim($osoba->prezime) === '') {
            $greske[] = 'Prezime je obavezno.';
        }
        $email = trim(mb_strtolower($osoba->email));
        if ($email === '') {
            $greske[] = 'E-pošta je obavezna.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $greske[] = 'E-pošta nije ispravna.';
        } elseif ($this->korisnici->emailZauzet($email, $osoba->idKorisnika)) {
            $greske[] = 'E-pošta je već zauzeta.';
        }

        if ($noviNalog) {
            if ($osoba->lozinka === null || $osoba->lozinka === '') {
                $greske[] = 'Lozinka je obavezna.';
            } elseif (mb_strlen($osoba->lozinka) < 8) {
                $greske[] = 'Lozinka mora imati bar 8 karaktera.';
            }
        } elseif ($osoba->lozinka !== null && $osoba->lozinka !== '' && mb_strlen($osoba->lozinka) < 8) {
            $greske[] = 'Nova lozinka mora imati bar 8 karaktera.';
        }

        return $greske;
    }

    public function sacuvajTerapeuta(OsobaDto $osoba): void
    {
        $greske = $this->validiraj($osoba, $osoba->idKartona === null);
        if ($greske) {
            throw new InvalidArgumentException(implode(' ', $greske));
        }

        $podaci = [
            'ime' => trim($osoba->ime),
            'prezime' => trim($osoba->prezime),
            'email' => trim(mb_strtolower($osoba->email)),
            'telefon' => $this->ocistiTelefon($osoba->telefon),
            'lozinka_hash' => ($osoba->lozinka !== null && $osoba->lozinka !== '')
                ? password_hash($osoba->lozinka, PASSWORD_DEFAULT)
                : null,
            'aktivan' => $osoba->aktivan,
        ];

        if ($osoba->idKartona === null) {
            $this->terapeuti->sacuvajNovog($podaci);
            return;
        }
        $this->terapeuti->izmeni($osoba->idKartona, $podaci);
    }

    public function sacuvajPacijenta(OsobaDto $osoba): void
    {
        $greske = $this->validiraj($osoba, $osoba->idKartona === null);
        if ($osoba->idFizioterapeuta === null || $this->terapeuti->nadjiPoId($osoba->idFizioterapeuta) === null) {
            $greske[] = 'Izaberite fizioterapeuta.';
        }
        if ($greske) {
            throw new InvalidArgumentException(implode(' ', $greske));
        }

        $podaci = [
            'ime' => trim($osoba->ime),
            'prezime' => trim($osoba->prezime),
            'email' => trim(mb_strtolower($osoba->email)),
            'telefon' => $this->ocistiTelefon($osoba->telefon),
            'id_fizioterapeuta' => (int) $osoba->idFizioterapeuta,
            'lozinka_hash' => ($osoba->lozinka !== null && $osoba->lozinka !== '')
                ? password_hash($osoba->lozinka, PASSWORD_DEFAULT)
                : null,
            'aktivan' => $osoba->aktivan,
        ];

        if ($osoba->idKartona === null) {
            $this->pacijenti->sacuvajNovog($podaci);
            return;
        }
        $this->pacijenti->izmeni($osoba->idKartona, $podaci);
    }

    public function ukloniTerapeuta(int $id): string
    {
        $ft = $this->terapeuti->nadjiPoId($id);
        if ($ft === null) {
            throw new RuntimeException('Fizioterapeut ne postoji.');
        }
        if ($this->terapeuti->imaPacijente($id)) {
            $this->terapeuti->deaktiviraj($id);
            return 'Terapeut ima pacijente, nalog je deaktiviran.';
        }
        $this->terapeuti->obrisi($id);
        return 'Fizioterapeut je obrisan.';
    }

    public function ukloniPacijenta(int $id): string
    {
        $pac = $this->pacijenti->nadjiPoId($id);
        if ($pac === null) {
            throw new RuntimeException('Pacijent ne postoji.');
        }
        if ($this->pacijenti->imaIstoriju($id)) {
            $this->pacijenti->deaktiviraj($id);
            return 'Pacijent ima istoriju, nalog je deaktiviran.';
        }
        $this->pacijenti->obrisi($id);
        return 'Pacijent je obrisan.';
    }

    private function ocistiTelefon(?string $telefon): ?string
    {
        $telefon = trim((string) $telefon);
        return $telefon === '' ? null : $telefon;
    }
}
