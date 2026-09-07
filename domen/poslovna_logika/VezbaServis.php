<?php
namespace Kinetika\Domen\PoslovnaLogika;

use Kinetika\Domen\Dto\VezbaDto;
use Kinetika\Domen\Interfejsi\IVezbaRepozitorijum;

/**
 * CRC: VezbaServis
 * Odgovornost: validacija kataloga vezbi
 * Saradnici: IVezbaRepozitorijum, ValidatorUnosa
 */
class VezbaServis
{
    public function __construct(
        private IVezbaRepozitorijum $repozitorijum,
        private ValidatorUnosa $validator
    ) {
    }

    public function lista(?string $pretraga = null, ?string $grupa = null): array
    {
        return $this->repozitorijum->nadjiSve($pretraga, $grupa);
    }

    public function grupe(): array
    {
        return $this->repozitorijum->grupe();
    }

    public function nadji(int $id): ?\Kinetika\Domen\Entiteti\Vezba
    {
        return $this->repozitorijum->nadjiPoId($id);
    }

    public function sacuvaj(VezbaDto $dto, int $idKreirao): array
    {
        $greske = $this->validiraj($dto);
        if ($greske) {
            return ['ok' => false, 'greske' => $greske];
        }
        $podaci = [
            'naziv' => $dto->naziv,
            'grupa_misica' => $dto->grupaMisica,
            'opis' => $dto->opis,
            'kontraindikacije' => $dto->kontraindikacije,
            'aktivna' => $dto->aktivna,
            'id_kreirao' => $idKreirao,
        ];
        if ($dto->idVezbe) {
            $this->repozitorijum->izmeni($dto->idVezbe, $podaci);
            return ['ok' => true, 'id' => $dto->idVezbe];
        }
        $id = $this->repozitorijum->sacuvaj($podaci);
        return ['ok' => true, 'id' => $id];
    }

    public function obrisi(int $id): array
    {
        if ($this->repozitorijum->nadjiPoId($id) === null) {
            return ['ok' => false, 'poruka' => 'Vezba ne postoji.'];
        }
        if ($this->repozitorijum->koristiSeUPlanu($id)) {
            return ['ok' => false, 'poruka' => 'Vezba je u planu terapije i ne moze da se obrise.'];
        }
        $this->repozitorijum->obrisi($id);
        return ['ok' => true, 'poruka' => 'Vezba je obrisana.'];
    }

    public function validiraj(VezbaDto $dto): array
    {
        $greske = [];
        if ($g = $this->validator->obavezno($dto->naziv, 'Naziv')) {
            $greske['naziv'] = $g;
        }
        if ($g = $this->validator->obavezno($dto->grupaMisica, 'Grupa misica')) {
            $greske['grupa_misica'] = $g;
        }
        if ($dto->naziv !== '' && $this->repozitorijum->nazivZauzet($dto->naziv, $dto->idVezbe)) {
            $greske['naziv'] = 'Vezba sa tim nazivom vec postoji.';
        }
        return $greske;
    }
}
