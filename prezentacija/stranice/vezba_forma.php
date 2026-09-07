<?php
/** @var array $korisnik */
/** @var array $podaci */
/** @var array $greske */
ob_start();
$izmena = !empty($podaci['id_vezbe']);
?>
<section class="uvod mali">
    <a class="dugme senka" href="/katalog-vezbi">Nazad na katalog</a>
</section>

<form class="papir forma" method="post" action="/katalog-vezbi/sacuvaj">
    <?php if ($izmena): ?>
        <input type="hidden" name="id_vezbe" value="<?= (int) $podaci['id_vezbe'] ?>">
    <?php endif; ?>
    <p>
        <label>Naziv</label>
        <input type="text" name="naziv" value="<?= htmlspecialchars($podaci['naziv'] ?? '') ?>" required>
        <?php if (!empty($greske['naziv'])): ?><small class="greska"><?= htmlspecialchars($greske['naziv']) ?></small><?php endif; ?>
    </p>
    <p>
        <label>Grupa mišića</label>
        <input type="text" name="grupa_misica" value="<?= htmlspecialchars($podaci['grupa_misica'] ?? '') ?>" list="grupe-predlog" required>
        <datalist id="grupe-predlog">
            <option value="karlica">
            <option value="kukovi">
            <option value="koleno">
            <option value="ledja">
            <option value="rame">
            <option value="vrat">
            <option value="skocni zglob">
        </datalist>
        <?php if (!empty($greske['grupa_misica'])): ?><small class="greska"><?= htmlspecialchars($greske['grupa_misica']) ?></small><?php endif; ?>
    </p>
    <p>
        <label>Opis</label>
        <textarea name="opis" rows="4"><?= htmlspecialchars($podaci['opis'] ?? '') ?></textarea>
    </p>
    <p>
        <label>Kontraindikacije</label>
        <textarea name="kontraindikacije" rows="3"><?= htmlspecialchars($podaci['kontraindikacije'] ?? '') ?></textarea>
    </p>
    <p>
        <label>Status</label>
        <select name="aktivna">
            <option value="1" <?= ($podaci['aktivna'] ?? '1') === '1' ? 'selected' : '' ?>>Aktivna</option>
            <option value="0" <?= ($podaci['aktivna'] ?? '') === '0' ? 'selected' : '' ?>>Neaktivna</option>
        </select>
    </p>
    <button type="submit">Sačuvaj</button>
</form>
<?php
$sadrzaj = ob_get_clean();
require dirname(__DIR__) . '/delovi/okvir.php';
