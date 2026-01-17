<?php
/**
 * PetFactory Configuration File
 */

// Environment
define('ENVIRONMENT', 'development'); // development or production

// Site Configuration
define('SITE_NAME', 'PetFactory');
define('SITE_URL', 'http://petfactory.ro');
define('SITE_EMAIL', 'contact@petfactory.ro');

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'petfactory');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Path Configuration
define('ROOT_PATH', dirname(__DIR__));
define('SRC_PATH', ROOT_PATH . '/src');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('VIEWS_PATH', ROOT_PATH . '/views');
define('UPLOAD_PATH', PUBLIC_PATH . '/images/uploads');

// Security
define('SESSION_LIFETIME', 3600 * 24); // 24 hours
define('CSRF_TOKEN_NAME', 'csrf_token');
define('PASSWORD_MIN_LENGTH', 8);

// Pagination
define('ITEMS_PER_PAGE', 12);
define('ADMIN_ITEMS_PER_PAGE', 20);

// Currency
define('CURRENCY_SYMBOL', 'RON');
define('CURRENCY_CODE', 'RON');

// Subscription Settings
define('SUBSCRIPTION_FREQUENCIES', [
    'weekly' => 'Weekly',
    'biweekly' => 'Bi-weekly',
    'monthly' => 'Monthly',
    'quarterly' => 'Quarterly'
]);

// Error Reporting
if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Timezone
date_default_timezone_set('Europe/Bucharest');

// Session Configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 for HTTPS
