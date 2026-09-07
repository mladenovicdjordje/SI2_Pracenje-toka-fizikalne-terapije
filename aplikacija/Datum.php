<?php
namespace Kinetika\Aplikacija;

/**
 * CRC: Datum
 * Odgovornost: prikaz i unos datuma u formatu dd/mm/yyyy
 * Saradnici: forme i liste
 */
class Datum
{
    public static function prikaz(?string $vrednost): string
    {
        $baza = self::zaBazu((string) $vrednost);
        if ($baza === '') {
            return '';
        }
        $dt = \DateTime::createFromFormat('Y-m-d', $baza);
        return $dt ? $dt->format('d/m/Y') : '';
    }

    public static function zaPolje(?string $vrednost): string
    {
        return self::prikaz($vrednost);
    }

    public static function zaBazu(string $unos): string
    {
        $unos = trim($unos);
        if ($unos === '') {
            return '';
        }
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})/', $unos, $m)) {
            return $m[1] . '-' . $m[2] . '-' . $m[3];
        }
        $unos = str_replace(['.', '-'], '/', $unos);
        $dt = \DateTime::createFromFormat('d/m/Y', $unos);
        return $dt ? $dt->format('Y-m-d') : '';
    }
}
