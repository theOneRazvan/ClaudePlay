<?php

namespace PetFactory\Models;

use PetFactory\Core\Database;
use PetFactory\Core\Auth;

class Cart
{
    public static function getSessionId()
    {
        if (!isset($_SESSION['cart_session_id'])) {
            $_SESSION['cart_session_id'] = bin2hex(random_bytes(16));
        }
        return $_SESSION['cart_session_id'];
    }

    public static function getItems()
    {
        $db = Database::getInstance();
        $sessionId = self::getSessionId();
        $userId = Auth::id();

        $sql = "SELECT ci.*, p.name, p.price, p.slug, p.stock_quantity, p.subscription_discount,
                (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as image
                FROM cart_items ci
                JOIN products p ON ci.product_id = p.id
                WHERE ci.session_id = ?";

        $params = [$sessionId];

        if ($userId) {
            $sql .= " OR ci.user_id = ?";
            $params[] = $userId;
        }

        return $db->fetchAll($sql, $params);
    }

    public static function addItem($productId, $quantity = 1, $isSubscription = false, $frequency = null)
    {
        $db = Database::getInstance();
        $sessionId = self::getSessionId();
        $userId = Auth::id();

        $existingItem = $db->fetchOne(
            "SELECT * FROM cart_items WHERE session_id = ? AND product_id = ? AND is_subscription = ?",
            [$sessionId, $productId, $isSubscription ? 1 : 0]
        );

        if ($existingItem) {
            $newQuantity = $existingItem['quantity'] + $quantity;
            $db->query(
                "UPDATE cart_items SET quantity = ?, subscription_frequency = ? WHERE id = ?",
                [$newQuantity, $frequency, $existingItem['id']]
            );
            return $existingItem['id'];
        } else {
            $db->query(
                "INSERT INTO cart_items (session_id, user_id, product_id, quantity, is_subscription, subscription_frequency)
                 VALUES (?, ?, ?, ?, ?, ?)",
                [$sessionId, $userId, $productId, $quantity, $isSubscription ? 1 : 0, $frequency]
            );
            return $db->lastInsertId();
        }
    }

    public static function updateQuantity($cartItemId, $quantity)
    {
        $db = Database::getInstance();

        if ($quantity <= 0) {
            return self::removeItem($cartItemId);
        }

        return $db->query(
            "UPDATE cart_items SET quantity = ? WHERE id = ?",
            [$quantity, $cartItemId]
        );
    }

    public static function removeItem($cartItemId)
    {
        $db = Database::getInstance();
        return $db->query("DELETE FROM cart_items WHERE id = ?", [$cartItemId]);
    }

    public static function clear()
    {
        $db = Database::getInstance();
        $sessionId = self::getSessionId();
        return $db->query("DELETE FROM cart_items WHERE session_id = ?", [$sessionId]);
    }

    public static function getTotal()
    {
        $items = self::getItems();
        $total = 0;

        foreach ($items as $item) {
            $price = $item['price'];

            if ($item['is_subscription'] && $item['subscription_discount'] > 0) {
                $price = $price * (1 - $item['subscription_discount'] / 100);
            }

            $total += $price * $item['quantity'];
        }

        return $total;
    }

    public static function getCount()
    {
        $items = self::getItems();
        $count = 0;

        foreach ($items as $item) {
            $count += $item['quantity'];
        }

        return $count;
    }

    public static function mergeGuestCart($userId)
    {
        $db = Database::getInstance();
        $sessionId = self::getSessionId();

        $db->query(
            "UPDATE cart_items SET user_id = ? WHERE session_id = ? AND user_id IS NULL",
            [$userId, $sessionId]
        );
    }
}
