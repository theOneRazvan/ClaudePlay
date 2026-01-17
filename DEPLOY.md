# Ghid Deploy EC2 - PetFactory E-commerce

## Pași Rapizi de Deploy

### 1. Conectează-te la serverul EC2

```bash
ssh -i your-key.pem ec2-user@ec2-18-192-68-112.eu-central-1.compute.amazonaws.com
```

### 2. Download și rulează scriptul de deploy

```bash
# Download script
curl -O https://raw.githubusercontent.com/theOneRazvan/ClaudePlay/claude/petfactory-ecommerce-site-vo004/deploy-ec2.sh

# Sau clone repository și rulează local
git clone -b claude/petfactory-ecommerce-site-vo004 https://github.com/theOneRazvan/ClaudePlay.git
cd ClaudePlay

# Rulează script
chmod +x deploy-ec2.sh
sudo ./deploy-ec2.sh
```

Scriptul va rula ~3-5 minute și va instala tot ce e necesar.

### 3. Accesează aplicația

După ce scriptul se termină, va afișa URL-ul aplicației:
- **Frontend:** `http://IP-PUBLIC-EC2/`
- **Admin:** `http://IP-PUBLIC-EC2/admin`

**Credențiale admin:**
- Email: `admin@petfactory.ro`
- Parolă: `admin123`

---

## Ce Face Scriptul?

✅ Instalează Apache, PHP 8.x, MariaDB
✅ Configurează baza de date MySQL
✅ Clonează codul din Git
✅ Importă schema bazei de date
✅ Configurează Apache VirtualHost
✅ Setează permisiunile corecte
✅ Pornește toate serviciile

---

## Troubleshooting

### Aplicația nu se încarcă

```bash
# Verifică statusul serviciilor
sudo systemctl status httpd mariadb

# Verifică logurile Apache
sudo tail -f /var/log/httpd/petfactory-error.log
sudo tail -f /var/log/httpd/error_log

# Restart servicii
sudo systemctl restart httpd
sudo systemctl restart mariadb
```

### Eroare "Permission denied" la upload imagini

```bash
sudo chmod -R 775 /var/www/html/petfactory/public/images/uploads
sudo chown -R apache:apache /var/www/html/petfactory
```

### Eroare conexiune bază de date

```bash
# Testează conexiunea MySQL
mysql -u petfactory_user -p petfactory

# Parolă: PetFactory2024!Secure

# Verifică baza de date
mysql -u root -p
# Parolă: RootPass2024!Secure

SHOW DATABASES;
USE petfactory;
SHOW TABLES;
```

### SELinux blochează Apache

```bash
# Dezactivează SELinux temporar (pentru debugging)
sudo setenforce 0

# Sau configurează SELinux permanent
sudo setsebool -P httpd_can_network_connect_db 1
sudo chcon -R -t httpd_sys_rw_content_t /var/www/html/petfactory/public/images/uploads
```

---

## Comenzi Utile

### Servicii
```bash
# Status
sudo systemctl status httpd
sudo systemctl status mariadb

# Restart
sudo systemctl restart httpd
sudo systemctl restart mariadb

# Stop
sudo systemctl stop httpd
sudo systemctl stop mariadb
```

### Logs
```bash
# Apache error log
sudo tail -f /var/log/httpd/petfactory-error.log

# Apache access log
sudo tail -f /var/log/httpd/petfactory-access.log

# MariaDB log
sudo tail -f /var/log/mariadb/mariadb.log
```

### Database
```bash
# Conectare ca root
mysql -u root -p
# Parolă: RootPass2024!Secure

# Conectare ca user aplicație
mysql -u petfactory_user -p petfactory
# Parolă: PetFactory2024!Secure

# Backup database
mysqldump -u root -p petfactory > backup-$(date +%Y%m%d).sql

# Restore database
mysql -u root -p petfactory < backup-20240101.sql
```

### Fișiere aplicație
```bash
# Location
cd /var/www/html/petfactory

# Pull latest changes
cd /var/www/html/petfactory
sudo git pull origin claude/petfactory-ecommerce-site-vo004

# După pull, restart Apache
sudo systemctl restart httpd
```

---

## Configurare Producție

### 1. Schimbă parolele

```bash
# Schimbă parola admin prin interfață web
# Accesează /admin și schimbă parola

# Schimbă parolele bazei de date
mysql -u root -p
ALTER USER 'petfactory_user'@'localhost' IDENTIFIED BY 'NEW_STRONG_PASSWORD';
FLUSH PRIVILEGES;

# Actualizează config/config.php
sudo nano /var/www/html/petfactory/config/config.php
# Schimbă DB_PASS cu noua parolă
```

### 2. Dezactivează error display

```bash
sudo nano /var/www/html/petfactory/config/config.php

# Schimbă:
define('APP_ENV', 'production');
```

### 3. Configurează SSL (când domeniul e activ)

```bash
# Instalează certbot
sudo yum install -y certbot python3-certbot-apache

# Obține certificat SSL
sudo certbot --apache -d petfactory.ro -d www.petfactory.ro
```

---

## Informații Sistem

### Aplicație
- **Path:** `/var/www/html/petfactory`
- **Public:** `/var/www/html/petfactory/public`
- **Uploads:** `/var/www/html/petfactory/public/images/uploads`

### Database
- **Database:** `petfactory`
- **User:** `petfactory_user`
- **Password:** `PetFactory2024!Secure`
- **Root Password:** `RootPass2024!Secure`

### Apache
- **Config:** `/etc/httpd/conf.d/petfactory.conf`
- **Error Log:** `/var/log/httpd/petfactory-error.log`
- **Access Log:** `/var/log/httpd/petfactory-access.log`

---

## Update Aplicație

```bash
cd /var/www/html/petfactory
sudo git pull origin claude/petfactory-ecommerce-site-vo004
sudo chown -R apache:apache .
sudo systemctl restart httpd
```

---

**Pentru suport:** contact@petfactory.ro
