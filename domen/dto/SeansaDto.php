<?php
namespace Kinetika\Domen\Dto;

/**
 * CRC: SeansaDto
 * Odgovornost: prenosi unos termina iz forme
 * Saradnici: SeansaServis, KontrolerKalendara
 */
class SeansaDto
{
    public function __construct(
        public int $idPlana,
        public int $idPacijenta,
        public int $idFizioterapeuta,
        public string $pocetak,
        public int $trajanjeMin,
        public string $status,
        public ?string $napomena,
        public float $cena = 0
    ) {
    }

    public static function izNiza(array $ulaz): self
    {
        $datum = trim((string) ($ulaz['datum'] ?? ''));
        $sat = trim((string) ($ulaz['sat'] ?? ''));
        $pocetak = trim((string) ($ulaz['pocetak'] ?? ''));
        if ($pocetak === '' && $datum !== '' && $sat !== '') {
            $pocetak = $datum . ' ' . $sat . ':00';
        }
        return new self(
            (int) ($ulaz['id_plana'] ?? 0),
            (int) ($ulaz['id_pacijenta'] ?? 0),
            (int) ($ulaz['id_fizioterapeuta'] ?? 0),
            $pocetak,
            (int) ($ulaz['trajanje_min'] ?? 60),
            (string) ($ulaz['status'] ?? 'zakazana'),
            trim((string) ($ulaz['napomena'] ?? '')) ?: null,
            (float) ($ulaz['cena'] ?? 0)
        );
    }
}
