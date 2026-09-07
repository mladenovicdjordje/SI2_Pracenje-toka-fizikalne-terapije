<?php
namespace Kinetika\Aplikacija;

/**
 * CRC: Meni
 * Odgovornost: personalizovane stavke menija prema ulozi
 * Saradnici: prezentacija/delovi/meni.php
 */
class Meni
{
    public static function stavke(string $uloga): array
    {
        return match ($uloga) {
            'admin' => [
                ['putanja' => '/pocetna', 'oznaka' => 'Početna'],
                ['putanja' => '/fizioterapeuti', 'oznaka' => 'Fizioterapeuti'],
                ['putanja' => '/pacijenti', 'oznaka' => 'Pacijenti'],
                ['putanja' => '/katalog-vezbi', 'oznaka' => 'Katalog vežbi'],
                ['putanja' => '/planovi', 'oznaka' => 'Plan terapije'],
                ['putanja' => '/kalendar', 'oznaka' => 'Kalendar'],
                ['putanja' => '/izvestaji', 'oznaka' => 'Izveštaji'],
            ],
            'fizioterapeut' => [
                ['putanja' => '/pocetna', 'oznaka' => 'Početna'],
                ['putanja' => '/pacijenti', 'oznaka' => 'Pacijenti'],
                ['putanja' => '/planovi', 'oznaka' => 'Plan terapije'],
                ['putanja' => '/kalendar', 'oznaka' => 'Kalendar'],
                ['putanja' => '/katalog-vezbi', 'oznaka' => 'Katalog vežbi'],
                ['putanja' => '/izvestaji', 'oznaka' => 'Izveštaji'],
            ],
            'pacijent' => [
                ['putanja' => '/pocetna', 'oznaka' => 'Početna'],
                ['putanja' => '/kalendar', 'oznaka' => 'Kalendar'],
                ['putanja' => '/plan-vezbanja', 'oznaka' => 'Plan vežbanja'],
                ['putanja' => '/planovi', 'oznaka' => 'Plan terapije'],
                ['putanja' => '/izvestaji', 'oznaka' => 'Izveštaji'],
            ],
            default => [['putanja' => '/pocetna', 'oznaka' => 'Početna']],
        };
    }
}
