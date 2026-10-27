<?php
final class AuthController
{
    public function __construct(private PDO $db) {}

    public function login(): never
    {
        Http::post();
        $input = Http::input();
        $statement = $this->db->prepare('SELECT * FROM user WHERE login = ?');
        $statement->execute([trim((string) ($input['login'] ?? ''))]);
        $user = $statement->fetch();
        if (!$user || !password_verify((string) ($input['password'] ?? ''), $user['password'])) {
            Http::json(['success' => false, 'message' => 'Неверный логин или пароль'], 401);
        }
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['fio'] = $user['fio'];
        $_SESSION['login'] = $user['login'];
        Http::json(['success' => true, 'role' => $user['role']]);
    }

    public function register(): never
    {
        Http::post();
        $data = Http::input();
        $login = trim((string) ($data['login'] ?? ''));
        $password = (string) ($data['password'] ?? '');
        $fio = trim((string) ($data['fio'] ?? ''));
        $phone = trim((string) ($data['phone'] ?? ''));
        $email = trim((string) ($data['email'] ?? ''));
        if (!preg_match('/^[a-zA-Z0-9]{6,100}$/', $login) || strlen($password) < 8 || strlen($password) > 72 || !preg_match('/^[А-Яа-яЁё\s]{3,150}$/u', $fio) || !preg_match('/^8\(\d{3}\)\d{3}-\d{2}-\d{2}$/', $phone) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Http::json(['success' => false, 'message' => 'Проверьте ФИО, логин, почту, телефон и пароль.'], 422);
        }
        $statement = $this->db->prepare('SELECT id FROM user WHERE login = ?');
        $statement->execute([$login]);
        if ($statement->fetch()) Http::json(['success' => false, 'message' => 'Логин занят'], 409);
        $statement = $this->db->prepare("INSERT INTO user (fio, login, email, password, phone, role) VALUES (?, ?, ?, ?, ?, 'user')");
        $statement->execute([$fio, $login, $email, password_hash($password, PASSWORD_DEFAULT), $phone]);
        Http::json(['success' => true, 'message' => 'Регистрация завершена']);
    }
}
