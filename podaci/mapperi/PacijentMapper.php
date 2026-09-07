<?php
namespace Kinetika\Podaci\Mapperi;

use Kinetika\Domen\Dto\PacijentDto;
use Kinetika\Domen\Entiteti\Pacijent;

/**
 * CRC: PacijentMapper
 * Odgovornost: pretvara red baze u entitet i DTO
 * Saradnici: PacijentRepozitorijum
 */
class PacijentMapper
{
    public function izReda(array $red): Pacijent
    {
        return new Pacijent(
            (int) $red['id_pacijenta'],
            (int) $red['id_korisnika'],
            (int) $red['id_fizioterapeuta'],
            (string) $red['ime'],
            (string) $red['prezime'],
            $red['telefon'] !== null ? (string) $red['telefon'] : null,
            (string) ($red['email'] ?? ''),
            (bool) ($red['aktivan'] ?? true),
            (string) ($red['ime_terapeuta'] ?? '')
        );
    }

    public function uDto(Pacijent $p): PacijentDto
    {
        return new PacijentDto(
            $p->idPacijenta(),
            $p->ime(),
            $p->prezime(),
            $p->email(),
            $p->telefon(),
            $p->idFizioterapeuta(),
            $p->imeTerapeuta(),
            $p->jeAktivan()
        );
    }
}
