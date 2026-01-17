<?php

namespace PetFactory\Core;

use PetFactory\Models\User;

class Auth
{
    public static function login($email, $password)
    {
        $user = User::findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            return false;
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
        $_SESSION['is_admin'] = $user['is_admin'];

        return true;
    }

    public static function logout()
    {
        session_unset();
        session_destroy();
    }

    public static function check()
    {
        return isset($_SESSION['user_id']);
    }

    public static function isAdmin()
    {
        return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
    }

    public static function user()
    {
        if (!self::check()) {
            return null;
        }

        return [
            'id' => $_SESSION['user_id'],
            'email' => $_SESSION['user_email'],
            'name' => $_SESSION['user_name'],
            'is_admin' => $_SESSION['is_admin']
        ];
    }

    public static function id()
    {
        return $_SESSION['user_id'] ?? null;
    }

    public static function requireLogin()
    {
        if (!self::check()) {
            header('Location: /login');
            exit;
        }
    }

    public static function requireAdmin()
    {
        if (!self::check() || !self::isAdmin()) {
            header('Location: /');
            exit;
        }
    }
}
