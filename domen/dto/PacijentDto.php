<?php
namespace Kinetika\Domen\Dto;

/**
 * CRC: PacijentDto
 * Odgovornost: prenosi podatke pacijenta izmedju formi i servisa
 * Saradnici: PacijentServis, kontroleri
 */
class PacijentDto
{
    public function __construct(
        public ?int $idPacijenta,
        public string $ime,
        public string $prezime,
        public string $email,
        public ?string $telefon,
        public int $idFizioterapeuta,
        public ?string $lozinka,
        public bool $aktivan = true
    ) {
    }

    public static function izNiza(array $ulaz): self
    {
        $aktivan = ($ulaz['aktivan'] ?? '1') === '1' || ($ulaz['aktivan'] ?? null) === 1 || ($ulaz['aktivan'] ?? null) === true;
        return new self(
            isset($ulaz['id_pacijenta']) && $ulaz['id_pacijenta'] !== ''
                ? (int) $ulaz['id_pacijenta']
                : null,
            trim((string) ($ulaz['ime'] ?? '')),
            trim((string) ($ulaz['prezime'] ?? '')),
            mb_strtolower(trim((string) ($ulaz['email'] ?? ''))),
            trim((string) ($ulaz['telefon'] ?? '')) ?: null,
            (int) ($ulaz['id_fizioterapeuta'] ?? 0),
            (string) ($ulaz['lozinka'] ?? '') !== '' ? (string) $ulaz['lozinka'] : null,
            $aktivan
        );
    }
}
