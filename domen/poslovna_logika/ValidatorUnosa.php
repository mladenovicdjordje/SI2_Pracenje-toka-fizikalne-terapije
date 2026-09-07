<?php
namespace Kinetika\Domen\PoslovnaLogika;

/**
 * CRC: ValidatorUnosa
 * Odgovornost: osnovne validacije potpunosti, formata i jedinstvenosti
 * Saradnici: servisi unosa
 *
 * Polimorfizam kroz overloading: validirajEmail($email) i
 * validirajEmail($email, $obavezno) koriste razlicit broj argumenata.
 */
class ValidatorUnosa
{
    public function validirajEmail(string $email, bool $obavezno = true): ?string
    {
        if ($email === '') {
            return $obavezno ? 'E-pošta je obavezna.' : null;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'E-pošta nije ispravna.';
        }
        return null;
    }

    public function validirajLozinku(?string $lozinka, bool $obavezno = true): ?string
    {
        if ($lozinka === null || $lozinka === '') {
            return $obavezno ? 'Lozinka je obavezna.' : null;
        }
        if (mb_strlen($lozinka) < 8) {
            return 'Lozinka mora imati bar 8 karaktera.';
        }
        return null;
    }

    public function obavezno(string $vrednost, string $nazivPolja): ?string
    {
        if (trim($vrednost) === '') {
            return $nazivPolja . ' je obavezno polje.';
        }
        return null;
    }
}
