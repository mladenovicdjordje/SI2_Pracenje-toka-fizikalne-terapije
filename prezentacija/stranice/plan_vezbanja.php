<?php
$lista = $lista ?? $planovi ?? [];
$stavkePoPlanu = $stavkePoPlanu ?? $stavke ?? [];
ob_start();
?>
<?php if (!$lista): ?>
    <div class="kartica"><p class="prazno">Nemate aktivan plan terapije.</p></div>
<?php endif; ?>

<?php foreach ($lista as $plan):
    $idPlana = is_object($plan) ? $plan->idPlana() : (int) ($plan['id_plana'] ?? 0);
    $deo = is_object($plan) ? $plan->deoTela() : (string) ($plan['deo_tela'] ?? '');
    $tip = is_object($plan) ? $plan->tipPovrede() : (string) ($plan['tip_povrede'] ?? '');
    $sirovo = $stavkePoPlanu[$idPlana] ?? [];
    $stavkeLista = (isset($sirovo['vezbe']) && is_array($sirovo['vezbe'])) ? $sirovo['vezbe'] : [];
?>
    <section class="kartica" style="margin-bottom:14px">
        <p class="natpis"><?= htmlspecialchars($deo) ?></p>
        <p><?= htmlspecialchars($tip) ?></p>
        <table class="tabela">
            <thead>
                <tr>
                    <th>Vežba</th>
                    <th>Deo tela</th>
                    <th>Serije</th>
                    <th>Ponavljanja</th>
                    <th>Opis</th>
                    <th>Kontraindikacije</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!$stavkeLista): ?>
                <tr><td colspan="6" class="prazno">Terapeut jos nije dodao vežbe.</td></tr>
            <?php endif; ?>
            <?php foreach ($stavkeLista as $s):
                $naziv = is_array($s) ? (string) ($s['naziv'] ?? $s['naziv_vezbe'] ?? '') : $s->nazivVezbe();
                $deoS = is_array($s) ? (string) ($s['deo_tela'] ?? '') : $s->nazivDelaTela();
                $ser = is_array($s) ? (int) ($s['serije'] ?? 0) : $s->serije();
                $pon = is_array($s) ? (int) ($s['ponavljanja'] ?? 0) : $s->ponavljanja();
                $opis = is_array($s) ? (string) ($s['opis'] ?? '') : '';
                $kontra = is_array($s) ? (string) ($s['kontraindikacije'] ?? '') : '';
            ?>
                <tr>
                    <td><?= htmlspecialchars($naziv) ?></td>
                    <td><?= htmlspecialchars($deoS) ?></td>
                    <td><?= $ser ?></td>
                    <td><?= $pon ?></td>
                    <td><?= htmlspecialchars($opis) ?></td>
                    <td><?= htmlspecialchars($kontra) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </section>
<?php endforeach; ?>
<?php
$sadrzaj = ob_get_clean();
require dirname(__DIR__) . '/delovi/okvir.php';