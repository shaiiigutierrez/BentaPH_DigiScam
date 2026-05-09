edit_category

<?php
$con = mysqli_connect("localhost", "root", "", "bentadb");

if (!$con) {
    echo "<script>alert('Database connection failed.'); window.location='add_item.php';</script>";
    exit;
}

function deleteItem($con, $id) {
    $result = mysqli_query($con, "SELECT * FROM products WHERE id = '$id'");
    $count = mysqli_num_rows($result);

    if ($count > 0) {
        // Delete the item kung nageexist
        mysqli_query($con, "DELETE FROM products WHERE id = '$id'");
    }
}

// nititingnan if 'id' exists
if (isset($_GET["id"])) {
    $id = $_GET["id"];
    deleteItem($con, $id);
}

// pabalik kung saan nagsimula
echo "<script>window.location='add_item.php';</script>";
?>
