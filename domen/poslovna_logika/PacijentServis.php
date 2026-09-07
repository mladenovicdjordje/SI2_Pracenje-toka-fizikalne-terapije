<?php
namespace Kinetika\Domen\PoslovnaLogika;

use Kinetika\Domen\Dto\PacijentDto;
use Kinetika\Domen\Interfejsi\IFizioterapeutRepozitorijum;
use Kinetika\Domen\Interfejsi\IPacijentRepozitorijum;

/**
 * CRC: PacijentServis
 * Odgovornost: validacija i pravila za karton pacijenta
 * Saradnici: IPacijentRepozitorijum, IFizioterapeutRepozitorijum, ValidatorUnosa
 */
class PacijentServis
{
    public function __construct(
        private IPacijentRepozitorijum $repozitorijum,
        private IFizioterapeutRepozitorijum $terapeuti,
        private ValidatorUnosa $validator
    ) {
    }

    public function lista(?string $pretraga = null, ?int $idFizioterapeuta = null, ?int $aktivan = null): array
    {
        return $this->repozitorijum->nadjiSve($pretraga, $idFizioterapeuta, $aktivan);
    }

    public function nadji(int $id): ?\Kinetika\Domen\Entiteti\Pacijent
    {
        return $this->repozitorijum->nadjiPoId($id);
    }

    public function sacuvaj(PacijentDto $dto): array
    {
        $greske = $this->validiraj($dto);
        if ($greske) {
            return ['ok' => false, 'greske' => $greske];
        }

        $podaci = [
            'ime' => $dto->ime,
            'prezime' => $dto->prezime,
            'email' => $dto->email,
            'telefon' => $dto->telefon,
            'id_fizioterapeuta' => $dto->idFizioterapeuta,
            'aktivan' => $dto->aktivan,
            'lozinka_hash' => $dto->lozinka ? password_hash($dto->lozinka, PASSWORD_DEFAULT) : null,
        ];

        if ($dto->idPacijenta) {
            $this->repozitorijum->izmeni($dto->idPacijenta, $podaci);
            return ['ok' => true, 'id' => $dto->idPacijenta];
        }

        $id = $this->repozitorijum->sacuvajNovog($podaci);
        return ['ok' => true, 'id' => $id];
    }

    public function obrisi(int $id): array
    {
        $postojeci = $this->repozitorijum->nadjiPoId($id);
        if ($postojeci === null) {
            return ['ok' => false, 'poruka' => 'Pacijent ne postoji.'];
        }
        if ($this->repozitorijum->imaIstoriju($id)) {
            $this->repozitorijum->deaktiviraj($id);
            return ['ok' => true, 'poruka' => 'Pacijent ima istoriju, nalog je deaktiviran.'];
        }
        $this->repozitorijum->obrisi($id);
        return ['ok' => true, 'poruka' => 'Pacijent je obrisan.'];
    }

    public function validiraj(PacijentDto $dto, ?bool $izmena = null): array
    {
        $izmena = $izmena ?? ($dto->idPacijenta !== null);
        $greske = [];

        if ($g = $this->validator->obavezno($dto->ime, 'Ime')) {
            $greske['ime'] = $g;
        }
        if ($g = $this->validator->obavezno($dto->prezime, 'Prezime')) {
            $greske['prezime'] = $g;
        }
        if ($g = $this->validator->validirajEmail($dto->email)) {
            $greske['email'] = $g;
        }
        if ($dto->idFizioterapeuta < 1) {
            $greske['id_fizioterapeuta'] = 'Izaberite fizioterapeuta.';
        } elseif ($this->terapeuti->nadjiPoId($dto->idFizioterapeuta) === null) {
            $greske['id_fizioterapeuta'] = 'Izabrani terapeut ne postoji.';
        }

        $idKorisnika = null;
        if ($dto->idPacijenta) {
            $postojeci = $this->repozitorijum->nadjiPoId($dto->idPacijenta);
            $idKorisnika = $postojeci?->idKorisnika();
        }
        if ($dto->email !== '' && $this->repozitorijum->emailZauzet($dto->email, $idKorisnika)) {
            $greske['email'] = 'E-pošta je već zauzeta.';
        }

        if ($g = $this->validator->validirajLozinku($dto->lozinka, !$izmena)) {
            $greske['lozinka'] = $g;
        }

        return $greske;
    }
}
