<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require dirname(__DIR__) . '/src/Database.php';
$db = Database::connect();
$db->exec(file_get_contents(dirname(__DIR__) . '/database/schema.sql'));
echo "Схема базы создана.\n";
