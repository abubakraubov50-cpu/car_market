<?php
// Настраиваем и запускаем сессию только если она еще не активна
if (session_status() === PHP_SESSION_NONE) {
    // Устанавливаем lifetime в 0, чтобы сессия жила до закрытия браузера или выхода из аккаунта
    $lifetime = 0;

    // Настраиваем куки сессии
    session_set_cookie_params([
        'lifetime' => $lifetime,
        'path'     => '/',
        'secure'   => isset($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Lax'
    ]);

    // Запускаем сессию
    session_start();
}

/**
 * 1. Защита от XSS (очистка ввода)
 */
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * 2. Проверка авторизации
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * 3. Принудительная защита защищенных страниц
 */
function check_auth() {
    if (!is_logged_in()) {
        header("Location: https://carmarket.local/PHP/auth/login.php");
        exit();
    }
}

function check_admin() {
    check_auth();
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        header("Location: http://localhost/PX/php/selectposters.php");
        exit();
    }
}
?>