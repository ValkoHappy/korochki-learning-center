<?php
final class Http
{
    public static function json(array $data, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public static function input(): array
    {
        $data = json_decode(file_get_contents('php://input'), true);
        return is_array($data) ? $data : $_POST;
    }

    public static function post(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') self::json(['success' => false, 'message' => 'Метод не поддерживается'], 405);
    }
}
