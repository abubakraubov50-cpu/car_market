<?php
require_once("../config/db.php");
require_once("../config/helpers.php");  

check_admin();

// Проверяем, передан ли ID объявления
if (!isset($_GET["id"]) || empty($_GET["id"])){
    echo "ID объявления не найден";
    exit();
}

$id = mysqli_real_escape_string($conn, $_GET["id"]);

// Получаем объявление по ID
$query = "SELECT * FROM poster WHERE p_id = '$id'";
$row = mysqli_query($conn, $query);

if(mysqli_num_rows($row) == 0) {
    echo "Объявление не найдено!";
    exit();
}

$poster = mysqli_fetch_assoc($row);

// Получаем списки для выпадающих списков (бренды, модели, пользователи)
$brands_res = mysqli_query($conn, "SELECT * FROM brands");
$models_res = mysqli_query($conn, "SELECT * FROM models");
$users_res  = mysqli_query($conn, "SELECT id, username FROM users");

// Обработка отправки формы
if(isset($_POST['update'])){   
    $p_title       = mysqli_real_escape_string($conn, $_POST["p_title"]);  
    $p_description = mysqli_real_escape_string($conn, $_POST["p_description"]);  
    $p_price       = (float)$_POST["p_price"];  
    $br_id         = (int)$_POST["br_id"];  
    $m_id          = (int)$_POST["m_id"];  
    $u_id          = (int)$_POST["u_id"];  

    $update_query = "UPDATE poster SET 
                        p_title = '$p_title', 
                        p_description = '$p_description', 
                        p_price = '$p_price', 
                        br_id = '$br_id', 
                        m_id = '$m_id', 
                        u_id = '$u_id' 
                     WHERE p_id = '$id'";
                     
    $result = mysqli_query($conn, $update_query);

    if($result){
       ?>
       <script>
           alert("Объявление успешно изменено!");
           window.location.href = "posters.php";
       </script>
       <?
       exit();
    } else {
       echo "Ошибка при обновлении объявления";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование объявления — Панель управления</title>

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
            margin: 40px 0 30px;
        }

        .page-header i {
            font-size: 40px;
            color: #0dcaf0;
        }

        .page-header h1 {
            font-weight: bold;
            margin-top: 15px;
            font-size: 26px;
        }

        /* Карточка формы */
        .form-card {
            background: #2b3035;
            border: 1px solid #41464b;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            max-width: 650px;
            margin: 0 auto;
        }

        .form-label {
            color: #adb5bd;
            font-weight: 500;
        }

        .form-control, .form-select {
            background-color: #343a40;
            border-color: #41464b;
            color: #f8f9fa;
            padding: 12px 15px;
            border-radius: 10px;
        }

        .form-control:focus, .form-select:focus {
            background-color: #343a40;
            border-color: #0dcaf0;
            color: #f8f9fa;
            box-shadow: 0 0 0 0.25rem rgba(13, 202, 240, 0.25);
        }

        /* Стилизация выпадающего списка опций для темной темы */
        .form-select option {
            background-color: #2b3035;
            color: #f8f9fa;
        }

        /* Кнопка назад */
        .back-link {
            display: inline-block;
            text-decoration: none;
            color: #adb5bd;
            margin-top: 30px;
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
            <i class="bi bi-pencil-square"></i>
            <h1>Редактирование объявления</h1>
            <p class="text-secondary">Измените данные и параметры выбранного объявления</p>
        </div>

        <!-- Форма -->
        <div class="form-card">
            <form action="" method="post">
                <div class="mb-3">
                    <label class="form-label">Заголовок:</label>
                    <input type="text" class="form-control" name="p_title" required value="<?= htmlspecialchars($poster['p_title']) ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Описание:</label>
                    <textarea class="form-control" name="p_description" rows="4" required><?= htmlspecialchars($poster['p_description']) ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Цена ($):</label>
                    <input type="number" step="0.01" class="form-control" name="p_price" required value="<?= $poster['p_price'] ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Марка автомобиля:</label>
                    <select name="br_id" class="form-select" required>
                        <?php while($brand = mysqli_fetch_assoc($brands_res)): ?>
                            <option value="<?= $brand['br_id'] ?>" <?= ($brand['br_id'] == $poster['br_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($brand['br_name']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Модель автомобиля:</label>
                    <select name="m_id" class="form-select" required>
                        <?php while($model = mysqli_fetch_assoc($models_res)): ?>
                            <option value="<?= $model['m_id'] ?>" <?= ($model['m_id'] == $poster['m_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($model['m_name']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label">Продавец (Пользователь):</label>
                    <select name="u_id" class="form-select" required>
                        <?php while($user = mysqli_fetch_assoc($users_res)): ?>
                            <option value="<?= $user['id'] ?>" <?= ($user['id'] == $poster['u_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($user['username']) ?> (ID: <?= $user['id'] ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" name="update" class="btn btn-info flex-grow-1 py-2 fw-bold text-dark">Сохранить изменения</button>
                    <a href="posters.php" class="btn btn-outline-secondary flex-grow-1 py-2">Отмена</a>
                </div>
            </form>
        </div>

        <!-- Назад внизу -->
        <div class="text-center">
            <a href="posters.php" class="back-link">
                <i class="bi bi-arrow-left"></i>
                Вернуться к списку объявлений
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