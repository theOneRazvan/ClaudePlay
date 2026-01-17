<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<div class="container">
    <section class="section">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; align-items: start;">
            <div>
                <?php if (!empty($product['images']) && $product['images'][0]['image_path']): ?>
                    <img src="<?= htmlspecialchars($product['images'][0]['image_path']) ?>"
                         alt="<?= htmlspecialchars($product['name']) ?>"
                         style="width: 100%; border-radius: 0.5rem;">
                <?php else: ?>
                    <div style="width: 100%; height: 400px; background: var(--bg-light); border-radius: 0.5rem;"></div>
                <?php endif; ?>
            </div>

            <div>
                <h1><?= htmlspecialchars($product['name']) ?></h1>

                <?php if ($product['collection_name']): ?>
                    <p style="color: var(--text-light); margin: 1rem 0;">
                        Colecție: <?= htmlspecialchars($product['collection_name']) ?>
                    </p>
                <?php endif; ?>

                <div style="font-size: 2rem; color: var(--primary-color); font-weight: bold; margin: 1.5rem 0;">
                    <?= number_format($product['price'], 2) ?> RON
                    <?php if ($product['compare_at_price']): ?>
                        <span style="font-size: 1.5rem; text-decoration: line-through; color: var(--text-light); margin-left: 1rem;">
                            <?= number_format($product['compare_at_price'], 2) ?> RON
                        </span>
                    <?php endif; ?>
                </div>

                <?php if ($product['description']): ?>
                    <div style="margin: 1.5rem 0; line-height: 1.8;">
                        <?= nl2br(htmlspecialchars($product['description'])) ?>
                    </div>
                <?php endif; ?>

                <?php if ($product['weight']): ?>
                    <p style="margin: 1rem 0;">
                        <strong>Greutate:</strong> <?= htmlspecialchars($product['weight']) ?> kg
                    </p>
                <?php endif; ?>

                <?php if ($product['stock_quantity'] > 0): ?>
                    <p style="color: var(--secondary-color); margin: 1rem 0;">
                        ✓ În stoc (<?= $product['stock_quantity'] ?> disponibile)
                    </p>
                <?php else: ?>
                    <p style="color: var(--danger-color); margin: 1rem 0;">
                        ✗ Stoc epuizat
                    </p>
                <?php endif; ?>

                <div style="margin: 2rem 0;">
                    <div class="form-group">
                        <label>Cantitate:</label>
                        <input type="number" id="quantity" value="1" min="1" max="<?= $product['stock_quantity'] ?>"
                               style="width: 100px;">
                    </div>

                    <?php if ($product['allow_subscription']): ?>
                        <div class="form-group">
                            <div class="checkbox-group">
                                <input type="checkbox" id="isSubscription">
                                <label for="isSubscription">
                                    Abonament
                                    <?php if ($product['subscription_discount'] > 0): ?>
                                        (economisești <?= $product['subscription_discount'] ?>%)
                                    <?php endif; ?>
                                </label>
                            </div>
                        </div>

                        <div class="form-group" id="frequencyGroup" style="display: none;">
                            <label>Frecvență livrare:</label>
                            <select id="frequency">
                                <?php foreach (SUBSCRIPTION_FREQUENCIES as $key => $label): ?>
                                    <option value="<?= $key ?>"><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>

                    <?php if ($product['stock_quantity'] > 0): ?>
                        <button onclick="addProductToCart()" class="btn btn-primary" style="width: 100%; padding: 1rem; font-size: 1.125rem;">
                            Adaugă în coș
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</div>

<input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

<script>
const isSubscriptionCheckbox = document.getElementById('isSubscription');
const frequencyGroup = document.getElementById('frequencyGroup');

if (isSubscriptionCheckbox) {
    isSubscriptionCheckbox.addEventListener('change', function() {
        frequencyGroup.style.display = this.checked ? 'block' : 'none';
    });
}

function addProductToCart() {
    const quantity = parseInt(document.getElementById('quantity').value);
    const isSubscription = document.getElementById('isSubscription')?.checked || false;
    const frequency = isSubscription ? document.getElementById('frequency').value : null;

    addToCart(<?= $product['id'] ?>, quantity, isSubscription, frequency);
}
</script>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
