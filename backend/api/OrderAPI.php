<?php

require_once __DIR__ . '/common.php';
require_once __DIR__ . '/../Model/Database.php';
require_once __DIR__ . '/../Model/OrderModel.php';
require_once __DIR__ . '/../Controller/OrderController.php';

use webshop\Controller\OrderController;

try {
    $controller = new OrderController();
    $method = $_SERVER['REQUEST_METHOD'];
    $action = $_GET['action'] ?? '';

    if ($method === 'GET' && $action === 'mine') {
        $user = requireLogin();
        $result = $controller->myOrders($user['user_id']);
    } elseif ($method === 'GET' && $action === 'all') {
        requireRole('admin');
        $result = $controller->overview();
    } elseif ($method === 'POST' && $action === 'checkout') {
        $user = requireLogin();
        $body = json_decode(file_get_contents('php://input'), true) ?? [];
        $result = $controller->checkout($user['user_id'], $body['items'] ?? []);
        http_response_code(!empty($result['success']) ? 201 : 400);
    } elseif ($method === 'PUT') {
        requireRole('admin');

        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            sendJson(['success' => false, 'message' => 'Ungültige oder fehlende ID.'], 400);
        }

        parse_str(file_get_contents('php://input'), $putData);
        $result = $controller->updateStatus((int) $_GET['id'], $putData['status'] ?? '');
        http_response_code(!empty($result['success']) ? 200 : 400);
    } elseif ($method === 'DELETE') {
        requireRole('admin');

        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            sendJson(['success' => false, 'message' => 'Ungültige oder fehlende ID.'], 400);
        }

        $result = $controller->destroy((int) $_GET['id']);
        http_response_code(!empty($result['success']) ? 200 : 400);
    } else {
        sendJson(['success' => false, 'message' => 'Unbekannte Aktion.'], 404);
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
