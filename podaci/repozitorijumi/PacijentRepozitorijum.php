<?php
namespace Kinetika\Podaci\Repozitorijumi;

use Kinetika\Domen\Entiteti\Pacijent;
use Kinetika\Domen\Interfejsi\IPacijentRepozitorijum;
use PDO;
use Throwable;

/**
 * CRC: PacijentRepozitorijum
 * Odgovornost: SQL pristup pacijentima i njihovim nalozima
 * Saradnici: BazniRepozitorijum, IPacijentRepozitorijum
 */
class PacijentRepozitorijum extends BazniRepozitorijum implements IPacijentRepozitorijum
{
    public function nadjiPoId(int $id): ?Pacijent
    {
        $upit = $this->baza->prepare(
            'SELECT p.id_pacijenta, p.id_korisnika, p.id_fizioterapeuta, p.ime, p.prezime,
                    p.telefon, k.email, k.aktivan,
                    CONCAT(f.ime, \' \', f.prezime) AS ime_terapeuta
             FROM pacijent p
             JOIN korisnik k ON k.id_korisnika = p.id_korisnika
             JOIN fizioterapeut f ON f.id_fizioterapeuta = p.id_fizioterapeuta
             WHERE p.id_pacijenta = :id LIMIT 1'
        );
        $upit->execute(['id' => $id]);
        $red = $upit->fetch(PDO::FETCH_ASSOC);
        return $red ? $this->izReda($red) : null;
    }

    /**
     * Overloading kroz opcioni broj argumenata.
     */
    public function nadjiSve(?string $pretraga = null, ?int $idFizioterapeuta = null, ?int $aktivan = null): array
    {
        $sql = 'SELECT p.id_pacijenta, p.id_korisnika, p.id_fizioterapeuta, p.ime, p.prezime,
                       p.telefon, k.email, k.aktivan,
                       CONCAT(f.ime, \' \', f.prezime) AS ime_terapeuta
                FROM pacijent p
                JOIN korisnik k ON k.id_korisnika = p.id_korisnika
                JOIN fizioterapeut f ON f.id_fizioterapeuta = p.id_fizioterapeuta
                WHERE 1=1';
        $par = [];
        if ($pretraga !== null && $pretraga !== '') {
            $sql .= ' AND (p.ime LIKE :q1 OR p.prezime LIKE :q2 OR k.email LIKE :q3)';
            $par['q1'] = $par['q2'] = $par['q3'] = '%' . $pretraga . '%';
        }
        if ($idFizioterapeuta) {
            $sql .= ' AND p.id_fizioterapeuta = :ft';
            $par['ft'] = $idFizioterapeuta;
        }
        if ($aktivan !== null) {
            $sql .= ' AND k.aktivan = :aktivan';
            $par['aktivan'] = $aktivan;
        }
        $sql .= ' ORDER BY p.prezime, p.ime';
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
                'uloga' => 'pacijent',
            ]);
            $idKorisnika = (int) $this->baza->lastInsertId();

            $this->baza->prepare(
                'INSERT INTO pacijent (id_korisnika, id_fizioterapeuta, ime, prezime, telefon)
                 VALUES (:id, :ft, :ime, :prezime, :tel)'
            )->execute([
                'id' => $idKorisnika,
                'ft' => $podaci['id_fizioterapeuta'],
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

    public function izmeni(int $idPacijenta, array $podaci): void
    {
        $postojeci = $this->nadjiPoId($idPacijenta);
        if ($postojeci === null) {
            return;
        }
        $this->zapocniTransakciju();
        try {
            $this->baza->prepare(
                'UPDATE pacijent
                 SET ime = :ime, prezime = :prezime, telefon = :tel, id_fizioterapeuta = :ft
                 WHERE id_pacijenta = :id'
            )->execute([
                'ime' => $podaci['ime'],
                'prezime' => $podaci['prezime'],
                'tel' => $podaci['telefon'],
                'ft' => $podaci['id_fizioterapeuta'],
                'id' => $idPacijenta,
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

    public function deaktiviraj(int $idPacijenta): void
    {
        $postojeci = $this->nadjiPoId($idPacijenta);
        if ($postojeci === null) {
            return;
        }
        $this->baza->prepare(
            'UPDATE korisnik SET aktivan = 0 WHERE id_korisnika = :id'
        )->execute(['id' => $postojeci->idKorisnika()]);
    }

    public function obrisi(int $idPacijenta): void
    {
        $postojeci = $this->nadjiPoId($idPacijenta);
        if ($postojeci === null) {
            return;
        }
        $this->zapocniTransakciju();
        try {
            $this->baza->prepare('DELETE FROM pacijent WHERE id_pacijenta = :id')
                ->execute(['id' => $idPacijenta]);
            $this->baza->prepare('DELETE FROM korisnik WHERE id_korisnika = :id')
                ->execute(['id' => $postojeci->idKorisnika()]);
            $this->potvrdi();
        } catch (Throwable $e) {
            $this->ponisti();
            throw $e;
        }
    }

    public function filtriraj(?int $idFizioterapeuta, string $pojam, string $status): array
    {
        $aktivan = null;
        if ($status === 'aktivan' || $status === '1') {
            $aktivan = 1;
        } elseif ($status === 'neaktivan' || $status === '0') {
            $aktivan = 0;
        }
        return $this->nadjiSve($pojam !== '' ? $pojam : null, $idFizioterapeuta, $aktivan);
    }

    public function imaIstoriju(int $idPacijenta): bool
    {
        $q1 = $this->baza->prepare('SELECT COUNT(*) FROM terapijski_plan WHERE id_pacijenta = :id');
        $q1->execute(['id' => $idPacijenta]);
        $q2 = $this->baza->prepare('SELECT COUNT(*) FROM seansa WHERE id_pacijenta = :id');
        $q2->execute(['id' => $idPacijenta]);
        return ((int) $q1->fetchColumn() + (int) $q2->fetchColumn()) > 0;
    }

    private function izReda(array $red): Pacijent
    {
        return new Pacijent(
            (int) $red['id_pacijenta'],
            (int) $red['id_korisnika'],
            (int) $red['id_fizioterapeuta'],
            (string) $red['ime'],
            (string) $red['prezime'],
            $red['telefon'] !== null ? (string) $red['telefon'] : null,
            (string) $red['email'],
            (bool) $red['aktivan'],
            (string) ($red['ime_terapeuta'] ?? '')
        );
    }
}
