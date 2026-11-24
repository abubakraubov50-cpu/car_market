<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once("config/db.php");
require_once("config/helpers.php");

if (!isset($_GET["id"]) || empty($_GET["id"])) {
    header("Location: selectposters.php");
    exit();
}

$id = mysqli_real_escape_string($conn, $_GET["id"]);

// Запрос с получением всех данных автомобиля, продавца и его u_id
$query = "SELECT poster.*, brands.br_name, models.m_name, users.id as seller_id, users.username, users.email, users.phone 
          FROM poster 
          JOIN brands ON poster.br_id = brands.br_id 
          JOIN models ON poster.m_id = models.m_id 
          JOIN users ON poster.u_id = users.id 
          WHERE poster.p_id = '$id'";

$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    echo '
    <!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <title>Объявление не найдено — ASO.tj</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="bg-light text-dark d-flex align-items-center justify-content-center" style="min-height: 100vh;">
        <div class="container text-center" style="max-width: 500px;">
            <div class="card p-5 border-0 shadow-lg rounded-4">
                <i class="bi bi-exclamation-circle text-primary display-1 mb-3"></i>
                <h4 class="fw-bold mb-2">Объявление не найдено!</h4>
                <p class="text-muted mb-4">Возможно, авто уже продано или ссылка устарела.</p>
                <a href="selectposters.php" class="btn btn-primary rounded-pill px-4 fw-bold">Вернуться на главную</a>
            </div>
        </div>
    </body>
    </html>';
    exit();
}

$car = mysqli_fetch_assoc($result);

// Получаем все фотографии для этого объявления
$photos_query = "SELECT path FROM photo WHERE post_id = '$id'";
$photos_result = mysqli_query($conn, $photos_query);
$photos = [];

if ($photos_result) {
    while ($p = mysqli_fetch_assoc($photos_result)) {
        $photos[] = $p['path'];
    }
}

if (empty($photos)) {
    $photos[] = !empty($car['p_photo']) ? $car['p_photo'] : 'default.jpg';
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= !empty($car['p_title']) ? htmlspecialchars($car['p_title']) : 'Детали автомобиля' ?> — ASO.tj</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        :root {
            --primary-color: #0f172a;
            --accent-color: #2563eb;
            --bg-light: #f8fafc;
            --card-border: #e2e8f0;
        }

        body {
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            background-color: var(--bg-light);
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        main {
            flex: 1;
        }

        /* Navbar */
        .navbar-marketplace {
            background-color: #ffffff;
            border-bottom: 1px solid var(--card-border);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        }

        .logo-brand {
            font-family: 'Arial Black', sans-serif;
            font-size: 26px;
            font-weight: 900;
            color: var(--primary-color);
            letter-spacing: -0.5px;
        }

        .logo-brand span {
            color: var(--accent-color);
        }

        /* Карточки и слайдер */
        .content-card {
            background-color: #ffffff;
            border: 1px solid var(--card-border);
            border-radius: 20px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
        }

        .car-main-img {
            height: 460px;
            object-fit: cover;
            border-radius: 16px;
        }

        .thumb-img {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.2s ease;
        }

        .thumb-img:hover, .thumb-img.active {
            border-color: var(--accent-color);
            opacity: 0.9;
        }

        .badge-price {
            font-size: 2rem;
            font-weight: 800;
            color: var(--accent-color);
        }

        .seller-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
        }

        .mega-dropdown-menu {
            width: 90vw;
            max-width: 1100px;
            left: 50% !important;
            transform: translateX(-50%) !important;
            margin-top: 15px !important;
            background-color: #ffffff;
            border: 1px solid var(--card-border);
            border-radius: 20px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.12);
        }

        footer {
            background-color: #0f172a;
            color: #94a3b8;
            padding: 40px 0;
            margin-top: 60px;
        }
    </style>
