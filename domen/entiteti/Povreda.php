<?php
namespace Kinetika\Domen\Entiteti;

/**
 * CRC: Povreda
 * Odgovornost: evidentirana povreda pacijenta
 * Saradnici: PovredaRepozitorijum, PlanServis
 */
class Povreda
{
    public function __construct(
        private int $idPovrede,
        private int $idPacijenta,
        private int $idDelaTela,
        private string $tip,
        private string $datumPovrede,
        private string $tezina,
        private ?string $opis,
        private string $nazivDelaTela = ''
    ) {
    }

    public function idPovrede(): int { return $this->idPovrede; }
    public function idPacijenta(): int { return $this->idPacijenta; }
    public function idDelaTela(): int { return $this->idDelaTela; }
    public function tip(): string { return $this->tip; }
    public function datumPovrede(): string { return $this->datumPovrede; }
    public function tezina(): string { return $this->tezina; }
    public function opis(): ?string { return $this->opis; }
    public function nazivDelaTela(): string { return $this->nazivDelaTela; }
}
