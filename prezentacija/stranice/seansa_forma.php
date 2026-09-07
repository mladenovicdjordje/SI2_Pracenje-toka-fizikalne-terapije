<?php
/** @var array $korisnik */
/** @var string $datum */
/** @var string $sat */
/** @var array $pacijenti */
/** @var array $planovi */
/** @var array $greske */
ob_start();
$greske = $greske ?? [];
$uloga = $korisnik['uloga'] ?? '';
$idFt = (int) ($_GET['ft'] ?? $_POST['id_fizioterapeuta'] ?? ($korisnik['id_fizioterapeuta'] ?? 0));
$idPac = (int) ($_POST['id_pacijenta'] ?? $_GET['pac'] ?? ($korisnik['id_pacijenta'] ?? 0));
$idPlanaSel = (int) ($_POST['id_plana'] ?? 0);
?>
<section class="uvod mali">
    <div>
        
        <p>Termin: <?= htmlspecialchars((new DateTime($datum))->format('d.m.Y')) ?> u <?= htmlspecialchars($sat) ?></p>
    </div>
    <a class="dugme senka" href="/kalendar">Nazad na kalendar</a>
</section>

<form class="papir forma" method="post" action="/kalendar/sacuvaj">
    <input type="hidden" name="datum" value="<?= htmlspecialchars($datum) ?>">
    <input type="hidden" name="sat" value="<?= htmlspecialchars($sat) ?>">
    <input type="hidden" name="id_fizioterapeuta" value="<?= $idFt ?>">

    <?php if ($greske): ?>
        <p class="greska"><?= htmlspecialchars(implode(' ', $greske)) ?></p>
    <?php endif; ?>

    <?php if ($uloga === 'pacijent'): ?>
        <input type="hidden" name="id_pacijenta" value="<?= (int) $korisnik['id_pacijenta'] ?>">
        <p>
            <label>Plan terapije</label>
            <select name="id_plana" required>
                <option value="">Izaberite plan</option>
                <?php foreach ($planovi as $plan): ?>
                    <option value="<?= (int) $plan->idPlana() ?>" <?= $idPlanaSel === $plan->idPlana() ? 'selected' : '' ?>>
                        <?= htmlspecialchars($plan->tipPovrede() . ' · ' . $plan->deoTela()) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (!$planovi): ?><small class="greska">Nemate aktivan plan. Javite se terapeutu.</small><?php endif; ?>
        </p>
    <?php else: ?>
        <p>
            <label>Pacijent</label>
            <select name="id_pacijenta" required onchange="if(this.value){ location='/kalendar/zakazi?datum=<?= rawurlencode($datum) ?>&sat=<?= rawurlencode($sat) ?>&ft=<?= $idFt ?>&pac='+this.value; }">
                <option value="">Izaberite pacijenta</option>
                <?php foreach ($pacijenti as $p): ?>
                    <option value="<?= (int) $p->idPacijenta() ?>" <?= $idPac === $p->idPacijenta() ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p->punoIme()) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>
        <p>
            <label>Plan terapije</label>
            <select name="id_plana" required>
                <option value="">Izaberite plan</option>
                <?php foreach ($planovi as $plan): ?>
                    <option value="<?= (int) $plan->idPlana() ?>" <?= $idPlanaSel === $plan->idPlana() ? 'selected' : '' ?>>
                        <?= htmlspecialchars($plan->tipPovrede() . ' · ' . $plan->deoTela()) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if ($idPac && !$planovi): ?><small class="greska">Pacijent nema aktivan plan.</small><?php endif; ?>
        </p>
    <?php endif; ?>

    <p>
        <label>Napomena</label>
        <textarea name="napomena" rows="2"><?= htmlspecialchars($_POST['napomena'] ?? '') ?></textarea>
    </p>
    <button type="submit"><?= $uloga === 'pacijent' ? 'Posalji zahtev' : 'Zakaži termin' ?></button>
</form>
<?php
$sadrzaj = ob_get_clean();
require dirname(__DIR__) . '/delovi/okvir.php';