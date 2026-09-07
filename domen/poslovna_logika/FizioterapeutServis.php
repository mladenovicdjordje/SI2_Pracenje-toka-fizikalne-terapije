<?php
namespace Kinetika\Domen\PoslovnaLogika;

use Kinetika\Domen\Dto\FizioterapeutDto;
use Kinetika\Domen\Interfejsi\IFizioterapeutRepozitorijum;

/**
 * CRC: FizioterapeutServis
 * Odgovornost: validacija i poslovna pravila za terapeutski nalog
 * Saradnici: IFizioterapeutRepozitorijum, ValidatorUnosa
 */
class FizioterapeutServis
{
    public function __construct(
        private IFizioterapeutRepozitorijum $repozitorijum,
        private ValidatorUnosa $validator
    ) {
    }

    public function lista(?string $pretraga = null, ?int $aktivan = null): array
    {
        return $this->repozitorijum->nadjiSve($pretraga, $aktivan);
    }

    public function nadji(int $id): ?\Kinetika\Domen\Entiteti\Fizioterapeut
    {
        return $this->repozitorijum->nadjiPoId($id);
    }

    public function sacuvaj(FizioterapeutDto $dto): array
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
            'aktivan' => $dto->aktivan,
            'lozinka_hash' => $dto->lozinka ? password_hash($dto->lozinka, PASSWORD_DEFAULT) : null,
        ];

        if ($dto->idFizioterapeuta) {
            $this->repozitorijum->izmeni($dto->idFizioterapeuta, $podaci);
            return ['ok' => true, 'id' => $dto->idFizioterapeuta];
        }

        $id = $this->repozitorijum->sacuvajNovog($podaci);
        return ['ok' => true, 'id' => $id];
    }

    public function obrisi(int $id): array
    {
        $postojeci = $this->repozitorijum->nadjiPoId($id);
        if ($postojeci === null) {
            return ['ok' => false, 'poruka' => 'Terapeut ne postoji.'];
        }
        if ($this->repozitorijum->imaPacijente($id)) {
            $this->repozitorijum->deaktiviraj($id);
            return ['ok' => true, 'poruka' => 'Terapeut ima pacijente, nalog je deaktiviran.'];
        }
        $this->repozitorijum->obrisi($id);
        return ['ok' => true, 'poruka' => 'Terapeut je obrisan.'];
    }

    /**
     * Overloading: validiraj($dto) za novi unos, validiraj($dto, true) za izmenu.
     */
    public function validiraj(FizioterapeutDto $dto, ?bool $izmena = null): array
    {
        $izmena = $izmena ?? ($dto->idFizioterapeuta !== null);
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

        $idKorisnika = null;
        if ($dto->idFizioterapeuta) {
            $postojeci = $this->repozitorijum->nadjiPoId($dto->idFizioterapeuta);
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
