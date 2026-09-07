<?php
namespace Kinetika\Podaci\Repozitorijumi;

use Kinetika\Domen\Entiteti\Fizioterapeut;
use Kinetika\Domen\Interfejsi\IFizioterapeutRepozitorijum;
use PDO;
use Throwable;

/**
 * CRC: FizioterapeutRepozitorijum
 * Odgovornost: SQL pristup terapeutima i njihovim nalozima
 * Saradnici: BazniRepozitorijum, IFizioterapeutRepozitorijum
 */
class FizioterapeutRepozitorijum extends BazniRepozitorijum implements IFizioterapeutRepozitorijum
{
    public function nadjiPoId(int $id): ?Fizioterapeut
    {
        $upit = $this->baza->prepare(
            'SELECT f.id_fizioterapeuta, f.id_korisnika, f.ime, f.prezime, f.telefon,
                    k.email, k.aktivan
             FROM fizioterapeut f
             JOIN korisnik k ON k.id_korisnika = f.id_korisnika
             WHERE f.id_fizioterapeuta = :id LIMIT 1'
        );
        $upit->execute(['id' => $id]);
        $red = $upit->fetch(PDO::FETCH_ASSOC);
        return $red ? $this->izReda($red) : null;
    }

    /**
     * Overloading kroz opcioni broj argumenata:
     * nadjiSve() — svi terapeuti
     * nadjiSve($pretraga) — po imenu/emailu
     * nadjiSve($pretraga, $aktivan) — plus filter aktivnosti
     */
    public function nadjiSve(?string $pretraga = null, ?int $aktivan = null): array
    {
        $sql = 'SELECT f.id_fizioterapeuta, f.id_korisnika, f.ime, f.prezime, f.telefon,
                       k.email, k.aktivan
                FROM fizioterapeut f
                JOIN korisnik k ON k.id_korisnika = f.id_korisnika
                WHERE 1=1';
        $par = [];
        if ($pretraga !== null && $pretraga !== '') {
            $sql .= ' AND (f.ime LIKE :q1 OR f.prezime LIKE :q2 OR k.email LIKE :q3)';
            $par['q1'] = $par['q2'] = $par['q3'] = '%' . $pretraga . '%';
        }
        if ($aktivan !== null) {
            $sql .= ' AND k.aktivan = :aktivan';
            $par['aktivan'] = $aktivan;
        }
        $sql .= ' ORDER BY f.prezime, f.ime';
        $upit = $this->baza->prepare($sql);
        $upit->execute($par);
        $lista = [];
        foreach ($upit->fetchAll(PDO::FETCH_ASSOC) as $red) {
            $lista[] = $this->izReda($red);
        }
        return $lista;
    }

    public function emailZauzet(string $email, ?int $idKorisnikaIzuzetak = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM korisnik WHERE email = :email';
        $par = ['email' => $email];
        if ($idKorisnikaIzuzetak) {
            $sql .= ' AND id_korisnika <> :id';
            $par['id'] = $idKorisnikaIzuzetak;
        }
        $upit = $this->baza->prepare($sql);
        $upit->execute($par);
        return (int) $upit->fetchColumn() > 0;
    }

    public function sacuvajNovog(array $podaci): int
    {
        $this->zapocniTransakciju();
        try {
            $this->baza->prepare(
                'INSERT INTO korisnik (email, lozinka_hash, uloga, aktivan)
                 VALUES (:email, :hash, :uloga, 1)'
            )->execute([
                'email' => $podaci['email'],
                'hash' => $podaci['lozinka_hash'],
                'uloga' => 'fizioterapeut',
            ]);
            $idKorisnika = (int) $this->baza->lastInsertId();

            $this->baza->prepare(
                'INSERT INTO fizioterapeut (id_korisnika, ime, prezime, telefon)
                 VALUES (:id, :ime, :prezime, :tel)'
            )->execute([
                'id' => $idKorisnika,
                'ime' => $podaci['ime'],
                'prezime' => $podaci['prezime'],
                'tel' => $podaci['telefon'],
            ]);
            $id = (int) $this->baza->lastInsertId();
            $this->potvrdi();
            return $id;
        } catch (Throwable $e) {
            $this->ponisti();
            throw $e;
        }
    }

