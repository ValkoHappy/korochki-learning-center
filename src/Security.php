<?php
declare(strict_types=1);

final class Security
{
    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) return;
        session_name('korochki_learning_center');
        session_start(['cookie_httponly' => true, 'cookie_samesite' => 'Lax', 'cookie_secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off', 'use_strict_mode' => true]);
        $_SESSION['csrf'] ??= bin2hex(random_bytes(32));
    }

    public static function checkRequest(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') return;
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $_POST['_csrf'] ?? '';
        if (!is_string($token) || !hash_equals($_SESSION['csrf'], $token)) {
            http_response_code(403);
            if (str_contains($_SERVER['CONTENT_TYPE'] ?? '', 'application/json')) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['success' => false, 'message' => 'Обновите страницу и повторите действие.']);
            } else {
                echo 'Обновите страницу и повторите действие.';
            }
            exit;
        }
    }

    public static function field(): string
    {
        return '<input type="hidden" name="_csrf" value="' . htmlspecialchars($_SESSION['csrf'], ENT_QUOTES, 'UTF-8') . '">';
    }
}
