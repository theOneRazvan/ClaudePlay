# PetFactory - E-commerce pentru Hrană Animale

Website e-commerce responsive pentru vânzarea de hrană pentru animale de companie, construit cu PHP, MySQL și JavaScript vanilla.

## Caracteristici

### Frontend (Customer-Facing)
- 🏠 **Pagină principală** cu produse recomandate și colecții
- 🛍️ **Catalog produse** cu paginare și filtrare
- 🔍 **Căutare produse** avansată
- 🛒 **Coș de cumpărături** interactiv
- 💳 **Sistem de checkout** complet
- 📦 **Abonamente** - cumpărături recurente cu reduceri
- 👤 **Autentificare și înregistrare** utilizatori
- 📱 **Design responsive** pentru toate dispozitivele

### Backend (Admin Panel)
- 📊 **Dashboard** cu statistici
- 📦 **Management produse** - CRUD complet
- 📁 **Management colecții** - organizare produse
- 🛒 **Management comenzi** - vizualizare și actualizare statusuri
- 🖼️ **Upload imagini** pentru produse
- ⚙️ **Configurare abonamente** per produs

### Funcționalități Tehnice
- ✅ Arhitectură MVC
- ✅ Sistem de rutare personalizat
- ✅ Autentificare cu sesiuni PHP
- ✅ Protecție CSRF
- ✅ Validare formulare server-side și client-side
- ✅ Design responsive cu CSS vanilla
- ✅ JavaScript vanilla (fără framework-uri)
- ✅ Queries SQL optimizate cu prepared statements
- ✅ Upload și management imagini

## Structura Proiectului

```
ClaudePlay/
├── config/
│   └── config.php                 # Configurări aplicație
├── database/
│   └── schema.sql                 # Schema bază de date
├── public/
│   ├── index.php                  # Entry point
│   ├── .htaccess                  # Rewrite rules
│   ├── css/
│   │   └── style.css             # Stiluri responsive
│   ├── js/
│   │   └── main.js               # JavaScript funcționalități
│   └── images/
│       └── uploads/              # Imagini produse
├── src/
│   ├── core/
│   │   ├── Auth.php              # Sistem autentificare
│   │   ├── Controller.php        # Base controller
│   │   ├── Database.php          # Conexiune DB
│   │   └── Router.php            # Sistem rutare
│   ├── controllers/
│   │   ├── AdminController.php   # Admin panel
│   │   ├── AuthController.php    # Login/Register
│   │   ├── CartController.php    # Coș cumpărături
│   │   ├── CheckoutController.php # Checkout
│   │   ├── HomeController.php    # Pagina principală
│   │   └── ProductController.php # Produse
│   └── models/
│       ├── Cart.php              # Model coș
│       ├── Collection.php        # Model colecții
│       ├── Order.php             # Model comenzi
│       ├── Product.php           # Model produse
│       ├── Subscription.php      # Model abonamente
│       └── User.php              # Model utilizatori
└── views/
    ├── layouts/
    │   ├── header.php            # Header site
    │   └── footer.php            # Footer site
    ├── pages/                    # Pagini publice
    └── admin/                    # Pagini admin
```

## Instalare

### Cerințe
- PHP 7.4 sau superior
- MySQL 5.7 sau superior
- Apache cu mod_rewrite activat
- Composer (opțional)

### Pași Instalare

1. **Clonează repository-ul**
```bash
git clone <repository-url>
cd ClaudePlay
```

2. **Configurează baza de date**

Creează o bază de date MySQL:
```sql
CREATE DATABASE petfactory CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Importă schema:
```bash
mysql -u root -p petfactory < database/schema.sql
```

3. **Configurează aplicația**

Editează `config/config.php` și actualizează setările bazei de date:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'petfactory');
define('DB_USER', 'root');
define('DB_PASS', '');
```

4. **Configurează Apache**

Asigură-te că DocumentRoot pointează către directorul proiectului sau configurează un VirtualHost:

```apache
<VirtualHost *:80>
    ServerName petfactory.local
    DocumentRoot /path/to/ClaudePlay
    <Directory /path/to/ClaudePlay>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Adaugă în `/etc/hosts` (sau `C:\Windows\System32\drivers\etc\hosts` pe Windows):
```
127.0.0.1 petfactory.local
```

5. **Setează permisiuni**

```bash
chmod -R 755 public/
chmod -R 775 public/images/uploads/
```

6. **Accesează aplicația**

- Frontend: `http://petfactory.local/`
- Admin: `http://petfactory.local/admin`

