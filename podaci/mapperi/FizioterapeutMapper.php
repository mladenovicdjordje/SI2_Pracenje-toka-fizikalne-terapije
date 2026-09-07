<?php
namespace Kinetika\Podaci\Mapperi;

use Kinetika\Domen\Dto\FizioterapeutDto;
use Kinetika\Domen\Entiteti\Fizioterapeut;

/**
 * CRC: FizioterapeutMapper
 * Odgovornost: pretvara red baze u entitet i DTO
 * Saradnici: FizioterapeutRepozitorijum
 */
class FizioterapeutMapper
{
    public function izReda(array $red): Fizioterapeut
    {
        return new Fizioterapeut(
            (int) $red['id_fizioterapeuta'],
            (int) $red['id_korisnika'],
            (string) $red['ime'],
            (string) $red['prezime'],
            $red['telefon'] !== null ? (string) $red['telefon'] : null,
            (string) ($red['email'] ?? ''),
            (bool) ($red['aktivan'] ?? true)
        );
    }

    public function uDto(Fizioterapeut $ft): FizioterapeutDto
    {
        return new FizioterapeutDto(
            $ft->idFizioterapeuta(),
            $ft->ime(),
            $ft->prezime(),
            $ft->email(),
            $ft->telefon(),
            $ft->jeAktivan()
        );
    }
}
