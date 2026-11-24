<?php
require_once("../config/db.php");
require_once("../config/helpers.php");

check_admin();

if (isset($_GET["id"]) && !empty($_GET["id"])) {
    $id = mysqli_real_escape_string($conn, $_GET["id"]);

    $query = "DELETE FROM models WHERE m_id = '$id'";
    $result = mysqli_query($conn, $query);

    if ($result) {
        ?>
        <script>
            alert("Модель успешно удалена!");
            window.location.href = "models.php";
        </script>
        <?php
        exit();
    }
}
header("Location: models.php");
exit();
?>