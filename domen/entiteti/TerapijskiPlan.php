<?php
namespace Kinetika\Domen\Entiteti;

/**
 * CRC: TerapijskiPlan
 * Odgovornost: plan lecenja vezan za povredu i pacijenta
 * Saradnici: PlanRepozitorijum, PlanServis
 */
class TerapijskiPlan
{
    public function __construct(
        private int $idPlana,
        private int $idPovrede,
        private int $idPacijenta,
        private int $idFizioterapeuta,
        private string $cilj,
        private string $datumOd,
        private ?string $datumDo,
        private int $predvidjenBrojSeansi,
        private string $status,
        private string $nacinNaplate,
        private float $cena,
        private ?string $napomena,
        private string $imePacijenta = '',
        private string $imeTerapeuta = '',
        private string $deoTela = '',
        private string $tipPovrede = '',
        private int $brojOdrzanih = 0
    ) {
    }

    public function idPlana(): int { return $this->idPlana; }
    public function idPovrede(): int { return $this->idPovrede; }
    public function idPacijenta(): int { return $this->idPacijenta; }
    public function idFizioterapeuta(): int { return $this->idFizioterapeuta; }
    public function cilj(): string { return $this->cilj; }
    public function datumOd(): string { return $this->datumOd; }
    public function datumDo(): ?string { return $this->datumDo; }
    public function predvidjenBrojSeansi(): int { return $this->predvidjenBrojSeansi; }
    public function status(): string { return $this->status; }
    public function nacinNaplate(): string { return $this->nacinNaplate; }
    public function cena(): float { return $this->cena; }
    public function napomena(): ?string { return $this->napomena; }
    public function imePacijenta(): string { return $this->imePacijenta; }
    public function imeTerapeuta(): string { return $this->imeTerapeuta; }
    public function deoTela(): string { return $this->deoTela; }
    public function tipPovrede(): string { return $this->tipPovrede; }
    public function brojOdrzanih(): int { return $this->brojOdrzanih; }
    public function jeAktivan(): bool { return $this->status === 'aktivan' || $this->status === 'nacrt'; }
}
