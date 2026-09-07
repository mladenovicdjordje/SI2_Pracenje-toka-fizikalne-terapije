<?php
namespace Kinetika\Domen\PoslovnaLogika;

use DateTime;
use Kinetika\Domen\Dto\SeansaDto;
use Kinetika\Podaci\Repozitorijumi\PlanRepozitorijum;
use Kinetika\Podaci\Repozitorijumi\SeansaRepozitorijum;

/**
 * CRC: SeansaServis
 * Odgovornost: pravila zakazivanja, zahteva, potvrde i otkaza
 * Saradnici: SeansaRepozitorijum, KalendarServis, PoslovnaPravila
 *
 * Overloading: validirajSlot($dto) za novi termin, validirajSlot($dto, $id) pri izmeni.
 */
class SeansaServis
{
    public function __construct(
        private SeansaRepozitorijum $seanse,
        private PlanRepozitorijum $planovi,
        private KalendarServis $kalendar,
        private PoslovnaPravila $pravila
    ) {
    }

    public function validirajSlot(SeansaDto $dto, ?int $iskljuciId = null): array
    {
        $greske = [];
        if ($dto->idPlana < 1) {
            $greske['id_plana'] = 'Izaberite plan terapije.';
        }
        if ($dto->idPacijenta < 1) {
            $greske['id_pacijenta'] = 'Izaberite pacijenta.';
        }
        if ($dto->idFizioterapeuta < 1) {
            $greske['id_fizioterapeuta'] = 'Nedostaje fizioterapeut.';
        }
        $dt = DateTime::createFromFormat('Y-m-d H:i:s', $dto->pocetak)
            ?: DateTime::createFromFormat('Y-m-d H:i', $dto->pocetak);
        if ($dt === false) {
            $greske['pocetak'] = 'Termin nije ispravan.';
            return $greske;
        }
        $datum = $dt->format('Y-m-d');
        $sat = $dt->format('H:i');
        if (!$this->kalendar->jeRadniDan($datum)) {
            $greske['pocetak'] = 'Ordinacija ne radi tog dana.';
        }
        if (!in_array($sat, $this->kalendar->slotoviDana(), true)) {
            $greske['pocetak'] = 'Satnica nije u radnim slotovima.';
        }
        if ($dt < new DateTime()) {
            $greske['pocetak'] = 'Ne može se zakazati termin u prošlosti.';
        }
        $max = (int) $this->pravila->vrednost('max_seansi_dnevno_po_ft', 8);
        if ($this->seanse->brojUDanu($dto->idFizioterapeuta, $datum) >= $max) {
            $greske['pocetak'] = 'Terapeut je tog dana već popunio maksimalan broj seansi.';
        }
        if ($this->pravila->vrednost('zabrani_preklapanje', true)
            && $this->seanse->imaPreklapanje($dto->idFizioterapeuta, $dt->format('Y-m-d H:i:s'), $dto->trajanjeMin, $iskljuciId)) {
            $greske['pocetak'] = 'Termin se preklapa sa postojećom seansom.';
        }
        return $greske;
    }

    public function zakazi(SeansaDto $dto): array
    {
        $greske = $this->validirajSlot($dto);
        if ($greske) {
            return ['ok' => false, 'greske' => $greske];
        }
        $plan = $this->planovi->nadjiPoId($dto->idPlana);
        if ($plan === null || $plan->idPacijenta() !== $dto->idPacijenta) {
            return ['ok' => false, 'greske' => ['id_plana' => 'Plan ne pripada izabranom pacijentu.']];
        }
        $limit = $plan->predvidjenBrojSeansi();
        $vec = $this->seanse->brojUPlanu($dto->idPlana);
        if ($vec >= $limit) {
            return ['ok' => false, 'greske' => [
                'id_plana' => 'Plan već ima predviđen broj seansi (' . $limit . ').',
            ]];
        }
        $id = $this->seanse->upisi([
            'id_plana' => $dto->idPlana,
            'id_pacijenta' => $dto->idPacijenta,
            'id_fizioterapeuta' => $dto->idFizioterapeuta,
            'pocetak' => $dto->pocetak,
            'trajanje_min' => $this->pravila->trajanjeSlota(),
            'status' => 'zakazana',
            'cena' => $this->pravila->cenaSeanse(),
            'napomena' => $dto->napomena,
        ]);
        return ['ok' => true, 'id' => $id, 'poruka' => 'Termin je zakazan.'];
    }

