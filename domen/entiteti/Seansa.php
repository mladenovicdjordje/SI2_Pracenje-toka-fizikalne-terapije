<?php
namespace Kinetika\Domen\Entiteti;

/**
 * CRC: Seansa
 * Odgovornost: jedan termin u kalendaru
 * Saradnici: SeansaRepozitorijum, KalendarServis
 */
class Seansa
{
    public function __construct(
        private int $idSeanse,
        private int $idPlana,
        private int $idPacijenta,
        private int $idFizioterapeuta,
        private string $pocetak,
        private int $trajanjeMin,
        private string $status,
        private ?int $nivoBola,
        private float $cena,
        private ?string $napomena,
        private string $imePacijenta = '',
        private string $imeTerapeuta = ''
    ) {
    }

    public function idSeanse(): int { return $this->idSeanse; }
    public function idPlana(): int { return $this->idPlana; }
    public function idPacijenta(): int { return $this->idPacijenta; }
    public function idFizioterapeuta(): int { return $this->idFizioterapeuta; }
    public function pocetak(): string { return $this->pocetak; }
    public function trajanjeMin(): int { return $this->trajanjeMin; }
    public function status(): string { return $this->status; }
    public function nivoBola(): ?int { return $this->nivoBola; }
    public function cena(): float { return $this->cena; }
    public function napomena(): ?string { return $this->napomena; }
    public function imePacijenta(): string { return $this->imePacijenta; }
    public function imeTerapeuta(): string { return $this->imeTerapeuta; }

    public function datum(): string { return substr($this->pocetak, 0, 10); }
    public function sat(): string { return substr($this->pocetak, 11, 5); }
    public function jeOtvorena(): bool
    {
        return in_array($this->status, ['zahtevana', 'zakazana'], true);
    }
}