</head>
<body>

    <!-- HEADER -->
    <header class="navbar-marketplace sticky-top py-2">
        <div class="container d-flex align-items-center justify-content-between">
            <a href="selectposters.php" class="text-decoration-none d-flex align-items-center">
                <span class="logo-brand">ASO<span>.tj</span></span>
            </a>

            <div class="d-none d-md-flex align-items-center gap-4 fw-semibold text-secondary">
                <div class="dropdown position-static">
                    <a href="#" class="text-decoration-none text-dark dropdown-toggle" data-bs-toggle="dropdown" data-bs-auto-close="outside" data-i18n="nav_cats">
                        Категории
                    </a>
                    <div class="dropdown-menu mega-dropdown-menu p-4">
                        <?php include('categories_menu.php'); ?>
                    </div>
                </div>
                <a href="user/profile.php" class="text-decoration-none text-dark" data-i18n="nav_sell">Продать авто</a>
            </div>

            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-light btn-sm rounded-pill fw-bold border px-3" id="langToggle" type="button">
                    <i class="bi bi-globe me-1"></i><span id="currentLang">TJ</span>
                </button>

                <?php if (is_logged_in()): ?>
                    <a href="user/profile.php" class="btn btn-outline-dark btn-sm rounded-pill px-3 fw-medium" data-i18n="profile_btn">
                        <i class="bi bi-person-circle me-1"></i> Профиль
                    </a>
                    <?php if (isset($_SESSION["role"]) && $_SESSION["role"] === "admin"): ?>
                        <a href="admin/manage.php" class="btn btn-primary btn-sm rounded-pill px-3 fw-medium" data-i18n="admin_btn">Управление</a>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="auth/login.php" class="btn btn-outline-dark btn-sm rounded-pill px-3 fw-medium" data-i18n="login_btn">Войти</a>
                    <a href="auth/reg.php" class="btn btn-primary btn-sm rounded-pill px-3 fw-bold" data-i18n="reg_btn">Регистрация</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main class="container py-4">
        <!-- Кнопка назад -->
        <div class="mb-3">
            <a href="selectposters.php" class="btn btn-link text-decoration-none text-secondary p-0 fw-semibold">
                <i class="bi bi-arrow-left me-1"></i> <span data-i18n="back_btn">Вернуться к объявлениям</span>
            </a>
        </div>

        <div class="row g-4">
            <!-- Левый блок: Галерея авто -->
            <div class="col-lg-7">
                <div class="content-card p-3">
                    <div id="carCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <?php foreach ($photos as $index => $photo_path): ?>
                                <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                                    <img src="../PHOTO/<?= htmlspecialchars($photo_path) ?>" 
                                         class="d-block w-100 car-main-img" 
                                         alt="Фото автомобиля">
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <?php if (count($photos) > 1): ?>
                            <button class="carousel-control-prev" type="button" data-bs-target="#carCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon rounded-circle bg-dark p-3" aria-hidden="true"></span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#carCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon rounded-circle bg-dark p-3" aria-hidden="true"></span>
                            </button>
                        <?php endif; ?>
                    </div>

                    <!-- Миниатюры снимков -->
                    <?php if (count($photos) > 1): ?>
                        <div class="d-flex gap-2 mt-3 overflow-auto pb-2">
                            <?php foreach ($photos as $index => $photo_path): ?>
                                <img src="../PHOTO/<?= htmlspecialchars($photo_path) ?>" 
                                     class="thumb-img <?= $index === 0 ? 'active' : '' ?>" 
                                     onclick="bootstrap.Carousel.getInstance(document.getElementById('carCarousel')).to(<?= $index ?>)">
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Описание -->
                <div class="content-card p-4 mt-4">
                    <h5 class="fw-bold text-dark mb-3" data-i18n="desc_title">Описание автомобиля</h5>
                    <p class="text-secondary mb-0 leading-relaxed" style="white-space: pre-line;">
                        <?= htmlspecialchars($car['p_description']) ?>
                    </p>
                </div>
            </div>

            <!-- Правый блок: Детали и Контакты -->
            <div class="col-lg-5">
                <div class="content-card p-4 h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <span class="badge bg-light text-primary border px-3 py-1.5 rounded-pill fw-semibold mb-2 d-inline-block">
                                    <?= htmlspecialchars($car['br_name']) ?>
                                </span>
                                <h2 class="fw-extrabold text-dark mb-1"><?= htmlspecialchars($car['br_name']) ?> <?= htmlspecialchars($car['m_name']) ?></h2>
                            </div>
                        </div>

                        <p class="text-muted small mb-3"><?= htmlspecialchars($car['p_title']) ?></p>

                        <div class="badge-price mb-4">
                            $<?= htmlspecialchars($car['p_price']) ?>
                        </div>

                        <!-- Характеристики -->
                        <div class="border-top border-bottom py-3 mb-4">
                            <div class="row g-2 text-sm">
                                <div class="col-6 text-muted" data-i18n="param_brand">Марка:</div>
                                <div class="col-6 fw-semibold text-end"><?= htmlspecialchars($car['br_name']) ?></div>
                                <div class="col-6 text-muted" data-i18n="param_model">Модель:</div>
                                <div class="col-6 fw-semibold text-end"><?= htmlspecialchars($car['m_name']) ?></div>
                                <div class="col-6 text-muted" data-i18n="param_year">Год выпуска:</div>
                                <div class="col-6 fw-semibold text-end"><?= !empty($car['p_year']) ? htmlspecialchars($car['p_year']) : '—' ?></div>
                                <div class="col-6 text-muted" data-i18n="param_date">Дата публикации:</div>
                                <div class="col-6 fw-semibold text-end"><?= htmlspecialchars($car['created_at']) ?></div>
                            </div>
                        </div>

                        <!-- Блок продавца: Позвонить -->
                        <div class="seller-card p-4 mb-3">
                            <h6 class="fw-bold mb-3 text-dark d-flex align-items-center gap-2">
                                <i class="bi bi-person-badge-fill text-primary"></i> <span data-i18n="seller_title">Продавец</span>
                            </h6>
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold fs-4" style="width: 48px; height: 48px;">
                                    <?= mb_substr(!empty($car['username']) ? $car['username'] : 'A', 0, 1) ?>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark"><?= !empty($car['username']) ? htmlspecialchars($car['username']) : 'Аноним' ?></h6>
                                    <small class="text-muted" data-i18n="seller_verified">Проверенный продавец</small>
                                </div>
                            </div>

                            <?php $phone = !empty($car['phone']) ? htmlspecialchars($car['phone']) : ''; ?>
                            
                            <?php if ($phone): ?>
                                <a href="tel:<?= $phone ?>" class="btn btn-primary w-100 rounded-3 py-2.5 fw-bold mb-2 d-flex align-items-center justify-content-center gap-2">
                                    <i class="bi bi-telephone-fill"></i> Позвонить <?= $phone ?>
                                </a>
                                <a href="user/profile_user.php?id=<?= $car['seller_id'] ?>" class="btn btn-primary w-100 rounded-3 py-2.5 fw-bold d-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-person-lines-fill"></i> Посмотреть профиль продовца
                            </a>
                            <?php else: ?>
                                <div class="alert alert-warning text-center small mb-0 py-2">
                                    Телефон продавца не указан
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="container text-center">
            <p class="mb-0">&copy; <?= date('Y') ?> ASO.tj — Официальный авторынок Таджикистана. Все права защищены.</p>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

    <script>
        const translations = {
            tj: {
                nav_cats: "Категорияҳо",
                nav_sell: "Фурӯхтани мошин",
                profile_btn: "Профил",
                admin_btn: "Идоракунӣ",
                login_btn: "Воридшавӣ",
                reg_btn: "Рӯйхатгирӣ",
                back_btn: "Бозгашт ба эълонҳо",
                desc_title: "Тавсифи мошин",
                param_brand: "Марка:",
                param_model: "Модел:",
                param_year: "Соли барориш:",
                param_date: "Санаи нашр:",
                seller_title: "Фурӯшанда",
                seller_verified: "Фурӯшандаи тасдиқшуда"
            },
            ru: {
                nav_cats: "Категории",
                nav_sell: "Продать авто",
                profile_btn: "Профиль",
                admin_btn: "Управление",
                login_btn: "Войти",
                reg_btn: "Регистрация",
                back_btn: "Вернуться к объявлениям",
                desc_title: "Описание автомобиля",
                param_brand: "Марка:",
                param_model: "Модель:",
                param_year: "Год выпуска:",
                param_date: "Дата публикации:",
                seller_title: "Продавец",
                seller_verified: "Проверенный продавец"
            },
            en: {
                nav_cats: "Categories",
                nav_sell: "Sell Car",
                profile_btn: "Profile",
                admin_btn: "Admin Panel",
                login_btn: "Sign In",
                reg_btn: "Register",
                back_btn: "Back to listings",
                desc_title: "Vehicle Description",
                param_brand: "Make:",
                param_model: "Model:",
                param_year: "Year:",
                param_date: "Published:",
                seller_title: "Seller",
                seller_verified: "Verified Seller"
            }
        };

        const langToggleBtn = document.getElementById('langToggle');
        const currentLangSpan = document.getElementById('currentLang');

        function setLanguage(lang) {
            localStorage.setItem('selectedLang', lang);
            currentLangSpan.textContent = lang.toUpperCase();

            document.querySelectorAll('[data-i18n]').forEach(element => {
                const key = element.getAttribute('data-i18n');
                if (translations[lang] && translations[lang][key]) {
                    element.textContent = translations[lang][key];
                }
            });
        }

        langToggleBtn.addEventListener('click', () => {
            const currentLang = localStorage.getItem('selectedLang') || 'tj';
            let newLang = 'ru';
            if (currentLang === 'tj') newLang = 'ru';
            else if (currentLang === 'ru') newLang = 'en';
            else newLang = 'tj';
            
            setLanguage(newLang);
        });

        document.addEventListener('DOMContentLoaded', () => {
            const savedLang = localStorage.getItem('selectedLang') || 'tj';
            setLanguage(savedLang);
        });
    </script>
</body>
</html>