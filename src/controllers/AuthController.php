<?php

namespace PetFactory\Controllers;

use PetFactory\Core\Controller;
use PetFactory\Core\Auth;
use PetFactory\Models\User;
use PetFactory\Models\Cart;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            $this->redirect('/');
        }

        $this->view('pages/login', [
            'csrf_token' => $this->generateCSRF()
        ]);
    }

    public function login()
    {
        if (!$this->validateCSRF()) {
            $_SESSION['error'] = 'Invalid request';
            $this->redirect('/login');
        }

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'Email and password are required';
            $this->redirect('/login');
        }

        if (Auth::login($email, $password)) {
            Cart::mergeGuestCart(Auth::id());
            $this->redirect('/');
        } else {
            $_SESSION['error'] = 'Invalid credentials';
            $this->redirect('/login');
        }
    }

    public function showRegister()
    {
        if (Auth::check()) {
            $this->redirect('/');
        }

        $this->view('pages/register', [
            'csrf_token' => $this->generateCSRF()
        ]);
    }

    public function register()
    {
        if (!$this->validateCSRF()) {
            $_SESSION['error'] = 'Invalid request';
            $this->redirect('/register');
        }

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $firstName = $_POST['first_name'] ?? '';
        $lastName = $_POST['last_name'] ?? '';
        $phone = $_POST['phone'] ?? '';

        if (empty($email) || empty($password) || empty($firstName) || empty($lastName)) {
            $_SESSION['error'] = 'All fields are required';
            $this->redirect('/register');
        }

        if ($password !== $confirmPassword) {
            $_SESSION['error'] = 'Passwords do not match';
            $this->redirect('/register');
        }

        if (strlen($password) < PASSWORD_MIN_LENGTH) {
            $_SESSION['error'] = 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters';
            $this->redirect('/register');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Invalid email address';
            $this->redirect('/register');
        }

        if (User::findByEmail($email)) {
            $_SESSION['error'] = 'Email already registered';
            $this->redirect('/register');
        }

        $userId = User::create([
            'email' => $email,
            'password' => $password,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'phone' => $phone
        ]);

        if ($userId) {
            Auth::login($email, $password);
            Cart::mergeGuestCart($userId);
            $_SESSION['success'] = 'Account created successfully';
            $this->redirect('/');
        } else {
            $_SESSION['error'] = 'Registration failed. Please try again.';
            $this->redirect('/register');
        }
    }

    public function logout()
    {
        Auth::logout();
        $this->redirect('/');
    }
}