### Credențiale Admin Inițiale

```
Email: admin@petfactory.ro
Parolă: admin123
```

**IMPORTANT:** Schimbă parola după prima autentificare!

## Utilizare

### Adăugare Produse

1. Autentifică-te în panoul admin (`/admin`)
2. Navighează la Produse → Adaugă produs
3. Completează detaliile produsului:
   - Nume, descriere, preț
   - Stoc disponibil
   - Colecție (opțional)
   - Imagine principală
   - Setări abonament (permite abonament, reducere %)
4. Salvează produsul

### Gestionare Colecții

1. Navighează la Colecții → Adaugă colecție
2. Setează nume, descriere, ordine afișare
3. Asociază produse la colecții prin editarea produselor

### Gestionare Comenzi

1. Vizualizează comenzile în Comenzi
2. Click pe o comandă pentru a vedea detalii
3. Actualizează statusul comenzii:
   - Pending → Processing → Shipped → Delivered
   - Sau Cancelled dacă este necesar

### Configurare Transport

În `CheckoutController.php`, metoda `calculateShipping()`:
```php
// Transport gratuit peste 200 RON
// 15 RON pentru 100-200 RON
// 25 RON sub 100 RON
```

## Caracteristici Abonamente

Clienții pot abona produse cu livrări recurente:
- **Frecvențe disponibile:** Săptămânal, Bi-săptămânal, Lunar, Trimestrial
- **Reduceri:** Configurabile per produs (%)
- **Management:** Clienții pot pausa/anula abonamentele

Pentru procesare automată abonamente, creează un cron job:
```bash
# Rulează zilnic la 2 AM
0 2 * * * php /path/to/ClaudePlay/cron/process-subscriptions.php
```

## Securitate

- ✅ Parole hash-uite cu `password_hash()`
- ✅ Protecție CSRF pe toate formularele
- ✅ SQL injection prevention cu prepared statements
- ✅ XSS prevention cu `htmlspecialchars()`
- ✅ Validare input server-side
- ✅ Session hijacking protection
- ✅ Acces admin restricționat

## Customizare

### Modificare Culori

Editează variabilele CSS în `public/css/style.css`:
```css
:root {
    --primary-color: #2563eb;
    --secondary-color: #10b981;
    --danger-color: #ef4444;
    /* ... */
}
```

### Adăugare Metode Plată

Editează `views/pages/checkout.php` și adaugă opțiuni în select-ul `payment_method`.

Pentru integrare gateway-uri de plată (Stripe, PayPal, etc.), implementează în `CheckoutController::process()`.

### Personalizare Email

Pentru a trimite email-uri de confirmare, adaugă funcționalitate în `CheckoutController::process()`:
```php
mail($order['email'], 'Confirmare comandă', $message, $headers);
```

## Performanță

### Optimizări Recomandate

1. **Cache:** Implementează Redis/Memcached pentru sesiuni și cache
2. **CDN:** Servește assets static prin CDN
3. **Imagini:** Optimizează și comprimă imaginile produselor
4. **Database:** Creează index-uri pe coloanele frecvent căutate
5. **Minificare:** Minifică CSS și JavaScript pentru producție

### Index-uri Database

Schema include deja index-uri pe:
- `email` (users)
- `slug` (products, collections)
- `collection_id` (products)
- `order_number` (orders)
- Etc.

## Troubleshooting

### Eroare "Page not found"
- Verifică că mod_rewrite este activat în Apache
- Verifică `.htaccess` files

### Eroare conectare bază de date
- Verifică credențialele în `config/config.php`
- Asigură-te că MySQL rulează

### Imagini nu se încarcă
- Verifică permisiunile pe `public/images/uploads/`
- Asigură-te că directorul există

### Session errors
- Verifică permisiunile pe directorul de sesiuni PHP
- Verifică setările session în php.ini

## Dezvoltare Viitoare

Funcționalități planificate:
- [ ] Integrare gateway plăți (Stripe/PayPal)
- [ ] Sistem review-uri produse
- [ ] Wishlist pentru utilizatori
- [ ] Newsletter
- [ ] Sistem cupoane de reducere
- [ ] Rapoarte avansate în admin
- [ ] Export comenzi (CSV, PDF)
- [ ] Multi-limbă (RO/EN)
- [ ] Notificări email automate
- [ ] API REST pentru mobile apps

## Licență

Acest proiect este licențiat sub MIT License.

## Suport

Pentru probleme sau întrebări, contactați: contact@petfactory.ro

---

**Dezvoltat cu ❤️ pentru PetFactory**
