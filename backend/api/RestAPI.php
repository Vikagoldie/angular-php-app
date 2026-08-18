<?php

require_once __DIR__ . '/common.php';
require_once __DIR__ . '/../Model/Database.php';
require_once __DIR__ . '/../Model/UserModel.php';
require_once __DIR__ . '/../Controller/UserController.php';

use webshop\Controller\UserController;

try {
    $controller = new UserController();
    $method = $_SERVER['REQUEST_METHOD'];
    $currentUser = currentUser();
    $isAdmin = $currentUser !== null && $currentUser['role'] === 'admin';

    switch ($method) {
        case 'GET':
            if (isset($_GET['id']) && is_numeric($_GET['id'])) {
                $id = (int) $_GET['id'];

                if (!$isAdmin && ($currentUser === null || $currentUser['user_id'] !== $id)) {
                    sendJson(['success' => false, 'message' => 'Keine Berechtigung.'], 403);
                }

                $result = $controller->show($id);
            } else {
                requireRole('admin');
                $result = $controller->index($_GET);
            }
            break;

        case 'POST':
            requireRole('admin');
            $result = $controller->store($_POST);
            http_response_code(!empty($result['success']) ? 201 : 400);
            break;

        case 'PUT':
            requireLogin();
            parse_str(file_get_contents("php://input"), $putData);

            if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
                sendJson(['success' => false, 'message' => 'Ungültige oder fehlende ID.'], 400);
            }

            $id = (int) $_GET['id'];

            if (!$isAdmin && $currentUser['user_id'] !== $id) {
                sendJson(['success' => false, 'message' => 'Keine Berechtigung.'], 403);
            }

            $result = $controller->update($id, $putData, $isAdmin);
            http_response_code(!empty($result['success']) ? 200 : 400);
            break;

        case 'DELETE':
            requireRole('admin');

            if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
                sendJson(['success' => false, 'message' => 'Ungültige oder fehlende ID.'], 400);
            }

            $result = $controller->destroy((int) $_GET['id']);
            http_response_code(!empty($result['success']) ? 200 : 400);
            break;

        default:
            sendJson(['success' => false, 'message' => 'Methode nicht erlaubt.'], 405);
    }

    echo json_encode($result);
    exit;
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Es ist ein interner Fehler aufgetreten."
    ]);
}
