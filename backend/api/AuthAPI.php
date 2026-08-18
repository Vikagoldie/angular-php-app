<?php

require_once __DIR__ . '/common.php';
require_once __DIR__ . '/../Model/Database.php';
require_once __DIR__ . '/../Model/AuthModel.php';
require_once __DIR__ . '/../Model/UserModel.php';
require_once __DIR__ . '/../Controller/AuthController.php';

use webshop\Controller\AuthController;

try {
    $controller = new AuthController();
    $method = $_SERVER['REQUEST_METHOD'];
    $action = $_GET['action'] ?? '';

    if ($method === 'GET' && $action === 'me') {
        $result = $controller->me();
    } elseif ($method === 'POST' && $action === 'login') {
        $result = $controller->login($_POST);
        http_response_code(!empty($result['success']) ? 200 : 401);
    } elseif ($method === 'POST' && $action === 'register') {
        $result = $controller->register($_POST);
        http_response_code(!empty($result['success']) ? 201 : 400);
    } elseif ($method === 'POST' && $action === 'logout') {
        $result = $controller->logout();
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
