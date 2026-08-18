<?php

require_once __DIR__ . '/common.php';
require_once __DIR__ . '/../Model/Database.php';
require_once __DIR__ . '/../Model/ProductModel.php';
require_once __DIR__ . '/../Controller/ProductController.php';

use webshop\Controller\ProductController;

try {
    $controller = new ProductController();
    $method = $_SERVER['REQUEST_METHOD'];
    $action = $_GET['action'] ?? null;

    switch ($method) {
        case 'GET':
            if (isset($_GET['id']) && is_numeric($_GET['id'])) {
                $result = $controller->show((int) $_GET['id']);
            } else {
                $result = $controller->index($_GET);
            }
            break;

        case 'POST':
            if ($action === 'upload') {
                requireRole('admin');

                if (!isset($_POST['product_id']) || !is_numeric($_POST['product_id'])) {
                    sendJson(['success' => false, 'message' => 'Ungültige oder fehlende Produkt-ID.'], 400);
                }

                $result = $controller->uploadImage((int) $_POST['product_id'], $_FILES['image'] ?? null);
                http_response_code(!empty($result['success']) ? 200 : 400);
                break;
            }

            requireRole('admin');
            $result = $controller->store($_POST);
            http_response_code(!empty($result['success']) ? 201 : 400);
            break;

        case 'PUT':
            requireRole('admin');
            parse_str(file_get_contents("php://input"), $putData);

            if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
                sendJson(['success' => false, 'message' => 'Ungültige oder fehlende ID.'], 400);
            }

            $result = $controller->update((int) $_GET['id'], $putData);
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
