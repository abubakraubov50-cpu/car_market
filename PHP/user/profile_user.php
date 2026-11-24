<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once('../config/db.php');
require_once('../config/helpers.php');

$seller_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($seller_id === 0) {
    header("Location: ../selectposters.php");
    exit();
}

// Получаем данные продавца
$user_q = mysqli_query($conn, "SELECT id, username, email, phone FROM users WHERE id = '$seller_id'");
$seller = mysqli_fetch_assoc($user_q);

if (!$seller) {
    echo "Пользователь не найден.";
    exit();
}

// Получаем все объявления этого продавца
$posters_q = mysqli_query($conn, "
    SELECT poster.*, brands.br_name, models.m_name 
    FROM poster 
    JOIN brands ON poster.br_id = brands.br_id 
    JOIN models ON poster.m_id = models.m_id 
    WHERE poster.u_id = '$seller_id' 
    ORDER BY poster.p_id DESC
");
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль продавца: <?= htmlspecialchars($seller['username']) ?> — ASO.tj</title>
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

        .profile-header-card {
            background: #ffffff;
            border: 1px solid var(--card-border);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
        }

        .car-card {
            background: #ffffff;
            border: 1px solid var(--card-border);
            border-radius: 16px;
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .car-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        }

        .car-img {
            height: 200px;
            object-fit: cover;
            width: 100%;
        }
    </style>
</head>
<body>

    <!-- Навигация -->
    <div class="container py-4">
        <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-medium mb-4">
            <i class="bi bi-arrow-left me-1"></i> Назад
        </a>

        <!-- Информация о продавце -->
        <div class="profile-header-card mb-4 d-flex align-items-center gap-4 flex-wrap">
            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold display-5" style="width: 80px; height: 80px;">
                <?= mb_substr(htmlspecialchars($seller['username']), 0, 1) ?>
            </div>
            <div>
                <h3 class="fw-bold text-dark mb-1"><?= htmlspecialchars($seller['username']) ?></h3>
                <p class="text-muted mb-2"><i class="bi bi-shield-check text-success me-1"></i> Проверенный продавец на ASO.tj</p>
                <div class="d-flex gap-3 text-secondary small">
                    <?php if (!empty($seller['phone'])): ?>
                        <span><i class="bi bi-telephone me-1"></i> <?= htmlspecialchars($seller['phone']) ?></span>
                    <?php endif; ?>
                    <?php if (!empty($seller['email'])): ?>
                        <span><i class="bi bi-envelope me-1"></i> <?= htmlspecialchars($seller['email']) ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Список объявлений продавца -->
        <h4 class="fw-bold mb-4 text-dark">Объявления пользователя (<?= mysqli_num_rows($posters_q) ?>)</h4>

        <div class="row g-4">
            <?php if (mysqli_num_rows($posters_q) > 0): ?>
                <?php while ($car = mysqli_fetch_assoc($posters_q)): ?>
                    <?php
                    // Получаем первое фото для карточки
                    $p_id = $car['p_id'];
                    $ph_q = mysqli_query($conn, "SELECT path FROM photo WHERE post_id = '$p_id' LIMIT 1");
                    $ph_row = mysqli_fetch_assoc($ph_q);
                    $photo = $ph_row ? $ph_row['path'] : 'default.jpg';
                    ?>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="car-card h-100 d-flex flex-column">
                            <img src="../PHOTO/<?= htmlspecialchars($photo) ?>" class="car-img" alt="Авто">
                            <div class="p-3 d-flex flex-column flex-grow-1">
                                <h5 class="fw-bold text-dark mb-1">
                                    <?= htmlspecialchars($car['br_name']) ?> <?= htmlspecialchars($car['m_name']) ?>
                                </h5>
                                <p class="text-muted small mb-2"><?= htmlspecialchars($car['p_title']) ?></p>
                                <div class="text-primary fw-extrabold fs-5 mb-3">$<?= htmlspecialchars($car['p_price']) ?></div>
                                
                                <div class="mt-auto pt-2 border-top d-flex justify-content-between align-items-center">
                                    <small class="text-muted"><?= !empty($car['p_year']) ? $car['p_year'].' г.' : '' ?></small>
                                    <a href="../car.php?id=<?= $car['p_id'] ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-bold">Подробнее</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-light text-center py-5 border rounded-4 text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-2 text-primary"></i>
                        У этого пользователя пока нет активных объявлений.
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>