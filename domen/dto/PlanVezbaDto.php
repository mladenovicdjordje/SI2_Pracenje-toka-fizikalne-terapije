<?php
namespace Kinetika\Domen\Dto;

/**
 * CRC: PlanVezbaDto
 * Odgovornost: prenosi vezbu u planu
 * Saradnici: PlanServis, KontrolerPlanova
 */
class PlanVezbaDto
{
    public function __construct(
        public int $idPlana,
        public int $idVezbe,
        public int $idDelaTela,
        public int $serije,
        public int $ponavljanja,
        public ?string $napomena
    ) {
    }

    public static function izNiza(array $ulaz): self
    {
        return new self(
            (int) ($ulaz['id_plana'] ?? 0),
            (int) ($ulaz['id_vezbe'] ?? 0),
            (int) ($ulaz['id_dela_tela'] ?? 0),
            max(1, (int) ($ulaz['serije'] ?? 3)),
            max(1, (int) ($ulaz['ponavljanja'] ?? 10)),
            trim((string) ($ulaz['napomena'] ?? '')) ?: null
        );
    }
}
