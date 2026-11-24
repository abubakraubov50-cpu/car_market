<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once('../config/db.php');
require_once('../config/helpers.php');

check_auth();

$user_id = $_SESSION['user_id'];

// 1. Данные пользователя
$user_query = "SELECT username, phone, email, `role` FROM users WHERE id = '$user_id'";
$user_res = mysqli_query($conn, $user_query);
$user_data = mysqli_fetch_assoc($user_res);

// 2. Объявления пользователя
$cars_query = "SELECT poster.*, brands.br_name, models.m_name 
               FROM poster 
               JOIN brands ON poster.br_id = brands.br_id 
               JOIN models ON poster.m_id = models.m_id 
               WHERE poster.u_id = '$user_id' 
               ORDER BY poster.p_id DESC";

$cars_res = mysqli_query($conn, $cars_query);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет — ASO.tj</title>
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

        /* Profile Card */
        .profile-card {
            background-color: #ffffff;
            border: 1px solid var(--card-border);
            border-radius: 20px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
        }

        .avatar-box {
            width: 72px;
            height: 72px;
            background: linear-gradient(135deg, #2563eb, #1e3a8a);
            color: #ffffff;
            font-size: 2rem;
            font-weight: 700;
        }

        /* Car Cards */
        .car-item-card {
            background: #ffffff;
            border: 1px solid var(--card-border);
            border-radius: 16px;
            overflow: hidden;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .car-item-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.08);
            border-color: #cbd5e1;
        }

        .badge-price {
            font-size: 1.2rem;
            font-weight: 800;
            color: var(--accent-color);
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
            
            <div class="d-flex align-items-center gap-4">
                <a href="../selectposters.php" class="text-decoration-none d-flex align-items-center">
                    <span class="logo-brand">ASO<span>.tj</span></span>
                </a>
                
                <a href="../selectposters.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-semibold d-none d-sm-inline-flex align-items-center gap-1">
                    <i class="bi bi-house-door"></i> <span data-i18n="nav_home">На главную</span>
                </a>
            </div>

            <div class="d-none d-md-flex align-items-center gap-4 fw-semibold text-secondary">
                <div class="dropdown position-static">
                    <a href="#" class="text-decoration-none text-dark dropdown-toggle" data-bs-toggle="dropdown" data-bs-auto-close="outside" data-i18n="nav_cats">
                        Категории
                    </a>
                    <div class="dropdown-menu mega-dropdown-menu p-4">
                        <?php include('../categories_menu.php'); ?>
                    </div>
                </div>
                <a href="add_car.php" class="text-decoration-none text-dark" data-i18n="nav_sell">Продать авто</a>
            </div>

            <div class="d-flex align-items-center gap-2">
                <!-- Ссылка на избранное в шапке -->
                <a href="../favorites.php" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-medium" title="Избранное">
                    <i class="bi bi-heart-fill me-1"></i> <span data-i18n="nav_fav">Избранное</span>
                </a>

                <button class="btn btn-light btn-sm rounded-pill fw-bold border px-3" id="langToggle" type="button">
                    <i class="bi bi-globe me-1"></i><span id="currentLang">TJ</span>
                </button>

                <a href="../auth/logout.php" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-medium">
                    <i class="bi bi-box-arrow-right me-1"></i> <span data-i18n="nav_logout">Выйти</span>
                </a>
            </div>

        </div>
    </header>

    <main class="container py-5">
        
        <div class="mb-3 d-sm-none">
            <a href="../selectposters.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-semibold">
                <i class="bi bi-arrow-left me-1"></i> <span data-i18n="nav_home">На главную</span>
            </a>
        </div>

        <!-- Блок профиля -->
        <div class="profile-card p-4 p-md-5 mb-5">
            <div class="row align-items-center">
                <div class="col-md-8 d-flex align-items-center gap-4 mb-4 mb-md-0">
                    <div class="avatar-box rounded-circle d-flex align-items-center justify-content-center shadow-sm">
                        <?= mb_substr(sanitize($user_data['username']), 0, 1) ?>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <h2 class="mb-0 fw-bold text-dark"><?php echo sanitize($user_data['username']); ?></h2>
                            <span class="badge bg-light text-primary border px-2.5 py-1 rounded-pill text-uppercase fs-7 fw-bold">
                                <?php echo sanitize($user_data['role']); ?>
                            </span>
                        </div>
                        <p class="text-muted mb-0 small" data-i18n="profile_sub">Личный кабинет продавца</p>
                    </div>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="edit_profile.php" class="btn btn-outline-primary fw-bold px-4 rounded-pill">
                        <i class="bi bi-pencil-square me-1"></i> <span data-i18n="edit_profile">Редактировать профиль</span>
                    </a>
                </div>
            </div>
            
            <hr class="my-4" style="border-color: #e2e8f0;">

            <div class="row g-3 text-secondary fs-6">
                <div class="col-sm-6 col-md-6">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-telephone-fill text-primary"></i>
                        <span data-i18n="phone_label">Телефон:</span>
                        <strong class="text-dark"><?php echo sanitize($user_data['phone']); ?></strong>
                    </div>
                </div>
                <div class="col-sm-6 col-md-6">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-envelope-fill text-primary"></i>
                        <span>Email:</span>
                        <strong class="text-dark"><?php echo sanitize($user_data['email']); ?></strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Заголовок секции автомобилей и кнопка добавления -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <h4 class="fw-bold text-dark m-0" data-i18n="my_cars_title">Мои опубликованные автомобили</h4>
            <a href="add_car.php" class="btn btn-primary fw-bold rounded-pill px-4 shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> <span data-i18n="add_car_btn">Добавить автомобиль</span>
            </a>
        </div>

        <!-- Список объявлений пользователя -->
        <?php if ($cars_res && mysqli_num_rows($cars_res) > 0): ?>
            <div class="row g-4">
                <?php while ($car = mysqli_fetch_assoc($cars_res)): ?>
                    <?php 
                        $post_id = $car['p_id'];
                        $photos_q = mysqli_query($conn, "SELECT path FROM photo WHERE post_id = '$post_id'");
                        $photos = [];
                        while ($p = mysqli_fetch_assoc($photos_q)) {
                            $photos[] = $p['path'];
                        }
                        
                        if (empty($photos)) {
                            $photos[] = 'default.jpg';
                        }
                    ?>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="car-item-card h-100 d-flex flex-column">
                            
                            <!-- Carousel -->
                            <div id="carCarousel_<?= $post_id ?>" class="carousel slide" data-bs-ride="carousel">
                                <div class="carousel-inner">
                                    <?php foreach ($photos as $index => $photo_path): ?>
                                        <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                                            <img src="../../PHOTO/<?= htmlspecialchars($photo_path) ?>" 
                                                 class="d-block w-100" 
                                                 style="height: 200px; object-fit: cover;" 
                                                 alt="Фото автомобиля">
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <?php if (count($photos) > 1): ?>
                                    <button class="carousel-control-prev" type="button" data-bs-target="#carCarousel_<?= $post_id ?>" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon rounded-circle bg-dark p-2" aria-hidden="true"></span>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#carCarousel_<?= $post_id ?>" data-bs-slide="next">
                                        <span class="carousel-control-next-icon rounded-circle bg-dark p-2" aria-hidden="true"></span>
                                    </button>
                                <?php endif; ?>
                            </div>
                            
                            <div class="p-3 d-flex flex-column flex-grow-1">
                                <h5 class="fw-bold text-dark mb-1">
                                    <?php echo sanitize($car['br_name']) . " " . sanitize($car['m_name']); ?> 
                                    <?php if (!empty($car['p_year'])): ?>
                                        <span class="text-muted fs-6">(<?= (int)$car['p_year'] ?> г.)</span>
                                    <?php endif; ?>
                                </h5>
                                <p class="text-muted small mb-2"><?php echo sanitize($car['p_title']); ?></p>
                                <div class="badge-price mb-3">$<?php echo sanitize($car['p_price']); ?></div>
                                
                                <!-- Блок кнопок (улучшенная сетка) -->
                                <div class="mt-auto pt-3 border-top d-flex flex-column gap-2">
                                    <a href="../car.php?id=<?php echo $car['p_id']; ?>" class="btn btn-sm btn-primary rounded-pill fw-bold w-100 py-2" data-i18n="view_btn">
                                        <i class="bi bi-eye me-1"></i> Посмотреть объявление
                                    </a>
                                    
                                    <div class="d-flex gap-2">
                                        <a href="edit_car.php?id=<?php echo $car['p_id']; ?>" class="btn btn-sm btn-outline-primary rounded-pill fw-bold w-50 py-1.5" data-i18n="edit_btn">
                                            <i class="bi bi-pencil me-1"></i> Изменить
                                        </a>
                                        <a href="delete_car.php?id=<?php echo $car['p_id']; ?>" class="btn btn-sm btn-outline-danger rounded-pill fw-semibold w-50 py-1.5" onclick="return confirm('Вы действительно хотите удалить это объявление?');" data-i18n="delete_btn">
                                            <i class="bi bi-trash me-1"></i> Удалить
                                        </a>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-light text-center py-5 border rounded-4 text-muted">
                <i class="bi bi-car-front fs-1 d-block mb-2 text-primary"></i>
                <span data-i18n="no_cars">Вы пока не добавили ни одного автомобиля. Нажмите кнопку «Добавить автомобиль», чтобы разместить объявление.</span>
            </div>
        <?php endif; ?>

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
                nav_home: "Ба саҳифаи асосӣ",
                nav_cats: "Категорияҳо",
                nav_sell: "Фурӯхтани мошин",
                nav_fav: "Панжараҳо",
                nav_logout: "Баромадан",
                profile_sub: "Кабинети шахсии фурӯшанда",
                edit_profile: "Таҳрири профил",
                phone_label: "Телефон:",
                my_cars_title: "Мошинҳои нашркардаи ман",
                add_car_btn: "Илова кардани мошин",
                view_btn: "Дидан",
                edit_btn: "Таҳрир",
                delete_btn: "Нест кардан",
                no_cars: "Шумо ҳанӯз ягон мошин илова накардаед. Барои ҷойгир кардани эълон тугмаи «Илова кардани мошин»-ро пахш кунед."
            },
            ru: {
                nav_home: "На главную",
                nav_cats: "Категории",
                nav_sell: "Продать авто",
                nav_fav: "Избранное",
                nav_logout: "Выйти",
                profile_sub: "Личный кабинет продавца",
                edit_profile: "Редактировать профиль",
                phone_label: "Телефон:",
                my_cars_title: "Мои опубликованные автомобили",
                add_car_btn: "Добавить автомобиль",
                view_btn: "Посмотреть",
                edit_btn: "Изменить",
                delete_btn: "Удалить",
                no_cars: "Вы пока не добавили ни одного автомобиля. Нажмите кнопку «Добавить автомобиль», чтобы разместить объявление."
            },
            en: {
                nav_home: "Home",
                nav_cats: "Categories",
                nav_sell: "Sell Car",
                nav_fav: "Favorites",
                nav_logout: "Sign Out",
                profile_sub: "Seller Personal Account",
                edit_profile: "Edit Profile",
                phone_label: "Phone:",
                my_cars_title: "My Published Vehicles",
                add_car_btn: "Add Vehicle",
                view_btn: "View",
                edit_btn: "Edit",
                delete_btn: "Delete",
                no_cars: "You haven't listed any vehicles yet. Click the 'Add Vehicle' button to post an ad."
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