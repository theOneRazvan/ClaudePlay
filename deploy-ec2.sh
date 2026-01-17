#!/bin/bash

# PetFactory E-commerce - EC2 Deployment Script
# For Amazon Linux 2023 / Amazon Linux 2

set -e  # Exit on error

echo "================================================"
echo "  PetFactory E-commerce - EC2 Deployment"
echo "================================================"
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuration
DB_NAME="petfactory"
DB_USER="petfactory_user"
DB_PASS="PetFactory2024!Secure"
DB_ROOT_PASS="RootPass2024!Secure"
APP_DIR="/var/www/html/petfactory"
REPO_URL="https://github.com/theOneRazvan/ClaudePlay.git"
BRANCH="claude/petfactory-ecommerce-site-vo004"

echo -e "${GREEN}Step 1: Detecting OS and updating system...${NC}"
if [ -f /etc/os-release ]; then
    . /etc/os-release
    echo "Operating System: $NAME $VERSION"
fi

sudo yum update -y

echo ""
echo -e "${GREEN}Step 2: Installing Apache, PHP 8.x, and MariaDB...${NC}"

# Install Apache
sudo yum install -y httpd

# Install PHP 8.x and extensions
sudo yum install -y php php-mysqlnd php-gd php-mbstring php-xml php-json php-curl

# Install MariaDB
sudo yum install -y mariadb105-server mariadb105

# Install Git
sudo yum install -y git

echo ""
echo -e "${GREEN}Step 3: Starting services...${NC}"

# Start and enable Apache
sudo systemctl start httpd
sudo systemctl enable httpd
echo "✓ Apache started and enabled"

# Start and enable MariaDB
sudo systemctl start mariadb
sudo systemctl enable mariadb
echo "✓ MariaDB started and enabled"

echo ""
echo -e "${GREEN}Step 4: Configuring MariaDB...${NC}"

# Secure MariaDB installation and create database
sudo mysql -u root <<MYSQL_SCRIPT
-- Set root password
ALTER USER 'root'@'localhost' IDENTIFIED BY '${DB_ROOT_PASS}';
FLUSH PRIVILEGES;

-- Create database
CREATE DATABASE IF NOT EXISTS ${DB_NAME} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create user
CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';
GRANT ALL PRIVILEGES ON ${DB_NAME}.* TO '${DB_USER}'@'localhost';
FLUSH PRIVILEGES;

MYSQL_SCRIPT

echo "✓ Database created: ${DB_NAME}"
echo "✓ User created: ${DB_USER}"

echo ""
echo -e "${GREEN}Step 5: Cloning repository...${NC}"

# Remove old directory if exists
sudo rm -rf ${APP_DIR}

# Clone repository
sudo git clone -b ${BRANCH} ${REPO_URL} ${APP_DIR}

echo "✓ Repository cloned to ${APP_DIR}"

echo ""
echo -e "${GREEN}Step 6: Importing database schema...${NC}"

# Import database schema
sudo mysql -u root -p${DB_ROOT_PASS} ${DB_NAME} < ${APP_DIR}/database/schema.sql

echo "✓ Database schema imported"

echo ""
echo -e "${GREEN}Step 7: Configuring application...${NC}"

# Create config.php with correct settings
sudo tee ${APP_DIR}/config/config.php > /dev/null <<'EOF'
<?php

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'petfactory');
define('DB_USER', 'petfactory_user');
define('DB_PASS', 'PetFactory2024!Secure');

// Application Configuration
define('APP_NAME', 'PetFactory');
define('APP_URL', 'http://' . $_SERVER['HTTP_HOST']);
define('APP_ENV', 'staging');

// Paths
define('ROOT_PATH', dirname(__DIR__));
define('SRC_PATH', ROOT_PATH . '/src');
define('VIEWS_PATH', ROOT_PATH . '/views');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('UPLOAD_PATH', PUBLIC_PATH . '/images/uploads');

// Session Configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_lifetime', 0);

// Error Reporting (disable in production)
if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Timezone
date_default_timezone_set('Europe/Bucharest');

// Admin Settings
define('ADMIN_ITEMS_PER_PAGE', 25);

// Subscription Frequencies
define('SUBSCRIPTION_FREQUENCIES', [
    'weekly' => 'Săptămânal',
    'biweekly' => 'La 2 săptămâni',
    'monthly' => 'Lunar',
    'quarterly' => 'Trimestrial'
]);
EOF

echo "✓ Configuration file created"

echo ""
echo -e "${GREEN}Step 8: Setting up directories and permissions...${NC}"

# Create upload directory
sudo mkdir -p ${APP_DIR}/public/images/uploads

# Set ownership to apache user
sudo chown -R apache:apache ${APP_DIR}

# Set directory permissions
sudo find ${APP_DIR} -type d -exec chmod 755 {} \;
sudo find ${APP_DIR} -type f -exec chmod 644 {} \;

# Set writable permissions for uploads
sudo chmod -R 775 ${APP_DIR}/public/images/uploads

echo "✓ Permissions configured"

echo ""
echo -e "${GREEN}Step 9: Configuring Apache...${NC}"

# Create Apache configuration
sudo tee /etc/httpd/conf.d/petfactory.conf > /dev/null <<EOF
<VirtualHost *:80>
    DocumentRoot "${APP_DIR}/public"

    <Directory "${APP_DIR}/public">
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    # Logging
    ErrorLog /var/log/httpd/petfactory-error.log
    CustomLog /var/log/httpd/petfactory-access.log combined
</VirtualHost>
EOF

echo "✓ Apache virtual host configured"

# Restart Apache to apply changes
sudo systemctl restart httpd

echo "✓ Apache restarted"

echo ""
echo -e "${GREEN}Step 10: Configuring firewall...${NC}"

# Open HTTP port in firewall
sudo firewall-cmd --permanent --add-service=http 2>/dev/null || echo "Firewall not active or already configured"
sudo firewall-cmd --reload 2>/dev/null || true

echo "✓ Firewall configured"

echo ""
echo "================================================"
echo -e "${GREEN}✓ DEPLOYMENT COMPLETED SUCCESSFULLY!${NC}"
echo "================================================"
echo ""
echo "Application Information:"
echo "------------------------"
echo -e "URL: ${YELLOW}http://$(curl -s http://169.254.169.254/latest/meta-data/public-ipv4)${NC}"
echo -e "Admin URL: ${YELLOW}http://$(curl -s http://169.254.169.254/latest/meta-data/public-ipv4)/admin${NC}"
echo ""
echo "Admin Credentials:"
echo "  Email: admin@petfactory.ro"
echo "  Password: admin123"
echo ""
echo "Database Information:"
echo "  Database: ${DB_NAME}"
echo "  User: ${DB_USER}"
echo "  Password: ${DB_PASS}"
echo "  Root Password: ${DB_ROOT_PASS}"
echo ""
echo -e "${YELLOW}IMPORTANT: Change the admin password after first login!${NC}"
echo ""
echo "Useful Commands:"
echo "  - View Apache logs: sudo tail -f /var/log/httpd/petfactory-error.log"
echo "  - Restart Apache: sudo systemctl restart httpd"
echo "  - Restart MariaDB: sudo systemctl restart mariadb"
echo "  - Check status: sudo systemctl status httpd mariadb"
echo ""
