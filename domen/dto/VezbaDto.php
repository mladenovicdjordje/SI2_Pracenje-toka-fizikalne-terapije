<?php
namespace Kinetika\Domen\Dto;

/**
 * CRC: VezbaDto
 * Odgovornost: prenosi podatke vezbe izmedju formi i servisa
 * Saradnici: VezbaServis, kontroleri
 */
class VezbaDto
{
    public function __construct(
        public ?int $idVezbe,
        public string $naziv,
        public string $grupaMisica,
        public ?string $opis,
        public ?string $kontraindikacije,
        public bool $aktivna = true
    ) {
    }

    public static function izNiza(array $ulaz): self
    {
        $aktivna = ($ulaz['aktivna'] ?? '1') === '1' || ($ulaz['aktivna'] ?? null) === 1 || ($ulaz['aktivna'] ?? null) === true;
        return new self(
            isset($ulaz['id_vezbe']) && $ulaz['id_vezbe'] !== ''
                ? (int) $ulaz['id_vezbe']
                : null,
            trim((string) ($ulaz['naziv'] ?? '')),
            trim((string) ($ulaz['grupa_misica'] ?? '')),
            trim((string) ($ulaz['opis'] ?? '')) ?: null,
            trim((string) ($ulaz['kontraindikacije'] ?? '')) ?: null,
            $aktivna
        );
    }
}
