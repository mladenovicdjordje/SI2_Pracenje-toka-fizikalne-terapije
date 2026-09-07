<?php
namespace Kinetika\Podaci\Repozitorijumi;

use Kinetika\Domen\Entiteti\DeoTela;
use PDO;

/**
 * CRC: DeoTelaRepozitorijum
 * Odgovornost: cita sifrarnik delova tela za combo box
 * Saradnici: BazniRepozitorijum, forme
 */
class DeoTelaRepozitorijum extends BazniRepozitorijum
{
    public function svi(): array
    {
        $redovi = $this->baza->query(
            'SELECT id_dela_tela, naziv, grupa FROM deo_tela ORDER BY grupa, naziv'
        )->fetchAll(PDO::FETCH_ASSOC);
        $lista = [];
        foreach ($redovi as $red) {
            $lista[] = new DeoTela((int) $red['id_dela_tela'], $red['naziv'], $red['grupa']);
        }
        return $lista;
    }
}
