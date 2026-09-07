<?php
$greska = $greska ?? '';
?>
<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Prijava · Kinetika</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=fraunces:560,700|figtree:400,500,600" rel="stylesheet">
    <link rel="stylesheet" href="/css/stil.css">
</head>
<body class="prijava-telo">
    <main class="prijava-okvir">
        <h1>Kinetika</h1>
        <p class="podnaslov">Ordinacija za dobar pokret</p>

        <form class="papir" method="post" action="/prijava" autocomplete="on">
            <h2>Prijavite se</h2>
            <?php if ($greska !== ''): ?>
                <p class="upozorenje"><?= htmlspecialchars($greska) ?></p>
            <?php endif; ?>
            <label for="email">E-pošta</label>
            <input id="email" name="email" type="email" required autofocus
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                   placeholder="vaša e-mail adresa">

            <label for="lozinka">Lozinka</label>
            <input id="lozinka" name="lozinka" type="password" required placeholder="vaša lozinka">

            <button type="submit">Prijavi se</button>
        </form>
        
    </main>
</body>
</html>
