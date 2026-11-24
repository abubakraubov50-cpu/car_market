<?php
require_once("../config/db.php");
require_once("../config/helpers.php");  

check_admin(); // Проверка прав администратора

if (!isset($_GET["id"]) || empty($_GET["id"])){
    echo "ID пользователя не найден";
    exit();
}

$id = mysqli_real_escape_string($conn, $_GET["id"]);

$query = "SELECT * FROM users WHERE id = '$id'";
$row = mysqli_query($conn, $query);

if(mysqli_num_rows($row) == 0) {
    echo "Пользователь с таким ID не найден!";
    exit();
}

$users = mysqli_fetch_assoc($row);

if(isset($_POST['update'])){
    $id       = mysqli_real_escape_string($conn, $_POST["id"]); 
    $username = mysqli_real_escape_string($conn, $_POST["username"]);  
    $phone    = mysqli_real_escape_string($conn, $_POST["phone"]);  
    $email    = mysqli_real_escape_string($conn, $_POST["email"]);  
    $role     = mysqli_real_escape_string($conn, $_POST["role"]);  

    $query = "UPDATE users SET 
                username = '$username',
                phone = '$phone',
                email = '$email',
                `role` = '$role'
              WHERE id = '$id'";

    $result = mysqli_query($conn, $query);

    if($result){
       ?>
       <script>
           alert("Данные пользователя обновлены!");
           window.location.href = "users.php";
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
    <title>Редактирование пользователя — Панель управления</title>

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
            max-width: 550px;
            margin: 0 auto;
        }

        .form-label {
            color: #adb5bd;
            font-weight: 500;
        }

        .form-control {
            background-color: #343a40;
            border-color: #41464b;
            color: #f8f9fa;
            padding: 12px 15px;
            border-radius: 10px;
        }

        .form-control:focus {
            background-color: #343a40;
            border-color: #0dcaf0;
            color: #f8f9fa;
            box-shadow: 0 0 0 0.25rem rgba(13, 202, 240, 0.25);
        }

        .form-check-input {
            background-color: #343a40;
            border-color: #41464b;
        }

        .form-check-input:checked {
            background-color: #0dcaf0;
            border-color: #0dcaf0;
        }

        .form-check-label {
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
            <i class="bi bi-person-gear"></i>
            <h1>Редактирование пользователя</h1>
            <p class="text-secondary">Измените персональные данные или роль пользователя</p>
        </div>

        <!-- Форма -->
        <div class="form-card">
            <form action="" method="post">
                <div class="mb-3">
                    <label class="form-label">Имя пользователя:</label>
                    <input type="text" class="form-control" maxlength="50" name="username" required value="<?= htmlspecialchars($users['username']) ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Телефон:</label>
                    <input type="tel" class="form-control" maxlength="15" name="phone" required value="<?= htmlspecialchars($users['phone']) ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Email:</label>
                    <input type="email" class="form-control" name="email" required value="<?= htmlspecialchars($users['email']) ?>">
                </div>

                <div class="mb-4">
                    <label class="form-label d-block">Роль пользователя:</label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="role" id="roleAdmin" value="admin" <?= $users['role'] === 'admin' ? 'checked' : '' ?>>
                        <label class="form-check-label" for="roleAdmin">Admin</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="role" id="roleUser" value="user" <?= $users['role'] === 'user' ? 'checked' : '' ?>>
                        <label class="form-check-label" for="roleUser">User</label>
                    </div>
                </div>

                <input type="hidden" name="id" value="<?= $users['id'] ?>">

                <div class="d-flex gap-2">
                    <button type="submit" name="update" class="btn btn-info flex-grow-1 py-2 fw-bold text-dark">Сохранить изменения</button>
                    <a href="users.php" class="btn btn-outline-secondary flex-grow-1 py-2">Отмена</a>
                </div>
            </form>
        </div>

        <!-- Назад внизу -->
        <div class="text-center">
            <a href="users.php" class="back-link">
                <i class="bi bi-arrow-left"></i>
                Вернуться к списку пользователей
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