
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AutoShopOnline — Панель управления</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- Иконки -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

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
            margin: 55px 0 40px;
        }

        .page-header i {
            font-size: 48px;
            color: #0dcaf0;
        }

        .page-header h1 {
            font-weight: bold;
            margin-top: 15px;
        }

        .page-header p {
            color: #adb5bd;
        }

        /* Карточки */
        .menu-card {
            display: block;
            text-decoration: none;
            color: #f8f9fa;
            background: #2b3035;
            border: 1px solid #41464b;
            border-radius: 16px;
            padding: 30px 20px;
            height: 100%;
            transition: 0.3s;
        }

        .menu-card:hover {
            transform: translateY(-7px);
            background: #343a40;
            border-color: #0dcaf0;
            box-shadow: 0 10px 25px rgba(13, 202, 240, 0.12);
            color: white;
        }

        .menu-icon {
            font-size: 38px;
            color: #0dcaf0;
            margin-bottom: 18px;
        }

        .menu-card h5 {
            font-weight: bold;
            margin-bottom: 10px;
        }

        .menu-card p {
            color: #adb5bd;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .card-link {
            color: #0dcaf0;
            font-size: 14px;
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
            <i class="bi bi-speedometer2"></i>
            <h1>Панель управления</h1>
            <p>Управляйте объявлениями и данными вашего сайта</p>
        </div>

        <!-- Меню -->
        <div class="row g-4">

            <div class="col-12 col-md-6 col-lg-3">
                <a href="posters.php" class="menu-card">
                    <div class="menu-icon">
                        <i class="bi bi-megaphone"></i>
                    </div>
                    <h5>Объявления</h5>
                    <p>Добавление, изменение и удаление объявлений.</p>
                    <span class="card-link">
                        Перейти <i class="bi bi-arrow-right"></i>
                    </span>
                </a>
            </div>

            <div class="col-12 col-md-6 col-lg-3">
                <a href="users.php" class="menu-card">
                    <div class="menu-icon">
                        <i class="bi bi-people"></i>
                    </div>
                    <h5>Пользователи</h5>
                    <p>Просмотр и управление пользователями сайта.</p>
                    <span class="card-link">
                        Перейти <i class="bi bi-arrow-right"></i>
                    </span>
                </a>
            </div>

            <div class="col-12 col-md-6 col-lg-3">
                <a href="brands.php" class="menu-card">
                    <div class="menu-icon">
                        <i class="bi bi-car-front"></i>
                    </div>
                    <h5>Марки машин</h5>
                    <p>Управление марками автомобилей.</p>
                    <span class="card-link">
                        Перейти <i class="bi bi-arrow-right"></i>
                    </span>
                </a>
            </div>

            <div class="col-12 col-md-6 col-lg-3">
                <a href="models.php" class="menu-card">
                    <div class="menu-icon">
                        <i class="bi bi-gear"></i>
                    </div>
                    <h5>Модели машин</h5>
                    <p>Управление моделями автомобилей.</p>
                    <span class="card-link">
                        Перейти <i class="bi bi-arrow-right"></i>
                    </span>
                </a>
            </div>

        </div>

        <!-- Назад -->
        <div class="text-center">
            <a href="../selectposters.php" class="back-link">
                <i class="bi bi-arrow-left"></i>
                Вернуться на главный экран
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
