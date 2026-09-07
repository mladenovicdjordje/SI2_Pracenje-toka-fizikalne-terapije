<?php
$lista = $lista ?? [];
$seansePoPlanu = $seansePoPlanu ?? [];
$vezbePoPlanu = $vezbePoPlanu ?? [];
$pojam = $pojam ?? '';
$status = $status ?? '';
?>
<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="utf-8">
    <title>Kinetika — izveštaj</title>
    <link rel="stylesheet" href="/css/stampa.css">
</head>
<body>
    <header>
        <p class="natpis">Ordinacija Kinetika</p>
        <h1>Izveštaj toka terapije</h1>
        <p>
            Filter:
            <?= $pojam !== '' ? 'pretraga „' . htmlspecialchars((string) $pojam) . '“ · ' : '' ?>
            <?= $status !== '' ? 'status ' . htmlspecialchars((string) $status) . ' · ' : '' ?>
            <?= count($lista) ?> zapis(a)
            · <?= date('d.m.Y. H:i') ?>
        </p>
        <button type="button" onclick="window.print()">Štampaj</button>
    </header>

<?php if (!$lista): ?>
    <p>Nema zapisa za dati filter.</p>
<?php endif; ?>

<?php foreach ($lista as $red):
    $idPlana = $red->idPlana();
    $seanse = $seansePoPlanu[$idPlana] ?? [];
    $vezbe = $vezbePoPlanu[$idPlana] ?? [];
?>
    <section class="blok-plan">
        <h2><?= htmlspecialchars($red->imePacijenta()) ?></h2>
        <p>
            Terapeut: <?= htmlspecialchars($red->imeTerapeuta()) ?>
            · <?= htmlspecialchars($red->tipPovrede()) ?>
            · <?= htmlspecialchars($red->deoTela()) ?>
            · seanse <?= (int) $red->brojOdrzanih() ?> / <?= (int) $red->predvidjenBrojSeansi() ?>
            · <?= htmlspecialchars($red->status()) ?>
        </p>

        <h3>Seanse</h3>
        <?php if (!$seanse): ?>
            <p>Nema zakazanih seansi.</p>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Datum</th>
                    <th>Vreme</th>
                    <th>Trajanje</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($seanse as $s):
                $dt = new DateTime($s->pocetak());
            ?>
                <tr>
                    <td><?= htmlspecialchars($dt->format('d.m.Y.')) ?></td>
                    <td><?= htmlspecialchars($dt->format('H:i')) ?></td>
                    <td><?= (int) $s->trajanjeMin() ?> min</td>
                    <td><?= htmlspecialchars($s->status()) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>

        <h3>Vežbe</h3>
        <?php if (!$vezbe): ?>
            <p>Nema vežbi u planu.</p>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Datum</th>
                    <th>Vreme</th>
                    <th>Trajanje</th>
                    <th>Status</th>
                    <th>Bol</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($seanse as $s):
                $dt = new DateTime($s->pocetak());
            ?>
                <tr>
                    <td><?= htmlspecialchars($dt->format('d.m.Y.')) ?></td>
                    <td><?= htmlspecialchars($dt->format('H:i')) ?></td>
                    <td><?= (int) $s->trajanjeMin() ?> min</td>
                    <td><?= htmlspecialchars($s->status()) ?></td>
                    <td>
                        <?php if ($s->nivoBola() !== null): ?>
                            <?= (int) $s->nivoBola() ?>/10
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </section>
<?php endforeach; ?>
</body>
</html>