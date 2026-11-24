<?php
require_once("../config/db.php");
require_once("../config/helpers.php");

check_admin();

$query = "SELECT * FROM brands order by br_name";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление марками — Панель управления</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Иконки -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            background: #212529;
            color: #f8f9fa;
            font-family: Arial, sans-serif;
            min-height: 100vh;
        }

        /* Верхняя панель */
        .navbar {
            background: #181b1e;
            border-bottom: 1px solid #343a40;
            padding: 18px 0;
        }

        .logo {
            font-size: 25px;
            font-weight: bold;
            color: #adb5bd;
            text-decoration: none;
        }

        .logo span {
            color: #0dcaf0;
        }

        /* Заголовок */
        .page-header {
            text-align: center;
            margin: 45px 0 35px;
        }

        .page-header i {
            font-size: 42px;
            color: #0dcaf0;
        }

        .page-header h1 {
            font-weight: bold;
            margin-top: 15px;
        }

        .page-header p {
            color: #adb5bd;
        }

        /* Таблица */
        .custom-table-container {
            background: #2b3035;
            border: 1px solid #41464b;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .table {
            color: #f8f9fa;
            margin-bottom: 0;
        }

        .table-dark {
            background-color: #343a40;
            border-color: #41464b;
        }

        .table > thead {
            border-bottom: 2px solid #41464b;
        }

        .table-striped > tbody > tr:nth-of-type(odd) {
            background-color: rgba(255, 255, 255, 0.02);
            color: #f8f9fa;
        }

        .table td, .table th {
            border-color: #343a40;
            padding: 14px 16px;
        }

        /* Кнопка назад */
        .back-link {
            display: inline-block;
            text-decoration: none;
            color: #adb5bd;
            margin-top: 35px;
            transition: 0.2s;
        }

        .back-link:hover {
            color: #0dcaf0;
        }

        /* Нижняя часть */
        footer {
            margin-top: 65px;
            padding: 22px 10px;
            text-align: center;
            color: #6c757d;
            border-top: 1px solid #343a40;
        }
    </style>
</head>

<body>

    <!-- Верхняя панель -->
    <nav class="navbar">
        <div class="container">
            <a href="../selectposters.php" class="logo">
                ASO<span>.tj</span>
            </a>

            <span class="text-secondary">
                <i class="bi bi-shield-lock"></i>
                Панель администратора
            </span>
        </div>
    </nav>

    <main class="container">

        <!-- Заголовок -->
        <div class="page-header">
            <i class="bi bi-car-front"></i>
            <h1>Марки автомобилей</h1>
            <p>Управление списком марок машин на сайте</p>
        </div>

        <!-- Панель действий (Кнопка назад в меню) -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="manage.php" class="btn btn-outline-light btn-sm rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> В меню админки
            </a>
            <!-- Если у тебя есть файл добавления марки, можно добавить кнопку сюда -->
            <!-- <a href="add_brand.php" class="btn btn-info btn-sm rounded-pill px-3"><i class="bi bi-plus-lg me-1"></i> Добавить марку</a> -->
        </div>

        <!-- Таблица с данными -->
        <div class="custom-table-container">
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 80px;">ID</th>
                            <th>Название марки</th>
                            <th class="text-end" style="width: 160px;">Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php while($brand = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><strong>#<?= $brand['br_id'] ?></strong></td>
                                    <td><?= htmlspecialchars($brand['br_name']) ?></td>
                                    <td class="text-end">
                                        <a href="upload_brand.php?id=<?= $brand['br_id'] ?>" class="btn btn-sm btn-outline-warning rounded-pill px-3 me-1">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                        <a href="delete_brand.php?id=<?= $brand['br_id'] ?>" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="return confirm('Вы действительно хотите удалить эту марку?');">
                                            <i class="bi bi-trash"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center text-secondary py-4">Марки автомобилей не найдены.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Назад внизу -->
        <div class="text-center">
            <a href="manage.php" class="back-link">
                <i class="bi bi-arrow-left"></i>
                Вернуться в панель управления
            </a>
        </div>

    </main>

    <footer>
        &copy; 2026 AutoShopOnline — ASO.tj
        <br>
        <small>Панель управления сайтом</small>
    </footer>

</body>
</html>