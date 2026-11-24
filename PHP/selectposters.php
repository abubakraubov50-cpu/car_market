<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once('config/db.php');
require_once('config/helpers.php'); 
?>
<!doctype html>
<html lang="ru">
    <head>
        <title>ASO.tj — Авторынок Таджикистана</title>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <!-- Bootstrap CSS v5.3.8 -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous"/>
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

            .hero-section {
                background: linear-gradient(135deg, rgba(15, 23, 42, 0.85), rgba(30, 58, 138, 0.75)), 
                            url('https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=1600&q=80') center/cover no-repeat;
                padding: 60px 0 80px 0;
                border-radius: 0 0 32px 32px;
                color: #ffffff;
            }

            .search-filter-card {
                background: #ffffff;
                border-radius: 20px;
                padding: 24px;
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
                color: #1e293b;
            }

            .form-control-custom {
                border: 1px solid #cbd5e1;
                border-radius: 12px;
                padding: 12px 16px;
                font-weight: 500;
            }

            .form-control-custom:focus {
                border-color: var(--accent-color);
                box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
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

            .car-item-card {
                background: #ffffff;
                border: 1px solid var(--card-border);
                border-radius: 16px;
                overflow: hidden;
                transition: transform 0.25s ease, box-shadow 0.25s ease;
            }

            .car-item-card:hover {
                transform: translateY(-6px);
                box-shadow: 0 12px 28px rgba(0, 0, 0, 0.08);
                border-color: #cbd5e1;
            }

            .badge-price {
                font-size: 1.25rem;
                font-weight: 800;
                color: var(--accent-color);
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
                
                <!-- Логотип -->
                <a href="selectposters.php" class="text-decoration-none d-flex align-items-center">
                    <span class="logo-brand">ASO<span>.tj</span></span>
                </a>

                <!-- Навигация -->
                <div class="d-none d-md-flex align-items-center gap-4 fw-semibold text-secondary">
                    <div class="dropdown position-static">
                        <a href="#" class="text-decoration-none text-dark dropdown-toggle" data-bs-toggle="dropdown" data-bs-auto-close="outside" data-i18n="nav_cats">
                            Категории
                        </a>
                        <div class="dropdown-menu mega-dropdown-menu p-4">
                            <?php include('categories_menu.php'); ?>
                        </div>
                    </div>

                    <a href="user/add_car.php" class="text-decoration-none text-dark" data-i18n="nav_sell">Продать авто</a>
                </div>

                <!-- Правая часть -->
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

        <main>
            <!-- БАННЕР С ФИЛЬТРОМ ПОИСКА -->
            <section class="hero-section mb-5">
                <div class="container">
                    <div class="row align-items-center g-4">
                        <div class="col-lg-5">
                            <h1 class="display-5 fw-extrabold mb-3 text-white" data-i18n="hero_title">Найдите лучший автомобиль в Таджикистане</h1>
                            <p class="fs-5 text-light opacity-75 mb-0" data-i18n="hero_subtitle">Тысячи проверенных объявлений от частных продавцов и автосалонов.</p>
                        </div>

                        <div class="col-lg-7">
                            <div class="search-filter-card">
                                <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                                    <i class="bi bi-search text-primary"></i> <span data-i18n="filter_title">Быстрый подбор авто</span>
                                </h5>
                                
                                <form action="selectposters.php" method="get">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <input 
                                                type="search" 
                                                name="search" 
                                                class="form-control form-control-custom" 
                                                placeholder="Поиск по марке или модели (например: Toyota, BMW)..." 
                                                data-i18n-placeholder="search_placeholder"
                                                value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>"
                                            >
                                        </div>
                                        
                                        <div class="col-6 col-md-8">
                                            <button type="submit" class="btn btn-primary w-100 rounded-3 fw-bold py-2" data-i18n="search_btn">
                                                Найти авто
                                            </button>
                                        </div>

                                        <div class="col-6 col-md-4">
                                            <a href="selectposters.php" class="btn btn-secondary w-100 rounded-3 fw-bold py-2">
                                                Очистить
                                            </a>
                                        </div>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ОСНОВНОЙ КАТОЛОГ ОБЪЯВЛЕНИЙ -->
            <div class="container">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="fw-bold text-dark mb-0">
                        <?php 
                        if (isset($_GET['cat_id'])) {
                            $cat_id = (int)$_GET['cat_id'];
                            $cat_name_q = mysqli_query($conn, "SELECT cat_name FROM categories WHERE cat_id = '$cat_id'");
                            $cat_data = mysqli_fetch_assoc($cat_name_q);
                            echo "<span data-i18n='cat_prefix' class='text-primary'>Категория:</span> " . htmlspecialchars($cat_data['cat_name'] ?? 'Все авто');
                        } elseif (isset($_GET['brand'])) {
                            echo "<span data-i18n='brand_prefix' class='text-primary'>Марка:</span> " . htmlspecialchars(strtoupper($_GET['brand']));
                        } elseif (isset($_GET['search'])) {
                            echo "<span data-i18n='search_prefix' class='text-primary'>Результаты поиска:</span> " . htmlspecialchars($_GET['search']);
                        } else {
                            echo "<span data-i18n='heading_latest'>Свежие предложения</span>";
                        }
                        ?>
                    </h3>
                </div>

                <?php
                if (isset($_GET["cat_id"]) && !empty($_GET["cat_id"])) {
                    $cat_id = (int)$_GET["cat_id"];
                    $query = "SELECT poster.*, brands.br_name, models.m_name 
                        FROM poster 
                        JOIN brands ON poster.br_id = brands.br_id 
                        JOIN models ON poster.m_id = models.m_id 
                        WHERE models.cat_id = '$cat_id'
                        ORDER BY poster.created_at DESC";
                    $result = mysqli_query($conn, $query);
                } elseif (isset($_GET["brand"]) && !empty($_GET["brand"])) {
                    $brand = mysqli_real_escape_string($conn, $_GET["brand"]);
                    $query = "SELECT poster.*, brands.br_name, models.m_name 
                        FROM poster 
                        JOIN brands ON poster.br_id = brands.br_id 
                        JOIN models ON poster.m_id = models.m_id 
                        WHERE brands.br_name LIKE '%$brand%' 
                           OR models.m_name LIKE '%$brand%'
                        ORDER BY poster.created_at DESC";
                    $result = mysqli_query($conn, $query);
                } elseif (isset($_GET["search"]) && !empty(trim($_GET["search"]))) {
                    $searchText = mysqli_real_escape_string($conn, trim($_GET["search"]));
                    $query = "SELECT poster.*, brands.br_name, models.m_name 
                              FROM poster 
                              JOIN brands ON poster.br_id = brands.br_id 
                              JOIN models ON poster.m_id = models.m_id 
                              WHERE brands.br_name LIKE '%$searchText%' 
                                 OR models.m_name LIKE '%$searchText%'
                              ORDER BY poster.created_at DESC";
                    $result = mysqli_query($conn, $query);
                } else {
                    $query = "SELECT poster.*, brands.br_name, models.m_name 
                        FROM poster 
                        JOIN brands ON poster.br_id = brands.br_id 
                        JOIN models ON poster.m_id = models.m_id 
                        ORDER BY poster.created_at DESC 
                        LIMIT 20";
                    $result = mysqli_query($conn, $query);
                }
                ?>

                <div class="row g-4">
                    <?php if ($result && mysqli_num_rows($result) > 0): ?>
                        <?php while ($poster = mysqli_fetch_assoc($result)): ?>
                            <div class="col-12 col-md-6 col-lg-4">
                                <div class="car-item-card h-100 d-flex flex-column position-relative">
                                    <?php
                                    $post_id = $poster['p_id'];
                                    $sql_photos = "SELECT path FROM photo WHERE post_id = $post_id";
                                    $photos_result = mysqli_query($conn, $sql_photos);
                                    $photos = [];

                                    if ($photos_result) {
                                        while ($photo = mysqli_fetch_assoc($photos_result)) {
                                            $photos[] = $photo['path'];
                                        }
                                    }

                                    if (empty($photos)) {
                                        $photos[] = $poster['p_photo'] ?? 'default.jpg';
                                    }
                                    ?>

                                    <!-- Кнопка Избранного (Сердечко) -->
                                    <?php if (is_logged_in()): ?>
                                        <?php 
                                            $current_uid = $_SESSION['user_id'];
                                            $fav_check = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM favorites WHERE user_id = '$current_uid' AND post_id = '$post_id'");
                                            $fav_row = mysqli_fetch_assoc($fav_check);
                                            $is_fav = ($fav_row['cnt'] > 0);
                                        ?>
                                        <a href="toggle_favorite.php?id=<?= $post_id ?>" class="btn btn-light btn-sm rounded-circle position-absolute top-0 end-0 m-3 shadow-sm d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; z-index: 10;" title="В избранное">
                                            <i class="bi bi-heart<?= $is_fav ? '-fill text-danger' : ' text-dark' ?>"></i>
                                        </a>
                                    <?php endif; ?>

                                    <!-- ИСПРАВЛЕННАЯ БАЗОВАЯ BOOTSTRAP КАРУСЕЛЬ ФОТОГРАФИЙ -->
                                    <div id="carCarousel_<?= $post_id ?>" class="carousel slide" data-bs-ride="false">
                                        <div class="carousel-inner">
                                            <?php foreach ($photos as $index => $photo_path): ?>
                                                <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                                                    <img src="https://carmarket.local/PHOTO/<?= htmlspecialchars($photo_path) ?>" 
                                                         class="d-block w-100" 
                                                         style="height: 220px; object-fit: cover;" 
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
                                            <?= htmlspecialchars($poster['br_name']) ?> <?= htmlspecialchars($poster['m_name']) ?> 
                                            <?php if (!empty($poster['p_year'])): ?>
                                                <span class="text-muted fs-6">(<?= (int)$poster['p_year'] ?> г.)</span>
                                            <?php endif; ?>
                                        </h5>
                                        <p class="text-muted small mb-2"><?= htmlspecialchars($poster['p_title']) ?></p>
                                        
                                        <div class="mt-auto pt-3 border-top d-flex align-items-center justify-content-between">
                                            <span class="badge-price">$<?= htmlspecialchars($poster['p_price']) ?></span>
                                            <a href="car.php?id=<?= $poster['p_id'] ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-bold" data-i18n="more_btn">
                                                Подробнее
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="alert alert-light text-center py-5 border rounded-4 text-muted" data-i18n="empty_filter">
                                <i class="bi bi-car-front fs-1 d-block mb-2 text-primary"></i>
                                По вашему запросу объявлений не найдено.
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>

        <footer>
            <div class="container text-center">
                <p class="mb-0">&copy; <?= date('Y') ?> ASO.tj — Официальный авторынок Таджикистана. <span data-i18n="footer_rights">Все права защищены.</span></p>
            </div>
        </footer>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script> 
        
        <script>
            const translations = {
                tj: {
                    nav_cats: "Категорияҳо",
                    nav_sell: "Фурӯхтани мошин",
                    nav_fav: "Панжараҳо",
                    search_placeholder: "Ҷустуҷӯи мошин (масалан: Toyota, BMW)...",
                    search_btn: "Натиҷаҳо",
                    profile_btn: "Профил",
                    admin_btn: "Идоракунӣ",
                    login_btn: "Воридшавӣ",
                    reg_btn: "Рӯйхатгирӣ",
                    hero_title: "Мошини беҳтаринро дар Тоҷикистон пайдо кунед",
                    hero_subtitle: "Ҳазорон эълонҳои санҷидашуда аз соҳибон ва автосалонҳо.",
                    filter_title: "Ҷустуҷӯи босуръат",
                    heading_latest: "Пешниҳодҳои нав",
                    cat_prefix: "Категория:",
                    brand_prefix: "Марка:",
                    search_prefix: "Натиҷаҳои ҷустуҷӯ:",
                    more_btn: "Муфассал",
                    empty_filter: "Мувофиқи дархости шумо ягон эълон ёфт нашуд",
                    footer_rights: "Ҳамаи ҳуқуқҳо ҳифз шудаанд."
                },
                ru: {
                    nav_cats: "Категории",
                    nav_sell: "Продать авто",
                    nav_fav: "Избранное",
                    search_placeholder: "Поиск по марке или модели (например: Toyota, BMW)...",
                    search_btn: "Найти авто",
                    profile_btn: "Профиль",
                    admin_btn: "Управление",
                    login_btn: "Войти",
                    reg_btn: "Регистрация",
                    hero_title: "Найдите лучший автомобиль в Таджикистане",
                    hero_subtitle: "Тысячи проверенных объявлений от частных продавцов и автосалонов.",
                    filter_title: "Быстрый подбор авто",
                    heading_latest: "Свежие предложения",
                    cat_prefix: "Категория:",
                    brand_prefix: "Марка:",
                    search_prefix: "Результаты поиска:",
                    more_btn: "Подробнее",
                    empty_filter: "По вашему запросу объявлений не найдено.",
                    footer_rights: "Все права защищены."
                },
                en: {
                    nav_cats: "Categories",
                    nav_sell: "Sell Car",
                    nav_fav: "Favorites",
                    search_placeholder: "Search by make or model (e.g. Toyota, BMW)...",
                    search_btn: "Find Cars",
                    profile_btn: "Profile",
                    admin_btn: "Admin Panel",
                    login_btn: "Sign In",
                    reg_btn: "Register",
                    hero_title: "Find Your Perfect Car in Tajikistan",
                    hero_subtitle: "Thousands of verified listings from owners and dealerships.",
                    filter_title: "Quick Search",
                    heading_latest: "Latest Offers",
                    cat_prefix: "Category:",
                    brand_prefix: "Make:",
                    search_prefix: "Search Results:",
                    more_btn: "View Details",
                    empty_filter: "No listings found matching your search.",
                    footer_rights: "All rights reserved."
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

                document.querySelectorAll('[data-i18n-placeholder]').forEach(element => {
                    const key = element.getAttribute('data-i18n-placeholder');
                    if (translations[lang] && translations[lang][key]) {
                        element.placeholder = translations[lang][key];
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