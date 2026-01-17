<?php

namespace PetFactory\Controllers;

use PetFactory\Core\Controller;
use PetFactory\Core\Auth;
use PetFactory\Models\Cart;
use PetFactory\Models\Order;
use PetFactory\Models\Subscription;
use PetFactory\Models\Product;

class CheckoutController extends Controller
{
    public function index()
    {
        $items = Cart::getItems();

        if (empty($items)) {
            $_SESSION['error'] = 'Your cart is empty';
            $this->redirect('/cart');
        }

        $subtotal = Cart::getTotal();
        $shippingCost = $this->calculateShipping($subtotal);
        $total = $subtotal + $shippingCost;

        $user = Auth::user();

        $this->view('pages/checkout', [
            'items' => $items,
            'subtotal' => $subtotal,
            'shipping_cost' => $shippingCost,
            'total' => $total,
            'user' => $user,
            'csrf_token' => $this->generateCSRF()
        ]);
    }

    public function process()
    {
        if (!$this->validateCSRF()) {
            $_SESSION['error'] = 'Invalid request';
            $this->redirect('/checkout');
        }

        $items = Cart::getItems();

        if (empty($items)) {
            $_SESSION['error'] = 'Your cart is empty';
            $this->redirect('/cart');
        }

        $data = [
            'user_id' => Auth::id(),
            'email' => $_POST['email'] ?? '',
            'first_name' => $_POST['first_name'] ?? '',
            'last_name' => $_POST['last_name'] ?? '',
            'phone' => $_POST['phone'] ?? '',
            'address' => $_POST['address'] ?? '',
            'city' => $_POST['city'] ?? '',
            'county' => $_POST['county'] ?? '',
            'postal_code' => $_POST['postal_code'] ?? '',
            'payment_method' => $_POST['payment_method'] ?? 'card',
            'notes' => $_POST['notes'] ?? ''
        ];

        if (empty($data['email']) || empty($data['first_name']) || empty($data['last_name']) ||
            empty($data['phone']) || empty($data['address']) || empty($data['city']) ||
            empty($data['county']) || empty($data['postal_code'])) {
            $_SESSION['error'] = 'All fields are required';
            $this->redirect('/checkout');
        }

        $subtotal = Cart::getTotal();
        $shippingCost = $this->calculateShipping($subtotal);
        $total = $subtotal + $shippingCost;

        $data['subtotal'] = $subtotal;
        $data['shipping_cost'] = $shippingCost;
        $data['total'] = $total;

        $orderResult = Order::create($data);
        $orderId = $orderResult['id'];
        $orderNumber = $orderResult['order_number'];

        $hasSubscription = false;
        $subscriptionItems = [];

        foreach ($items as $item) {
            $price = $item['price'];

            if ($item['is_subscription'] && $item['subscription_discount'] > 0) {
                $price = $price * (1 - $item['subscription_discount'] / 100);
                $hasSubscription = true;
                $subscriptionItems[] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'frequency' => $item['subscription_frequency']
                ];
            }

            Order::addItem(
                $orderId,
                $item['product_id'],
                $item['name'],
                $item['slug'],
                $item['quantity'],
                $price
            );

            $product = Product::findById($item['product_id']);
            if ($product) {
                Product::update($item['product_id'], [
                    'name' => $product['name'],
                    'slug' => $product['slug'],
                    'price' => $product['price'],
                    'stock_quantity' => max(0, $product['stock_quantity'] - $item['quantity'])
                ]);
            }
        }

        if ($hasSubscription && Auth::check()) {
            $frequency = $subscriptionItems[0]['frequency'] ?? 'monthly';

            $subscriptionResult = Subscription::create([
                'user_id' => Auth::id(),
                'frequency' => $frequency,
                'shipping_address' => $data['address'],
                'city' => $data['city'],
                'county' => $data['county'],
                'postal_code' => $data['postal_code']
            ]);

            $subscriptionId = $subscriptionResult['id'];

            foreach ($subscriptionItems as $subItem) {
                Subscription::addItem($subscriptionId, $subItem['product_id'], $subItem['quantity']);
            }
        }

        Cart::clear();

        $_SESSION['success'] = 'Order placed successfully!';
        $this->redirect('/order-confirmation/' . $orderNumber);
    }

    public function confirmation($orderNumber)
    {
        $order = Order::findByOrderNumber($orderNumber);

        if (!$order) {
            http_response_code(404);
            $this->view('pages/404');
            return;
        }

        $items = Order::getItems($order['id']);

        $this->view('pages/order-confirmation', [
            'order' => $order,
            'items' => $items
        ]);
    }

    private function calculateShipping($subtotal)
    {
        if ($subtotal >= 200) {
            return 0;
        } elseif ($subtotal >= 100) {
            return 15;
        } else {
            return 25;
        }
    }
}
