<?php
namespace Kinetika\Domen\PoslovnaLogika;

use DateInterval;
use DateTime;

/**
 * CRC: KalendarServis
 * Odgovornost: racuna slotove i radnu nedelju iz JSON pravila
 * Saradnici: PoslovnaPravila, KontrolerKalendara
 */
class KalendarServis
{
    public function __construct(private PoslovnaPravila $pravila)
    {
    }

    public function ponedeljakNedelje(string $datum): DateTime
    {
        $dt = new DateTime($datum !== '' ? $datum : 'now');
        $dan = (int) $dt->format('N');
        if ($dan !== 1) {
            $dt->modify('-' . ($dan - 1) . ' days');
        }
        $dt->setTime(0, 0);
        return $dt;
    }

    public function daniNedelje(string $ponedeljak): array
    {
        $dt = new DateTime($ponedeljak);
        $dani = [];
        for ($i = 0; $i < 6; $i++) {
            $dani[] = $dt->format('Y-m-d');
            $dt->modify('+1 day');
        }
        return $dani;
    }

    public function slotoviDana(): array
    {
        $od = DateTime::createFromFormat('H:i', $this->pravila->radnoVremeOd());
        $do = DateTime::createFromFormat('H:i', $this->pravila->radnoVremeDo());
        $trajanje = $this->pravila->trajanjeSlota();
        $pauza = $this->pravila->pauzaIzmedju();
        $korak = $trajanje + $pauza;
        $lista = [];
        $tek = clone $od;
        while (true) {
            $kraj = clone $tek;
            $kraj->add(new DateInterval('PT' . $trajanje . 'M'));
            if ($kraj > $do) {
                break;
            }
            $lista[] = $tek->format('H:i');
            $tek->add(new DateInterval('PT' . $korak . 'M'));
        }
        return $lista;
    }

    public function jeRadniDan(string $datum): bool
    {
        $n = (int) (new DateTime($datum))->format('N');
        return in_array($n, $this->pravila->radniDani(), true);
    }
}
