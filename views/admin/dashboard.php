<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - PetFactory</title>
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
            <h1>Dashboard</h1>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; margin: 2rem 0;">
                <div style="background: var(--white); padding: 2rem; border-radius: 0.5rem; box-shadow: var(--shadow);">
                    <h3 style="color: var(--text-light); margin-bottom: 0.5rem;">Total Produse</h3>
                    <p style="font-size: 2.5rem; font-weight: bold; color: var(--primary-color);">
                        <?= $stats['total_products'] ?>
                    </p>
                </div>

                <div style="background: var(--white); padding: 2rem; border-radius: 0.5rem; box-shadow: var(--shadow);">
                    <h3 style="color: var(--text-light); margin-bottom: 0.5rem;">Total Comenzi</h3>
                    <p style="font-size: 2.5rem; font-weight: bold; color: var(--secondary-color);">
                        <?= $stats['total_orders'] ?>
                    </p>
                </div>

                <div style="background: var(--white); padding: 2rem; border-radius: 0.5rem; box-shadow: var(--shadow);">
                    <h3 style="color: var(--text-light); margin-bottom: 0.5rem;">Venituri Totale</h3>
                    <p style="font-size: 2.5rem; font-weight: bold; color: var(--primary-color);">
                        <?= number_format($stats['total_revenue'], 2) ?> RON
                    </p>
                </div>

                <div style="background: var(--white); padding: 2rem; border-radius: 0.5rem; box-shadow: var(--shadow);">
                    <h3 style="color: var(--text-light); margin-bottom: 0.5rem;">Comenzi în Așteptare</h3>
                    <p style="font-size: 2.5rem; font-weight: bold; color: var(--danger-color);">
                        <?= $stats['pending_orders'] ?>
                    </p>
                </div>
            </div>

            <h2 style="margin-top: 3rem; margin-bottom: 1rem;">Comenzi Recente</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Nr. Comandă</th>
                        <th>Client</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Data</th>
                        <th>Acțiuni</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentOrders as $order): ?>
                        <tr>
                            <td><?= htmlspecialchars($order['order_number']) ?></td>
                            <td><?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?></td>
                            <td><?= number_format($order['total'], 2) ?> RON</td>
                            <td>
                                <?php
                                $badgeClass = match($order['status']) {
                                    'pending' => 'badge-warning',
                                    'processing' => 'badge-info',
                                    'shipped' => 'badge-info',
                                    'delivered' => 'badge-success',
                                    'cancelled' => 'badge-danger',
                                    default => 'badge-info'
                                };
                                ?>
                                <span class="badge <?= $badgeClass ?>">
                                    <?= ucfirst($order['status']) ?>
                                </span>
                            </td>
                            <td><?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></td>
                            <td>
                                <a href="/admin/orders/<?= $order['id'] ?>" class="btn btn-outline" style="padding: 0.5rem 1rem;">
                                    Vezi detalii
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>
