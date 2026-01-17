<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comenzi - Admin - PetFactory</title>
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
            <h1>Comenzi</h1>

            <table class="table">
                <thead>
                    <tr>
                        <th>Nr. Comandă</th>
                        <th>Client</th>
                        <th>Email</th>
                        <th>Total</th>
                        <th>Status Comandă</th>
                        <th>Status Plată</th>
                        <th>Data</th>
                        <th>Acțiuni</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?= htmlspecialchars($order['order_number']) ?></td>
                            <td><?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?></td>
                            <td><?= htmlspecialchars($order['email']) ?></td>
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
                            <td>
                                <?php
                                $paymentBadge = match($order['payment_status']) {
                                    'pending' => 'badge-warning',
                                    'paid' => 'badge-success',
                                    'failed' => 'badge-danger',
                                    'refunded' => 'badge-info',
                                    default => 'badge-warning'
                                };
                                ?>
                                <span class="badge <?= $paymentBadge ?>">
                                    <?= ucfirst($order['payment_status']) ?>
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
        </main>
    </div>
</body>
</html>
