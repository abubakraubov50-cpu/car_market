<?php
require_once("../config/db.php");
require_once("../config/helpers.php");  

check_admin();

if (!isset($_GET["id"]) || empty($_GET["id"])){
    echo "ID модели не найден";
    exit();
}

$id = mysqli_real_escape_string($conn, $_GET["id"]);

// Получаем модель по ID
$query = "SELECT * FROM models WHERE m_id = '$id'";
$row = mysqli_query($conn, $query);

if(mysqli_num_rows($row) == 0) {
    echo "Модель не найдена!";
    exit();
}

$model = mysqli_fetch_assoc($row);

// Получаем список всех марок для выпадающего списка
$brands_res = mysqli_query($conn, "SELECT * FROM brands");

if(isset($_POST['update'])){   
    $m_name = mysqli_real_escape_string($conn, $_POST["m_name"]);  
    $br_id  = (int)$_POST["br_id"];  

    $update_query = "UPDATE models SET m_name = '$m_name', br_id = '$br_id' WHERE m_id = '$id'";
    $result = mysqli_query($conn, $update_query);

    if($result){
       ?>
       <script>
           alert("Модель успешно изменена!");
           window.location.href = "models.php";
       </script>
       <?php
       exit();
    } else {
       echo "Ошибка при обновлении";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование модели — Панель управления</title>

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
            max-width: 500px;
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
            <h1>Редактирование модели авто</h1>
            <p class="text-secondary">Измените марку или название модели автомобиля</p>
        </div>

        <!-- Форма -->
        <div class="form-card">
            <form action="" method="post">
                <div class="mb-3">
                    <label class="form-label">Марка автомобиля:</label>
                    <select name="br_id" class="form-select" required>
                        <?php while($brand = mysqli_fetch_assoc($brands_res)): ?>
                            <option value="<?= $brand['br_id'] ?>" <?= ($brand['br_id'] == $model['br_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($brand['br_name']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label">Название модели:</label>
                    <input type="text" class="form-control" name="m_name" required value="<?= htmlspecialchars($model['m_name']) ?>">
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" name="update" class="btn btn-info flex-grow-1 py-2 fw-bold text-dark">Сохранить</button>
                    <a href="models.php" class="btn btn-outline-secondary flex-grow-1 py-2">Отмена</a>
                </div>
            </form>
        </div>

        <!-- Назад внизу -->
        <div class="text-center">
            <a href="models.php" class="back-link">
                <i class="bi bi-arrow-left"></i>
                Вернуться к списку моделей
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