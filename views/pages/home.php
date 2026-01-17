<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<section class="hero">
    <div class="container">
        <h1>Hrană de calitate pentru animalele tale</h1>
        <p>Descoperă gama noastră completă de produse pentru câini și pisici</p>
        <a href="/products" class="btn btn-primary">Vezi produsele</a>
    </div>
</section>

<section class="section">
    <div class="container">
        <h2 class="section-title">Colecții populare</h2>

        <div class="product-grid">
            <?php foreach ($collections as $collection): ?>
                <div class="product-card">
                    <div class="product-info">
                        <h3 class="product-name">
                            <a href="/collections/<?= htmlspecialchars($collection['slug']) ?>">
                                <?= htmlspecialchars($collection['name']) ?>
                            </a>
                        </h3>
                        <p><?= htmlspecialchars($collection['description']) ?></p>
                        <a href="/collections/<?= htmlspecialchars($collection['slug']) ?>" class="btn btn-outline">
                            Explorează
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section" style="background: var(--bg-light);">
    <div class="container">
        <h2 class="section-title">Produse recomandate</h2>

        <div class="product-grid">
            <?php foreach ($featuredProducts as $product): ?>
                <div class="product-card">
                    <?php if ($product['primary_image']): ?>
                        <img src="<?= htmlspecialchars($product['primary_image']) ?>"
                             alt="<?= htmlspecialchars($product['name']) ?>"
                             class="product-image">
                    <?php else: ?>
                        <div class="product-image"></div>
                    <?php endif; ?>

                    <div class="product-info">
                        <h3 class="product-name">
                            <a href="/products/<?= htmlspecialchars($product['slug']) ?>">
                                <?= htmlspecialchars($product['name']) ?>
                            </a>
                        </h3>

                        <div class="product-price">
                            <?= number_format($product['price'], 2) ?> RON
                            <?php if ($product['compare_at_price']): ?>
                                <span class="price-compare">
                                    <?= number_format($product['compare_at_price'], 2) ?> RON
                                </span>
                            <?php endif; ?>
                        </div>

                        <button onclick="addToCart(<?= $product['id'] ?>)" class="btn btn-primary" style="width: 100%;">
                            Adaugă în coș
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div style="text-align: center; max-width: 800px; margin: 0 auto;">
            <h2 class="section-title">De ce să alegi PetFactory?</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 2rem; margin-top: 2rem;">
                <div>
                    <h3>Produse de calitate</h3>
                    <p>Selectăm doar cele mai bune produse pentru animalele tale</p>
                </div>
                <div>
                    <h3>Livrare rapidă</h3>
                    <p>Livrăm în toată țara în 24-48 ore</p>
                </div>
                <div>
                    <h3>Abonamente flexibile</h3>
                    <p>Primește produsele automat la intervale regulate</p>
                </div>
            </div>
        </div>
    </div>
</section>

<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
