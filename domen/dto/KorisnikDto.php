<?php
namespace Kinetika\Domen\Dto;

/**
 * CRC: KorisnikDto
 * Odgovornost: prenosi podatke korisnika između slojeva bez lozinke
 * Saradnici: KorisnikMapper, prezentacija
 */
class KorisnikDto
{
    public int $idKorisnika;
    public string $email;
    public string $uloga;

    public function __construct(int $idKorisnika, string $email, string $uloga)
    {
        $this->idKorisnika = $idKorisnika;
        $this->email = $email;
        $this->uloga = $uloga;
    }
}
