<?php
$currentPage = basename($_SERVER['PHP_SELF'] ?? 'index.php');
$navItems = [
    'index.php' => ['Home', '/'],
    'about.php' => ['About', '/about.php'],
    'services.php' => ['Services', '/services.php'],
    'contact.php' => ['Contact', '/contact.php'],
];
$pageTitle = $pageTitle ?? 'Nova Debt Counselling';
$pageDescription = $pageDescription ?? 'Professional debt counselling services from Nova Debt Counselling.';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?= htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8') ?>">
    <meta name="theme-color" content="#4B2661">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="icon" type="image/png" sizes="32x32" href="/images/Nova_favi_1757765316803.png">
    <link rel="shortcut icon" href="/images/Nova_favi_1757765316803.png" type="image/png">
    <link rel="stylesheet" href="/css/styles.css">
    <link rel="stylesheet" href="/css/icons.css">
</head>
<body>
<a class="skip-link" href="#main-content">Skip to content</a>
<header>
    <nav class="navbar" id="navbar" aria-label="Main navigation">
        <div class="container">
            <a class="nav-brand" href="/" aria-label="Nova Debt Counselling home">
                <span class="logo complete-logo"><img class="logo-text" src="/images/nova-text-logo.png" alt="Nova Debt Counselling"></span>
            </a>
            <button class="mobile-toggle" id="mobile-toggle" type="button" aria-label="Toggle navigation" aria-controls="nav-menu" aria-expanded="false">
                <span class="hamburger"></span><span class="hamburger"></span><span class="hamburger"></span>
            </button>
            <div class="nav-menu" id="nav-menu">
                <?php foreach ($navItems as $file => [$label, $href]): ?>
                    <a class="nav-link<?= $file === $currentPage ? ' active' : '' ?>" href="<?= htmlspecialchars($href, ENT_QUOTES, 'UTF-8') ?>"<?= $file === $currentPage ? ' aria-current="page"' : '' ?>><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></a>
                <?php endforeach; ?>
            </div>
        </div>
    </nav>
</header>
