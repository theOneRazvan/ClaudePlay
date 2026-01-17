<?php

session_start();

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
