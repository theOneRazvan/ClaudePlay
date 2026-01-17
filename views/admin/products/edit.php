<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editează Produs - Admin - PetFactory</title>
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
            <h1>Editează Produs</h1>

            <form method="POST" action="/admin/products/<?= $product['id'] ?>" enctype="multipart/form-data" style="max-width: 800px;">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                <div class="form-group">
                    <label>Nume produs *</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Slug</label>
                    <input type="text" name="slug" value="<?= htmlspecialchars($product['slug']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Colecție</label>
                    <select name="collection_id">
                        <option value="">Fără colecție</option>
                        <?php foreach ($collections as $collection): ?>
                            <option value="<?= $collection['id'] ?>"
                                    <?= $product['collection_id'] == $collection['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($collection['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Descriere</label>
                    <textarea name="description"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label>Preț (RON) *</label>
                        <input type="number" name="price" step="0.01" value="<?= $product['price'] ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Preț vechi (RON)</label>
                        <input type="number" name="compare_at_price" step="0.01"
                               value="<?= $product['compare_at_price'] ?? '' ?>">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label>Stoc</label>
                        <input type="number" name="stock_quantity" value="<?= $product['stock_quantity'] ?>">
                    </div>

                    <div class="form-group">
                        <label>Greutate (kg)</label>
                        <input type="number" name="weight" step="0.01" value="<?= $product['weight'] ?? '' ?>">
                    </div>

                    <div class="form-group">
                        <label>SKU</label>
                        <input type="text" name="sku" value="<?= htmlspecialchars($product['sku'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label>Reducere abonament (%)</label>
                    <input type="number" name="subscription_discount"
                           value="<?= $product['subscription_discount'] ?>" min="0" max="100">
                </div>

                <div class="form-group">
                    <label>Imagine principală nouă</label>
                    <input type="file" name="image" accept="image/*">
                    <?php if (!empty($product['images'])): ?>
                        <small>Imagine curentă: <?= htmlspecialchars($product['images'][0]['image_path']) ?></small>
                    <?php endif; ?>
                </div>

                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
                    <div class="checkbox-group">
                        <input type="checkbox" name="is_active" id="is_active"
                               <?= $product['is_active'] ? 'checked' : '' ?>>
                        <label for="is_active">Activ</label>
                    </div>

                    <div class="checkbox-group">
                        <input type="checkbox" name="is_featured" id="is_featured"
                               <?= $product['is_featured'] ? 'checked' : '' ?>>
                        <label for="is_featured">Produs recomandat</label>
                    </div>

                    <div class="checkbox-group">
                        <input type="checkbox" name="allow_subscription" id="allow_subscription"
                               <?= $product['allow_subscription'] ? 'checked' : '' ?>>
                        <label for="allow_subscription">Permite abonament</label>
                    </div>
                </div>

                <div style="display: flex; gap: 1rem;">
                    <button type="submit" class="btn btn-primary">Salvează modificările</button>
                    <a href="/admin/products" class="btn btn-outline">Anulează</a>
                </div>
            </form>
        </main>
    </div>
</body>
</html>
