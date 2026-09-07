<?php
/** @var array $korisnik */
/** @var array $podaci */
/** @var array $greske */
/** @var object|null $zapis */
ob_start();
$id = (int) ($podaci['id_pacijenta'] ?? 0);
$novi = $id === 0;
$listaGresaka = $greske ?? [];
if ($listaGresaka && array_keys($listaGresaka) !== range(0, count($listaGresaka) - 1)) {
    $listaGresaka = array_values($listaGresaka);
}
?>


<form class="papir forma-uska" method="post" action="/pacijenti/sacuvaj">
    <input type="hidden" name="id_pacijenta" value="<?= $id ?>">
    <?php foreach ($listaGresaka as $g): ?>
        <p class="upozorenje"><?= htmlspecialchars((string) $g) ?></p>
    <?php endforeach; ?>

    <div class="dve-polja">
        <div>
            <label for="ime">Ime</label>
            <input id="ime" name="ime" type="text" required value="<?= htmlspecialchars((string) ($podaci['ime'] ?? '')) ?>">
        </div>
        <div>
            <label for="prezime">Prezime</label>
            <input id="prezime" name="prezime" type="text" required value="<?= htmlspecialchars((string) ($podaci['prezime'] ?? '')) ?>">
        </div>
    </div>

    <label for="email">E-pošta</label>
    <input id="email" name="email" type="email" required value="<?= htmlspecialchars((string) ($podaci['email'] ?? '')) ?>">

    <label for="telefon">Telefon</label>
    <input id="telefon" name="telefon" type="text" value="<?= htmlspecialchars((string) ($podaci['telefon'] ?? '')) ?>">

    <?php if (($korisnik['uloga'] ?? '') === 'admin'): ?>
        <label for="id_fizioterapeuta">Fizioterapeut</label>
        <select id="id_fizioterapeuta" name="id_fizioterapeuta" required>
            <option value="">Izaberite terapeuta</option>
            <?php foreach ($terapeuti as $ft): ?>
                <option value="<?= (int) $ft->idFizioterapeuta() ?>"
                    <?= (int) ($podaci['id_fizioterapeuta'] ?? 0) === $ft->idFizioterapeuta() ? 'selected' : '' ?>>
                    <?= htmlspecialchars($ft->punoIme()) ?>
                </option>
            <?php endforeach; ?>
        </select>
    <?php else: ?>
        <input type="hidden" name="id_fizioterapeuta" value="<?= (int) ($podaci['id_fizioterapeuta'] ?? 0) ?>">
    <?php endif; ?>

    <label for="lozinka"><?= $novi ? 'Lozinka za nalog pacijenta' : 'Nova lozinka (prazno = bez izmene)' ?></label>
    <input id="lozinka" name="lozinka" type="password" <?= $novi ? 'required minlength="8"' : 'minlength="8"' ?>>

    <?php if (!$novi): ?>
        <label class="cek">
            <input type="checkbox" name="aktivan" value="1" <?= (($podaci['aktivan'] ?? '1') === '1' || ($podaci['aktivan'] ?? 0) == 1) ? 'checked' : '' ?>>
            Nalog je aktivan
        </label>
    <?php else: ?>
        <input type="hidden" name="aktivan" value="1">
    <?php endif; ?>

    <button type="submit">Sačuvaj</button>
    <a class="dugme bledo" href="/pacijenti">Nazad</a>
</form>
<?php
$sadrzaj = ob_get_clean();
require dirname(__DIR__) . '/delovi/okvir.php';
