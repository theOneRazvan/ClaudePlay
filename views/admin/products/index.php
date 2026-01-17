<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produse - Admin - PetFactory</title>
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
                <h1>Produse</h1>
                <a href="/admin/products/create" class="btn btn-primary">+ Adaugă produs</a>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nume</th>
                        <th>Colecție</th>
                        <th>Preț</th>
                        <th>Stoc</th>
                        <th>Status</th>
                        <th>Acțiuni</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?= $product['id'] ?></td>
                            <td><?= htmlspecialchars($product['name']) ?></td>
                            <td><?= htmlspecialchars($product['collection_name'] ?? 'N/A') ?></td>
                            <td><?= number_format($product['price'], 2) ?> RON</td>
                            <td><?= $product['stock_quantity'] ?></td>
                            <td>
                                <span class="badge <?= $product['is_active'] ? 'badge-success' : 'badge-danger' ?>">
                                    <?= $product['is_active'] ? 'Activ' : 'Inactiv' ?>
                                </span>
                            </td>
                            <td>
                                <a href="/admin/products/<?= $product['id'] ?>/edit" class="btn btn-outline" style="padding: 0.5rem 1rem;">
                                    Editează
                                </a>
                                <form method="POST" action="/admin/products/<?= $product['id'] ?>/delete"
                                      style="display: inline;"
                                      onsubmit="return confirm('Sigur doriți să ștergeți acest produs?')">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                    <button type="submit" class="btn btn-danger" style="padding: 0.5rem 1rem;">
                                        Șterge
                                    </button>
                                </form>
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
