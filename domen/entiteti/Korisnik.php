<?php
namespace Kinetika\Domen\Entiteti;

/**
 * CRC: Korisnik
 * Odgovornost: predstavlja nalog za prijavu (enkapsulacija podataka korisnika)
 * Saradnici: KorisnikRepozitorijum, Sesija
 */
class Korisnik
{
    private int $idKorisnika;
    private string $email;
    private string $lozinkaHash;
    private string $uloga;
    private bool $aktivan;

    public function __construct(
        int $idKorisnika,
        string $email,
        string $lozinkaHash,
        string $uloga,
        bool $aktivan
    ) {
        $this->idKorisnika = $idKorisnika;
        $this->email = $email;
        $this->lozinkaHash = $lozinkaHash;
        $this->uloga = $uloga;
        $this->aktivan = $aktivan;
    }

    public function idKorisnika(): int
    {
        return $this->idKorisnika;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function uloga(): string
    {
        return $this->uloga;
    }

    public function jeAktivan(): bool
    {
        return $this->aktivan;
    }

    public function lozinkaOdgovara(string $lozinka): bool
    {
        return password_verify($lozinka, $this->lozinkaHash);
    }
}
