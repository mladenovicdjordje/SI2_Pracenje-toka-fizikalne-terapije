<?php
$naslov = 'Pristup zabranjen';
$poruka = $poruka ?? 'Nemate pravo da otvorite ovu stranicu.';
ob_start();
?>
<section class="kartica">
    <p class="natpis">403</p>
    <h2><?= htmlspecialchars($poruka) ?></h2>
    <p><a class="dugme" href="/pocetna">Nazad na početnu</a></p>
</section>
<?php
$sadrzaj = ob_get_clean();
require dirname(__DIR__) . '/delovi/okvir.php';
