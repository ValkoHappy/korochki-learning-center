<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require dirname(__DIR__) . '/src/Database.php';
$email = getenv('ADMIN_EMAIL') ?: '';
$password = getenv('ADMIN_PASSWORD') ?: '';
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 12 || strlen($password) > 72) {
    fwrite(STDERR, "Задайте ADMIN_EMAIL и ADMIN_PASSWORD длиной от 12 до 72 символов.\n");
    exit(1);
}
$db = Database::connect();
$hash = password_hash($password, PASSWORD_DEFAULT);
$statement = $db->prepare("INSERT INTO user (fio, login, email, password, phone, role) VALUES (?, ?, ?, ?, ?, 'admin')");
$statement->execute(['Администратор', 'administrator', $email, $hash, '8(000)000-00-00']);
echo "Администратор создан.\n";
