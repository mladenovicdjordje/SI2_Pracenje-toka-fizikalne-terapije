<?php
namespace Kinetika\Domen\PoslovnaLogika;

use PDO;

/**
 * CRC: NalogServis
 * Odgovornost: kreiranje i izmena korisnickog naloga unutar transakcije
 * Saradnici: FizioterapeutServis, PacijentServis
 */
class NalogServis
{
    public function __construct(private PDO $baza)
    {
    }

    public function emailZauzet(string $email, ?int $izuzmiId = null): bool
    {
        $sql = 'SELECT id_korisnika FROM korisnik WHERE email = :email';
        $par = ['email' => $email];
        if ($izuzmiId !== null) {
            $sql .= ' AND id_korisnika <> :id';
            $par['id'] = $izuzmiId;
        }
        $upit = $this->baza->prepare($sql . ' LIMIT 1');
        $upit->execute($par);
        return (bool) $upit->fetchColumn();
    }

    public function napravi(string $email, string $lozinka, string $uloga): int
    {
        $upit = $this->baza->prepare(
            'INSERT INTO korisnik (email, lozinka_hash, uloga, aktivan)
             VALUES (:email, :hash, :uloga, 1)'
        );
        $upit->execute([
            'email' => $email,
            'hash' => password_hash($lozinka, PASSWORD_DEFAULT),
            'uloga' => $uloga,
        ]);
        return (int) $this->baza->lastInsertId();
    }

    public function izmeni(int $idKorisnika, string $email, ?string $lozinka, ?int $aktivan = null): void
    {
        $polja = ['email = :email'];
        $par = ['email' => $email, 'id' => $idKorisnika];
        if ($lozinka !== null && $lozinka !== '') {
            $polja[] = 'lozinka_hash = :hash';
            $par['hash'] = password_hash($lozinka, PASSWORD_DEFAULT);
        }
        if ($aktivan !== null) {
            $polja[] = 'aktivan = :aktivan';
            $par['aktivan'] = $aktivan;
        }
        $sql = 'UPDATE korisnik SET ' . implode(', ', $polja) . ' WHERE id_korisnika = :id';
        $this->baza->prepare($sql)->execute($par);
    }

    public function postaviAktivan(int $idKorisnika, bool $aktivan): void
    {
        $this->baza->prepare('UPDATE korisnik SET aktivan = :a WHERE id_korisnika = :id')
            ->execute(['a' => $aktivan ? 1 : 0, 'id' => $idKorisnika]);
    }

    public function obrisi(int $idKorisnika): void
    {
        $this->baza->prepare('DELETE FROM korisnik WHERE id_korisnika = :id')->execute(['id' => $idKorisnika]);
    }
}
