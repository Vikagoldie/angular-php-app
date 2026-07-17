<?php

namespace webshop\Model;

class AuthModel extends Database
{
    public function findUserByEmail(string $email)
    {
        $sql = "SELECT u.user_id, u.firstname, u.lastname, u.email, u.password_hash, r.role_name
                FROM users u
                JOIN roles r ON r.role_id = u.role_id
                WHERE u.email = :email";

        $pdo = $this->linkDB();
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['email' => $email]);

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}