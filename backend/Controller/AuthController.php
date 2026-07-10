<?php

namespace webshop\Controller;

use webshop\Model\AuthModel;

class AuthController
{
    private $authModel;

    public function __construct()
    {
        $this->authModel = new AuthModel();
    }

    public function login(array $post)
    {
        session_start();

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

        return ['success' => true, 'user' => $user];
    }

    public function logout()
    {
        session_start();
        session_unset();
        session_destroy();

        return ['success' => true];
    }
}