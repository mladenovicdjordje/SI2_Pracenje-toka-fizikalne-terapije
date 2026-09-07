<?php
namespace Kinetika\Podaci\Repozitorijumi;

use Kinetika\Podaci\Konekcija;
use PDO;

/**
 * CRC: BazniRepozitorijum
 * Odgovornost: zajednicki PDO i transakcije za naslednike
 * Saradnici: svi konkretni repozitorijumi
 */
abstract class BazniRepozitorijum
{
    protected PDO $baza;

    public function __construct(?PDO $baza = null)
    {
        $this->baza = $baza ?? Konekcija::uzmi();
    }

    protected function zapocniTransakciju(): void
    {
        if (!$this->baza->inTransaction()) {
            $this->baza->beginTransaction();
        }
    }

    protected function potvrdi(): void
    {
        if ($this->baza->inTransaction()) {
            $this->baza->commit();
        }
    }

    protected function ponisti(): void
    {
        if ($this->baza->inTransaction()) {
            $this->baza->rollBack();
        }
    }
}
