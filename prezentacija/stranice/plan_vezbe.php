<?php
$poruka = $poruka ?? '';
$samoPregled = $samoPregled ?? false;
$stavke = $stavke ?? [];
$katalog = $katalog ?? [];
$delovi = $delovi ?? [];
$planId = is_array($plan) ? (int) ($plan['id_plana'] ?? 0) : (int) $plan->idPlana();
$planDeoId = is_array($plan) ? (int) ($plan['id_dela_tela'] ?? 0) : (int) $plan->idDelaTela();
$planIme = is_array($plan)
    ? trim(($plan['ime_pacijenta'] ?? '') . ' ' . ($plan['prezime_pacijenta'] ?? ''))
    : $plan->imePacijenta();
$planPovreda = is_array($plan) ? (string) ($plan['tip_povrede'] ?? '') : $plan->tipPovrede();
$planDeoNaziv = is_array($plan) ? (string) ($plan['deo_tela'] ?? '') : $plan->deoTela();
ob_start();
?>
<p class="alatka"><a class="dugme senka" href="/planovi">Nazad na planove</a></p>

<?php if ($poruka !== ''): ?>
    <p class="obavestenje"><?= htmlspecialchars($poruka) ?></p>
<?php endif; ?>

<p><?= htmlspecialchars(trim($planIme . ' · ' . $planPovreda . ' · ' . $planDeoNaziv)) ?></p>

<?php if (!$samoPregled): ?>
<form class="kartica filter" method="post" action="/planovi/vezbe/dodaj">
    <input type="hidden" name="id_plana" value="<?= $planId ?>">
    <label>Vežba
        <select name="id_vezbe" required>
            <option value="">Iz kataloga</option>
            <?php foreach ($katalog as $v):
                $idV = is_array($v) ? (int) ($v['id_vezbe'] ?? 0) : $v->idVezbe();
                $nazivV = is_array($v) ? (string) ($v['naziv'] ?? '') : $v->naziv();
            ?>
                <option value="<?= $idV ?>"><?= htmlspecialchars($nazivV) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <label>Deo tela
        <select name="id_dela_tela" required>
            <option value="">Deo tela</option>
            <?php foreach ($delovi as $deo):
                $deoId = is_array($deo) ? (int) ($deo['id_dela_tela'] ?? 0) : $deo->idDelaTela();
                $deoNaziv = is_array($deo) ? (string) ($deo['naziv'] ?? '') : $deo->naziv();
            ?>
                <option value="<?= $deoId ?>" <?= $planDeoId === $deoId ? 'selected' : '' ?>>
                    <?= htmlspecialchars($deoNaziv) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>
    <label>Serije
        <input type="number" name="serije" min="1" value="3" required>
    </label>
    <label>Ponavljanja
        <input type="number" name="ponavljanja" min="1" value="10" required>
    </label>
    <button type="submit">Dodaj</button>
</form>
<?php endif; ?>

<div class="kartica">
    <table class="tabela">
        <thead>
            <tr>
                <th>Vežba</th>
                <th>Deo tela</th>
                <th>Serije</th>
                <th>Ponavljanja</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <?php if (!$stavke): ?>
            <tr><td colspan="5" class="prazno">Jos nema vežbi u ovom planu.</td></tr>
        <?php endif; ?>
        <?php foreach ($stavke as $s):
            if (is_array($s)) {
                $idPv = (int) ($s['id_plan_vezba'] ?? 0);
                $nazivS = (string) ($s['naziv_vezbe'] ?? $s['naziv'] ?? '');
                $deoS = (string) ($s['deo_tela'] ?? $s['naziv_dela'] ?? '');
                $serije = (int) ($s['serije'] ?? 0);
                $pon = (int) ($s['ponavljanja'] ?? 0);
            } else {
                $idPv = $s->idPlanVezba();
                $nazivS = $s->nazivVezbe();
                $deoS = $s->nazivDelaTela();
                $serije = $s->serije();
                $pon = $s->ponavljanja();
            }
        ?>
            <tr>
                <td><?= htmlspecialchars($nazivS) ?></td>
                <td><?= htmlspecialchars($deoS) ?></td>
                <td><?= $serije ?></td>
                <td><?= $pon ?></td>
                <td class="akcije">
                    <?php if (!$samoPregled): ?>
                        <form method="post" action="/planovi/vezbe/ukloni" onsubmit="return confirm('Ukloniti vežbu iz plana?');">
                            <input type="hidden" name="id_plana" value="<?= $planId ?>">
                            <input type="hidden" name="id_plan_vezba" value="<?= $idPv ?>">
                            <button type="submit" class="link-opasnost">Ukloni</button>
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