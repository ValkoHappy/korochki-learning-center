<?php
require_once dirname(__DIR__, 2) . '/src/bootstrap.php';
require_once dirname(__DIR__, 2) . '/src/Http.php';
require_once dirname(__DIR__, 2) . '/src/AuthController.php';
try {
    (new AuthController(Database::connect()))->login();
} catch (PDOException $error) {
    Http::json(['success' => false, 'message' => 'Не удалось выполнить операцию.'], 503);
}
