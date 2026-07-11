<?php

namespace ppa\Model;

use PDO;

class UserModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAll(): array
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
            ORDER BY lastname, firstname
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}