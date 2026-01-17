<?php

namespace PetFactory\Controllers;

use PetFactory\Core\Controller;
use PetFactory\Models\Cart;
use PetFactory\Models\Product;

class CartController extends Controller
{
    public function index()
    {
        $items = Cart::getItems();
        $total = Cart::getTotal();

        $this->view('pages/cart', [
            'items' => $items,
            'total' => $total,
            'csrf_token' => $this->generateCSRF()
        ]);
    }

    public function add()
    {
        if (!$this->validateCSRF()) {
            $this->json(['success' => false, 'message' => 'Invalid request'], 400);
        }

        $productId = $_POST['product_id'] ?? null;
        $quantity = $_POST['quantity'] ?? 1;
        $isSubscription = isset($_POST['is_subscription']) && $_POST['is_subscription'] == '1';
        $frequency = $_POST['frequency'] ?? null;

        if (!$productId) {
            $this->json(['success' => false, 'message' => 'Product ID is required'], 400);
        }

        $product = Product::findById($productId);

        if (!$product || !$product['is_active']) {
            $this->json(['success' => false, 'message' => 'Product not found'], 404);
        }

        if ($product['stock_quantity'] < $quantity) {
            $this->json(['success' => false, 'message' => 'Insufficient stock'], 400);
        }

        if ($isSubscription && !$product['allow_subscription']) {
            $this->json(['success' => false, 'message' => 'This product does not support subscriptions'], 400);
        }

        Cart::addItem($productId, $quantity, $isSubscription, $frequency);

        $this->json([
            'success' => true,
            'message' => 'Product added to cart',
            'cart_count' => Cart::getCount()
        ]);
    }

    public function update()
    {
        if (!$this->validateCSRF()) {
            $this->json(['success' => false, 'message' => 'Invalid request'], 400);
        }

        $cartItemId = $_POST['cart_item_id'] ?? null;
        $quantity = $_POST['quantity'] ?? 0;

        if (!$cartItemId) {
            $this->json(['success' => false, 'message' => 'Cart item ID is required'], 400);
        }

        Cart::updateQuantity($cartItemId, $quantity);

        $this->json([
            'success' => true,
            'message' => 'Cart updated',
            'cart_total' => Cart::getTotal(),
            'cart_count' => Cart::getCount()
        ]);
    }

    public function remove()
    {
        if (!$this->validateCSRF()) {
            $this->json(['success' => false, 'message' => 'Invalid request'], 400);
        }

        $cartItemId = $_POST['cart_item_id'] ?? null;

        if (!$cartItemId) {
            $this->json(['success' => false, 'message' => 'Cart item ID is required'], 400);
        }

        Cart::removeItem($cartItemId);

        $this->json([
            'success' => true,
            'message' => 'Item removed from cart',
            'cart_total' => Cart::getTotal(),
            'cart_count' => Cart::getCount()
        ]);
    }

    public function clear()
    {
        if (!$this->validateCSRF()) {
            $this->json(['success' => false, 'message' => 'Invalid request'], 400);
        }

        Cart::clear();

        $this->json([
            'success' => true,
            'message' => 'Cart cleared'
        ]);
    }

    public function count()
    {
        $this->json([
            'count' => Cart::getCount()
        ]);
    }
}
