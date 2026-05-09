<?php
session_start();
include("connect.php");

if (!isset($_POST['item_id']) || !isset($_SESSION['cart'])) {
    echo "<script>alert('Invalid request!'); window.location.href = 'cart.php';</script>";
    exit();
}

$item_id = intval($_POST['item_id']);
$username = $_SESSION['username'];

if (!isset($_SESSION['cart'][$item_id])) {
    echo "<script>alert('Item not found in cart!'); window.location.href = 'cart.php';</script>";
    exit();
}

mysqli_query($con, "DELETE FROM bag WHERE id = $item_id AND username = '$username'");
$item_name = $_SESSION['cart'][$item_id]['name'];
unset($_SESSION['cart'][$item_id]);

echo "<script>alert('$item_name has been removed from your cart!'); window.location.href = 'cart.php';</script>";
mysqli_close($con);
?>
