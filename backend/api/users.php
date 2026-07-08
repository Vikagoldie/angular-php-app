<?php

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

use ppa\Config\Database;
use ppa\Model\UserModel;

require_once '../config/Database.php';
require_once '../models/UserModel.php';


try {

    $database = new Database();
    $pdo = $database->linkDB();

    $userModel = new UserModel($pdo);

    $users = $userModel->getAll();

    echo json_encode($users);

} catch (Exception $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
