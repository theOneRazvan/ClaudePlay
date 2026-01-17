<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<div class="container">
    <section class="section">
        <div style="max-width: 800px; margin: 0 auto; text-align: center;">
            <div style="font-size: 4rem; color: var(--secondary-color); margin-bottom: 1rem;">✓</div>
            <h1>Comanda ta a fost plasată cu succes!</h1>
            <p style="font-size: 1.25rem; margin: 1.5rem 0;">
                Număr comandă: <strong><?= htmlspecialchars($order['order_number']) ?></strong>
            </p>
        </div>

        <div style="max-width: 800px; margin: 3rem auto;">
            <div style="background: var(--bg-light); padding: 2rem; border-radius: 0.5rem;">
                <h2>Detalii comandă</h2>

                <div style="margin: 1.5rem 0;">
                    <h3>Informații livrare:</h3>
                    <p>
                        <?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?><br>
                        <?= htmlspecialchars($order['address']) ?><br>
                        <?= htmlspecialchars($order['city']) ?>, <?= htmlspecialchars($order['county']) ?>, <?= htmlspecialchars($order['postal_code']) ?><br>
                        Telefon: <?= htmlspecialchars($order['phone']) ?><br>
                        Email: <?= htmlspecialchars($order['email']) ?>
                    </p>
                </div>

                <div style="margin: 1.5rem 0;">
                    <h3>Produse comandate:</h3>
                    <?php foreach ($items as $item): ?>
                        <div style="display: flex; justify-content: space-between; margin: 0.5rem 0; padding: 0.5rem 0; border-bottom: 1px solid var(--border-color);">
                            <span><?= htmlspecialchars($item['product_name']) ?> x <?= $item['quantity'] ?></span>
                            <span><?= number_format($item['total'], 2) ?> RON</span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div style="margin: 1.5rem 0; padding-top: 1rem; border-top: 2px solid var(--border-color);">
                    <div style="display: flex; justify-content: space-between; margin: 0.5rem 0;">
                        <span>Subtotal:</span>
                        <span><?= number_format($order['subtotal'], 2) ?> RON</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin: 0.5rem 0;">
                        <span>Transport:</span>
                        <span><?= $order['shipping_cost'] == 0 ? 'GRATUIT' : number_format($order['shipping_cost'], 2) . ' RON' ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin: 1rem 0; font-size: 1.25rem; font-weight: bold;">
                        <span>Total:</span>
                        <span style="color: var(--primary-color);"><?= number_format($order['total'], 2) ?> RON</span>
                    </div>
                </div>

                <p style="margin-top: 2rem; color: var(--text-light);">
                    Vei primi un email de confirmare în curând. Comanda ta va fi procesată și expediată în cel mai scurt timp posibil.
                </p>
            </div>

            <div style="text-align: center; margin-top: 2rem;">
                <a href="/" class="btn btn-primary">Înapoi la pagina principală</a>
                <a href="/products" class="btn btn-outline">Continuă cumpărăturile</a>
            </div>
        </div>
    </section>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
