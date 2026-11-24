<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once("config/db.php");
require_once("config/helpers.php");

// Если пользователь не вошел, перенаправляем на вход
if (!is_logged_in()) {
    header("Location: auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'selectposters.php';

if ($post_id > 0) {
    // Проверяем, есть ли уже в избранном
    $check = mysqli_query($conn, "SELECT * FROM favorites WHERE user_id = $user_id AND post_id = $post_id");
    
    if (mysqli_num_rows($check) > 0) {
        // Если уже есть — удаляем (убираем из избранного)
        mysqli_query($conn, "DELETE FROM favorites WHERE user_id = '$user_id' AND post_id = '$post_id'");
    } else {
        // Если нет — добавляем
        mysqli_query($conn, "INSERT INTO favorites (user_id, post_id) VALUES ('$user_id', '$post_id')");
    }
}

// Возвращаем пользователя туда, откуда он пришел
header("Location: " . $redirect);
exit();