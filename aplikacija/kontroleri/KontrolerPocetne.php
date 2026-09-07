<?php
namespace Kinetika\Aplikacija\Kontroleri;

use Kinetika\Aplikacija\ProveraPristupa;
use Kinetika\Podaci\Konekcija;

/**
 * CRC: KontrolerPocetne
 * Odgovornost: početna tabla prema ulozi
 * Saradnici: ProveraPristupa, Konekcija
 */
class KontrolerPocetne
{
    public function prikazi(): void
    {
        $korisnik = ProveraPristupa::zahtevajPrijavu();
        $baza = Konekcija::uzmi();
        $podaci = [
            'broj_ft' => 0,
            'broj_pacijenata' => 0,
            'danas_termina' => 0,
            'zahteva' => 0,
            'raspored' => [],
        ];

        if ($korisnik['uloga'] === 'admin') {
            $podaci['broj_ft'] = (int) $baza->query('SELECT COUNT(*) FROM fizioterapeut')->fetchColumn();
            $podaci['broj_pacijenata'] = (int) $baza->query('SELECT COUNT(*) FROM pacijent')->fetchColumn();
            $podaci['danas_termina'] = (int) $baza->query(
                "SELECT COUNT(*) FROM seansa
                 WHERE DATE(pocetak) = CURDATE()
                   AND status IN ('zakazana', 'zahtevana')"
            )->fetchColumn();
            $podaci['zahteva'] = (int) $baza->query(
                "SELECT COUNT(*) FROM seansa WHERE status = 'zahtevana'"
            )->fetchColumn();

            $podaci['raspored'] = $baza->query(
                "SELECT s.pocetak, s.status, p.ime, p.prezime,
                        ft.ime AS ime_ft, ft.prezime AS prezime_ft,
                        (
                            SELECT COUNT(*) FROM seansa s2
                            WHERE s2.id_plana = s.id_plana
                              AND s2.status IN ('zakazana', 'zahtevana', 'odrzana')
                              AND s2.pocetak <= s.pocetak
                        ) AS redni,
                        tp.predvidjen_broj_seansi AS ukupno
                 FROM seansa s
                 JOIN pacijent p ON p.id_pacijenta = s.id_pacijenta
                 JOIN fizioterapeut ft ON ft.id_fizioterapeuta = s.id_fizioterapeuta
                 JOIN terapijski_plan tp ON tp.id_plana = s.id_plana
                 WHERE DATE(s.pocetak) BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 2 DAY)
                   AND s.status IN ('zakazana', 'zahtevana', 'odrzana')
                 ORDER BY s.pocetak"
            )->fetchAll();
        }

        if ($korisnik['uloga'] === 'fizioterapeut' && $korisnik['id_fizioterapeuta']) {
            $q = $baza->prepare('SELECT COUNT(*) FROM pacijent WHERE id_fizioterapeuta = :id');
            $q->execute(['id' => $korisnik['id_fizioterapeuta']]);
            $podaci['broj_pacijenata'] = (int) $q->fetchColumn();

            $q = $baza->prepare(
                "SELECT COUNT(*) FROM seansa
                 WHERE id_fizioterapeuta = :id AND DATE(pocetak) = CURDATE()
                   AND status IN ('zakazana', 'zahtevana')"
            );
            $q->execute(['id' => $korisnik['id_fizioterapeuta']]);
            $podaci['danas_termina'] = (int) $q->fetchColumn();

            $q = $baza->prepare(
                "SELECT COUNT(*) FROM seansa WHERE id_fizioterapeuta = :id AND status = 'zahtevana'"
            );
            $q->execute(['id' => $korisnik['id_fizioterapeuta']]);
            $podaci['zahteva'] = (int) $q->fetchColumn();

            $q = $baza->prepare(
                "SELECT s.pocetak, s.status, p.ime, p.prezime,
                        tp.predvidjen_broj_seansi AS ukupno,
                        (
                            SELECT COUNT(*)
                            FROM seansa s2
                            WHERE s2.id_plana = s.id_plana
                              AND s2.status IN ('zahtevana', 'zakazana', 'odrzana')
                              AND s2.pocetak <= s.pocetak
                        ) AS redni
                 FROM seansa s
                 JOIN pacijent p ON p.id_pacijenta = s.id_pacijenta
                 LEFT JOIN terapijski_plan tp ON tp.id_plana = s.id_plana
                 WHERE s.id_fizioterapeuta = :id AND DATE(s.pocetak) BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 2 DAY)
                   AND s.status IN ('zakazana', 'zahtevana', 'odrzana')
                 ORDER BY s.pocetak"
            );
            $q->execute(['id' => $korisnik['id_fizioterapeuta']]);
            $podaci['raspored'] = $q->fetchAll();
        }

        if ($korisnik['uloga'] === 'pacijent' && $korisnik['id_pacijenta']) {
            $q = $baza->prepare(
                "SELECT s.pocetak, s.status,
                        tp.predvidjen_broj_seansi AS ukupno,
                        (
                            SELECT COUNT(*)
                            FROM seansa s2
                            WHERE s2.id_plana = s.id_plana
                              AND s2.status IN ('zahtevana', 'zakazana', 'odrzana')
                              AND s2.pocetak <= s.pocetak
                        ) AS redni
                 FROM seansa s
                 JOIN terapijski_plan tp ON tp.id_plana = s.id_plana
                 WHERE s.id_pacijenta = :id AND s.pocetak >= NOW()
                   AND s.status IN ('zakazana', 'zahtevana')
                 ORDER BY s.pocetak LIMIT 8"
            );
            $q->execute(['id' => $korisnik['id_pacijenta']]);
            $podaci['raspored'] = $q->fetchAll();

        }

        $naslov = 'Početna';
        require dirname(__DIR__, 2) . '/prezentacija/stranice/pocetna.php';
    }
}
