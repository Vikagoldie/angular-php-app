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
            // Die genaue Fehlermeldung nur im Server-Log ausgeben, nicht an den
            // Client senden - sie könnte interne Details der Datenbank verraten.
            error_log('Datenbankverbindung fehlgeschlagen: ' . $e->getMessage());

            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "Die Datenbank ist aktuell nicht erreichbar."
            ]);
            exit;
        }
    }
}