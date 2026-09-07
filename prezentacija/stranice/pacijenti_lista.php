<?php
/** @var array $korisnik */
/** @var array $lista */
/** @var array $terapeuti */
/** @var string $pretraga */
/** @var string $status */
/** @var string $poruka */
ob_start();
$pretraga = $pretraga ?? (string) ($_GET['q'] ?? '');
$status = $status ?? (string) ($_GET['status'] ?? '');
$poruka = $poruka ?? (string) ($_GET['poruka'] ?? '');
$ftIzbor = (string) ($_GET['ft'] ?? '');
?>
<section class="uvod mali">
    <a class="dugme" href="/pacijenti/novi">Novi pacijent</a>
</section>

<?php if ($poruka !== ''): ?>
    <p class="obavestenje"><?= htmlspecialchars($poruka) ?></p>
<?php endif; ?>

<form class="filter" method="get" action="/pacijenti">
    <label>Pretraga
        <input type="text" name="q" value="<?= htmlspecialchars($pretraga) ?>" placeholder="Ime, prezime ili e-posta">
    </label>
    <?php if ($korisnik['uloga'] === 'admin'): ?>
        <label>Fizioterapeut
            <select name="ft">
                <option value="">Svi</option>
                <?php foreach ($terapeuti as $ft): ?>
                    <option value="<?= (int) $ft->idFizioterapeuta() ?>" <?= $ftIzbor === (string) $ft->idFizioterapeuta() ? 'selected' : '' ?>>
                        <?= htmlspecialchars($ft->punoIme()) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
    <?php endif; ?>
    <label>Status
        <select name="status">
            <option value="">Svi</option>
            <option value="1" <?= $status === '1' ? 'selected' : '' ?>>Aktivni</option>
            <option value="0" <?= $status === '0' ? 'selected' : '' ?>>Neaktivni</option>
        </select>
    </label>
    <button type="submit">Primeni</button>
</form>

<div class="kartica">
    <table class="tabela">
        <thead>
            <tr>
                <th>Ime</th>
                <th>Terapeut</th>
                <th>E-pošta</th>
                <th>Telefon</th>
                <th>Status</th>
                
                <th></th>
            </tr>
        </thead>
        <tbody>
        <?php if (!$lista): ?>
            <tr><td colspan="5" class="prazno">Nema pacijenata za dati filter.</td></tr>
        <?php endif; ?>
        <?php foreach ($lista as $red): ?>
            <tr>
                <td><?= htmlspecialchars($red->punoIme()) ?></td>
                <td><?= htmlspecialchars($red->imeTerapeuta()) ?></td>
                <td><?= htmlspecialchars($red->email()) ?></td>
                <td><?= htmlspecialchars($red->telefon()) ?></td>
                <td><span class="status <?= $red->jeAktivan() ? 'da' : 'ne' ?>"><?= $red->jeAktivan() ? 'aktivan' : 'neaktivan' ?></span></td>
                <td class="akcije">
                    <a href="/pacijenti/izmena?id=<?= (int) $red->idPacijenta() ?>">Izmena</a>
                    <form method="post" action="/pacijenti/obrisi" onsubmit="return confirm('Obrisati ili deaktivirati ovaj karton?');">
                        <input type="hidden" name="id" value="<?= (int) $red->idPacijenta() ?>">
                        <button type="submit" class="link-opasnost">Brisanje</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php
$sadrzaj = ob_get_clean();
require dirname(__DIR__) . '/delovi/okvir.php';
