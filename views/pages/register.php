<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<div class="container">
    <section class="section">
        <div style="max-width: 500px; margin: 0 auto;">
            <h1 style="text-align: center; margin-bottom: 2rem;">Creare cont</h1>

            <form method="POST" action="/register" style="background: var(--white); padding: 2rem; border-radius: 0.5rem; box-shadow: var(--shadow);">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label>Prenume</label>
                        <input type="text" name="first_name" required>
                    </div>

                    <div class="form-group">
                        <label>Nume</label>
                        <input type="text" name="last_name" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Telefon</label>
                    <input type="tel" name="phone">
                </div>

                <div class="form-group">
                    <label>Parolă</label>
                    <input type="password" name="password" required minlength="8">
                    <small style="color: var(--text-light);">Minim 8 caractere</small>
                </div>

                <div class="form-group">
                    <label>Confirmă parola</label>
                    <input type="password" name="confirm_password" required>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    Creează cont
                </button>

                <p style="text-align: center; margin-top: 1.5rem;">
                    Ai deja cont? <a href="/login" style="color: var(--primary-color);">Autentifică-te</a>
                </p>
            </form>
        </div>
    </section>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
