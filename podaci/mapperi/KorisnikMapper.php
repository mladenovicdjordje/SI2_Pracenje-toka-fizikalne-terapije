<?php
namespace Kinetika\Podaci\Mapperi;

use Kinetika\Domen\Dto\KorisnikDto;
use Kinetika\Domen\Entiteti\Korisnik;

/**
 * CRC: KorisnikMapper
 * Odgovornost: pretvara red baze / DTO u entitet i obrnuto
 * Saradnici: KorisnikRepozitorijum, KorisnikDto
 */
class KorisnikMapper
{
    public function izReda(array $red): Korisnik
    {
        return new Korisnik(
            (int) $red['id_korisnika'],
            (string) $red['email'],
            (string) $red['lozinka_hash'],
            (string) $red['uloga'],
            (bool) $red['aktivan']
        );
    }

    public function uDto(Korisnik $korisnik): KorisnikDto
    {
        return new KorisnikDto(
            $korisnik->idKorisnika(),
            $korisnik->email(),
            $korisnik->uloga()
        );
    }
}
