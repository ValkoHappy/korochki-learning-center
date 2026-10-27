<?php require_once dirname(__DIR__) . '/src/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta name="csrf-token" content="<?= htmlspecialchars($_SESSION['csrf'], ENT_QUOTES, 'UTF-8') ?>">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Корочки.Есть - Учебный центр</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet">
</head>

<body>

    <header>
        <div class="container">
            <a href="index.php" class="logo">
                <img src="img/logo.png" alt="Корочки.Есть" class="logo-img">
            </a>

            <button class="burger-menu" id="burgerMenu">
                <span></span>
                <span></span>
                <span></span>
            </button>

            <nav class="hidden-mobile" id="navMenu">
                <ul>
                    <li><a href="#about">О нас</a></li>
                    <li><a href="#services">Курсы</a></li>
                    <li><a href="#reviews">Отзывы</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="pages/dashboard.php" class="nav-link-primary">Личный кабинет</a></li>
                    <?php else: ?>
                        <li><a href="pages/login_page.php" class="nav-link-primary">Войти</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <section class="slider-container">
            <div class="slider-wrapper">
                <div class="slider-backgrounds">
                    <div class="slide-bg active"
                        style="background-image: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('img/image11.jpg');">
                    </div>
                    <div class="slide-bg"
                        style="background-image: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('img/image06.jpg');">
                    </div>
                    <div class="slide-bg"
                        style="background-image: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('img/image07.jpg');">
                    </div>
                    <div class="slide-bg"
                        style="background-image: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('img/image10.webp');">
                    </div>
                </div>

                <div class="slider-content-static">
                    <div class="container">
                        <h1>Корочки.Есть</h1>
                        <p>Учебный центр</p>
                        <a href="#services" class="btn btn-light">Выбрать курс</a>
                    </div>
                </div>
            </div>

            <button class="slider-btn prev-btn" id="prevBtn">&#10094;</button>
            <button class="slider-btn next-btn" id="nextBtn">&#10095;</button>

            <div class="slider-dots">
                <span class="dot active" data-slide="0"></span>
                <span class="dot" data-slide="1"></span>
                <span class="dot" data-slide="2"></span>
                <span class="dot" data-slide="3"></span>
            </div>
        </section>

        <section class="container mb-5 section-padding" id="about">
            <h2 class="section-title">О нас</h2>
            <div class="card about-card">
                <div class="about-col">
                    <p class="about-text">
                        Учебный центр «Корочки.Есть» — это современная образовательная платформа, специализирующаяся на
                        подготовке высококвалифицированных IT-специалистов. Мы предлагаем актуальные курсы,
                        разработанные практикующими экспертами, чтобы помочь вам освоить востребованные навыки в
                        программировании, веб-дизайне и работе с данными.
                        <br><br>
                        Наша миссия — сделать качественное IT-образование доступным и понятным. Мы сочетаем
                        фундаментальную теорию с интенсивной практикой, обеспечивая нашим студентам уверенный старт
                        карьеры.
                    </p>
                </div>
                <div class="about-col">
                    <img src="img/image07.jpg" alt="О нас" class="about-img">
                </div>
            </div>
        </section>

        <section class="container section-padding" id="services">
            <h2 class="section-title">Наши курсы</h2>
            <div class="services-grid">
                <div class="service-card">
                    <img src="img/image08.webp" alt="Алгоритмизация">
                    <div class="service-content">
                        <h3>Основы алгоритмизации и программирования</h3>
                        <p class="service-desc">Фундаментальный курс по структурам данных и логике кода.</p>
                        <a href="pages/dashboard.php?create_claim=1&course=1" class="btn btn-block">Получить доступ
                            &rarr;</a>
                    </div>
                </div>
                <div class="service-card">
                    <img src="img/image09.webp" alt="Веб-дизайн">
                    <div class="service-content">
                        <h3>Основы веб-дизайна</h3>
                        <p class="service-desc">UX/UI, работа в Figma, принципы композиции и типографики.</p>
                        <a href="pages/dashboard.php?create_claim=1&course=2" class="btn btn-block">Получить доступ
                            &rarr;</a>
                    </div>
                </div>
                <div class="service-card">
                    <img src="img/image10.webp" alt="Базы данных">
                    <div class="service-content">
                        <h3>Основы проектирования баз данных</h3>
                        <p class="service-desc">SQL, ER-диаграммы, нормализация и работа с данными.</p>
                        <a href="pages/dashboard.php?create_claim=1&course=3" class="btn btn-block">Получить доступ
                            &rarr;</a>
                    </div>
                </div>
            </div>
        </section>

        <section class="container" id="reviews">
            <h2 class="section-title">Отзывы</h2>
            <div class="reviews-grid">
                <div class="review-card">
                    <img src="img/image06.jpg" alt="Ivan" class="review-avatar">
                    <div class="review-content">
                        <div class="review-header">
                            <span class="review-name">Иван Г</span>
                            <div class="review-stars">★★★★★</div>
                        </div>
                        <p>Курс вообще топ, спасибо</p>
                    </div>
                </div>
                <div class="review-card">
                    <img src="img/image11.jpg" alt="Olya" class="review-avatar">
                    <div class="review-content">
                        <div class="review-header">
                            <span class="review-name">Оля Г</span>
                            <div class="review-stars">★★★★</div>
                        </div>
                        <p>Курс имба, знания хорошие получила, спасибо</p>
                    </div>
                </div>
                <div class="review-card">
                    <img src="img/image07.jpg" alt="Liza" class="review-avatar">
                    <div class="review-content">
                        <div class="review-header">
                            <span class="review-name">Лиза П</span>
                            <div class="review-stars">★★★★★</div>
                        </div>
                        <p>Спасибо другу Артуру что купил мне этот курс, все очень понравилось</p>
                    </div>
                </div>
                <div class="review-card">
                    <img src="img/image08.webp" alt="Pavel" class="review-avatar">
                    <div class="review-content">
                        <div class="review-header">
                            <span class="review-name">Павел Н</span>
                            <div class="review-stars">★★★★★&#10095;&#10095;</div>
                        </div>
                        <p>Все супер топ. кайф. свег</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="container footer-content">
            <div class="footer-logo-wrap">
                <img src="img/logo2.png" alt="Корочки.Есть" class="footer-logo-img">
            </div>

            <div class="footer-links-desktop">
                <a href="#about">О нас</a>
                <a href="#services">Курсы</a>
                <a href="#reviews">Отзывы</a>
                <a href="pages/dashboard.php">Личный кабинет</a>
            </div>
        </div>

        <div class="container footer-copy">
            <p>KorokNET<br>2025</p>
        </div>
    </footer>

    <script src="js/main.js"></script>
</body>
</html>
