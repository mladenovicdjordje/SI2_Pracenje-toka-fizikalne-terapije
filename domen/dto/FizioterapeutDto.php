<?php
namespace Kinetika\Domen\Dto;

/**
 * CRC: FizioterapeutDto
 * Odgovornost: prenosi podatke terapeuta izmedju formi i servisa
 * Saradnici: FizioterapeutServis, kontroleri
 */
class FizioterapeutDto
{
    public function __construct(
        public ?int $idFizioterapeuta,
        public string $ime,
        public string $prezime,
        public string $email,
        public ?string $telefon,
        public ?string $lozinka,
        public bool $aktivan = true
    ) {
    }

    public static function izNiza(array $ulaz): self
    {
        $aktivan = ($ulaz['aktivan'] ?? '1') === '1' || ($ulaz['aktivan'] ?? null) === 1 || ($ulaz['aktivan'] ?? null) === true;
        return new self(
            isset($ulaz['id_fizioterapeuta']) && $ulaz['id_fizioterapeuta'] !== ''
                ? (int) $ulaz['id_fizioterapeuta']
                : null,
            trim((string) ($ulaz['ime'] ?? '')),
            trim((string) ($ulaz['prezime'] ?? '')),
            mb_strtolower(trim((string) ($ulaz['email'] ?? ''))),
            trim((string) ($ulaz['telefon'] ?? '')) ?: null,
            (string) ($ulaz['lozinka'] ?? '') !== '' ? (string) $ulaz['lozinka'] : null,
            $aktivan
        );
    }
}