    public function izmeni(int $idFizioterapeuta, array $podaci): void
    {
        $postojeci = $this->nadjiPoId($idFizioterapeuta);
        if ($postojeci === null) {
            return;
        }
        $this->zapocniTransakciju();
        try {
            $this->baza->prepare(
                'UPDATE fizioterapeut
                 SET ime = :ime, prezime = :prezime, telefon = :tel
                 WHERE id_fizioterapeuta = :id'
            )->execute([
                'ime' => $podaci['ime'],
                'prezime' => $podaci['prezime'],
                'tel' => $podaci['telefon'],
                'id' => $idFizioterapeuta,
            ]);

            $sql = 'UPDATE korisnik SET email = :email, aktivan = :aktivan';
            $par = [
                'email' => $podaci['email'],
                'aktivan' => $podaci['aktivan'] ? 1 : 0,
                'id' => $postojeci->idKorisnika(),
            ];
            if (!empty($podaci['lozinka_hash'])) {
                $sql .= ', lozinka_hash = :hash';
                $par['hash'] = $podaci['lozinka_hash'];
            }
            $sql .= ' WHERE id_korisnika = :id';
            $this->baza->prepare($sql)->execute($par);
            $this->potvrdi();
        } catch (Throwable $e) {
            $this->ponisti();
            throw $e;
        }
    }

    public function deaktiviraj(int $idFizioterapeuta): void
    {
        $postojeci = $this->nadjiPoId($idFizioterapeuta);
        if ($postojeci === null) {
            return;
        }
        $this->baza->prepare(
            'UPDATE korisnik SET aktivan = 0 WHERE id_korisnika = :id'
        )->execute(['id' => $postojeci->idKorisnika()]);
    }

    public function obrisi(int $idFizioterapeuta): void
    {
        $postojeci = $this->nadjiPoId($idFizioterapeuta);
        if ($postojeci === null) {
            return;
        }
        $this->zapocniTransakciju();
        try {
            $this->baza->prepare(
                'DELETE FROM fizioterapeut WHERE id_fizioterapeuta = :id'
            )->execute(['id' => $idFizioterapeuta]);
            $this->baza->prepare(
                'DELETE FROM korisnik WHERE id_korisnika = :id'
            )->execute(['id' => $postojeci->idKorisnika()]);
            $this->potvrdi();
        } catch (Throwable $e) {
            $this->ponisti();
            throw $e;
        }
    }

    public function svi(): array
    {
        return $this->nadjiSve();
    }

    public function brojPacijenata(int $idFizioterapeuta): int
    {
        $upit = $this->baza->prepare(
            'SELECT COUNT(*) FROM pacijent WHERE id_fizioterapeuta = :id'
        );
        $upit->execute(['id' => $idFizioterapeuta]);
        return (int) $upit->fetchColumn();
    }

    public function imaPacijente(int $idFizioterapeuta): bool
    {
        $upit = $this->baza->prepare(
            'SELECT COUNT(*) FROM pacijent WHERE id_fizioterapeuta = :id'
        );
        $upit->execute(['id' => $idFizioterapeuta]);
        return (int) $upit->fetchColumn() > 0;
    }

    private function izReda(array $red): Fizioterapeut
    {
        return new Fizioterapeut(
            (int) $red['id_fizioterapeuta'],
            (int) $red['id_korisnika'],
            (string) $red['ime'],
            (string) $red['prezime'],
            $red['telefon'] !== null ? (string) $red['telefon'] : null,
            (string) $red['email'],
            (bool) $red['aktivan'],
        );
    }
}
