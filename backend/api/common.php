<?php

const ALLOWED_ORIGIN = 'http://localhost:4200';

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin === ALLOWED_ORIGIN) {
    header('Access-Control-Allow-Origin: ' . ALLOWED_ORIGIN);
}
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'samesite' => 'Lax',
]);
session_start();

function sendJson(array $data, int $statusCode = 200): void
{
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

function currentUser(): ?array
{
    if (!isset($_SESSION['user_id'])) {
        return null;
    }

    return [
        'user_id' => (int) $_SESSION['user_id'],
        'role' => $_SESSION['role'] ?? null,
        'firstname' => $_SESSION['firstname'] ?? null,
    ];
}

function requireLogin(): array
{
    $user = currentUser();

    if ($user === null) {
        sendJson(['success' => false, 'message' => 'Bitte zuerst einloggen.'], 401);
    }

    return $user;
}

function requireRole(string $role): array
{
    $user = requireLogin();

    if (($user['role'] ?? null) !== $role) {
        sendJson(['success' => false, 'message' => 'Keine Berechtigung für diese Aktion.'], 403);
    }

    return $user;
}
