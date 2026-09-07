<?php
namespace Kinetika\Domen\Entiteti;

/**
 * CRC: Fizioterapeut
 * Odgovornost: karton terapeuta i veza sa nalogom
 * Saradnici: FizioterapeutRepozitorijum, FizioterapeutServis
 */
class Fizioterapeut
{
    public function __construct(
        private int $idFizioterapeuta,
        private int $idKorisnika,
        private string $ime,
        private string $prezime,
        private ?string $telefon,
        private string $email,
        private bool $aktivan,
    ) {
    }

    public function idFizioterapeuta(): int { return $this->idFizioterapeuta; }
    public function idKorisnika(): int { return $this->idKorisnika; }
    public function ime(): string { return $this->ime; }
    public function prezime(): string { return $this->prezime; }
    public function punoIme(): string { return trim($this->ime . ' ' . $this->prezime); }
    public function telefon(): ?string { return $this->telefon; }
    public function email(): string { return $this->email; }
    public function jeAktivan(): bool { return $this->aktivan; }
    public function aktivan(): bool { return $this->aktivan; }
}
