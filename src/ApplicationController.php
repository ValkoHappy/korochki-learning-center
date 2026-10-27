<?php
final class ApplicationController
{
    private const STATUSES = ['Новая', 'Идет обучение', 'Обучение завершено'];
    private const COURSES = ['Основы алгоритмизации и программирования', 'Основы веб-дизайна', 'Основы проектирования баз данных'];

    public function __construct(private PDO $db) {}

    private function requireUser(): int
    {
        if (empty($_SESSION['user_id'])) Http::json(['success' => false, 'message' => 'Войдите в аккаунт'], 401);
        return (int) $_SESSION['user_id'];
    }

    public function index(): never
    {
        $id = $this->requireUser();
        if (($_SESSION['role'] ?? '') === 'admin') {
            $rows = $this->db->query('SELECT a.*, u.fio, u.email FROM applications a JOIN user u ON a.user_id = u.id ORDER BY a.id DESC')->fetchAll();
        } else {
            $statement = $this->db->prepare('SELECT * FROM applications WHERE user_id = ? ORDER BY id DESC');
            $statement->execute([$id]);
            $rows = $statement->fetchAll();
        }
        Http::json(['success' => true, 'role' => $_SESSION['role'], 'data' => $rows]);
    }

    public function create(): never
    {
        Http::post();
        $id = $this->requireUser();
        $data = Http::input();
        $course = (string) ($data['course_name'] ?? '');
        $date = (string) ($data['start_date'] ?? '');
        $payment = (string) ($data['payment_method'] ?? '');
        $parsed = DateTimeImmutable::createFromFormat('!Y-m-d', $date);
        if (!in_array($course, self::COURSES, true) || !in_array($payment, ['Наличные', 'Перевод по номеру телефона'], true) || !$parsed || $parsed->format('Y-m-d') !== $date || $date < date('Y-m-d')) {
            Http::json(['success' => false, 'message' => 'Проверьте курс, дату и способ оплаты'], 422);
        }
        $statement = $this->db->prepare("INSERT INTO applications (user_id, course_name, start_date, payment_method, status) VALUES (?, ?, ?, ?, 'Новая')");
        $statement->execute([$id, $course, $date, $payment]);
        Http::json(['success' => true, 'message' => 'Заявка создана']);
    }

    public function update(): never
    {
        Http::post();
        $this->requireUser();
        if (($_SESSION['role'] ?? '') !== 'admin') Http::json(['success' => false, 'message' => 'Нет доступа'], 403);
        $data = Http::input();
        $id = (int) ($data['id'] ?? 0);
        $status = $data['status'] ?? '';
        if ($id < 1 || !in_array($status, self::STATUSES, true)) Http::json(['success' => false, 'message' => 'Недопустимый статус или номер заявки'], 422);
        $statement = $this->db->prepare('UPDATE applications SET status = ? WHERE id = ?');
        $statement->execute([$status, $id]);
        Http::json(['success' => true, 'message' => 'Статус обновлён']);
    }

    public function delete(): never
    {
        Http::post();
        $userId = $this->requireUser();
        $id = (int) (Http::input()['id'] ?? 0);
        $admin = ($_SESSION['role'] ?? '') === 'admin';
        $statement = $this->db->prepare('DELETE FROM applications WHERE id = ?' . ($admin ? '' : ' AND user_id = ?'));
        $statement->execute($admin ? [$id] : [$id, $userId]);
        if (!$statement->rowCount()) Http::json(['success' => false, 'message' => 'Заявка не найдена'], 404);
        Http::json(['success' => true, 'message' => 'Заявка удалена']);
    }
}
