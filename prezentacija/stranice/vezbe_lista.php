<?php
/** @var array $korisnik */
/** @var array $lista */
/** @var array $grupe */
/** @var string $pretraga */
/** @var string $grupa */
/** @var string $poruka */
ob_start();
$pretraga = $pretraga ?? '';
$status = $status ?? '';
$poruka = $poruka ?? '';
$grupa = $grupa ?? '';
?>
<section class="uvod mali">
    <a class="dugme" href="/katalog-vezbi/nova">Nova vežba</a>
</section>

<?php if ($poruka !== ''): ?>
    <p class="obavestenje"><?= htmlspecialchars($poruka) ?></p>
<?php endif; ?>

<form class="filter" method="get" action="/katalog-vezbi">
    <label>Pretraga
        <input type="text" name="q" value="<?= htmlspecialchars($pretraga) ?>" placeholder="Naziv ili opis">
    </label>
    <label>Grupa mišića
        <select name="grupa">
            <option value="">Sve</option>
            <?php foreach ($grupe as $g): ?>
                <option value="<?= htmlspecialchars($g) ?>" <?= $grupa === $g ? 'selected' : '' ?>><?= htmlspecialchars($g) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <button type="submit">Primeni</button>
</form>

<div class="kartica">
    <table class="tabela">
        <thead>
            <tr>
                <th>Naziv</th>
                <th>Grupa</th>
                <th>Opis</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <?php if (!$lista): ?>
            <tr><td colspan="5" class="prazno">Nema vežbi za dati filter.</td></tr>
        <?php endif; ?>
        <?php foreach ($lista as $red): ?>
            <tr>
                <td><?= htmlspecialchars($red->naziv()) ?></td>
                <td><?= htmlspecialchars($red->grupaMisica()) ?></td>
                <td><?= htmlspecialchars(mb_strimwidth($red->opis() ?? '—', 0, 80, '…')) ?></td>
                <td><span class="status <?= $red->jeAktivna() ? 'da' : 'ne' ?>"><?= $red->jeAktivna() ? 'aktivna' : 'neaktivna' ?></span></td>
                <td class="akcije">
                    <a href="/katalog-vezbi/izmena?id=<?= (int) $red->idVezbe() ?>">Izmena</a>
                    <form method="post" action="/katalog-vezbi/obrisi" onsubmit="return confirm('Obrisati vezbu iz kataloga?');">
                        <input type="hidden" name="id" value="<?= (int) $red->idVezbe() ?>">
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
