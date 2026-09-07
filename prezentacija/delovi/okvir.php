<?php
use Kinetika\Aplikacija\Meni;

$korisnik = $korisnik ?? null;
$naslov = $naslov ?? 'Kinetika';
$aktivna = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
?>
<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($naslov) ?> · Kinetika</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=fraunces:500,600,700|figtree:400,500,600" rel="stylesheet">
    <link rel="stylesheet" href="/css/stil.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>
<body class="app">
<aside class="bok">
    <a class="znak" href="/pocetna">
        <span class="znak-krug">K</span>
        <span>
            <strong>Kinetika</strong>
            <small>Ordinacija za dobar pokret</small>
        </span>
    </a>
    <nav class="meni">
        <?php foreach (Meni::stavke($korisnik['uloga'] ?? '') as $stavka): ?>
            <?php
            $aktivnaKlasa = ($aktivna === $stavka['putanja']
                || ($stavka['putanja'] !== '/pocetna' && str_starts_with((string) $aktivna, $stavka['putanja'])))
                ? 'aktivno' : '';
            ?>
            <a href="<?= htmlspecialchars($stavka['putanja']) ?>" class="<?= $aktivnaKlasa ?>">
                <?= htmlspecialchars($stavka['oznaka']) ?>
            </a>
        <?php endforeach; ?>
    </nav>
    <div class="bok-nalog">
        <span class="inicijali"><?= htmlspecialchars(mb_substr($korisnik['prikaz_ime'] ?? 'K', 0, 1)) ?></span>
        <span>
            <strong><?= htmlspecialchars($korisnik['prikaz_ime'] ?? '') ?></strong>
            <small><?= htmlspecialchars($korisnik['uloga'] ?? '') ?></small>
        </span>
        <a class="odjava" href="/odjava">Odjava</a>
    </div>
</aside>
<div class="platno">
    <header class="traka">
        <div>
            <p class="datum"><?= htmlspecialchars(strftime_sr()) ?></p>
            <h1><?= htmlspecialchars($naslov) ?></h1>
        </div>
    </header>

    <main class="sadrzaj">
        <?= $sadrzaj ?? '' ?>
    </main>

</div>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="/js/datum.js"></script>
</body>
</html>
<?php
function strftime_sr(): string
{
    $dani = ['nedelja', 'ponedeljak', 'utorak', 'sreda', 'četvrtak', 'petak', 'subota'];
    $meseci = [1 => 'januar', 'februar', 'mart', 'april', 'maj', 'jun', 'jul', 'avgust', 'septembar', 'oktobar', 'novembar', 'decembar'];
    $dt = new DateTime('now');
    return $dani[(int) $dt->format('w')] . ', ' . $dt->format('j') . '. ' . $meseci[(int) $dt->format('n')] . ' ' . $dt->format('Y');
}
