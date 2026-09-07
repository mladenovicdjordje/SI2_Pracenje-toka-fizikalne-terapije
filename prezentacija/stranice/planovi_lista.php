<?php
/** @var array $korisnik */
/** @var array $lista */
/** @var array $pacijenti */
/** @var string $pretraga */
/** @var string $status */
/** @var string $poruka */
ob_start();
$pretraga = $pretraga ?? '';
$status = $status ?? '';
$poruka = $poruka ?? '';
$pacIzbor = (string) ($_GET['pac'] ?? '');
$samoPregled = ($korisnik['uloga'] ?? '') === 'pacijent';
?>
<section class="uvod mali">
    <?php if (!$samoPregled): ?>
        <a class="dugme" href="/planovi/novi">Novi plan</a>
    <?php endif; ?>
</section>

<?php if ($poruka !== ''): ?>
    <p class="obavestenje"><?= htmlspecialchars($poruka) ?></p>
<?php endif; ?>

<?php if (!$samoPregled): ?>
<form class="filter" method="get" action="/planovi">
    <label>Pretraga
        <input type="text" name="q" value="<?= htmlspecialchars($pretraga) ?>" placeholder="Pacijent, povreda ili deo tela">
    </label>
    <?php if (($korisnik['uloga'] ?? '') === 'admin' || ($korisnik['uloga'] ?? '') === 'fizioterapeut'): ?>
        <label>Pacijent
            <select name="pac">
                <option value="">Svi</option>
                <?php foreach ($pacijenti as $p): ?>
                    <option value="<?= (int) $p->idPacijenta() ?>" <?= $pacIzbor === (string) $p->idPacijenta() ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p->punoIme()) ?>
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
            <tr><td colspan="6" class="prazno">Nema planova za dati filter.</td></tr>
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
                    <a href="/planovi/vezbe?id=<?= (int) $red->idPlana() ?>">Vežbe</a>
                    <?php if (!$samoPregled): ?>
                        <a href="/planovi/izmena?id=<?= (int) $red->idPlana() ?>">Izmena</a>
                        <form method="post" action="/planovi/obrisi" onsubmit="return confirm('Obrisati plan ili ga prekinuti ako ima seanse?');">
                            <input type="hidden" name="id" value="<?= (int) $red->idPlana() ?>">
                            <button type="submit" class="link-opasnost">Brisanje</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php
$sadrzaj = ob_get_clean();
require dirname(__DIR__) . '/delovi/okvir.php';
