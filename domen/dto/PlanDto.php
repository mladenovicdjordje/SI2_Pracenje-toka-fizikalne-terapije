<?php
namespace Kinetika\Domen\Dto;

use Kinetika\Aplikacija\Datum;

/**
 * CRC: PlanDto
 * Odgovornost: prenosi podatke forme plana i povrede
 * Saradnici: PlanServis, KontrolerPlanova
 */
class PlanDto
{
    public function __construct(
        public ?int $idPlana,
        public ?int $idPovrede,
        public int $idPacijenta,
        public int $idFizioterapeuta,
        public int $idDelaTela,
        public string $tipPovrede,
        public string $datumPovrede,
        public string $tezina,
        public ?string $opisPovrede,
        public string $cilj,
        public string $datumOd,
        public ?string $datumDo,
        public int $predvidjenBrojSeansi,
        public string $status,
        public string $nacinNaplate,
        public float $cena,
        public ?string $napomena
    ) {
    }

    public static function izNiza(array $ulaz): self
    {
        $do = Datum::zaBazu((string) ($ulaz['datum_do'] ?? ''));
        return new self(
            isset($ulaz['id_plana']) && $ulaz['id_plana'] !== '' ? (int) $ulaz['id_plana'] : null,
            isset($ulaz['id_povrede']) && $ulaz['id_povrede'] !== '' ? (int) $ulaz['id_povrede'] : null,
            (int) ($ulaz['id_pacijenta'] ?? 0),
            (int) ($ulaz['id_fizioterapeuta'] ?? 0),
            (int) ($ulaz['id_dela_tela'] ?? 0),
            trim((string) ($ulaz['tip_povrede'] ?? '')),
            Datum::zaBazu((string) ($ulaz['datum_povrede'] ?? '')) ?? '',
            (string) ($ulaz['tezina'] ?? 'umerena'),
            trim((string) ($ulaz['opis_povrede'] ?? '')) ?: null,
            trim((string) ($ulaz['cilj'] ?? '')),
            Datum::zaBazu((string) ($ulaz['datum_od'] ?? '')),
            $do !== '' ? $do : null,
            (int) ($ulaz['predvidjen_broj_seansi'] ?? 8),
            (string) ($ulaz['status'] ?? 'aktivan'),
            (string) ($ulaz['nacin_naplate'] ?? 'po_seansi'),
            (float) str_replace(',', '.', (string) ($ulaz['cena'] ?? '0')),
            trim((string) ($ulaz['napomena'] ?? '')) ?: null
        );
    }
}
