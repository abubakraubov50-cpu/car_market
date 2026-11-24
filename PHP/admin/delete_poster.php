<?php
require_once("../config/db.php");
require_once("../config/helpers.php");

check_admin();

if (isset($_GET["id"]) && !empty($_GET["id"])) {
    $id = mysqli_real_escape_string($conn, $_GET["id"]);

    $query = "DELETE FROM poster WHERE p_id = '$id'";
    $result = mysqli_query($conn, $query);

    if ($result) {
        ?>
        <script>
            alert("Объявление успешно удалено!");
            window.location.href = "posters.php";
        </script>
        <?php
        exit();
    }
}
header("Location: posters.php");
exit();
?>