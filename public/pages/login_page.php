<?php require_once dirname(__DIR__, 2) . '/src/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta name="csrf-token" content="<?= htmlspecialchars($_SESSION['csrf'], ENT_QUOTES, 'UTF-8') ?>">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Корочки.Есть - Вход</title>
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

            <nav class="hidden-mobile">
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
                <h1 class="auth-title">Вход</h1>

                <form id="loginForm">
                    <div class="form-group">
                        <label class="text-left auth-label">Логин</label>
                        <input type="text" name="login" placeholder="Введите логин" required>
                    </div>

                    <div class="form-group">
                        <label class="text-left auth-label">Пароль</label>
                        <input type="password" name="password" placeholder="Введите пароль" required>
                    </div>

                    <div id="message" class="error-message error-msg"></div>

                    <button type="submit" class="btn btn-block btn-dark">Войти</button>

                    <div class="text-center mt-2">
                        <a href="register_page.php" class="auth-link">Еще не зарегистрированы? Регистрация</a>
                    </div>
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
