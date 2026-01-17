<?php

namespace PetFactory\Controllers;

use PetFactory\Core\Controller;
use PetFactory\Core\Auth;
use PetFactory\Models\Product;
use PetFactory\Models\Collection;
use PetFactory\Models\Order;

class AdminController extends Controller
{
    public function __construct()
    {
        Auth::requireAdmin();
    }

    public function dashboard()
    {
        $stats = [
            'total_products' => Product::count(),
            'total_orders' => Order::count(),
            'total_revenue' => Order::getTotalRevenue(),
            'pending_orders' => count(array_filter(Order::all(100), function($order) {
                return $order['status'] === 'pending';
            }))
        ];

        $recentOrders = Order::all(10);

        $this->view('admin/dashboard', [
            'stats' => $stats,
            'recentOrders' => $recentOrders
        ]);
    }

    public function products()
    {
        $page = $_GET['page'] ?? 1;
        $offset = ($page - 1) * ADMIN_ITEMS_PER_PAGE;

        $products = Product::all(false, ADMIN_ITEMS_PER_PAGE, $offset);
        $totalProducts = Product::count();
        $totalPages = ceil($totalProducts / ADMIN_ITEMS_PER_PAGE);

        $this->view('admin/products/index', [
            'products' => $products,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ]);
    }

    public function createProduct()
    {
        $collections = Collection::all();

        $this->view('admin/products/create', [
            'collections' => $collections,
            'csrf_token' => $this->generateCSRF()
        ]);
    }

    public function storeProduct()
    {
        if (!$this->validateCSRF()) {
            $_SESSION['error'] = 'Invalid request';
            $this->redirect('/admin/products/create');
        }

        $data = [
            'collection_id' => $_POST['collection_id'] ?? null,
            'name' => $_POST['name'] ?? '',
            'slug' => $this->generateSlug($_POST['name'] ?? ''),
            'description' => $_POST['description'] ?? '',
            'price' => $_POST['price'] ?? 0,
            'compare_at_price' => $_POST['compare_at_price'] ?? null,
            'stock_quantity' => $_POST['stock_quantity'] ?? 0,
            'weight' => $_POST['weight'] ?? null,
            'sku' => $_POST['sku'] ?? null,
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
            'allow_subscription' => isset($_POST['allow_subscription']) ? 1 : 0,
            'subscription_discount' => $_POST['subscription_discount'] ?? 0
        ];

        if (empty($data['name']) || empty($data['price'])) {
            $_SESSION['error'] = 'Name and price are required';
            $this->redirect('/admin/products/create');
        }

        $productId = Product::create($data);

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $this->uploadProductImage($productId, $_FILES['image'], true);
        }

