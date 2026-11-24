<?php
require_once("../config/helpers.php");

// Если получено подтверждение через GET-параметр — уничтожаем сессию и куки
if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
    $_SESSION = array();

    // Удаляем куки сессии на стороне браузера
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    session_destroy();
    
    echo '<script>window.location.href = "../selectposters.php";</script>';
    exit();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Выход из системы</title>
</head>
<body>
    <script>
        if (confirm("Вы уверены, что хотите выйти из аккаунта?")) {
            window.location.href = "logout.php?confirm=yes";
        } else {
            window.history.back();
        }
    </script>
</body>
</html>