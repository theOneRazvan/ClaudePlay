<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'PetFactory - Hrană pentru animale de companie' ?></title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <a href="/">PetFactory</a>
                </div>

                <nav class="nav">
                    <a href="/">Acasă</a>
                    <a href="/products">Produse</a>
                    <a href="/about">Despre noi</a>
                    <a href="/contact">Contact</a>
                </nav>

                <div class="header-actions">
                    <div class="search-box">
                        <form action="/search" method="GET">
                            <input type="text" name="q" placeholder="Caută produse..." required>
                            <button type="submit">🔍</button>
                        </form>
                    </div>

                    <a href="/cart" class="cart-icon">
                        🛒 <span class="cart-count" id="cartCount">0</span>
                    </a>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="user-menu">
                            <span>👤 <?= htmlspecialchars($_SESSION['user_name']) ?></span>
                            <?php if ($_SESSION['is_admin']): ?>
                                <a href="/admin">Admin</a>
                            <?php endif; ?>
                            <a href="/logout">Ieșire</a>
                        </div>
                    <?php else: ?>
                        <a href="/login" class="btn btn-primary">Autentificare</a>
                    <?php endif; ?>
                </div>

                <button class="mobile-menu-toggle" id="mobileMenuToggle">☰</button>
            </div>

            <nav class="mobile-nav" id="mobileNav">
                <a href="/">Acasă</a>
                <a href="/products">Produse</a>
                <a href="/about">Despre noi</a>
                <a href="/contact">Contact</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($_SESSION['is_admin']): ?>
                        <a href="/admin">Admin</a>
                    <?php endif; ?>
                    <a href="/logout">Ieșire</a>
                <?php else: ?>
                    <a href="/login">Autentificare</a>
                    <a href="/register">Înregistrare</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main class="main">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($_SESSION['success']) ?>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($_SESSION['error']) ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
