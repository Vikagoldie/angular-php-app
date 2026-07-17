<?php

namespace webshop\Model;

class Database
{
    private $dbName = "pbbfa24akr_giftboxes";
    private $linkName = "mysql.pb.bib.de";
    private $user = "pbbfa24akr";
    private $pw = "x5uMyFQSv87E";

    public function linkDB()
    {
        try {
            $pdo = new \PDO(
                "mysql:dbname=$this->dbName;host=$this->linkName;charset=utf8mb4",
                $this->user,
                $this->pw,
                [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
                ]
            );

            return $pdo;
        } catch (\PDOException $e) {
            echo json_encode([
                "success" => false,
                "message" => $e->getMessage()
            ]);
            exit;
        }
    }
}