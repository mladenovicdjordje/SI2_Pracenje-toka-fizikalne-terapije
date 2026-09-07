<?php
namespace Kinetika\Domen\Entiteti;

/**
 * CRC: Pacijent
 * Odgovornost: karton pacijenta i veza sa terapeutom
 * Saradnici: PacijentRepozitorijum, PacijentServis
 */
class Pacijent
{
    public function __construct(
        private int $idPacijenta,
        private int $idKorisnika,
        private int $idFizioterapeuta,
        private string $ime,
        private string $prezime,
        private ?string $telefon,
        private string $email,
        private bool $aktivan,
        private string $imeTerapeuta = ''
    ) {
    }

    public function idPacijenta(): int { return $this->idPacijenta; }
    public function idKorisnika(): int { return $this->idKorisnika; }
    public function idFizioterapeuta(): int { return $this->idFizioterapeuta; }
    public function ime(): string { return $this->ime; }
    public function prezime(): string { return $this->prezime; }
    public function punoIme(): string { return trim($this->ime . ' ' . $this->prezime); }
    public function telefon(): ?string { return $this->telefon; }
    public function email(): string { return $this->email; }
    public function jeAktivan(): bool { return $this->aktivan; }
    public function aktivan(): bool { return $this->aktivan; }
    public function imeTerapeuta(): string { return $this->imeTerapeuta; }
}
