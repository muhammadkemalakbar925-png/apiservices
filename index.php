<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once __DIR__ . '/helpers/Response.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/StudySessionController.php';

$method = $_SERVER['REQUEST_METHOD'];
$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri    = rtrim($uri, '/');

// Hapus base path jika deploy di subfolder, misal: /study-track-api
// $uri = str_replace('/study-track-api', '', $uri);

$parts = explode('/', trim($uri, '/'));
// misal: /api/sessions/5 → ['api', 'sessions', '5']

$resource = $parts[1] ?? '';   // 'sessions' atau 'login' / 'register'
$id       = isset($parts[2]) && is_numeric($parts[2]) ? (int)$parts[2] : null;

match(true) {
    $resource === 'register' && $method === 'POST' => register(),
    $resource === 'login'    && $method === 'POST' => login(),

    $resource === 'sessions' && $method === 'GET'    && $id === null => getSessions(),
    $resource === 'sessions' && $method === 'GET'    && $id !== null => getSession($id),
    $resource === 'sessions' && $method === 'POST'                   => createSession(),
    $resource === 'sessions' && $method === 'PUT'    && $id !== null => updateSession($id),
    $resource === 'sessions' && $method === 'DELETE' && $id !== null => deleteSession($id),

    default => sendError('Endpoint tidak ditemukan', 404),
};