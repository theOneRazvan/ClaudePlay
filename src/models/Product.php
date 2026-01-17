<?php

namespace PetFactory\Models;

use PetFactory\Core\Database;

class Product
{
    public static function all($activeOnly = false, $limit = null, $offset = 0)
    {
        $db = Database::getInstance();
        $sql = "SELECT p.*, c.name as collection_name,
                (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image
                FROM products p
                LEFT JOIN collections c ON p.collection_id = c.id";

        if ($activeOnly) {
            $sql .= " WHERE p.is_active = 1";
        }

        $sql .= " ORDER BY p.created_at DESC";

        if ($limit) {
            $sql .= " LIMIT $limit OFFSET $offset";
        }

        return $db->fetchAll($sql);
    }

    public static function findById($id)
    {
        $db = Database::getInstance();
        $product = $db->fetchOne(
            "SELECT p.*, c.name as collection_name
             FROM products p
             LEFT JOIN collections c ON p.collection_id = c.id
             WHERE p.id = ?",
            [$id]
        );

        if ($product) {
            $product['images'] = self::getImages($id);
        }

        return $product;
    }

    public static function findBySlug($slug)
    {
        $db = Database::getInstance();
        $product = $db->fetchOne(
            "SELECT p.*, c.name as collection_name
             FROM products p
             LEFT JOIN collections c ON p.collection_id = c.id
             WHERE p.slug = ? AND p.is_active = 1",
            [$slug]
        );

        if ($product) {
            $product['images'] = self::getImages($product['id']);
        }

        return $product;
    }

    public static function getByCollection($collectionId, $limit = null, $offset = 0)
    {
        $db = Database::getInstance();
        $sql = "SELECT p.*,
                (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image
                FROM products p
                WHERE p.collection_id = ? AND p.is_active = 1
                ORDER BY p.name ASC";

        if ($limit) {
            $sql .= " LIMIT $limit OFFSET $offset";
        }

        return $db->fetchAll($sql, [$collectionId]);
    }

    public static function getFeatured($limit = 8)
    {
        $db = Database::getInstance();
        return $db->fetchAll(
            "SELECT p.*,
             (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image
             FROM products p
             WHERE p.is_featured = 1 AND p.is_active = 1
             ORDER BY p.created_at DESC
             LIMIT ?",
            [$limit]
        );
    }

    public static function search($query, $limit = null)
    {
        $db = Database::getInstance();
        $searchTerm = "%$query%";

        $sql = "SELECT p.*,
                (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image
                FROM products p
                WHERE (p.name LIKE ? OR p.description LIKE ?) AND p.is_active = 1
                ORDER BY p.name ASC";

        if ($limit) {
            $sql .= " LIMIT $limit";
        }

        return $db->fetchAll($sql, [$searchTerm, $searchTerm]);
    }

    public static function create($data)
    {
        $db = Database::getInstance();

        $db->query(
            "INSERT INTO products (collection_id, name, slug, description, price, compare_at_price,
             stock_quantity, weight, sku, is_active, is_featured, allow_subscription, subscription_discount)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $data['collection_id'] ?? null,
                $data['name'],
                $data['slug'],
                $data['description'] ?? null,
                $data['price'],
                $data['compare_at_price'] ?? null,
                $data['stock_quantity'] ?? 0,
                $data['weight'] ?? null,
                $data['sku'] ?? null,
                $data['is_active'] ?? 1,
                $data['is_featured'] ?? 0,
                $data['allow_subscription'] ?? 1,
                $data['subscription_discount'] ?? 0
            ]
        );

        return $db->lastInsertId();
    }

    public static function update($id, $data)
    {
        $db = Database::getInstance();

        $db->query(
            "UPDATE products SET collection_id = ?, name = ?, slug = ?, description = ?, price = ?,
             compare_at_price = ?, stock_quantity = ?, weight = ?, sku = ?, is_active = ?, is_featured = ?,
             allow_subscription = ?, subscription_discount = ?
             WHERE id = ?",
            [
                $data['collection_id'] ?? null,
                $data['name'],
                $data['slug'],
                $data['description'] ?? null,
                $data['price'],
                $data['compare_at_price'] ?? null,
                $data['stock_quantity'] ?? 0,
                $data['weight'] ?? null,
                $data['sku'] ?? null,
                $data['is_active'] ?? 1,
                $data['is_featured'] ?? 0,
                $data['allow_subscription'] ?? 1,
                $data['subscription_discount'] ?? 0,
                $id
            ]
        );

        return true;
    }

    public static function delete($id)
    {
        $db = Database::getInstance();
        return $db->query("DELETE FROM products WHERE id = ?", [$id]);
    }

    public static function getImages($productId)
    {
        $db = Database::getInstance();
        return $db->fetchAll(
            "SELECT * FROM product_images WHERE product_id = ? ORDER BY is_primary DESC, display_order ASC",
            [$productId]
        );
    }

    public static function addImage($productId, $imagePath, $isPrimary = false)
    {
        $db = Database::getInstance();

        if ($isPrimary) {
            $db->query(
                "UPDATE product_images SET is_primary = 0 WHERE product_id = ?",
                [$productId]
            );
        }

        $db->query(
            "INSERT INTO product_images (product_id, image_path, is_primary) VALUES (?, ?, ?)",
            [$productId, $imagePath, $isPrimary ? 1 : 0]
        );

        return $db->lastInsertId();
    }

    public static function deleteImage($imageId)
    {
        $db = Database::getInstance();
        return $db->query("DELETE FROM product_images WHERE id = ?", [$imageId]);
    }

    public static function count($activeOnly = false)
    {
        $db = Database::getInstance();
        $sql = "SELECT COUNT(*) as count FROM products";

        if ($activeOnly) {
            $sql .= " WHERE is_active = 1";
        }

        $result = $db->fetchOne($sql);
        return $result['count'] ?? 0;
    }
}
