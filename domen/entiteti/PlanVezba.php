<?php
namespace Kinetika\Domen\Entiteti;

/**
 * CRC: PlanVezba
 * Odgovornost: vezba dodeljena planu, sa delom tela i dozom
 * Saradnici: PlanVezbaRepozitorijum
 */
class PlanVezba
{
    public function __construct(
        private int $idPlanVezba,
        private int $idPlana,
        private int $idVezbe,
        private int $idDelaTela,
        private int $serije,
        private int $ponavljanja,
        private ?string $napomena,
        private string $nazivVezbe = '',
        private string $nazivDelaTela = ''
    ) {
    }

    public function idPlanVezba(): int { return $this->idPlanVezba; }
    public function idPlana(): int { return $this->idPlana; }
    public function idVezbe(): int { return $this->idVezbe; }
    public function idDelaTela(): int { return $this->idDelaTela; }
    public function serije(): int { return $this->serije; }
    public function ponavljanja(): int { return $this->ponavljanja; }
    public function napomena(): ?string { return $this->napomena; }
    public function nazivVezbe(): string { return $this->nazivVezbe; }
    public function nazivDelaTela(): string { return $this->nazivDelaTela; }
}
