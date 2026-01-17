#!/bin/bash

# PetFactory - Debug & Fix Script
# Rulează acest script pentru a găsi și fixa problema

echo "========================================="
echo "  PetFactory - Debug & Fix Script"
echo "========================================="
echo ""

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

APP_DIR="/var/www/html/petfactory"

echo -e "${YELLOW}Step 1: Fixing session issue in index.php${NC}"

# Fix index.php - remove session_start() from top
sudo tee ${APP_DIR}/public/index.php > /dev/null <<'EOF'
<?php

require_once __DIR__ . '/../config/config.php';

spl_autoload_register(function ($class) {
    $prefix = 'PetFactory\\';
    $baseDir = SRC_PATH . '/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

use PetFactory\Core\Router;

$router = new Router();

$router->get('/', 'HomeController', 'index');
$router->get('/about', 'HomeController', 'about');
$router->get('/contact', 'HomeController', 'contact');

$router->get('/login', 'AuthController', 'showLogin');
$router->post('/login', 'AuthController', 'login');
$router->get('/register', 'AuthController', 'showRegister');
$router->post('/register', 'AuthController', 'register');
$router->get('/logout', 'AuthController', 'logout');

$router->get('/products', 'ProductController', 'index');
$router->get('/products/{slug}', 'ProductController', 'show');
$router->get('/collections/{slug}', 'ProductController', 'byCollection');
$router->get('/search', 'ProductController', 'search');

$router->get('/cart', 'CartController', 'index');
$router->post('/cart/add', 'CartController', 'add');
$router->post('/cart/update', 'CartController', 'update');
$router->post('/cart/remove', 'CartController', 'remove');
$router->post('/cart/clear', 'CartController', 'clear');
$router->get('/cart/count', 'CartController', 'count');

$router->get('/checkout', 'CheckoutController', 'index');
$router->post('/checkout', 'CheckoutController', 'process');
$router->get('/order-confirmation/{orderNumber}', 'CheckoutController', 'confirmation');

$router->get('/admin', 'AdminController', 'dashboard');
$router->get('/admin/products', 'AdminController', 'products');
$router->get('/admin/products/create', 'AdminController', 'createProduct');
$router->post('/admin/products', 'AdminController', 'storeProduct');
$router->get('/admin/products/{id}/edit', 'AdminController', 'editProduct');
$router->post('/admin/products/{id}', 'AdminController', 'updateProduct');
$router->post('/admin/products/{id}/delete', 'AdminController', 'deleteProduct');

$router->get('/admin/collections', 'AdminController', 'collections');
$router->get('/admin/collections/create', 'AdminController', 'createCollection');
$router->post('/admin/collections', 'AdminController', 'storeCollection');
$router->get('/admin/collections/{id}/edit', 'AdminController', 'editCollection');
$router->post('/admin/collections/{id}', 'AdminController', 'updateCollection');
$router->post('/admin/collections/{id}/delete', 'AdminController', 'deleteCollection');

$router->get('/admin/orders', 'AdminController', 'orders');
$router->get('/admin/orders/{id}', 'AdminController', 'viewOrder');
$router->post('/admin/orders/{id}/status', 'AdminController', 'updateOrderStatus');

$url = $_GET['url'] ?? '';
$router->dispatch($url);
EOF

echo -e "${GREEN}✓ Fixed session_start() issue${NC}"

echo ""
echo -e "${YELLOW}Step 2: Fixing config.php - move session settings before session_start${NC}"

sudo tee ${APP_DIR}/config/config.php > /dev/null <<'EOF'
<?php

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'petfactory');
define('DB_USER', 'petfactory_user');
define('DB_PASS', 'PetFactory2024!Secure');

// Application Configuration
define('APP_NAME', 'PetFactory');
define('APP_URL', 'http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost'));
define('APP_ENV', 'development'); // Changed to development for debugging

// Paths
define('ROOT_PATH', dirname(__DIR__));
define('SRC_PATH', ROOT_PATH . '/src');
define('VIEWS_PATH', ROOT_PATH . '/views');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('UPLOAD_PATH', PUBLIC_PATH . '/images/uploads');

// Session Configuration - MUST be set BEFORE session_start()
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_lifetime', 0);

// Start session after ini settings
session_start();

// Error Reporting
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

echo -e "${GREEN}✓ Fixed config.php${NC}"

echo ""
echo -e "${YELLOW}Step 3: Creating missing files if needed${NC}"

# Create Database.php if missing or broken
sudo tee ${APP_DIR}/src/core/Database.php > /dev/null <<'EOF'
<?php

namespace PetFactory\Core;

use PDO;
use PDOException;

class Database
{
    private static $instance = null;
    private $pdo;

    private function __construct()
    {
        try {
            $this->pdo = new PDO(
                'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            die('Database connection failed: ' . $e->getMessage());
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function query($sql, $params = [])
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log('Database query error: ' . $e->getMessage());
            return false;
        }
    }

    public function fetchAll($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt ? $stmt->fetchAll() : [];
    }

    public function fetchOne($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt ? $stmt->fetch() : false;
    }

    public function lastInsertId()
    {
        return $this->pdo->lastInsertId();
    }
}
EOF

echo -e "${GREEN}✓ Database.php ready${NC}"

echo ""
echo -e "${YELLOW}Step 4: Setting correct permissions${NC}"

sudo chown -R apache:apache ${APP_DIR}
sudo chmod -R 755 ${APP_DIR}
sudo chmod -R 775 ${APP_DIR}/public/images/uploads

echo -e "${GREEN}✓ Permissions set${NC}"

echo ""
echo -e "${YELLOW}Step 5: Restarting Apache${NC}"

sudo systemctl restart httpd

echo -e "${GREEN}✓ Apache restarted${NC}"

echo ""
echo -e "${YELLOW}Step 6: Testing application${NC}"

# Test homepage
echo "Testing homepage..."
RESPONSE=$(curl -s -o /dev/null -w "%{http_code}" http://localhost/)

if [ "$RESPONSE" = "200" ]; then
    echo -e "${GREEN}✓ Homepage works! (HTTP 200)${NC}"
else
    echo -e "${RED}✗ Homepage returned HTTP $RESPONSE${NC}"
    echo "Checking error log..."
    sudo tail -20 /var/log/httpd/error_log
fi

echo ""
echo "========================================="
echo -e "${GREEN}Fix completed!${NC}"
echo "========================================="
echo ""
echo "Test your application:"
echo "  http://$(curl -s http://169.254.169.254/latest/meta-data/public-ipv4)/"
echo "  http://$(curl -s http://169.254.169.254/latest/meta-data/public-ipv4)/admin"
echo ""
echo "If still having issues, check:"
echo "  sudo tail -50 /var/log/httpd/error_log"
echo ""
