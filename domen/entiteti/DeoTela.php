<?php
namespace Kinetika\Domen\Entiteti;

/**
 * CRC: DeoTela
 * Odgovornost: stavka sifrarnika dela tela
 * Saradnici: DeoTelaRepozitorijum, forme plana
 */
class DeoTela
{
    public function __construct(
        private int $idDelaTela,
        private string $naziv,
        private string $grupa
    ) {
    }

    public function idDelaTela(): int { return $this->idDelaTela; }
    public function naziv(): string { return $this->naziv; }
    public function grupa(): string { return $this->grupa; }
}
