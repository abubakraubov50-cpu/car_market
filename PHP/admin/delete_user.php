<?php
require_once("../config/db.php");
require_once("../config/helpers.php");

check_admin();

if (isset($_GET["id"]) && !empty($_GET["id"])) {
    $id = mysqli_real_escape_string($conn, $_GET["id"]);

    // Сначала удаляем все объявления этого пользователя
    mysqli_query($conn, "DELETE FROM poster WHERE u_id = '$id'");

    // Затем удаляем самого пользователя
    $query = "DELETE FROM users WHERE id = '$id'";
    $result = mysqli_query($conn, $query);

    if ($result) {
        ?>
        <script>
            alert("Пользователь успешно удален!");
            window.location.href = "users.php";
        </script>
        <?php
        exit();
    }
}
header("Location: users.php");
exit();
?>