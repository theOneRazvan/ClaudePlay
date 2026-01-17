<?php

namespace PetFactory\Models;

use PetFactory\Core\Database;

class Collection
{
    public static function all($activeOnly = false)
    {
        $db = Database::getInstance();
        $sql = "SELECT * FROM collections";

        if ($activeOnly) {
            $sql .= " WHERE is_active = 1";
        }

        $sql .= " ORDER BY display_order ASC, name ASC";

        return $db->fetchAll($sql);
    }

    public static function findById($id)
    {
        $db = Database::getInstance();
        return $db->fetchOne(
            "SELECT * FROM collections WHERE id = ?",
            [$id]
        );
    }

    public static function findBySlug($slug)
    {
        $db = Database::getInstance();
        return $db->fetchOne(
            "SELECT * FROM collections WHERE slug = ?",
            [$slug]
        );
    }

    public static function create($data)
    {
        $db = Database::getInstance();

        $db->query(
            "INSERT INTO collections (name, slug, description, image, is_active, display_order) VALUES (?, ?, ?, ?, ?, ?)",
            [
                $data['name'],
                $data['slug'],
                $data['description'] ?? null,
                $data['image'] ?? null,
                $data['is_active'] ?? 1,
                $data['display_order'] ?? 0
            ]
        );

        return $db->lastInsertId();
    }

    public static function update($id, $data)
    {
        $db = Database::getInstance();

        $db->query(
            "UPDATE collections SET name = ?, slug = ?, description = ?, image = ?, is_active = ?, display_order = ? WHERE id = ?",
            [
                $data['name'],
                $data['slug'],
                $data['description'] ?? null,
                $data['image'] ?? null,
                $data['is_active'] ?? 1,
                $data['display_order'] ?? 0,
                $id
            ]
        );

        return true;
    }

    public static function delete($id)
    {
        $db = Database::getInstance();
        return $db->query("DELETE FROM collections WHERE id = ?", [$id]);
    }

    public static function getProductCount($collectionId)
    {
        $db = Database::getInstance();
        $result = $db->fetchOne(
            "SELECT COUNT(*) as count FROM products WHERE collection_id = ?",
            [$collectionId]
        );
        return $result['count'] ?? 0;
    }
}
