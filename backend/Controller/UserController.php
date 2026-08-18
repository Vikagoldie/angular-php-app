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

        $users = $this->userModel->getAll($sort, $direction);

        return array_map([$this, 'hidePasswordHash'], $users);
    }

    public function show(int $id)
    {
        $user = $this->userModel->getById($id);

        return $user ? $this->hidePasswordHash($user) : $user;
    }

    private function hidePasswordHash(array $user): array
    {
        unset($user['password_hash']);
        return $user;
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

    public function update(int $id, array $post, bool $isAdmin = false)
    {
        // Nur Admins dürfen die Rolle eines Benutzers ändern. Normale Benutzer
        // behalten beim Bearbeiten ihres eigenen Profils ihre bisherige Rolle,
        // egal was im Request mitgeschickt wurde.
        if (!$isAdmin) {
            $existing = $this->userModel->getById($id);
            $post['role_id'] = $existing['role_id'] ?? $post['role_id'] ?? null;
        }

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