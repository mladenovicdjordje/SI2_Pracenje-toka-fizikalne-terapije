<?php
namespace Kinetika\Domen\PoslovnaLogika;

use Kinetika\Domen\Interfejsi\IKorisnikRepozitorijum;

/**
 * CRC: PrijavaServis
 * Odgovornost: provera emaila, lozinke i aktivnosti naloga
 * Saradnici: IKorisnikRepozitorijum, KontrolerPrijave
 */
class PrijavaServis
{
    private IKorisnikRepozitorijum $korisnici;

    public function __construct(IKorisnikRepozitorijum $korisnici)
    {
        $this->korisnici = $korisnici;
    }

    public function proveri(string $email, string $lozinka): array
    {
        $email = trim(mb_strtolower($email));
        if ($email === '' || $lozinka === '') {
            return ['ok' => false, 'poruka' => 'Unesite e-poštu i lozinku.'];
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['ok' => false, 'poruka' => 'E-pošta nije ispravna.'];
        }

        $korisnik = $this->korisnici->nadjiPoEmailu($email);
        if ($korisnik === null || !$korisnik->lozinkaOdgovara($lozinka)) {
            return ['ok' => false, 'poruka' => 'Pogrešna e-pošta ili lozinka.'];
        }
        if (!$korisnik->jeAktivan()) {
            return ['ok' => false, 'poruka' => 'Nalog je deaktiviran.'];
        }

        return [
            'ok' => true,
            'korisnik' => $korisnik,
        ];
    }
}
