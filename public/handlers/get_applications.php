<?php
require_once dirname(__DIR__, 2) . '/src/bootstrap.php';
require_once dirname(__DIR__, 2) . '/src/Http.php';
require_once dirname(__DIR__, 2) . '/src/ApplicationController.php';
try {
    (new ApplicationController(Database::connect()))->index();
} catch (PDOException $error) {
    Http::json(['success' => false, 'message' => 'Не удалось выполнить операцию.'], 503);
}
