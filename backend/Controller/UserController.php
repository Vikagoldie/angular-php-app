<?php

namespace webshop\Controller;

use webshop\Model\UserModel;



require_once '../Model/Database.php';
require_once '../Model/UserModel.php';


class UserController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index(array $query = [])
    {
        $sort = $query['sort'] ?? 'lastname';
        $direction = $query['direction'] ?? 'ASC';

        return $this->userModel->getAll($sort, $direction);
    }

    public function show(int $id)
    {
        return $this->userModel->getById($id);
    }

    public function store(array $post)
    {
        $errors = $this->validateUser($post, true);

        if ($this->userModel->getByEmail($post['email'] ?? '')) {
            $errors[] = "Die E-Mail-Adresse ist bereits vergeben.";
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $this->userModel->create($post);

        return ['success' => true];
    }

    public function update(int $id, array $post)
    {
        $errors = $this->validateUser($post, false);

        $existingUser = $this->userModel->getByEmail($post['email'] ?? '');
        if ($existingUser && (int)$existingUser['user_id'] !== $id) {
            $errors[] = "Die E-Mail-Adresse ist bereits vergeben.";
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $this->userModel->update($id, $post);

        if (!empty($post['password'])) {
            $this->userModel->updatePassword($id, $post['password']);
        }

        return ['success' => true];
    }

    public function destroy(int $id)
    {
        $this->userModel->delete($id);
        return ['success' => true];
    }

    private function validateUser(array $data, bool $isCreate): array
    {
        $errors = [];

        if (empty(trim($data['firstname'] ?? ''))) {
            $errors[] = "Vorname ist erforderlich.";
        }

        if (empty(trim($data['lastname'] ?? ''))) {
            $errors[] = "Nachname ist erforderlich.";
        }

        if (empty($data['email'] ?? '') || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Bitte eine gültige E-Mail-Adresse eingeben.";
        }

        if (!isset($data['role_id']) || filter_var($data['role_id'], FILTER_VALIDATE_INT) === false) {
            $errors[] = "Bitte eine gültige Rolle auswählen.";
        }

        if ($isCreate) {
            if (empty($data['password'] ?? '')) {
                $errors[] = "Passwort ist erforderlich.";
            } elseif (strlen($data['password']) < 8) {
                $errors[] = "Das Passwort muss mindestens 8 Zeichen lang sein.";
            }
        } else {
            if (!empty($data['password']) && strlen($data['password']) < 8) {
                $errors[] = "Neues Passwort muss mindestens 8 Zeichen lang sein.";
            }
        }

        return $errors;
    }
}