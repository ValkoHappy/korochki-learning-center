<?php
require_once dirname(__DIR__, 2) . '/src/bootstrap.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login_page.php');
    exit;
}
$role = $_SESSION['role'] ?? 'user';
$fio = $_SESSION['fio'] ?? 'Пользователь';
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta name="csrf-token" content="<?= htmlspecialchars($_SESSION['csrf'], ENT_QUOTES, 'UTF-8') ?>">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет - Корочки.Есть</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/dashboard.css">
</head>

<body data-role="<?php echo $role; ?>">

    <header>
        <div class="container">
            <a href="../index.php" class="logo">
                <img src="../img/logo.png" alt="Корочки.Есть" style="height: 50px;">
            </a>

            <button class="burger-menu" id="burgerMenu">
                <span></span>
                <span></span>
                <span></span>
            </button>

            <nav id="navMenu">
                <ul>
                    <?php if ($role === 'admin'): ?>
                        <li><a href="../index.php">Главная</a></li>
                        <li><a href="#" class="active">Панель администратора</a></li>
                        <li><form action="../handlers/logout.php" method="post" style="display:inline"><?= Security::field() ?><button type="submit"  style="color: var(--primary-color);">Выход (Admin)</button></form></li>
                    <?php else: ?>
                        <li><a href="../index.php">Главная</a></li>
                        <li><a href="../index.php#services">Курсы</a></li>
                        <li><a href="#" onclick="showTab('list')" id="nav-list" class="active">Мои заявки</a></li>
                        <li><a href="#" onclick="showTab('create')" id="nav-create">Создать заявку</a></li>
                        <li><form action="../handlers/logout.php" method="post" style="display:inline"><?= Security::field() ?><button type="submit"  style="color: var(--primary-color);">Выход</button></form></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container dashboard-main">

        <div class="dashboard-header">
            <h1 class="dashboard-title">
                <?php echo $role === 'admin' ? 'Управление заявками' : 'Личный кабинет'; ?>
            </h1>
            <p class="dashboard-welcome">Добро пожаловать, <b><?= htmlspecialchars($fio, ENT_QUOTES, 'UTF-8') ?></b></p>
        </div>

        <div id="tab-list" class="tab-content" style="display: block; width: 100%;">

            <div class="search-container">
                <div class="search-icon"></div>
                <input type="text" id="searchInput"
                    placeholder="<?php echo $role === 'admin' ? 'Поиск по ФИО или курсу...' : 'Поиск по курсам...'; ?>">
            </div>

            <div class="status-filters">
                <button class="filter-btn active" data-status="all">Все</button>
                <button class="filter-btn" data-status="Новая">Новые</button>
                <button class="filter-btn" data-status="Идет обучение">В обучении</button>
                <button class="filter-btn" data-status="Обучение завершено">Завершенные</button>
            </div>

            <div id="applicationsList" class="applications-grid">
                <p class="text-center loading-text">Загрузка данных...</p>
            </div>
        </div>

        <?php if ($role !== 'admin'): ?>
            <div id="tab-create" class="tab-content tab-content-hidden">
                <div class="card create-form-card">
                    <h2 class="text-center mb-4">Новая заявка</h2>
                    <form id="createAppForm">
                        <div class="form-group">
                            <label class="form-label">Выберите курс</label>
                            <select name="course_name" class="form-select" required>
                                <option value="">-- Не выбрано --</option>
                                <option value="Основы алгоритмизации и программирования">Основы алгоритмизации и
                                    программирования</option>
                                <option value="Основы веб-дизайна">Основы веб-дизайна</option>
                                <option value="Основы проектирования баз данных">Основы проектирования баз данных</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Дата начала</label>
                            <input name="start_date" id="startDateInput" type="date" class="form-input" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Способ оплаты</label>
                            <select name="payment_method" class="form-select" required>
                                <option value="">-- Не выбрано --</option>
                                <option value="Наличные">Наличные</option>
                                <option value="Перевод по номеру телефона">Перевод по номеру телефона</option>
                            </select>
                        </div>

                        <div id="createMessage" class="error-message error-msg"></div>

                        <button type="submit" class="btn btn-block btn-submit">Отправить заявку</button>
                        <button type="button" class="btn btn-secondary btn-block mt-2 btn-cancel"
                            onclick="showTab('list')">Отмена</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>

    </main>

    <div id="reviewModal" class="review-modal">
        <div class="review-modal-content">
            <h3 class="review-modal-title">Оставить отзыв</h3>
            <div class="form-group review-form-group">
                <label>Курс</label>
                <input type="text" id="reviewCourseName" class="review-course-input" readonly>
            </div>
            <form id="reviewForm">
                <input type="hidden" id="reviewAppId" value="">
                <div class="form-group">
                    <label>Ваш отзыв</label>
                    <textarea id="reviewText" class="review-textarea" rows="4" required
                        placeholder="Напишите ваш отзыв..."></textarea>
                </div>
                <div class="form-group">
                    <label>Оценка</label>
                    <select id="reviewRating" class="review-select">
                        <option value="5">5 - Отлично</option>
                        <option value="4">4 - Хорошо</option>
                        <option value="3">3 - Нормально</option>
                        <option value="2">2 - Плохо</option>
                        <option value="1">1 - Ужасно</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-block review-submit-btn">Отправить</button>
                <button type="button" class="btn btn-secondary btn-block review-cancel-btn"
                    onclick="closeReviewModal()">Отмена</button>
            </form>
        </div>
    </div>

    <footer>
        <div class="container footer-content text-center footer-center">
            <div class="footer-logo-wrap">
                <img src="../img/logo2.png" alt="Корочки.Есть" class="footer-logo-img">
            </div>
        </div>
        <div class="container footer-copy footer-copy-text">
            <p>KorokNET &copy; 2025</p>
        </div>
    </footer>

    <script src="../js/dashboard.js"></script>
</body>

</html>
