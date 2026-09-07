<?php
/** @var array $korisnik */
/** @var array $lista */
/** @var array $pacijenti */
/** @var array $terapeuti */
$idPac = $idPac ?? '';
$idFt = $idFt ?? '';
$status = $status ?? '';
$pojam = $pojam ?? '';
$qs = http_build_query(array_filter([
    'pac' => $idPac,
    'ft' => $idFt,
    'status' => $status,
    'q' => $pojam,
], static fn ($v) => $v !== '' && $v !== null));
ob_start();
?>
<section class="uvod mali">
    <a class="dugme" href="/izvestaji/stampa?<?= htmlspecialchars($qs) ?>" target="_blank">Štampa spiska</a>
</section>

<?php if (($korisnik['uloga'] ?? '') !== 'pacijent'): ?>
    <form class="filter" method="get" action="/izvestaji">
        <label>Pretraga
            <input type="text" name="q" value="<?= htmlspecialchars((string) $pojam) ?>" placeholder="Pacijent ili povreda">
        </label>
        <?php if (($korisnik['uloga'] ?? '') !== 'pacijent'): ?>
            <label>Pacijent
                <select name="pac">
                    <option value="">Svi</option>
                    <?php foreach ($pacijenti as $p): ?>
                        <option value="<?= (int) $p->idPacijenta() ?>" <?= (string) $idPac === (string) $p->idPacijenta() ? 'selected' : '' ?>>
                            <?= htmlspecialchars($p->punoIme()) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
        <?php endif; ?>
        <?php if (($korisnik['uloga'] ?? '') === 'admin'): ?>
            <label>Fizioterapeut
                <select name="ft">
                    <option value="">Svi</option>
                    <?php foreach ($terapeuti as $ft): ?>
                        <option value="<?= (int) $ft->idFizioterapeuta() ?>" <?= (string) $idFt === (string) $ft->idFizioterapeuta() ? 'selected' : '' ?>>
                            <?= htmlspecialchars($ft->punoIme()) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
        <?php endif; ?>
        <label>Status
            <select name="status">
                <option value="">Svi</option>
                <?php foreach (['nacrt' => 'Nacrt', 'aktivan' => 'Aktivan', 'zavrsen' => 'Završen', 'prekinut' => 'Prekinut'] as $kod => $oznaka): ?>
                    <option value="<?= $kod ?>" <?= $status === $kod ? 'selected' : '' ?>><?= $oznaka ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <button type="submit">Primeni</button>
    </form>
<?php endif; ?>

<div class="kartica">
    <table class="tabela">
        <thead>
            <tr>
                <th>Pacijent</th>
                <th>Terapeut</th>
                <th>Povreda</th>
                <th>Deo tela</th>
                <th>Seanse</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <?php if (!$lista): ?>
            <tr><td colspan="7" class="prazno">Nema zapisa za dati filter.</td></tr>
        <?php endif; ?>
        <?php foreach ($lista as $red): ?>
            <tr>
                <td><?= htmlspecialchars($red->imePacijenta()) ?></td>
                <td><?= htmlspecialchars($red->imeTerapeuta()) ?></td>
                <td><?= htmlspecialchars($red->tipPovrede()) ?></td>
                <td><?= htmlspecialchars($red->deoTela()) ?></td>
                <td><?= (int) $red->brojOdrzanih() ?> / <?= (int) $red->predvidjenBrojSeansi() ?></td>
                <td><span class="status <?= $red->status() === 'aktivan' ? 'da' : 'ne' ?>"><?= htmlspecialchars(['nacrt'=>'Nacrt','aktivan'=>'Aktivan','zavrsen'=>'Završen','prekinut'=>'Prekinut'][$red->status()] ?? $red->status()) ?></span></td>
                <td class="akcije">
                    <a href="/izvestaji/stampa?pac=<?= (int) $red->idPacijenta() ?>&status=<?= rawurlencode($red->status()) ?>" target="_blank">Štampa</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php
$sadrzaj = ob_get_clean();
require dirname(__DIR__) . '/delovi/okvir.php';
