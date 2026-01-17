<?php

namespace PetFactory\Models;

use PetFactory\Core\Database;

class User
{
    public static function findByEmail($email)
    {
        $db = Database::getInstance();
        return $db->fetchOne(
            "SELECT * FROM users WHERE email = ?",
            [$email]
        );
    }

    public static function findById($id)
    {
        $db = Database::getInstance();
        return $db->fetchOne(
            "SELECT * FROM users WHERE id = ?",
            [$id]
        );
    }

    public static function create($data)
    {
        $db = Database::getInstance();
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

        $db->query(
            "INSERT INTO users (email, password, first_name, last_name, phone) VALUES (?, ?, ?, ?, ?)",
            [
                $data['email'],
                $hashedPassword,
                $data['first_name'],
                $data['last_name'],
                $data['phone'] ?? null
            ]
        );

        return $db->lastInsertId();
    }

    public static function update($id, $data)
    {
        $db = Database::getInstance();

        $fields = [];
        $values = [];

        if (isset($data['email'])) {
            $fields[] = 'email = ?';
            $values[] = $data['email'];
        }
        if (isset($data['first_name'])) {
            $fields[] = 'first_name = ?';
            $values[] = $data['first_name'];
        }
        if (isset($data['last_name'])) {
            $fields[] = 'last_name = ?';
            $values[] = $data['last_name'];
        }
        if (isset($data['phone'])) {
            $fields[] = 'phone = ?';
            $values[] = $data['phone'];
        }
        if (isset($data['password'])) {
            $fields[] = 'password = ?';
            $values[] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $values[] = $id;

        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
        return $db->query($sql, $values);
    }

    public static function all()
    {
        $db = Database::getInstance();
        return $db->fetchAll("SELECT * FROM users ORDER BY created_at DESC");
    }
}
