<?php

namespace webshop\Controller;

use webshop\Model\AuthModel;
use webshop\Model\UserModel;

class AuthController
{
    private $authModel;
    private $userModel;

    public function __construct()
    {
        $this->authModel = new AuthModel();
        $this->userModel = new UserModel();
    }

    public function login(array $post)
    {
        $errors = [];

        if (empty($post['email']) || !filter_var($post['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Bitte eine gültige E-Mail eingeben.";
        }

        if (empty($post['password'])) {
            $errors[] = "Bitte ein Passwort eingeben.";
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $user = $this->authModel->findUserByEmail($post['email']);

        if (!$user || !password_verify($post['password'], $user['password_hash'])) {
            return ['success' => false, 'errors' => ['Login fehlgeschlagen.']];
        }

        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['role'] = $user['role_name'];
        $_SESSION['firstname'] = $user['firstname'];

        unset($user['password_hash']);

        return ['success' => true, 'user' => $user];
    }

    public function logout()
    {
        session_unset();
        session_destroy();

        return ['success' => true];
    }

    public function register(array $post)
    {
        $errors = [];

        if (empty(trim($post['firstname'] ?? ''))) {
            $errors[] = "Vorname ist erforderlich.";
        }

        if (empty(trim($post['lastname'] ?? ''))) {
            $errors[] = "Nachname ist erforderlich.";
        }

        if (empty($post['email'] ?? '') || !filter_var($post['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Bitte eine gültige E-Mail-Adresse eingeben.";
        }

        if (empty($post['password'] ?? '') || strlen($post['password']) < 8) {
            $errors[] = "Das Passwort muss mindestens 8 Zeichen lang sein.";
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        if ($this->userModel->getByEmail($post['email'])) {
            return ['success' => false, 'errors' => ["Diese E-Mail-Adresse ist bereits registriert."]];
        }

        $this->userModel->create([
            'firstname' => $post['firstname'],
            'lastname' => $post['lastname'],
            'email' => $post['email'],
            'password' => $post['password'],
            'role_id' => 1,
        ]);

        return ['success' => true];
    }

    public function me()
    {
        if (!isset($_SESSION['user_id'])) {
            return ['success' => true, 'loggedIn' => false];
        }

        return [
            'success' => true,
            'loggedIn' => true,
            'user' => [
                'user_id' => (int) $_SESSION['user_id'],
                'role' => $_SESSION['role'],
                'firstname' => $_SESSION['firstname'],
            ],
        ];
    }
}