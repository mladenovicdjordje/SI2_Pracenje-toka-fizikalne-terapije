<?php
namespace Kinetika\Domen\PoslovnaLogika;

use RuntimeException;

/**
 * CRC: PoslovnaPravila
 * Odgovornost: čita parametre odlučivanja iz JSON fajla i daje ih ostalim klasama
 * Saradnici: KalendarServis, SeansaServis
 */
class PoslovnaPravila
{
    private array $pravila;

    public function __construct(?string $putanja = null)
    {
        $putanja = $putanja ?? dirname(__DIR__, 2) . '/konfiguracija/poslovna_pravila.json';
        if (!is_file($putanja)) {
            throw new RuntimeException('Nedostaje poslovna_pravila.json.');
        }
        $sadrzaj = file_get_contents($putanja);
        $this->pravila = json_decode($sadrzaj, true) ?? [];
    }

    public function vrednost(string $kljuc, mixed $podrazumevano = null): mixed
    {
        return $this->pravila[$kljuc] ?? $podrazumevano;
    }

    public function cenaSeanse(): float
    {
        return (float) $this->vrednost('cena_seanse', 0);
    }

    public function trajanjeSlota(): int
    {
        return (int) $this->vrednost('trajanje_slota_min', 60);
    }

    public function pauzaIzmedju(): int
    {
        return (int) $this->vrednost('pauza_izmedju_slotova_min', 15);
    }

    public function radnoVremeOd(): string
    {
        return (string) $this->vrednost('radno_vreme_od', '08:00');
    }

    public function radnoVremeDo(): string
    {
        return (string) $this->vrednost('radno_vreme_do', '18:00');
    }

    public function radniDani(): array
    {
        return $this->vrednost('radni_dani', [1, 2, 3, 4, 5, 6]);
    }

    public function rokOtkazivanjaSati(): int
    {
        return (int) $this->vrednost('rok_otkazivanja_sati', 12);
    }
}
