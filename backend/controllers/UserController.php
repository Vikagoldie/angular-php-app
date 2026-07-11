<?php

namespace ppa\Controller;
use PDO;
use ppa\Model\UserModel;

require_once '../models/UserModel.php';
require_once '../config/Database.php';

class UserController
{
    private UserModel $user;
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
        $this->user = new UserModel($db);
    }

    public function getUsers()
    {
        return $this->user->getAll();
}
}