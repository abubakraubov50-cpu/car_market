<?php
require_once("../config/db.php");
require_once("../config/helpers.php");

check_admin();

if (isset($_GET["id"]) && !empty($_GET["id"])) {
    $id = mysqli_real_escape_string($conn, $_GET["id"]);

    mysqli_query($conn, "DELETE FROM models WHERE br_id = '$id'");
    $query = "DELETE FROM brands WHERE br_id = '$id'";
    $result = mysqli_query($conn, $query);

    if ($result) {
        ?>
        <script>
            alert("Марка успешно удалена!");
            window.location.href = "brands.php";
        </script>
        <?php
        exit();
    }
}
header("Location: brands.php");
exit();
?>