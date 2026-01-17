<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editează Colecție - Admin - PetFactory</title>
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
            <h1>Editează Colecție</h1>

            <form method="POST" action="/admin/collections/<?= $collection['id'] ?>" style="max-width: 600px;">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                <div class="form-group">
                    <label>Nume colecție *</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($collection['name']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Slug</label>
                    <input type="text" name="slug" value="<?= htmlspecialchars($collection['slug']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Descriere</label>
                    <textarea name="description"><?= htmlspecialchars($collection['description'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label>Ordine afișare</label>
                    <input type="number" name="display_order" value="<?= $collection['display_order'] ?>">
                </div>

                <div class="checkbox-group" style="margin-bottom: 1.5rem;">
                    <input type="checkbox" name="is_active" id="is_active"
                           <?= $collection['is_active'] ? 'checked' : '' ?>>
                    <label for="is_active">Activ</label>
                </div>

                <div style="display: flex; gap: 1rem;">
                    <button type="submit" class="btn btn-primary">Salvează modificările</button>
                    <a href="/admin/collections" class="btn btn-outline">Anulează</a>
                </div>
            </form>
        </main>
    </div>
</body>
</html>
