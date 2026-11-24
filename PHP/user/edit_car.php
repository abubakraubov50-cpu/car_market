<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once('../config/db.php');
require_once('../config/helpers.php');

check_auth();

$user_id = $_SESSION['user_id'];
$car_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($car_id === 0) {
    header("Location: profile.php");
    exit();
}

// Проверяем, принадлежит ли объявление текущему пользователю, и получаем данные
$car_q = mysqli_query($conn, "SELECT * FROM poster WHERE p_id = '$car_id' AND u_id = '$user_id'");
$car = mysqli_fetch_assoc($car_q);

if (!$car) {
    header("Location: profile.php");
    exit();
}

$error = '';

if (isset($_POST['edit_car'])) {
    $title       = sanitize($_POST['p_title']);
    $description = sanitize($_POST['p_description']);
    $price       = sanitize($_POST['p_price']);
    $year        = (int)$_POST['p_year'];

    if (empty($title) || empty($price) || empty($year)) {
        $error = "Пожалуйста, заполните все обязательные поля!";
    } else {
        $update_q = "UPDATE poster SET p_title = '$title', p_description = '$description', p_price = '$price', p_year = '$year' WHERE p_id = '$car_id' AND u_id = '$user_id'";
        
        if (mysqli_query($conn, $update_q)) {
            ?>
            <script>
                alert("Объявление успешно обновлено!");
                window.location.href = "profile.php";
            </script>
            <?php
            exit();
        } else {
            $error = "Ошибка при обновлении: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактировать объявление — ASO.tj</title>
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
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .edit-card {
            background: #ffffff;
            border: 1px solid var(--card-border);
            border-radius: 20px;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
        }

        .logo-brand {
            font-family: 'Arial Black', sans-serif;
            font-size: 24px;
            font-weight: 900;
            color: var(--primary-color);
            text-decoration: none;
        }

        .logo-brand span {
            color: var(--accent-color);
        }

        .form-control {
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            padding: 10px 14px;
        }

        .form-control:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }
    </style>
</head>
<body>

    <div class="position-absolute top-0 start-0 end-0 p-4 d-flex justify-content-between align-items-center container">
        <a href="profile.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-medium">
            <i class="bi bi-arrow-left me-1"></i> Назад в профиль
        </a>
    </div>

    <div class="edit-card p-4 p-md-5 shadow-sm my-5">
        <div class="text-center mb-4">
            <a href="../selectposters.php" class="logo-brand mb-2 d-inline-block">ASO<span>.tj</span></a>
            <h3 class="fw-bold text-dark">Редактирование объявления</h3>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger py-2 small mb-4 rounded-3"><?= $error ?></div>
        <?php endif; ?>

        <form action="edit_car.php?id=<?= $car_id ?>" method="post">
            <div class="mb-3">
                <label class="form-label fw-semibold small">Заголовок объявления:</label>
                <input type="text" name="p_title" class="form-control" value="<?= htmlspecialchars($car['p_title']) ?>" required>
            </div>

            <div class="row mb-3">
                <div class="col-6">
                    <label class="form-label fw-semibold small">Цена ($):</label>
                    <input type="number" name="p_price" class="form-control" value="<?= htmlspecialchars($car['p_price']) ?>" required>
                </div>
                <div class="col-6">
                    <label class="form-label fw-semibold small">Год выпуска:</label>
                    <input type="number" name="p_year" class="form-control" value="<?= htmlspecialchars($car['p_year']) ?>" min="1900" max="<?= date('Y') ?>" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold small">Описание:</label>
                <textarea name="p_description" class="form-control" rows="5" required><?= htmlspecialchars($car['p_description']) ?></textarea>
            </div>

            <div class="d-flex justify-content-between gap-3">
                <a href="profile.php" class="btn btn-outline-secondary w-50 rounded-pill py-2.5 fw-semibold">Отмена</a>
                <button type="submit" name="edit_car" class="btn btn-primary w-50 fw-bold rounded-pill py-2.5 shadow-sm">Сохранить</button>
            </div>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>