<?php
/** @var array $korisnik */
/** @var array $dani */
/** @var array $slotovi */
/** @var array $mapa */
/** @var array $terapeuti */
/** @var string $pon */
/** @var string $prethodna */
/** @var string $sledeca */
/** @var string $poruka */
ob_start();
$poruka = $poruka ?? '';
$idFt = (string) ($_GET['ft'] ?? '');
$uloga = $korisnik['uloga'] ?? '';
$naziviDana = ['Ponedeljak', 'Utorak', 'Sreda', 'Četvrtak', 'Petak', 'Subota'];
$ftQuery = $idFt !== '' ? '&ft=' . rawurlencode($idFt) : '';
$ikonicaOk = '<svg viewBox="0 0 16 16" width="12" height="12" aria-hidden="true"><path fill="#fff" d="M6.2 11.4 2.8 8l1.1-1.1 2.3 2.3 5-5L12.3 5z"/></svg>';
$ikonicaX = '<svg viewBox="0 0 16 16" width="10" height="10" aria-hidden="true"><path fill="#fff" d="M3.5 3.5 8 8l4.5-4.5 1 1L9 9l4.5 4.5-1 1L8 10l-4.5 4.5-1-1L7 9 2.5 4.5z"/></svg>';
?>
<?php if ($uloga === 'admin'): ?>
<form class="filter" method="get" action="/kalendar">
    <input type="hidden" name="nedelja" value="<?= htmlspecialchars($pon) ?>">
    <label>Fizioterapeut
        <select name="ft" onchange="this.form.submit()">
            <option value="">Svi terapeuti</option>
            <?php foreach ($terapeuti as $ft): ?>
                <option value="<?= (int) $ft->idFizioterapeuta() ?>" <?= $idFt === (string) $ft->idFizioterapeuta() ? 'selected' : '' ?>>
                    <?= htmlspecialchars($ft->punoIme()) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>
</form>

<?php endif; ?>
<?php if ($poruka !== ''): ?>
    <p class="trakica <?= str_contains($poruka, 'nije moguće') ? 'greska' : 'ok' ?>">
        <?= htmlspecialchars($poruka) ?>
    </p>
<?php endif; ?>

<div class="kal-glava">
    <a class="kal-dugme" href="/kalendar?nedelja=<?= htmlspecialchars($prethodna) . $ftQuery ?>">‹</a>
    <div class="kalendar-okvir">
        <table class="kalendar-mreza">
            <thead>
                <tr>
                    <th class="sat-kolona"></th>
                    <?php foreach ($dani as $i => $datum): ?>
                        <th>
                            <?= $naziviDana[$i] ?>
                            <small><?= (new DateTime($datum))->format('d.m.') ?></small>
                        </th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($slotovi as $sat):
                $satPrikaz = is_array($sat) ? ($sat['pocetak'] ?? '') : $sat;
            ?>
                <tr>
                    <th class="sat-kolona"><?= htmlspecialchars($satPrikaz) ?></th>
                    <?php foreach ($dani as $datum):
                        $kljuc = $datum . '|' . $satPrikaz;
                        $uSlotu = $mapa[$kljuc] ?? [];
                    ?>
                        <td>
                            <?php if ($uSlotu): ?>
                                <?php foreach ($uSlotu as $s): ?>
                                    <div class="slot zauzece status-<?= htmlspecialchars($s->status()) ?>">
                                        <strong><?= htmlspecialchars($s->imePacijenta()) ?></strong>
                                        <?php if ($uloga === 'admin'): ?>
                                            <small><?= htmlspecialchars($s->imeTerapeuta()) ?></small>
                                        <?php endif; ?>
                                        <?php if (in_array($s->status(), ['zahtevana', 'zakazana'], true)): ?>
                                            <span class="slot-akcije">
                                                <?php if ($uloga !== 'pacijent' && $s->status() === 'zahtevana'): ?>
                                                <form method="post" action="/kalendar/potvrdi">
                                                    <input type="hidden" name="id" value="<?= (int) $s->idSeanse() ?>">
                                                    <button type="submit" class="slot-ok" title="Potvrdi"><?= $ikonicaOk ?></button>
                                                </form>
                                                <?php endif; ?>
                                                <?php if ($uloga !== 'pacijent' && $s->status() === 'zakazana'): ?>
                                                <form method="post" action="/kalendar/odrzana" onsubmit="return pitajBol(this);">
                                                    <input type="hidden" name="id" value="<?= (int) $s->idSeanse() ?>">
                                                    <input type="hidden" name="nivo_bola" value="">
                                                    <button type="submit" class="slot-ok" title="Odrzana"><?= $ikonicaOk ?></button>
                                                </form>
                                                <?php endif; ?>
                                                <form method="post" action="/kalendar/otkazi" onsubmit="return confirm('Otkazati ovaj termin?');">
                                                    <input type="hidden" name="id" value="<?= (int) $s->idSeanse() ?>">
                                                    <button type="submit" class="slot-x" title="Otkazi"><?= $ikonicaX ?></button>
                                                </form>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <a class="slot slobodan" href="/kalendar/zakazi?datum=<?= htmlspecialchars($datum) ?>&sat=<?= htmlspecialchars($satPrikaz) . $ftQuery ?>">slobodan</a>
                            <?php endif; ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <a class="kal-dugme" href="/kalendar?nedelja=<?= htmlspecialchars($sledeca) . $ftQuery ?>">›</a>
</div>

<script>
function pitajBol(forma) {
    var unos = prompt('Nivo bola (0-10). Ostavi prazno ako pacijent nije prijavio.');
    if (unos === null) {
        return false;
    }
    unos = unos.trim();
    if (unos === '') {
        return true;
    }
    var n = parseInt(unos, 10);
    if (isNaN(n) || n < 0 || n > 10) {
        alert('Unesi broj od 0 do 10.');
        return false;
    }
    forma.nivo_bola.value = n;
    return true;
}
</script>
<?php
$sadrzaj = ob_get_clean();
$poruka = '';
require dirname(__DIR__) . '/delovi/okvir.php';