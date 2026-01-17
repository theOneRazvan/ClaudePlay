<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<div class="container">
    <section class="section">
        <h1 class="section-title">Contact</h1>

        <div style="max-width: 600px; margin: 0 auto;">
            <div style="background: var(--bg-light); padding: 2rem; border-radius: 0.5rem; margin-bottom: 2rem;">
                <h2 style="margin-bottom: 1.5rem;">Informații de contact</h2>
                <p style="margin-bottom: 1rem;">
                    <strong>Email:</strong> contact@petfactory.ro
                </p>
                <p style="margin-bottom: 1rem;">
                    <strong>Telefon:</strong> +40 123 456 789
                </p>
                <p style="margin-bottom: 1rem;">
                    <strong>Program:</strong> Luni - Vineri: 9:00 - 18:00
                </p>
                <p>
                    <strong>Adresă:</strong> București, România
                </p>
            </div>

            <h2 style="text-align: center; margin-bottom: 1.5rem;">Trimite-ne un mesaj</h2>
            <form method="POST" action="/contact" style="background: var(--white); padding: 2rem; border-radius: 0.5rem; box-shadow: var(--shadow);">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                <div class="form-group">
                    <label>Nume</label>
                    <input type="text" name="name" required>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>

                <div class="form-group">
                    <label>Subiect</label>
                    <input type="text" name="subject" required>
                </div>

                <div class="form-group">
                    <label>Mesaj</label>
                    <textarea name="message" required></textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    Trimite mesaj
                </button>
            </form>
        </div>
    </section>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
