<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<div class="container">
    <section class="section">
        <h1 class="section-title">Finalizare comandă</h1>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 3rem; margin-top: 2rem;">
            <form method="POST" action="/checkout">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                <h2>Informații de livrare</h2>

                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" value="<?= $user['email'] ?? '' ?>" required>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label>Prenume *</label>
                        <input type="text" name="first_name" value="<?= explode(' ', $user['name'] ?? '')[0] ?? '' ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Nume *</label>
                        <input type="text" name="last_name" value="<?= explode(' ', $user['name'] ?? ' ')[1] ?? '' ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Telefon *</label>
                    <input type="tel" name="phone" required>
                </div>

                <div class="form-group">
                    <label>Adresă *</label>
                    <input type="text" name="address" placeholder="Strada, număr, bloc, apartament" required>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label>Oraș *</label>
                        <input type="text" name="city" required>
                    </div>

                    <div class="form-group">
                        <label>Județ *</label>
                        <input type="text" name="county" required>
                    </div>

                    <div class="form-group">
                        <label>Cod poștal *</label>
                        <input type="text" name="postal_code" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Metoda de plată *</label>
                    <select name="payment_method" required>
                        <option value="card">Card bancar</option>
                        <option value="cash">Numerar la livrare</option>
                        <option value="transfer">Transfer bancar</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Observații (opțional)</label>
                    <textarea name="notes" placeholder="Instrucțiuni speciale pentru livrare..."></textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem; font-size: 1.125rem;">
                    Plasează comanda
                </button>
            </form>

            <div>
                <div style="background: var(--bg-light); padding: 2rem; border-radius: 0.5rem; position: sticky; top: 100px;">
                    <h2 style="margin-bottom: 1.5rem;">Sumar comandă</h2>

                    <div style="max-height: 300px; overflow-y: auto; margin-bottom: 1.5rem;">
                        <?php foreach ($items as $item): ?>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border-color);">
                                <div>
                                    <strong><?= htmlspecialchars($item['name']) ?></strong>
                                    <br>
                                    <small>Cantitate: <?= $item['quantity'] ?></small>
                                </div>
                                <div style="font-weight: bold;">
                                    <?php
                                    $itemPrice = $item['price'];
                                    if ($item['is_subscription'] && $item['subscription_discount'] > 0) {
                                        $itemPrice = $itemPrice * (1 - $item['subscription_discount'] / 100);
                                    }
                                    echo number_format($itemPrice * $item['quantity'], 2);
                                    ?> RON
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span>Subtotal:</span>
                        <span><?= number_format($subtotal, 2) ?> RON</span>
                    </div>

                    <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border-color);">
                        <span>Transport:</span>
                        <span><?= $shipping_cost == 0 ? 'GRATUIT' : number_format($shipping_cost, 2) . ' RON' ?></span>
                    </div>

                    <div style="display: flex; justify-content: space-between; font-size: 1.25rem; font-weight: bold;">
                        <span>Total:</span>
                        <span style="color: var(--primary-color);"><?= number_format($total, 2) ?> RON</span>
                    </div>

                    <?php if ($subtotal < 200): ?>
                        <p style="margin-top: 1rem; font-size: 0.875rem; color: var(--text-light);">
                            Adaugă produse în valoare de <?= number_format(200 - $subtotal, 2) ?> RON pentru transport gratuit!
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
