<?php


namespace ppa\Config;
use PDO;
use PDOException;

class Database {
    
   
    private $dbName = "pbbfa24akr_giftboxes"; //Datenbankname
    private $linkName = "mysql.pb.bib.de"; //Datenbank-Server«
    private $user = "pbbfa24akr"; //Benutzername
    private $pw = "x5uMyFQSv87E"; //Passwort
    
    /**
     * Stellt eine Verbindung zur Datenbank herss
     * 
     * @return \PDO Gibt eine Datenbankverbindung zurueck
     */
    public function linkDB() {
        try {
            $pdo = new \PDO("mysql:dbname=$this->dbName;host=$this->linkName"
                , $this->user
                , $this->pw
                , array(\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION));
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

   


