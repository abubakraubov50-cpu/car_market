<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once('config/db.php');
require_once('config/helpers.php'); 

$cat_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($cat_id === 0) {
    header("Location: selectposters.php");
    exit();
}

// 1. Получаем данные текущей категории
$cat_q = mysqli_query($conn, "SELECT * FROM categories WHERE cat_id = '$cat_id'");
$cat_data = mysqli_fetch_assoc($cat_q);

if (!$cat_data) {
    header("Location: selectposters.php");
    exit();
}

// 2. Общее количество объявлений в категории
$total_posters_q = mysqli_query($conn, "
    SELECT COUNT(poster.p_id) as total 
    FROM poster 
    JOIN models ON poster.m_id = models.m_id 
    WHERE models.cat_id = '$cat_id'
");
$total_posters = mysqli_fetch_assoc($total_posters_q)['total'];

// 3. Список марок для категории
$brands_stat_q = mysqli_query($conn, "
    SELECT brands.br_id, brands.br_name, COUNT(DISTINCT poster.p_id) as count_posters 
    FROM brands 
    JOIN models ON brands.br_id = models.br_id 
    LEFT JOIN poster ON poster.m_id = models.m_id 
    WHERE models.cat_id = '$cat_id' 
    GROUP BY brands.br_id, brands.br_name 
    ORDER BY count_posters DESC, brands.br_name ASC
");

$all_brands = [];
if ($brands_stat_q && mysqli_num_rows($brands_stat_q) > 0) {
    while ($b_stat = mysqli_fetch_assoc($brands_stat_q)) {
        $all_brands[] = $b_stat;
    }
}

$limit = 8;
$total_brands = count($all_brands);
?>
<!doctype html>
<html lang="ru">
    <head>
        <title><?= htmlspecialchars($cat_data['cat_name']) ?> — ASO.tj</title>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <!-- Bootstrap 5 CSS -->
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

            .category-hero {
                background: linear-gradient(135deg, #0f172a, #1e3a8a);
                color: #ffffff;
                padding: 40px 0;
                border-radius: 0 0 24px 24px;
            }

            .brands-card {
                background: #ffffff;
                border: 1px solid var(--card-border);
                border-radius: 20px;
                padding: 24px;
                box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
            }

            .brand-link-item {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 8px 12px;
                background: #f1f5f9;
                border-radius: 10px;
                color: #0f172a;
                font-weight: 600;
                text-decoration: none;
                transition: all 0.2s ease;
            }

            .brand-link-item:hover {
                background: #e2e8f0;
                color: var(--accent-color);
                transform: translateY(-2px);
            }

            .brand-badge-count {
                font-size: 0.8rem;
                background: #ffffff;
                color: #64748b;
                padding: 2px 8px;
                border-radius: 20px;
            }

            .hidden-brand {
                display: none !important;
            }

            .filter-sidebar {
                background: #ffffff;
                border: 1px solid var(--card-border);
                border-radius: 20px;
                padding: 24px;
                box-shadow: 0 4px 16px rgba(0, 0, 0, 0.02);
            }

            .form-label-sm-custom {
                font-size: 0.85rem;
                font-weight: 700;
                color: #475569;
                margin-bottom: 6px;
            }

            .form-control-custom {
                border: 1px solid #cbd5e1;
                border-radius: 12px;
                padding: 9px 12px;
                font-size: 0.88rem;
                background-color: #fff;
            }

            .form-control-custom:focus {
                border-color: var(--accent-color);
                box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
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

        <main>
            <!-- БАННЕР КАТЕГОРИИ -->
            <section class="category-hero mb-4">
                <div class="container">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div>
                            <h1 class="fw-extrabold mb-1 display-6"><?= htmlspecialchars($cat_data['cat_name']) ?> в Таджикистане</h1>
                            <p class="mb-0 opacity-75">Всего объявлений в этой категории: <strong><?= number_format($total_posters, 0, '', ' ') ?></strong></p>
                        </div>
                        <a href="selectposters.php" class="btn btn-outline-light btn-sm rounded-pill px-3">
                            <i class="bi bi-arrow-left me-1"></i> В главный
                        </a>
                    </div>
                </div>
            </section>

            <div class="container">
                
                <!-- Блок со списком марок -->
                <div class="brands-card mb-4">
                    <h5 class="fw-bold mb-3 text-dark d-flex align-items-center gap-2">
                        <i class="bi bi-grid text-primary"></i> Популярные марки
                    </h5>

                    <div class="row g-2 fs-6" id="brandsContainer">
                        <?php if ($total_brands > 0): ?>
                            <?php foreach ($all_brands as $index => $b_stat): ?>
                                <div class="col-6 col-sm-4 col-md-3 brand-item <?= $index >= $limit ? 'hidden-brand' : '' ?>">
                                    <a href="selectposters.php?brand=<?= urlencode($b_stat['br_name']) ?>" class="brand-link-item">
                                        <span><?= htmlspecialchars($b_stat['br_name']) ?></span>
                                        <span class="brand-badge-count"><?= number_format($b_stat['count_posters'], 0, '', ' ') ?></span>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted small mb-0">Марок в этой категории пока нет</p>
                        <?php endif; ?>
                    </div>

                    <?php if ($total_brands > $limit): ?>
                        <div class="mt-3 pt-3 border-top text-center">
                            <button type="button" id="toggleBrandsBtn" class="btn btn-outline-primary rounded-pill px-4 btn-sm fw-bold">
                                Показать ещё (<?= $total_brands - $limit ?>) <i class="bi bi-chevron-down ms-1" id="btnIcon"></i>
                            </button>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- ОСНОВНАЯ СЕТКА: Фильтры слева, Объявления справа -->
                <div class="row g-4">
                    
                    <!-- ЛЕВАЯ КОЛОНКА: Фильтры -->
                    <div class="col-lg-3">
                        <div class="filter-sidebar">
                            <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                                <i class="bi bi-funnel text-primary"></i> Фильтры
                            </h5>

                            <form action="category.php" method="get">
                                <input type="hidden" name="id" value="<?= $cat_id ?>">

                                <!-- Сортировка -->
                                <div class="mb-3">
                                    <label class="form-label-sm-custom">Сортировка</label>
                                    <select name="sort" class="form-control form-control-custom">
                                        <option value="">По умолчанию</option>
                                        <option value="new" <?= (isset($_GET['sort']) && $_GET['sort']=='new')?'selected':'' ?>>Сначала новые</option>
                                        <option value="cheap" <?= (isset($_GET['sort']) && $_GET['sort']=='cheap')?'selected':'' ?>>Сначала дешевые</option>
                                        <option value="expensive" <?= (isset($_GET['sort']) && $_GET['sort']=='expensive')?'selected':'' ?>>Сначала дорогие</option>
                                    </select>
                                </div>

                                <!-- Цена -->
                                <div class="mb-3">
                                    <label class="form-label-sm-custom">Цена, $</label>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <input type="number" name="price_min" class="form-control form-control-custom" placeholder="От" value="<?= isset($_GET['price_min']) && $_GET['price_min'] !== '' ? (int)$_GET['price_min'] : '' ?>">
                                        </div>
                                        <div class="col-6">
                                            <input type="number" name="price_max" class="form-control form-control-custom" placeholder="До" value="<?= isset($_GET['price_max']) && $_GET['price_max'] !== '' ? (int)$_GET['price_max'] : '' ?>">
                                        </div>
                                    </div>
                                </div>

                                <!-- Год выпуска -->
                                <div class="mb-4">
                                    <label class="form-label-sm-custom">Год выпуска</label>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <input type="number" name="year_min" class="form-control form-control-custom" placeholder="От" value="<?= isset($_GET['year_min']) && $_GET['year_min'] !== '' ? (int)$_GET['year_min'] : '' ?>">
                                        </div>
                                        <div class="col-6">
                                            <input type="number" name="year_max" class="form-control form-control-custom" placeholder="Год до" value="<?= isset($_GET['year_max']) && $_GET['year_max'] !== '' ? (int)$_GET['year_max'] : '' ?>">
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold py-2 shadow-sm">
                                    Применить фильтры
                                </button>

                                <?php if (!empty($_GET['price_min']) || !empty($_GET['price_max']) || !empty($_GET['year_min']) || !empty($_GET['year_max']) || !empty($_GET['sort'])): ?>
                                    <div class="mt-3 text-center">
                                        <a href="category.php?id=<?= $cat_id ?>" class="text-danger small text-decoration-none fw-semibold">
                                            <i class="bi bi-x-circle me-1"></i> Сбросить
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>

                    <!-- ПРАВАЯ КОЛОНКА: Объявления с учетом фильтров -->
                    <div class="col-lg-9">
                        <h4 class="mb-4 fw-bold text-dark">Все объявления в категории «<?= htmlspecialchars($cat_data['cat_name']) ?>»</h4>
                        
                        <?php
                        // Формируем условия для фильтрации
                        $where_clauses = ["models.cat_id = '$cat_id'"];

                        if (isset($_GET["price_min"]) && is_numeric($_GET["price_min"]) && $_GET["price_min"] !== '') {
                            $p_min = (float)$_GET["price_min"];
                            $where_clauses[] = "poster.p_price >= $p_min";
                        }
                        if (isset($_GET["price_max"]) && is_numeric($_GET["price_max"]) && $_GET["price_max"] !== '') {
                            $p_max = (float)$_GET["price_max"];
                            $where_clauses[] = "poster.p_price <= $p_max";
                        }
                        if (isset($_GET["year_min"]) && is_numeric($_GET["year_min"]) && $_GET["year_min"] !== '') {
                            $y_min = (int)$_GET["year_min"];
                            $where_clauses[] = "poster.p_year >= $y_min";
                        }
                        if (isset($_GET["year_max"]) && is_numeric($_GET["year_max"]) && $_GET["year_max"] !== '') {
                            $y_max = (int)$_GET["year_max"];
                            $where_clauses[] = "poster.p_year <= $y_max";
                        }

                        $order_by = "ORDER BY poster.p_id DESC";
                        if (isset($_GET["sort"])) {
                            if ($_GET["sort"] === 'new') $order_by = "ORDER BY poster.p_id DESC";
                            elseif ($_GET["sort"] === 'cheap') $order_by = "ORDER BY poster.p_price ASC";
                            elseif ($_GET["sort"] === 'expensive') $order_by = "ORDER BY poster.p_price DESC";
                        }

                        $where_sql = implode(" AND ", $where_clauses);
                        $posters_q = mysqli_query($conn, "
                            SELECT poster.*, brands.br_name, models.m_name 
                            FROM poster 
                            JOIN brands ON poster.br_id = brands.br_id 
                            JOIN models ON poster.m_id = models.m_id 
                            WHERE $where_sql 
                            $order_by
                        ");
                        ?>

                        <div class="row g-4">
                            <?php if ($posters_q && mysqli_num_rows($posters_q) > 0): ?>
                                <?php while ($poster = mysqli_fetch_assoc($posters_q)): ?>
                                    <div class="col-12 col-md-6">
                                        <div class="car-item-card h-100 d-flex flex-column">
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
                                                $photos[] = 'default.jpg';
                                            }
                                            ?>

                                            <!-- Стандартная Bootstrap Carousel (как в профиле и на главной) -->
                                            <div id="carCarousel_<?= $post_id ?>" class="carousel slide" data-bs-ride="carousel">
                                                <div class="carousel-inner">
                                                    <?php foreach ($photos as $index => $photo_path): ?>
                                                        <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                                                            <img src="../PHOTO/<?= htmlspecialchars($photo_path) ?>" 
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
                                        В этой категории по вашему запросу объявлений не найдено.
                                    </div>
                                </div>
                            <?php endif; ?>
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

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const toggleBtn = document.getElementById('toggleBrandsBtn');
                if (toggleBtn) {
                    const hiddenItems = document.querySelectorAll('.brand-item.hidden-brand');
                    const remainingCount = <?= $total_brands - $limit ?>;
                    let isExpanded = false;

                    toggleBtn.addEventListener('click', function () {
                        isExpanded = !isExpanded;

                        hiddenItems.forEach(item => {
                            if (isExpanded) {
                                item.classList.remove('hidden-brand');
                            } else {
                                item.classList.add('hidden-brand');
                            }
                        });

                        if (isExpanded) {
                            toggleBtn.innerHTML = 'Свернуть <i class="bi bi-chevron-up ms-1"></i>';
                        } else {
                            toggleBtn.innerHTML = 'Показать ещё (' + remainingCount + ') <i class="bi bi-chevron-down ms-1"></i>';
                        }
                    });
                }
            });

            const translations = {
                tj: {
                    nav_cats: "Категорияҳо",
                    nav_sell: "Фурӯхтани мошин",
                    profile_btn: "Профил",
                    admin_btn: "Идоракунӣ",
                    login_btn: "Воридшавӣ",
                    reg_btn: "Рӯйхатгирӣ",
                    more_btn: "Муфассал",
                    empty_filter: "Дар ин категория ягон эълон нест"
                },
                ru: {
                    nav_cats: "Категории",
                    nav_sell: "Продать авто",
                    profile_btn: "Профиль",
                    admin_btn: "Управление",
                    login_btn: "Войти",
                    reg_btn: "Регистрация",
                    more_btn: "Подробнее",
                    empty_filter: "В этой категории пока нет объявлений."
                },
                en: {
                    nav_cats: "Categories",
                    nav_sell: "Sell Car",
                    profile_btn: "Profile",
                    admin_btn: "Admin Panel",
                    login_btn: "Sign In",
                    reg_btn: "Register",
                    more_btn: "View Details",
                    empty_filter: "No listings available in this category yet."
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