    public function zahtevaj(SeansaDto $dto): array
    {
        $dto->status = 'zahtevana';
        $greske = $this->validirajSlot($dto);
        if ($greske) {
            return ['ok' => false, 'greske' => $greske];
        }
        $plan = $this->planovi->nadjiPoId($dto->idPlana);
        if ($plan === null) {
            return ['ok' => false, 'greske' => ['id_plana' => 'Plan nije pronađen.']];
        }
        $limit = $plan->predvidjenBrojSeansi();
        $vec = $this->seanse->brojUPlanu($dto->idPlana);
        if ($vec >= $limit) {
            return ['ok' => false, 'greske' => [
                'id_plana' => 'Plan već ima predviđen broj seansi (' . $limit . ').',
            ]];
        }
        $id = $this->seanse->upisi([
            'id_plana' => $dto->idPlana,
            'id_pacijenta' => $dto->idPacijenta,
            'id_fizioterapeuta' => $dto->idFizioterapeuta,
            'pocetak' => $dto->pocetak,
            'trajanje_min' => $this->pravila->trajanjeSlota(),
            'status' => 'zahtevana',
            'cena' => $this->pravila->cenaSeanse(),
            'napomena' => $dto->napomena,
        ]);
        return ['ok' => true, 'id' => $id, 'poruka' => 'Zahtev za termin je poslat.'];
    }

    public function potvrdi(int $id): array
    {
        $seansa = $this->seanse->nadjiPoId($id);
        if ($seansa === null || $seansa->status() !== 'zahtevana') {
            return ['ok' => false, 'poruka' => 'Zahtev ne postoji ili je već obrađen.'];
        }
        try {
            $this->seanse->potvrdiZahtev($id);
        } catch (\Throwable $e) {
            return ['ok' => false, 'poruka' => 'Potvrda nije uspela: ' . $e->getMessage()];
        }
        return ['ok' => true, 'poruka' => 'Termin je potvrđen.'];
    }

    public function otkazi(int $id, string $uloga): array
    {
        $seansa = $this->seanse->nadjiPoId($id);
        if ($seansa === null || !$seansa->jeOtvorena()) {
            return ['ok' => false, 'poruka' => 'Seansa se ne može otkazati.'];
        }
        $rok = $this->pravila->rokOtkazivanjaSati();
        $pocetak = new DateTime($seansa->pocetak());
        $granica = (new DateTime())->modify('+' . $rok . ' hours');
        if ($uloga !== 'admin' && $pocetak <= $granica) {
            return ['ok' => false, 'poruka' => 'Termin nije moguće otkazati 12 sati pre početka.'];
        }
        
        $this->seanse->promeniStatus($id, 'otkazana', $uloga);
        return ['ok' => true, 'poruka' => 'Termin je otkazan.'];
    }

        public function oznaciOdrzanu(int $id, ?int $nivoBola = null, string $uloga = ''): array
    {
        $seansa = $this->seanse->nadjiPoId($id);
        if ($seansa === null || $seansa->status() !== 'zakazana') {
            return ['ok' => false, 'poruka' => 'Samo zakazana seansa može da se označi kao održana.'];
        }
        if ($uloga === 'fizioterapeut' && new DateTime($seansa->pocetak()) > new DateTime()) {
            return ['ok' => false, 'poruka' => 'Seansa se ne može označiti kao održana pre početka termina.'];
        }
        if ($nivoBola !== null && ($nivoBola < 0 || $nivoBola > 10)) {
            return ['ok' => false, 'poruka' => 'Nivo bola mora biti od 0 do 10.'];
        }

        $this->seanse->promeniStatus($id, 'odrzana', null, $nivoBola);

        $plan = $this->planovi->nadjiPoId($seansa->idPlana());
        if ($plan !== null) {
            $odrzanih = $this->seanse->brojOdrzanihUPlanu($seansa->idPlana());
            if ($odrzanih >= $plan->predvidjenBrojSeansi()) {
                $this->planovi->promeniStatus($plan->idPlana(), 'zavrsen');
                return ['ok' => true, 'poruka' => 'Seansa je održana. Plan je označen kao završen.'];
            }
        }
        return ['ok' => true, 'poruka' => 'Seansa je oznacena kao održana.'];
    }
}