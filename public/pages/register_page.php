<?php require_once dirname(__DIR__, 2) . '/src/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta name="csrf-token" content="<?= htmlspecialchars($_SESSION['csrf'], ENT_QUOTES, 'UTF-8') ?>">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Корочки.Есть - Регистрация</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/auth.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet">
</head>

<body>

    <header>
        <div class="container">
            <a href="../index.php" class="logo">
                <img src="../img/logo.png" alt="Корочки.Есть" class="logo-img">
            </a>

            <button class="burger-menu" id="burgerMenu">
                <span></span>
                <span></span>
                <span></span>
            </button>

            <nav class="hidden-mobile" id="navMenu">
                <ul>
                    <li><a href="../index.php">Главная</a></li>
                    <li><a href="../index.php#services">Курсы</a></li>
                    <li><a href="../index.php#reviews">Отзывы</a></li>
                    <li><a href="login_page.php" class="nav-link-primary">Войти</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="auth-container">
            <div class="card text-center auth-card">
                <h1 class="auth-title">Регистрация</h1>

                <form id="regForm">
                    <div class="form-group">
                        <label class="text-left auth-label">Логин (лат. + цифры, мин 6 симв.)</label>
                        <input type="text" name="login" placeholder="Введите логин" pattern="^[a-zA-Z0-9]{6,}$"
                            title="Латиница и цифры, минимум 6 символов" required>
                    </div>

                    <div class="form-group">
                        <label class="text-left auth-label">Пароль (мин 8 симв.)</label>
                        <input type="password" name="password" placeholder="Введите пароль" pattern="^.{8,}$"
                            title="Минимум 8 символов" required>
                    </div>

                    <div class="form-group">
                        <label class="text-left auth-label">ФИО (кириллица + пробелы)</label>
                        <input type="text" name="fio" placeholder="Введите ФИО" pattern="^[А-Яа-яЁё\s]+$"
                            title="Только кириллица и пробелы" required>
                    </div>

                    <div class="form-group">
                        <label class="text-left auth-label">Телефон (8(XXX)XXX-XX-XX)</label>
                        <input type="tel" name="phone" placeholder="8(XXX)XXX-XX-XX"
                            pattern="^8\(\d{3}\)\d{3}-\d{2}-\d{2}$" title="Формат: 8(XXX)XXX-XX-XX" required>
                    </div>

                    <div class="form-group">
                        <label class="text-left auth-label">Эл.почта</label>
                        <input type="email" name="email" placeholder="Введите почту" required>
                    </div>

                    <div id="message" class="error-message error-msg"></div>

                    <button type="submit" class="btn btn-block btn-dark">Создать пользователя</button>

                    <a href="login_page.php" class="btn btn-secondary btn-block mt-2 btn-no-border">Войти</a>
                </form>
            </div>
        </div>
    </main>

    <footer>
        <div class="container footer-content text-center footer-center">
            <div class="footer-logo-wrap">
                <img src="../img/logo2.png" alt="Корочки.Есть" class="footer-logo-img">
            </div>
            <div class="footer-links-desktop">
                <a href="../index.php">Главная</a>
                <a href="../index.php#services">Курсы</a>
                <a href="../index.php#reviews">Отзывы</a>
            </div>
        </div>
        <div class="container footer-copy">
            <p>KorokNET<br>2025</p>
        </div>
    </footer>

    <script src="../js/auth.js"></script>
</body>

</html>
