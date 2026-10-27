<?php
header('Content-Type: application/json');
require_once dirname(__DIR__, 2) . '/src/bootstrap.php';

if (isset($_SESSION['user_id'])) {
    echo json_encode([
        'item' => [
            'login' => $_SESSION['login'] ?? '',
            'role' => $_SESSION['role'] ?? 'user',
            'fio' => $_SESSION['fio'] ?? '',
        ],
        'isLoggedIn' => true
    ]);
} else {
    echo json_encode(['item' => null, 'isLoggedIn' => false]);
}
?>
