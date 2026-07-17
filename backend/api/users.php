<?php

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

use ppa\Config\Database;
use ppa\Model\UserModel;
use ppa\Controller\UserController;

require_once '../config/Database.php';
require_once '../controllers/UserController.php';


try {

    $database = new Database();
    $pdo = $database->linkDB();

    $controller = new UserController($pdo);

    $users = $controller->getUsers();

    echo json_encode($users);
    exit;

} catch (Exception $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
