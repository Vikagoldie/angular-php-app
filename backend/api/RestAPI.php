<?php

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once '../Model/Database.php';
require_once '../Model/UserModel.php';
require_once '../Controller/UserController.php';

use webshop\Controller\UserController;
use webshop\Model\UserModel;
use webshop\Model\Database;

try {
    $controller = new UserController();
    $method = $_SERVER['REQUEST_METHOD'];

    switch ($method) {
        case 'GET':
            if (isset($_GET['id']) && is_numeric($_GET['id'])) {
                $result = $controller->show((int) $_GET['id']);
            } else {
                $result = $controller->index($_GET);
            }
            break;

        case 'POST':
            $result = $controller->store($_POST);
            http_response_code(!empty($result['success']) ? 201 : 400);
            break;

        case 'PUT':
            parse_str(file_get_contents("php://input"), $putData);

            if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
                http_response_code(400);
                echo json_encode([
                    "success" => false,
                    "message" => "Ungültige oder fehlende ID."
                ]);
                exit;
            }

            $result = $controller->update((int) $_GET['id'], $putData);
            http_response_code(!empty($result['success']) ? 200 : 400);
            break;

        case 'DELETE':
            if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
                http_response_code(400);
                echo json_encode([
                    "success" => false,
                    "message" => "Ungültige oder fehlende ID."
                ]);
                exit;
            }

            $result = $controller->destroy((int) $_GET['id']);
            http_response_code(!empty($result['success']) ? 200 : 400);
            break;

        default:
            http_response_code(405);
            echo json_encode([
                "success" => false,
                "message" => "Methode nicht erlaubt."
            ]);
            exit;
    }

    echo json_encode($result);
    exit;
} catch (Exception $e) {
    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}