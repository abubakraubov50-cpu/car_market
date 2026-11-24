<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once('config/db.php');
require_once('config/helpers.php');

check_auth();

$user_id = $_SESSION['user_id'];

// Исправленный запрос: сортируем по дате создания объявления или по poster.p_id
$query = "
    SELECT poster.*, brands.br_name, models.m_name 
    FROM favorites 
    JOIN poster ON favorites.post_id = poster.p_id 
    JOIN brands ON poster.br_id = brands.br_id 
    JOIN models ON poster.m_id = models.m_id 
    WHERE favorites.user_id = '$user_id' 
    ORDER BY poster.created_at DESC
";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Избранное — ASO.tj</title>
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
                <a href="selectposters.php" class="text-decoration-none d-flex align-items-center">
                    <span class="logo-brand">ASO<span>.tj</span></span>
                </a>
                <a href="selectposters.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-semibold">
                    <i class="bi bi-house-door"></i> На главную
                </a>
            </div>

            <div class="d-flex align-items-center gap-2">
                <a href="user/profile.php" class="btn btn-outline-dark btn-sm rounded-pill px-3 fw-medium">
                    <i class="bi bi-person-circle me-1"></i> Профиль
                </a>
            </div>
        </div>
    </header>

    <main class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold text-dark m-0">
                <i class="bi bi-heart-fill text-danger me-2"></i> Избранные автомобили
            </h3>
        </div>

        <?php if ($result && mysqli_num_rows($result) > 0): ?>
            <div class="row g-4">
                <?php while ($car = mysqli_fetch_assoc($result)): ?>
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
                        <div class="car-item-card h-100 d-flex flex-column position-relative">
                            
                            <!-- Кнопка удаления из избранного (сердечко) -->
                            <a href="toggle_favorite.php?id=<?= $post_id ?>" class="btn btn-light btn-sm rounded-circle position-absolute top-0 end-0 m-3 shadow-sm d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; z-index: 10;" title="Удалить из избранного">
                                <i class="bi bi-heart-fill text-danger"></i>
                            </a>

                            <!-- Фото карусель -->
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
                                    <?= htmlspecialchars($car['br_name']) ?> <?= htmlspecialchars($car['m_name']) ?>
                                    <?php if (!empty($car['p_year'])): ?>
                                        <span class="text-muted fs-6">(<?= (int)$car['p_year'] ?> г.)</span>
                                    <?php endif; ?>
                                </h5>
                                <p class="text-muted small mb-2"><?= htmlspecialchars($car['p_title']) ?></p>
                                <div class="badge-price mb-3">$<?= htmlspecialchars($car['p_price']) ?></div>
                                
                                <div class="mt-auto pt-3 border-top">
                                    <a href="car.php?id=<?= $car['p_id'] ?>" class="btn btn-sm btn-primary rounded-pill fw-bold w-100 py-2">
                                        <i class="bi bi-eye me-1"></i> Подробнее
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-light text-center py-5 border rounded-4 text-muted">
                <i class="bi bi-heart fs-1 d-block mb-2 text-danger"></i>
                В вашем списке избранного пока ничего нет. Перейдите в каталог и добавьте понравившиеся автомобили!
                <div class="mt-3">
                    <a href="selectposters.php" class="btn btn-primary rounded-pill px-4 fw-bold">Перейти в каталог</a>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <footer>
        <div class="container text-center">
            <p class="mb-0">&copy; <?= date('Y') ?> ASO.tj — Официальный авторынок Таджикистана. Все права защищены.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>