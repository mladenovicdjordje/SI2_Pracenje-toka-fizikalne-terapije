<?php
/** @var array $korisnik */
/** @var array $podaci */
/** @var array $greske */
/** @var object|null $zapis */
ob_start();
$izmena = !empty($podaci['id_fizioterapeuta']);
?>
<section class="uvod mali">
    <a class="dugme senka" href="/fizioterapeuti">Nazad na listu</a>
</section>

<form class="papir forma" method="post" action="/fizioterapeuti/sacuvaj">
    <?php if ($izmena): ?>
        <input type="hidden" name="id_fizioterapeuta" value="<?= (int) $podaci['id_fizioterapeuta'] ?>">
    <?php endif; ?>
    <div class="dve-polja">
        <p>
            <label>Ime</label>
            <input type="text" name="ime" value="<?= htmlspecialchars($podaci['ime'] ?? '') ?>" required>
            <?php if (!empty($greske['ime'])): ?><small class="greska"><?= htmlspecialchars($greske['ime']) ?></small><?php endif; ?>
        </p>
        <p>
            <label>Prezime</label>
            <input type="text" name="prezime" value="<?= htmlspecialchars($podaci['prezime'] ?? '') ?>" required>
            <?php if (!empty($greske['prezime'])): ?><small class="greska"><?= htmlspecialchars($greske['prezime']) ?></small><?php endif; ?>
        </p>
    </div>
    <p>
        <label>E-pošta</label>
        <input type="email" name="email" value="<?= htmlspecialchars($podaci['email'] ?? '') ?>" required>
        <?php if (!empty($greske['email'])): ?><small class="greska"><?= htmlspecialchars($greske['email']) ?></small><?php endif; ?>
    </p>
    <p>
        <label>Telefon</label>
        <input type="text" name="telefon" value="<?= htmlspecialchars($podaci['telefon'] ?? '') ?>">
    </p>
    <p>
        <label>Lozinka<?= $izmena ? ' (opciono)' : '' ?></label>
        <input type="password" name="lozinka" autocomplete="new-password">
        <?php if (!empty($greske['lozinka'])): ?><small class="greska"><?= htmlspecialchars($greske['lozinka']) ?></small><?php endif; ?>
    </p>
    <?php if ($izmena): ?>
        <p>
            <label>Status</label>
            <select name="aktivan">
                <option value="1" <?= ($podaci['aktivan'] ?? '1') === '1' ? 'selected' : '' ?>>Aktivan</option>
                <option value="0" <?= ($podaci['aktivan'] ?? '') === '0' ? 'selected' : '' ?>>Neaktivan</option>
            </select>
        </p>
    <?php endif; ?>
    <button type="submit">Sačuvaj</button>
</form>
<?php
$sadrzaj = ob_get_clean();
require dirname(__DIR__) . '/delovi/okvir.php';