        $_SESSION['success'] = 'Product created successfully';
        $this->redirect('/admin/products');
    }

    public function editProduct($id)
    {
        $product = Product::findById($id);

        if (!$product) {
            $_SESSION['error'] = 'Product not found';
            $this->redirect('/admin/products');
        }

        $collections = Collection::all();

        $this->view('admin/products/edit', [
            'product' => $product,
            'collections' => $collections,
            'csrf_token' => $this->generateCSRF()
        ]);
    }

    public function updateProduct($id)
    {
        if (!$this->validateCSRF()) {
            $_SESSION['error'] = 'Invalid request';
            $this->redirect('/admin/products/' . $id . '/edit');
        }

        $product = Product::findById($id);

        if (!$product) {
            $_SESSION['error'] = 'Product not found';
            $this->redirect('/admin/products');
        }

        $data = [
            'collection_id' => $_POST['collection_id'] ?? null,
            'name' => $_POST['name'] ?? '',
            'slug' => $_POST['slug'] ?? $this->generateSlug($_POST['name'] ?? ''),
            'description' => $_POST['description'] ?? '',
            'price' => $_POST['price'] ?? 0,
            'compare_at_price' => $_POST['compare_at_price'] ?? null,
            'stock_quantity' => $_POST['stock_quantity'] ?? 0,
            'weight' => $_POST['weight'] ?? null,
            'sku' => $_POST['sku'] ?? null,
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
            'allow_subscription' => isset($_POST['allow_subscription']) ? 1 : 0,
            'subscription_discount' => $_POST['subscription_discount'] ?? 0
        ];

        Product::update($id, $data);

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $this->uploadProductImage($id, $_FILES['image'], true);
        }

        $_SESSION['success'] = 'Product updated successfully';
        $this->redirect('/admin/products');
    }

    public function deleteProduct($id)
    {
        Product::delete($id);
        $_SESSION['success'] = 'Product deleted successfully';
        $this->redirect('/admin/products');
    }

    public function collections()
    {
        $collections = Collection::all();

        foreach ($collections as &$collection) {
            $collection['product_count'] = Collection::getProductCount($collection['id']);
        }

        $this->view('admin/collections/index', [
            'collections' => $collections
        ]);
    }

    public function createCollection()
    {
        $this->view('admin/collections/create', [
            'csrf_token' => $this->generateCSRF()
        ]);
    }

    public function storeCollection()
    {
        if (!$this->validateCSRF()) {
            $_SESSION['error'] = 'Invalid request';
            $this->redirect('/admin/collections/create');
        }

        $data = [
            'name' => $_POST['name'] ?? '',
            'slug' => $this->generateSlug($_POST['name'] ?? ''),
            'description' => $_POST['description'] ?? '',
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'display_order' => $_POST['display_order'] ?? 0
        ];

        if (empty($data['name'])) {
            $_SESSION['error'] = 'Name is required';
            $this->redirect('/admin/collections/create');
        }

        Collection::create($data);

        $_SESSION['success'] = 'Collection created successfully';
        $this->redirect('/admin/collections');
    }

    public function editCollection($id)
    {
        $collection = Collection::findById($id);

        if (!$collection) {
            $_SESSION['error'] = 'Collection not found';
            $this->redirect('/admin/collections');
        }

        $this->view('admin/collections/edit', [
            'collection' => $collection,
            'csrf_token' => $this->generateCSRF()
        ]);
    }

    public function updateCollection($id)
    {
        if (!$this->validateCSRF()) {
            $_SESSION['error'] = 'Invalid request';
            $this->redirect('/admin/collections/' . $id . '/edit');
        }

        $data = [
            'name' => $_POST['name'] ?? '',
            'slug' => $_POST['slug'] ?? $this->generateSlug($_POST['name'] ?? ''),
            'description' => $_POST['description'] ?? '',
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'display_order' => $_POST['display_order'] ?? 0
        ];

        Collection::update($id, $data);

        $_SESSION['success'] = 'Collection updated successfully';
        $this->redirect('/admin/collections');
    }

    public function deleteCollection($id)
    {
        Collection::delete($id);
        $_SESSION['success'] = 'Collection deleted successfully';
        $this->redirect('/admin/collections');
    }

    public function orders()
    {
        $page = $_GET['page'] ?? 1;
        $offset = ($page - 1) * ADMIN_ITEMS_PER_PAGE;

        $orders = Order::all(ADMIN_ITEMS_PER_PAGE, $offset);
        $totalOrders = Order::count();
        $totalPages = ceil($totalOrders / ADMIN_ITEMS_PER_PAGE);

        $this->view('admin/orders/index', [
            'orders' => $orders,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ]);
    }

    public function viewOrder($id)
    {
        $order = Order::findById($id);

        if (!$order) {
            $_SESSION['error'] = 'Order not found';
            $this->redirect('/admin/orders');
        }

        $items = Order::getItems($id);

        $this->view('admin/orders/view', [
            'order' => $order,
            'items' => $items,
            'csrf_token' => $this->generateCSRF()
        ]);
    }

    public function updateOrderStatus($id)
    {
        if (!$this->validateCSRF()) {
            $_SESSION['error'] = 'Invalid request';
            $this->redirect('/admin/orders/' . $id);
        }

        $status = $_POST['status'] ?? '';

        if (empty($status)) {
            $_SESSION['error'] = 'Status is required';
            $this->redirect('/admin/orders/' . $id);
        }

        Order::updateStatus($id, $status);

        $_SESSION['success'] = 'Order status updated successfully';
        $this->redirect('/admin/orders/' . $id);
    }

    private function generateSlug($text)
    {
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9]+/', '-', $text);
        $text = trim($text, '-');
        return $text;
    }

    private function uploadProductImage($productId, $file, $isPrimary = false)
    {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];

        if (!in_array($file['type'], $allowedTypes)) {
            return false;
        }

        if (!is_dir(UPLOAD_PATH)) {
            mkdir(UPLOAD_PATH, 0755, true);
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'product_' . $productId . '_' . time() . '.' . $extension;
        $filepath = UPLOAD_PATH . '/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            Product::addImage($productId, '/images/uploads/' . $filename, $isPrimary);
            return true;
        }

        return false;
    }
}
