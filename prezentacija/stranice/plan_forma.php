<?php
/** @var array $korisnik */
/** @var array $podaci */
/** @var array $greske */
/** @var array $pacijenti */
/** @var array $deloviTela */
/** @var array $terapeuti */
ob_start();
$izmena = !empty($podaci['id_plana']);
$greske = $greske ?? [];
$ymd = static function (?string $v): string {
    $v = trim((string) $v);
    if ($v === '') {
        return '';
    }
    if (preg_match('#^(\d{2})/(\d{2})/(\d{4})$#', $v, $m)) {
        return $m[3] . '-' . $m[2] . '-' . $m[1];
    }
    return substr($v, 0, 10);
};
?>
<section class="uvod mali">
    <a class="dugme senka" href="/planovi">Nazad na listu</a>
</section>

<form class="papir forma" method="post" action="/planovi/sacuvaj" style="max-width:760px">
    <?php if ($izmena): ?>
        <input type="hidden" name="id_plana" value="<?= (int) $podaci['id_plana'] ?>">
    <?php endif; ?>

    <p class="natpis">Povreda</p>
    <p>
        <label>Pacijent</label>
        <select name="id_pacijenta" required>
            <option value="">Izaberite pacijenta</option>
            <?php foreach ($pacijenti as $p): ?>
                <option value="<?= (int) $p->idPacijenta() ?>" <?= (int) ($podaci['id_pacijenta'] ?? 0) === $p->idPacijenta() ? 'selected' : '' ?>>
                    <?= htmlspecialchars($p->punoIme()) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (!empty($greske['id_pacijenta'])): ?><small class="greska"><?= htmlspecialchars($greske['id_pacijenta']) ?></small><?php endif; ?>
    </p>
        </select>
        <?php if (!empty($greske['id_fizioterapeuta'])): ?><small class="greska"><?= htmlspecialchars($greske['id_fizioterapeuta']) ?></small><?php endif; ?>
    </p>
    <div class="dve-polja">
        <p>
            <label>Deo tela</label>
            <select name="id_dela_tela" required>
                <option value="">Izaberite deo tela</option>
                <?php
                $grupa = '';
                foreach ($deloviTela as $deo):
                    $deoGrupa = is_array($deo) ? (string) $deo['grupa'] : $deo->grupa();
                    $deoId = is_array($deo) ? (int) $deo['id_dela_tela'] : $deo->idDelaTela();
                    $deoNaziv = is_array($deo) ? (string) $deo['naziv'] : $deo->naziv();
                    if ($grupa !== $deoGrupa):
                        if ($grupa !== '') {
                            echo '</optgroup>';
                        }
                        $grupa = $deoGrupa;
                ?>
                        <optgroup label="<?= htmlspecialchars($deoGrupa) ?>">
                <?php endif; ?>
                    <option value="<?= $deoId ?>" <?= (int) ($podaci['id_dela_tela'] ?? 0) === $deoId ? 'selected' : '' ?>>
                        <?= htmlspecialchars($deoNaziv) ?>
                    </option>
                <?php endforeach; if ($grupa !== '') echo '</optgroup>'; ?>
            </select>
            <?php if (!empty($greske['id_dela_tela'])): ?><small class="greska"><?= htmlspecialchars($greske['id_dela_tela']) ?></small><?php endif; ?>
        </p>
        <p>
            <label>Težina povrede</label>
            <select name="tezina">
                <option value="blaga" <?= ($podaci['tezina'] ?? '') === 'blaga' ? 'selected' : '' ?>>Blaga</option>
                <option value="umerena" <?= ($podaci['tezina'] ?? 'umerena') === 'umerena' ? 'selected' : '' ?>>Umerena</option>
                <option value="teska" <?= ($podaci['tezina'] ?? '') === 'teska' ? 'selected' : '' ?>>Teška</option>
            </select>
        </p>
    </div>
    <div class="dve-polja">
        <p>
            <label>Tip povrede</label>
            <input type="text" name="tip_povrede" value="<?= htmlspecialchars($podaci['tip_povrede'] ?? '') ?>" placeholder="npr. uganuće, diskus hernija" required>
            <?php if (!empty($greske['tip_povrede'])): ?><small class="greska"><?= htmlspecialchars($greske['tip_povrede']) ?></small><?php endif; ?>
        </p>
        <p>
            <label>Datum povrede</label>
            <input type="date" name="datum_povrede" value="<?= htmlspecialchars($ymd($podaci['datum_povrede'] ?? '')) ?>" required>
            <?php if (!empty($greske['datum_povrede'])): ?><small class="greska"><?= htmlspecialchars($greske['datum_povrede']) ?></small><?php endif; ?>
        </p>
    </div>
    <p>
        <label>Opis povrede</label>
        <textarea name="opis_povrede" rows="2"><?= htmlspecialchars($podaci['opis_povrede'] ?? '') ?></textarea>
    </p>

    <p class="natpis">Plan</p>
    <p>
        <label>Cilj</label>
        <input type="text" name="cilj" value="<?= htmlspecialchars($podaci['cilj'] ?? '') ?>" required>
        <?php if (!empty($greske['cilj'])): ?><small class="greska"><?= htmlspecialchars($greske['cilj']) ?></small><?php endif; ?>
    </p>
    <div class="dve-polja">
        <p>
            <label>Datum od</label>
            <input type="date" name="datum_od" value="<?= htmlspecialchars($ymd($podaci['datum_od'] ?? '')) ?>" required>
            <?php if (!empty($greske['datum_od'])): ?><small class="greska"><?= htmlspecialchars($greske['datum_od']) ?></small><?php endif; ?>
        </p>
        <p>
            <label>Datum do</label>
            <input type="date" name="datum_do" value="<?= htmlspecialchars($ymd($podaci['datum_do'] ?? '')) ?>">
            <?php if (!empty($greske['datum_do'])): ?><small class="greska"><?= htmlspecialchars($greske['datum_do']) ?></small><?php endif; ?>
        </p>
    </div>
    <div class="dve-polja">
        <p>
            <label>Predviđen broj seansi</label>
            <input type="number" name="predvidjen_broj_seansi" min="1" max="60" value="<?= (int) ($podaci['predvidjen_broj_seansi'] ?? 8) ?>">
            <?php if (!empty($greske['predvidjen_broj_seansi'])): ?><small class="greska"><?= htmlspecialchars($greske['predvidjen_broj_seansi']) ?></small><?php endif; ?>
        </p>
        <p>
            <label>Status</label>
            <select name="status">
                <?php foreach (['nacrt' => 'Nacrt', 'aktivan' => 'Aktivan', 'zavrsen' => 'Završen', 'prekinut' => 'Prekinut'] as $kod => $oznaka): ?>
                    <option value="<?= $kod ?>" <?= ($podaci['status'] ?? 'aktivan') === $kod ? 'selected' : '' ?>><?= $oznaka ?></option>
                <?php endforeach; ?>
            </select>
            <?php if (!empty($greske['status'])): ?><small class="greska"><?= htmlspecialchars($greske['status']) ?></small><?php endif; ?>
        </p>
    </div>
    <p>
        <label>Napomena</label>
        <textarea name="napomena" rows="2"><?= htmlspecialchars($podaci['napomena'] ?? '') ?></textarea>
    </p>
    <button type="submit">Sačuvaj plan</button>
</form>
<?php
$sadrzaj = ob_get_clean();
require dirname(__DIR__) . '/delovi/okvir.php';