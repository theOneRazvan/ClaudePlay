<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comandă #<?= htmlspecialchars($order['order_number']) ?> - Admin - PetFactory</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <div class="admin-layout">
        <aside class="admin-sidebar">
            <h2>PetFactory Admin</h2>
            <nav>
                <a href="/admin">📊 Dashboard</a>
                <a href="/admin/products">📦 Produse</a>
                <a href="/admin/collections">📁 Colecții</a>
                <a href="/admin/orders">🛒 Comenzi</a>
                <a href="/">🏠 Înapoi la site</a>
                <a href="/logout">🚪 Ieșire</a>
            </nav>
        </aside>

        <main class="admin-content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <h1>Comandă #<?= htmlspecialchars($order['order_number']) ?></h1>
                <a href="/admin/orders" class="btn btn-outline">← Înapoi la comenzi</a>
            </div>

            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
                <div>
                    <div style="background: var(--white); padding: 2rem; border-radius: 0.5rem; box-shadow: var(--shadow); margin-bottom: 2rem;">
                        <h2>Informații client</h2>
                        <p style="margin: 0.5rem 0;">
                            <strong>Nume:</strong> <?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?>
                        </p>
                        <p style="margin: 0.5rem 0;">
                            <strong>Email:</strong> <?= htmlspecialchars($order['email']) ?>
                        </p>
                        <p style="margin: 0.5rem 0;">
                            <strong>Telefon:</strong> <?= htmlspecialchars($order['phone']) ?>
                        </p>
                        <p style="margin: 0.5rem 0;">
                            <strong>Adresă:</strong> <?= htmlspecialchars($order['address']) ?>
                        </p>
                        <p style="margin: 0.5rem 0;">
                            <strong>Oraș:</strong> <?= htmlspecialchars($order['city']) ?>
                        </p>
                        <p style="margin: 0.5rem 0;">
                            <strong>Județ:</strong> <?= htmlspecialchars($order['county']) ?>
                        </p>
                        <p style="margin: 0.5rem 0;">
                            <strong>Cod poștal:</strong> <?= htmlspecialchars($order['postal_code']) ?>
                        </p>
                    </div>

                    <div style="background: var(--white); padding: 2rem; border-radius: 0.5rem; box-shadow: var(--shadow);">
                        <h2>Produse comandate</h2>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Produs</th>
                                    <th>SKU</th>
                                    <th>Cantitate</th>
                                    <th>Preț unitar</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $item): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($item['product_name']) ?></td>
                                        <td><?= htmlspecialchars($item['product_sku'] ?? 'N/A') ?></td>
                                        <td><?= $item['quantity'] ?></td>
                                        <td><?= number_format($item['price'], 2) ?> RON</td>
                                        <td><?= number_format($item['total'], 2) ?> RON</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                        <div style="margin-top: 2rem; padding-top: 1rem; border-top: 2px solid var(--border-color);">
                            <div style="display: flex; justify-content: space-between; margin: 0.5rem 0;">
                                <span>Subtotal:</span>
                                <span><?= number_format($order['subtotal'], 2) ?> RON</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 0.5rem 0;">
                                <span>Transport:</span>
                                <span><?= number_format($order['shipping_cost'], 2) ?> RON</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 1rem 0; font-size: 1.25rem; font-weight: bold;">
                                <span>Total:</span>
                                <span style="color: var(--primary-color);"><?= number_format($order['total'], 2) ?> RON</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <div style="background: var(--white); padding: 2rem; border-radius: 0.5rem; box-shadow: var(--shadow); margin-bottom: 2rem;">
                        <h2>Detalii comandă</h2>
                        <p style="margin: 0.5rem 0;">
                            <strong>Data:</strong> <?= date('d.m.Y H:i', strtotime($order['created_at'])) ?>
                        </p>
                        <p style="margin: 0.5rem 0;">
                            <strong>Metodă plată:</strong> <?= htmlspecialchars($order['payment_method']) ?>
                        </p>
                        <p style="margin: 0.5rem 0;">
                            <strong>Status comandă:</strong>
                            <span class="badge badge-info"><?= ucfirst($order['status']) ?></span>
                        </p>
                        <p style="margin: 0.5rem 0;">
                            <strong>Status plată:</strong>
                            <span class="badge badge-info"><?= ucfirst($order['payment_status']) ?></span>
                        </p>
                        <?php if ($order['notes']): ?>
                            <p style="margin: 1rem 0;">
                                <strong>Observații:</strong><br>
                                <?= nl2br(htmlspecialchars($order['notes'])) ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <div style="background: var(--white); padding: 2rem; border-radius: 0.5rem; box-shadow: var(--shadow);">
                        <h2>Actualizează status</h2>
                        <form method="POST" action="/admin/orders/<?= $order['id'] ?>/status">
                            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                            <div class="form-group">
                                <label>Status comandă</label>
                                <select name="status">
                                    <option value="pending" <?= $order['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="processing" <?= $order['status'] == 'processing' ? 'selected' : '' ?>>Processing</option>
                                    <option value="shipped" <?= $order['status'] == 'shipped' ? 'selected' : '' ?>>Shipped</option>
                                    <option value="delivered" <?= $order['status'] == 'delivered' ? 'selected' : '' ?>>Delivered</option>
                                    <option value="cancelled" <?= $order['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary" style="width: 100%;">
                                Actualizează status
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
