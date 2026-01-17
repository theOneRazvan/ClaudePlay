<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<div class="container">
    <section class="section">
        <h1 class="section-title">Toate produsele</h1>

        <div style="margin-bottom: 2rem;">
            <h3>Filtrează după colecție:</h3>
            <div style="display: flex; gap: 1rem; flex-wrap: wrap; margin-top: 1rem;">
                <a href="/products" class="btn btn-outline">Toate</a>
                <?php foreach ($collections as $collection): ?>
                    <a href="/collections/<?= htmlspecialchars($collection['slug']) ?>" class="btn btn-outline">
                        <?= htmlspecialchars($collection['name']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if (empty($products)): ?>
            <p style="text-align: center; padding: 3rem 0;">Nu am găsit niciun produs.</p>
        <?php else: ?>
            <div class="product-grid">
                <?php foreach ($products as $product): ?>
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

                            <?php if ($product['stock_quantity'] > 0): ?>
                                <button onclick="addToCart(<?= $product['id'] ?>)" class="btn btn-primary" style="width: 100%;">
                                    Adaugă în coș
                                </button>
                            <?php else: ?>
                                <button class="btn btn-outline" disabled style="width: 100%;">Stoc epuizat</button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <?php if ($i == $currentPage): ?>
                            <span class="active"><?= $i ?></span>
                        <?php else: ?>
                            <a href="?page=<?= $i ?>"><?= $i ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </section>
</div>

<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
