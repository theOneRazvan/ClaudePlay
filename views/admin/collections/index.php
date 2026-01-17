<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Colecții - Admin - PetFactory</title>
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
                <h1>Colecții</h1>
                <a href="/admin/collections/create" class="btn btn-primary">+ Adaugă colecție</a>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nume</th>
                        <th>Slug</th>
                        <th>Produse</th>
                        <th>Ordine</th>
                        <th>Status</th>
                        <th>Acțiuni</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($collections as $collection): ?>
                        <tr>
                            <td><?= $collection['id'] ?></td>
                            <td><?= htmlspecialchars($collection['name']) ?></td>
                            <td><?= htmlspecialchars($collection['slug']) ?></td>
                            <td><?= $collection['product_count'] ?></td>
                            <td><?= $collection['display_order'] ?></td>
                            <td>
                                <span class="badge <?= $collection['is_active'] ? 'badge-success' : 'badge-danger' ?>">
                                    <?= $collection['is_active'] ? 'Activ' : 'Inactiv' ?>
                                </span>
                            </td>
                            <td>
                                <a href="/admin/collections/<?= $collection['id'] ?>/edit" class="btn btn-outline" style="padding: 0.5rem 1rem;">
                                    Editează
                                </a>
                                <form method="POST" action="/admin/collections/<?= $collection['id'] ?>/delete"
                                      style="display: inline;"
                                      onsubmit="return confirm('Sigur doriți să ștergeți această colecție?')">
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
        </main>
    </div>
</body>
</html>
