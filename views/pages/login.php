<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<div class="container">
    <section class="section">
        <div style="max-width: 500px; margin: 0 auto;">
            <h1 style="text-align: center; margin-bottom: 2rem;">Autentificare</h1>

            <form method="POST" action="/login" style="background: var(--white); padding: 2rem; border-radius: 0.5rem; box-shadow: var(--shadow);">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>

                <div class="form-group">
                    <label>Parolă</label>
                    <input type="password" name="password" required>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    Autentifică-te
                </button>

                <p style="text-align: center; margin-top: 1.5rem;">
                    Nu ai cont? <a href="/register" style="color: var(--primary-color);">Înregistrează-te</a>
                </p>
            </form>
        </div>
    </section>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
