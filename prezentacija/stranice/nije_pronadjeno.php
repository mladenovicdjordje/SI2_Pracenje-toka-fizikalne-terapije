<?php
$naslov = 'Stranica nije spremna';
ob_start();
?>
<section class="kartica">
    <p class="natpis">404</p>
    <p><a class="dugme" href="/pocetna">Nazad na početnu</a></p>
</section>
<?php
$sadrzaj = ob_get_clean();
if (!empty($korisnik)) {
    require dirname(__DIR__) . '/delovi/okvir.php';
} else {
    header('Location: /prijava');
}
