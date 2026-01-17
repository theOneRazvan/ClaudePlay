<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<div class="container">
    <section class="section">
        <h1 class="section-title">Coșul tău de cumpărături</h1>

        <?php if (empty($items)): ?>
            <div style="text-align: center; padding: 3rem 0;">
                <p style="font-size: 1.25rem; margin-bottom: 2rem;">Coșul tău este gol</p>
                <a href="/products" class="btn btn-primary">Continuă cumpărăturile</a>
            </div>
        <?php else: ?>
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; margin-top: 2rem;">
                <div>
                    <?php foreach ($items as $item): ?>
                        <div class="cart-item">
                            <?php if ($item['image']): ?>
                                <img src="<?= htmlspecialchars($item['image']) ?>"
                                     alt="<?= htmlspecialchars($item['name']) ?>"
                                     class="cart-item-image">
                            <?php else: ?>
                                <div class="cart-item-image" style="background: var(--bg-light);"></div>
                            <?php endif; ?>

                            <div>
                                <h3><?= htmlspecialchars($item['name']) ?></h3>
                                <p style="color: var(--text-light);">
                                    <?php
                                    $itemPrice = $item['price'];
                                    if ($item['is_subscription'] && $item['subscription_discount'] > 0) {
                                        $itemPrice = $itemPrice * (1 - $item['subscription_discount'] / 100);
                                        echo "Abonament ({$item['subscription_frequency']})";
                                    } else {
                                        echo "Cumpărare unică";
                                    }
                                    ?>
                                </p>
                            </div>

                            <div class="quantity-control">
                                <button onclick="updateCartItem(<?= $item['id'] ?>, <?= $item['quantity'] - 1 ?>)">-</button>
                                <input type="number" value="<?= $item['quantity'] ?>" readonly>
                                <button onclick="updateCartItem(<?= $item['id'] ?>, <?= $item['quantity'] + 1 ?>)">+</button>
                            </div>

                            <div style="font-weight: bold; font-size: 1.125rem;">
                                <?php
                                $itemPrice = $item['price'];
                                if ($item['is_subscription'] && $item['subscription_discount'] > 0) {
                                    $itemPrice = $itemPrice * (1 - $item['subscription_discount'] / 100);
                                }
                                echo number_format($itemPrice * $item['quantity'], 2);
                                ?> RON
                            </div>

                            <button onclick="removeCartItem(<?= $item['id'] ?>)" class="btn btn-danger">
                                Elimină
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div>
                    <div style="background: var(--bg-light); padding: 2rem; border-radius: 0.5rem; position: sticky; top: 100px;">
                        <h2 style="margin-bottom: 1.5rem;">Sumar comandă</h2>

                        <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border-color);">
                            <span>Subtotal:</span>
                            <span style="font-weight: bold;"><?= number_format($total, 2) ?> RON</span>
                        </div>

                        <div style="display: flex; justify-content: space-between; margin-bottom: 1.5rem; font-size: 1.25rem; font-weight: bold;">
                            <span>Total:</span>
                            <span style="color: var(--primary-color);"><?= number_format($total, 2) ?> RON</span>
                        </div>

                        <a href="/checkout" class="btn btn-primary" style="width: 100%; padding: 1rem; font-size: 1.125rem;">
                            Finalizează comanda
                        </a>

                        <a href="/products" class="btn btn-outline" style="width: 100%; margin-top: 1rem;">
                            Continuă cumpărăturile
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </section>
</div>

<input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
