<?php

namespace PetFactory\Models;

use PetFactory\Core\Database;
use DateTime;

class Subscription
{
    public static function create($data)
    {
        $db = Database::getInstance();

        $subscriptionNumber = 'SUB' . date('Ymd') . strtoupper(bin2hex(random_bytes(4)));
        $nextDeliveryDate = self::calculateNextDeliveryDate($data['frequency']);

        $db->query(
            "INSERT INTO subscriptions (subscription_number, user_id, frequency, next_delivery_date,
             shipping_address, city, county, postal_code)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $subscriptionNumber,
                $data['user_id'],
                $data['frequency'],
                $nextDeliveryDate,
                $data['shipping_address'],
                $data['city'],
                $data['county'],
                $data['postal_code']
            ]
        );

        return [
            'id' => $db->lastInsertId(),
            'subscription_number' => $subscriptionNumber
        ];
    }

    public static function addItem($subscriptionId, $productId, $quantity)
    {
        $db = Database::getInstance();

        $db->query(
            "INSERT INTO subscription_items (subscription_id, product_id, quantity) VALUES (?, ?, ?)",
            [$subscriptionId, $productId, $quantity]
        );

        return $db->lastInsertId();
    }

    public static function findById($id)
    {
        $db = Database::getInstance();
        return $db->fetchOne("SELECT * FROM subscriptions WHERE id = ?", [$id]);
    }

    public static function getItems($subscriptionId)
    {
        $db = Database::getInstance();
        return $db->fetchAll(
            "SELECT si.*, p.name, p.price, p.slug
             FROM subscription_items si
             JOIN products p ON si.product_id = p.id
             WHERE si.subscription_id = ?",
            [$subscriptionId]
        );
    }

    public static function getByUser($userId)
    {
        $db = Database::getInstance();
        return $db->fetchAll(
            "SELECT * FROM subscriptions WHERE user_id = ? ORDER BY created_at DESC",
            [$userId]
        );
    }

    public static function updateStatus($subscriptionId, $status)
    {
        $db = Database::getInstance();
        return $db->query(
            "UPDATE subscriptions SET status = ? WHERE id = ?",
            [$status, $subscriptionId]
        );
    }

    public static function updateNextDeliveryDate($subscriptionId, $frequency = null)
    {
        $db = Database::getInstance();

        if (!$frequency) {
            $subscription = self::findById($subscriptionId);
            $frequency = $subscription['frequency'];
        }

        $nextDate = self::calculateNextDeliveryDate($frequency);

        return $db->query(
            "UPDATE subscriptions SET next_delivery_date = ? WHERE id = ?",
            [$nextDate, $subscriptionId]
        );
    }

    public static function getDueSubscriptions()
    {
        $db = Database::getInstance();
        return $db->fetchAll(
            "SELECT * FROM subscriptions WHERE status = 'active' AND next_delivery_date <= CURDATE()"
        );
    }

    private static function calculateNextDeliveryDate($frequency)
    {
        $date = new DateTime();

        switch ($frequency) {
            case 'weekly':
                $date->modify('+1 week');
                break;
            case 'biweekly':
                $date->modify('+2 weeks');
                break;
            case 'monthly':
                $date->modify('+1 month');
                break;
            case 'quarterly':
                $date->modify('+3 months');
                break;
            default:
                $date->modify('+1 month');
        }

        return $date->format('Y-m-d');
    }

    public static function count($userId = null)
    {
        $db = Database::getInstance();

        if ($userId) {
            $result = $db->fetchOne(
                "SELECT COUNT(*) as count FROM subscriptions WHERE user_id = ?",
                [$userId]
            );
        } else {
            $result = $db->fetchOne("SELECT COUNT(*) as count FROM subscriptions");
        }

        return $result['count'] ?? 0;
    }
}
