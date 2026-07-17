<?php

namespace webshop\Model;

class UserModel extends Database
{
    public function getAll(string $sort = 'lastname', string $direction = 'ASC'): array
    {
        $allowedSort = ['user_id', 'firstname', 'lastname', 'email', 'role_id'];
        $allowedDirection = ['ASC', 'DESC'];

        if (!in_array($sort, $allowedSort, true)) {
            $sort = 'lastname';
        }

        if (!in_array($direction, $allowedDirection, true)) {
            $direction = 'ASC';
        }

        $sql = "
            SELECT
                user_id,
                firstname,
                lastname,
                email,
                password_hash,
                role_id
            FROM users
            ORDER BY $sort $direction
        ";

        $pdo = $this->linkDB();
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getById(int $id)
    {
        $sql = "
            SELECT
                user_id,
                firstname,
                lastname,
                email,
                password_hash,
                role_id
            FROM users
            WHERE user_id = :id
        ";

        $pdo = $this->linkDB();
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function getByEmail(string $email)
    {
        $sql = "
            SELECT
                user_id,
                firstname,
                lastname,
                email,
                password_hash,
                role_id
            FROM users
            WHERE email = :email
        ";

        $pdo = $this->linkDB();
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['email' => $email]);

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function create(array $data): bool
    {
        $sql = "
            INSERT INTO users (firstname, lastname, email, password_hash, role_id)
            VALUES (:firstname, :lastname, :email, :password_hash, :role_id)
        ";

        $pdo = $this->linkDB();
        $stmt = $pdo->prepare($sql);

        return $stmt->execute([
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
            'email' => $data['email'],
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
            'role_id' => $data['role_id']
        ]);
    }

    public function update(int $id, array $data): bool
    {
        $sql = "
            UPDATE users
            SET firstname = :firstname,
                lastname = :lastname,
                email = :email,
                role_id = :role_id
            WHERE user_id = :id
        ";

        $pdo = $this->linkDB();
        $stmt = $pdo->prepare($sql);

        return $stmt->execute([
            'id' => $id,
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
            'email' => $data['email'],
            'role_id' => $data['role_id']
        ]);
    }

    public function updatePassword(int $id, string $password): bool
    {
        $sql = "
            UPDATE users
            SET password_hash = :password_hash
            WHERE user_id = :id
        ";

        $pdo = $this->linkDB();
        $stmt = $pdo->prepare($sql);

        return $stmt->execute([
            'id' => $id,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT)
        ]);
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM users WHERE user_id = :id";

        $pdo = $this->linkDB();
        $stmt = $pdo->prepare($sql);

        return $stmt->execute(['id' => $id]);
    }
}