<?php
namespace Kinetika\Podaci\Repozitorijumi;

use Kinetika\Domen\Entiteti\Korisnik;
use Kinetika\Domen\Interfejsi\IKorisnikRepozitorijum;
use Kinetika\Podaci\Konekcija;
use Kinetika\Podaci\Mapperi\KorisnikMapper;
use PDO;

/**
 * CRC: KorisnikRepozitorijum
 * Odgovornost: čitanje korisnika iz tabele korisnik
 * Saradnici: Konekcija, KorisnikMapper, IKorisnikRepozitorijum
 */
class KorisnikRepozitorijum implements IKorisnikRepozitorijum
{
    private PDO $baza;
    private KorisnikMapper $mapper;

    public function __construct(?PDO $baza = null, ?KorisnikMapper $mapper = null)
    {
        $this->baza = $baza ?? Konekcija::uzmi();
        $this->mapper = $mapper ?? new KorisnikMapper();
    }

    public function nadjiPoEmailu(string $email): ?Korisnik
    {
        $upit = $this->baza->prepare(
            'SELECT id_korisnika, email, lozinka_hash, uloga, aktivan
             FROM korisnik WHERE email = :email LIMIT 1'
        );
        $upit->execute(['email' => $email]);
        $red = $upit->fetch();
        return $red ? $this->mapper->izReda($red) : null;
    }

    public function nadjiPoId(int $idKorisnika): ?Korisnik
    {
        $upit = $this->baza->prepare(
            'SELECT id_korisnika, email, lozinka_hash, uloga, aktivan
             FROM korisnik WHERE id_korisnika = :id LIMIT 1'
        );
        $upit->execute(['id' => $idKorisnika]);
        $red = $upit->fetch();
        return $red ? $this->mapper->izReda($red) : null;
    }

    public function emailZauzet(string $email, ?int $izuzmiId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM korisnik WHERE email = :email';
        $parametri = ['email' => $email];
        if ($izuzmiId !== null) {
            $sql .= ' AND id_korisnika <> :id';
            $parametri['id'] = $izuzmiId;
        }
        $upit = $this->baza->prepare($sql);
        $upit->execute($parametri);
        return (int) $upit->fetchColumn() > 0;
    }

    public function upisi(string $email, string $lozinkaHash, string $uloga): int
    {
        $upit = $this->baza->prepare(
            'INSERT INTO korisnik (email, lozinka_hash, uloga, aktivan)
             VALUES (:email, :hash, :uloga, 1)'
        );
        $upit->execute([
            'email' => $email,
            'hash' => $lozinkaHash,
            'uloga' => $uloga,
        ]);
        return (int) $this->baza->lastInsertId();
    }

    public function izmeniNalog(int $idKorisnika, string $email, ?string $lozinkaHash, bool $aktivan): void
    {
        if ($lozinkaHash !== null) {
            $upit = $this->baza->prepare(
                'UPDATE korisnik SET email = :email, lozinka_hash = :hash, aktivan = :aktivan
                 WHERE id_korisnika = :id'
            );
            $upit->execute([
                'email' => $email,
                'hash' => $lozinkaHash,
                'aktivan' => $aktivan ? 1 : 0,
                'id' => $idKorisnika,
            ]);
            return;
        }
        $upit = $this->baza->prepare(
            'UPDATE korisnik SET email = :email, aktivan = :aktivan WHERE id_korisnika = :id'
        );
        $upit->execute([
            'email' => $email,
            'aktivan' => $aktivan ? 1 : 0,
            'id' => $idKorisnika,
        ]);
    }

    public function deaktiviraj(int $idKorisnika): void
    {
        $upit = $this->baza->prepare('UPDATE korisnik SET aktivan = 0 WHERE id_korisnika = :id');
        $upit->execute(['id' => $idKorisnika]);
    }

    public function obrisi(int $idKorisnika): void
    {
        $upit = $this->baza->prepare('DELETE FROM korisnik WHERE id_korisnika = :id');
        $upit->execute(['id' => $idKorisnika]);
    }
}
