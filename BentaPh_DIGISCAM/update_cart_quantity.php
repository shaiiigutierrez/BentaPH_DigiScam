<?php
session_start();
include("connect.php");
if (!isset($_SESSION['username'])) exit;

if (!isset($_POST['item_id']) || !isset($_POST['quantity'])) exit;

$item_id = $_POST['item_id'];
$quantity = $_POST['quantity'];
$username = $_SESSION['username'];

if (!isset($_SESSION['cart'][$item_id])) exit;

$price = isset($_SESSION['cart'][$item_id]['price']) ? $_SESSION['cart'][$item_id]['price'] : 0;
$total = $price * $quantity;

$_SESSION['cart'][$item_id]['quantity'] = $quantity;
$_SESSION['cart'][$item_id]['total'] = $total;

mysqli_query($con, "UPDATE bag SET quantity = $quantity, total = $total 
                   WHERE id = $item_id AND username = '$username'");
mysqli_close($con);
