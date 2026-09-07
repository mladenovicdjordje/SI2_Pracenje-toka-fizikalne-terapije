<?php
namespace Kinetika\Domen\Entiteti;

/**
 * CRC: Vezba
 * Odgovornost: stavka kataloga vezbi
 * Saradnici: VezbaRepozitorijum, VezbaServis
 */
class Vezba
{
    public function __construct(
        private int $idVezbe,
        private string $naziv,
        private string $grupaMisica,
        private ?string $opis,
        private ?string $kontraindikacije,
        private int $idKreirao,
        private bool $aktivna
    ) {
    }

    public function idVezbe(): int { return $this->idVezbe; }
    public function naziv(): string { return $this->naziv; }
    public function grupaMisica(): string { return $this->grupaMisica; }
    public function opis(): ?string { return $this->opis; }
    public function kontraindikacije(): ?string { return $this->kontraindikacije; }
    public function idKreirao(): int { return $this->idKreirao; }
    public function jeAktivna(): bool { return $this->aktivna; }
}
