<?php
/** @var array $korisnik */
/** @var array $podaci */
ob_start();
$pozdrav = match ((int) date('G')) {
    5, 6, 7, 8, 9, 10, 11 => 'Dobro jutro',
    12, 13, 14, 15, 16, 17 => 'Dobar dan',
    default => 'Dobro veče',
};
?>
<section class="uvod">
    <h2><?= htmlspecialchars($pozdrav) ?><?= $korisnik['uloga'] === 'admin' ? '.' : ', ' . htmlspecialchars($korisnik['ime']) . '.' ?></h2>
</section>

<div class="mreza-brojki">
    <?php if ($korisnik['uloga'] === 'admin'): ?>
        <article class="kartica"><p>Fizioterapeuti</p><strong><?= (int) $podaci['broj_ft'] ?></strong></article>
        <article class="kartica"><p>Pacijenti</p><strong><?= (int) $podaci['broj_pacijenata'] ?></strong></article>
        <article class="kartica"><p>Današnji termini</p><strong><?= (int) $podaci['danas_termina'] ?></strong></article>
        <article class="kartica"><p>Zahtevi na čekanju</p><strong><?= (int) $podaci['zahteva'] ?></strong></article>
    <?php elseif ($korisnik['uloga'] === 'fizioterapeut'): ?>
        <article class="kartica"><p>Moji pacijenti</p><strong><?= (int) $podaci['broj_pacijenata'] ?></strong></article>
        <article class="kartica"><p>Današnji termini</p><strong><?= (int) $podaci['danas_termina'] ?></strong></article>
        <article class="kartica"><p>Zahtevi</p><strong><?= (int) $podaci['zahteva'] ?></strong></article>
    <?php else: ?>
        <article class="kartica"><p>Naredni termini</p><strong><?= count($podaci['raspored']) ?></strong></article>
    <?php endif; ?>
</div>

<div class="dve-kolone">
    <section class="kartica">
        <h3><?= $korisnik['uloga'] === 'pacijent' ? 'Vaši termini' : 'Raspored' ?></h3>
        <?php if (empty($podaci['raspored'])): ?>
            <p class="prazno">Nema zakazanih seansi za prikaz.</p>
        <?php else: ?>
            <ul class="lista-termina">
                <?php foreach ($podaci['raspored'] as $red): ?>
                    <li>
                        <time><?= htmlspecialchars((new DateTime($red['pocetak']))->format('d.m.Y.')) ?><br><?= htmlspecialchars((new DateTime($red['pocetak']))->format('H:i')) ?></time>
                        <span>
                            <?php if (isset($red['ime'])): ?>
                                <span class="termin-glava">
                                    <?= htmlspecialchars($red['ime'] . ' ' . $red['prezime']) ?>
                                    <?php if (($red['status'] ?? '') === 'odrzana'): ?>
                                        <span class="znak-odrzana" title="Održana">✓</span>
                                    <?php endif; ?>
                                </span>
                                <?php if (!empty($red['ime_ft'])): ?>
                                    <small><?= htmlspecialchars($red['ime_ft'] . ' ' . $red['prezime_ft']) ?></small>
                                <?php endif; ?>
                            <?php else: ?>
                                <?= htmlspecialchars($red['cilj'] ?? 'Seansa') ?>
                            <?php endif; ?>
                            <small>Seansa <?= (int) ($red['redni'] ?? 0) ?>/<?= (int) ($red['ukupno'] ?? 0) ?></small>
                        </span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>
</div>
<?php
$sadrzaj = ob_get_clean();
require dirname(__DIR__) . '/delovi/okvir.php';
