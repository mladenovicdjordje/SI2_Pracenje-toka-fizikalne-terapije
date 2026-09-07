<?php
/**
 * CRC: UcitavacKlasa
 * Odgovornost: mapiranje prostora imena na putanje i učitavanje PHP klasa
 * Saradnici: pokretač aplikacije
 */
class UcitavacKlasa
{
    private string $koren;
    private array $mape;

    public function __construct(string $koren)
    {
        $this->koren = rtrim($koren, '/');
        $this->mape = [
            'Kinetika\\Aplikacija\\Kontroleri\\' => '/aplikacija/kontroleri/',
            'Kinetika\\Aplikacija\\' => '/aplikacija/',
            'Kinetika\\Domen\\Entiteti\\' => '/domen/entiteti/',
            'Kinetika\\Domen\\Interfejsi\\' => '/domen/interfejsi/',
            'Kinetika\\Domen\\PoslovnaLogika\\' => '/domen/poslovna_logika/',
            'Kinetika\\Domen\\Dto\\' => '/domen/dto/',
            'Kinetika\\Podaci\\Repozitorijumi\\' => '/podaci/repozitorijumi/',
            'Kinetika\\Podaci\\Mapperi\\' => '/podaci/mapperi/',
            'Kinetika\\Podaci\\' => '/podaci/',
            'Kinetika\\Servisi\\' => '/servisi/',
        ];
    }

    public function registruj(): void
    {
        spl_autoload_register([$this, 'ucitaj']);
    }

    public function ucitaj(string $klasa): void
    {
        foreach ($this->mape as $prefiks => $folder) {
            if (str_starts_with($klasa, $prefiks)) {
                $ostatak = substr($klasa, strlen($prefiks));
                $putanja = $this->koren . $folder . str_replace('\\', '/', $ostatak) . '.php';
                if (is_file($putanja)) {
                    require_once $putanja;
                }
                return;
            }
        }
    }
}
