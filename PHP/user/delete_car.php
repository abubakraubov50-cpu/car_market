<?php
session_start();
require_once("../config/db.php");
require_once("../config/helpers.php");

// Проверяем, авторизован ли пользователь
if (!is_logged_in()) {
    header("Location: ../auth/login.php");
    exit();
}

if (isset($_GET["id"]) && !empty($_GET["id"])) {
    $poster_id = mysqli_real_escape_string($conn, $_GET["id"]);
    $user_id = $_SESSION["user_id"]; // ID текущего вошедшего пользователя

    // Безопасность: удаляем только то объявление, которое принадлежит ЭТОМУ пользователю
    $query = "DELETE FROM poster WHERE p_id = '$poster_id' AND u_id = '$user_id'";
    $result = mysqli_query($conn, $query);

    if ($result) {
        ?>
        <script>
            alert("Ваше объявление успешно удалено!");
            window.location.href = "profile.php";
        </script>
        <?php
        exit();
    } else {
        echo "Ошибка при удалении объявления.";
    }
} else {
    header("Location: profile.php");
    exit();
}
?>