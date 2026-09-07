<?php
namespace Kinetika\Domen\Dto;

/**
 * CRC: OsobaDto
 * Odgovornost: prenosi podatke forme izmedju kontrolera i poslovne logike
 * Saradnici: ServisOsoblja, kontroleri
 */
class OsobaDto
{
    public function __construct(
        public ?int $idKartona,
        public ?int $idKorisnika,
        public string $ime,
        public string $prezime,
        public string $email,
        public ?string $telefon,
        public ?string $lozinka,
        public ?int $idFizioterapeuta = null,
        public bool $aktivan = true
    ) {
    }
}
