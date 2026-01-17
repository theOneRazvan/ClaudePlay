<?php

namespace PetFactory\Models;

use PetFactory\Core\Database;

class Order
{
    public static function create($data)
    {
        $db = Database::getInstance();

        $orderNumber = 'PF' . date('Ymd') . strtoupper(bin2hex(random_bytes(4)));

        $db->query(
            "INSERT INTO orders (order_number, user_id, email, first_name, last_name, phone,
             address, city, county, postal_code, subtotal, shipping_cost, total, payment_method, notes)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $orderNumber,
                $data['user_id'] ?? null,
                $data['email'],
                $data['first_name'],
                $data['last_name'],
                $data['phone'],
                $data['address'],
                $data['city'],
                $data['county'],
                $data['postal_code'],
                $data['subtotal'],
                $data['shipping_cost'] ?? 0,
                $data['total'],
                $data['payment_method'] ?? 'card',
                $data['notes'] ?? null
            ]
        );

        return [
            'id' => $db->lastInsertId(),
            'order_number' => $orderNumber
        ];
    }

    public static function addItem($orderId, $productId, $productName, $productSku, $quantity, $price)
    {
        $db = Database::getInstance();

        $total = $price * $quantity;

        $db->query(
            "INSERT INTO order_items (order_id, product_id, product_name, product_sku, quantity, price, total)
             VALUES (?, ?, ?, ?, ?, ?, ?)",
            [$orderId, $productId, $productName, $productSku, $quantity, $price, $total]
        );

        return $db->lastInsertId();
    }

    public static function findById($id)
    {
        $db = Database::getInstance();
        return $db->fetchOne("SELECT * FROM orders WHERE id = ?", [$id]);
    }

    public static function findByOrderNumber($orderNumber)
    {
        $db = Database::getInstance();
        return $db->fetchOne("SELECT * FROM orders WHERE order_number = ?", [$orderNumber]);
    }

    public static function getItems($orderId)
    {
        $db = Database::getInstance();
        return $db->fetchAll("SELECT * FROM order_items WHERE order_id = ?", [$orderId]);
    }

    public static function getByUser($userId, $limit = null)
    {
        $db = Database::getInstance();
        $sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC";

        if ($limit) {
            $sql .= " LIMIT $limit";
        }

        return $db->fetchAll($sql, [$userId]);
    }

    public static function all($limit = null, $offset = 0)
    {
        $db = Database::getInstance();
        $sql = "SELECT * FROM orders ORDER BY created_at DESC";

        if ($limit) {
            $sql .= " LIMIT $limit OFFSET $offset";
        }

        return $db->fetchAll($sql);
    }

    public static function updateStatus($orderId, $status)
    {
        $db = Database::getInstance();
        return $db->query(
            "UPDATE orders SET status = ? WHERE id = ?",
            [$status, $orderId]
        );
    }

    public static function updatePaymentStatus($orderId, $paymentStatus)
    {
        $db = Database::getInstance();
        return $db->query(
            "UPDATE orders SET payment_status = ? WHERE id = ?",
            [$paymentStatus, $orderId]
        );
    }

    public static function count()
    {
        $db = Database::getInstance();
        $result = $db->fetchOne("SELECT COUNT(*) as count FROM orders");
        return $result['count'] ?? 0;
    }

    public static function getTotalRevenue()
    {
        $db = Database::getInstance();
        $result = $db->fetchOne("SELECT SUM(total) as revenue FROM orders WHERE payment_status = 'paid'");
        return $result['revenue'] ?? 0;
    }
}
