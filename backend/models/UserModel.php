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
                id,
                username,
                firstname,
                lastname,
                email,
                created
            FROM user
            ORDER BY lastname, firstname
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}