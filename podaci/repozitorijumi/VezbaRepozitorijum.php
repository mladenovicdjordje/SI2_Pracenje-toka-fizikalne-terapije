<?php
namespace Kinetika\Podaci\Repozitorijumi;

use Kinetika\Domen\Entiteti\Vezba;
use Kinetika\Domen\Interfejsi\IVezbaRepozitorijum;
use PDO;

/**
 * CRC: VezbaRepozitorijum
 * Odgovornost: SQL pristup katalogu vezbi
 * Saradnici: BazniRepozitorijum, IVezbaRepozitorijum
 */
class VezbaRepozitorijum extends BazniRepozitorijum implements IVezbaRepozitorijum
{
    public function nadjiPoId(int $id): ?Vezba
    {
        $upit = $this->baza->prepare('SELECT * FROM vezba WHERE id_vezbe = :id LIMIT 1');
        $upit->execute(['id' => $id]);
        $red = $upit->fetch(PDO::FETCH_ASSOC);
        return $red ? $this->izReda($red) : null;
    }

    public function sve(?string $grupa = null, ?string $pretraga = null): array
    {
        $lista = $this->nadjiSve($pretraga, $grupa);
        $redovi = [];
        foreach ($lista as $v) {
            $redovi[] = [
                'id_vezbe' => $v->idVezbe(),
                'naziv' => $v->naziv(),
                'grupa_misica' => $v->grupaMisica(),
                'opis' => $v->opis(),
                'aktivna' => $v->jeAktivna() ? 1 : 0,
            ];
        }
        return $redovi;
    }

    public function nazivPostoji(string $naziv, ?int $idIzuzetak = null): bool
    {
        return $this->nazivZauzet($naziv, $idIzuzetak);
    }

    public function nadjiSve(?string $pretraga = null, ?string $grupa = null): array
    {
        $sql = 'SELECT * FROM vezba WHERE 1=1';
        $par = [];
        if ($pretraga !== null && $pretraga !== '') {
            $sql .= ' AND (naziv LIKE :q1 OR opis LIKE :q2 OR grupa_misica LIKE :q3)';
            $par['q1'] = $par['q2'] = $par['q3'] = '%' . $pretraga . '%';
        }
        if ($grupa !== null && $grupa !== '') {
            $sql .= ' AND grupa_misica = :grupa';
            $par['grupa'] = $grupa;
        }
        $sql .= ' ORDER BY grupa_misica, naziv';
        $upit = $this->baza->prepare($sql);
        $upit->execute($par);
        $lista = [];
        foreach ($upit->fetchAll(PDO::FETCH_ASSOC) as $red) {
            $lista[] = $this->izReda($red);
        }
        return $lista;
    }

    public function grupe(): array
    {
        return $this->baza->query(
            'SELECT DISTINCT grupa_misica FROM vezba ORDER BY grupa_misica'
        )->fetchAll(PDO::FETCH_COLUMN);
    }

    public function nazivZauzet(string $naziv, ?int $idIzuzetak = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM vezba WHERE naziv = :naziv';
        $par = ['naziv' => $naziv];
        if ($idIzuzetak) {
            $sql .= ' AND id_vezbe <> :id';
            $par['id'] = $idIzuzetak;
        }
        $upit = $this->baza->prepare($sql);
        $upit->execute($par);
        return (int) $upit->fetchColumn() > 0;
    }

    public function sacuvaj(array $podaci, int $id = 0): int
    {
        $norm = [
            'naziv' => $podaci['naziv'] ?? '',
            'grupa_misica' => $podaci['grupa_misica'] ?? $podaci['grupa'] ?? '',
            'opis' => $podaci['opis'] ?? '',
            'kontraindikacije' => $podaci['kontraindikacije'] ?? $podaci['kontra'] ?? '',
            'id_kreirao' => $podaci['id_kreirao'] ?? $podaci['kreirao'] ?? 1,
            'aktivna' => $podaci['aktivna'] ?? $podaci['akt'] ?? 1,
        ];
        if ($id > 0) {
            $this->izmeni($id, $norm);
            return $id;
        }
        return $this->upisiVezbu($norm);
    }

    public function upisiVezbu(array $podaci): int
    {
        $this->baza->prepare(
            'INSERT INTO vezba (naziv, grupa_misica, opis, kontraindikacije, id_kreirao, aktivna)
             VALUES (:naziv, :grupa, :opis, :kontra, :kreirao, :aktivna)'
        )->execute([
            'naziv' => $podaci['naziv'],
            'grupa' => $podaci['grupa_misica'],
            'opis' => $podaci['opis'],
            'kontra' => $podaci['kontraindikacije'],
            'kreirao' => $podaci['id_kreirao'],
            'aktivna' => $podaci['aktivna'] ? 1 : 0,
        ]);
        return (int) $this->baza->lastInsertId();
    }

    public function izmeni(int $idVezbe, array $podaci): void
    {
        $this->baza->prepare(
            'UPDATE vezba
             SET naziv = :naziv, grupa_misica = :grupa, opis = :opis,
                 kontraindikacije = :kontra, aktivna = :aktivna
             WHERE id_vezbe = :id'
        )->execute([
            'naziv' => $podaci['naziv'],
            'grupa' => $podaci['grupa_misica'],
            'opis' => $podaci['opis'],
            'kontra' => $podaci['kontraindikacije'],
            'aktivna' => $podaci['aktivna'] ? 1 : 0,
            'id' => $idVezbe,
        ]);
    }

    public function obrisi(int $idVezbe): void
    {
        $this->baza->prepare('DELETE FROM vezba WHERE id_vezbe = :id')->execute(['id' => $idVezbe]);
    }

    public function koristiSeUPlanu(int $idVezbe): bool
    {
        $upit = $this->baza->prepare('SELECT COUNT(*) FROM plan_vezba WHERE id_vezbe = :id');
        $upit->execute(['id' => $idVezbe]);
        return (int) $upit->fetchColumn() > 0;
    }

    private function izReda(array $red): Vezba
    {
        return new Vezba(
            (int) $red['id_vezbe'],
            (string) $red['naziv'],
            (string) $red['grupa_misica'],
            $red['opis'] !== null ? (string) $red['opis'] : null,
            $red['kontraindikacije'] !== null ? (string) $red['kontraindikacije'] : null,
            (int) $red['id_kreirao'],
            (bool) $red['aktivna']
        );
    }
}
