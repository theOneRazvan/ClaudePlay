<?php

namespace PetFactory\Core;

class Controller
{
    protected function view($viewPath, $data = [])
    {
        extract($data);

        $viewFile = VIEWS_PATH . '/' . $viewPath . '.php';

        if (!file_exists($viewFile)) {
            die("View not found: $viewPath");
        }

        require_once $viewFile;
    }

    protected function json($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function redirect($url)
    {
        header("Location: $url");
        exit;
    }

    protected function validateCSRF()
    {
        if (!isset($_POST[CSRF_TOKEN_NAME]) || !isset($_SESSION[CSRF_TOKEN_NAME])) {
            return false;
        }

        if (!hash_equals($_SESSION[CSRF_TOKEN_NAME], $_POST[CSRF_TOKEN_NAME])) {
            return false;
        }

        return true;
    }

    protected function generateCSRF()
    {
        if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
            $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
        }
        return $_SESSION[CSRF_TOKEN_NAME];
    }
}
