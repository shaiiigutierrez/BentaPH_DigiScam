<?php
include("../connect.php");

if (!$con) {
    echo "<script>alert('Database connection failed.'); window.location='management.php';</script>";
    exit;
}

function deleteItem($con, $id) {
    // First, get the product details before deletion
    $result = mysqli_query($con, "SELECT * FROM products WHERE id = '$id'");
    $count = mysqli_num_rows($result);

    if ($count > 0) {
        $product = mysqli_fetch_array($result);
        $itemname = $product['item'];
        
        // Get users BEFORE deleting from bag
        $users = mysqli_query($con, "SELECT DISTINCT username FROM bag WHERE itemname = '$itemname'");
        while ($user = mysqli_fetch_assoc($users)) {
            $username = $user['username'];
            mysqli_query($con, "INSERT INTO item_deletion (username, itemname) VALUES ('$username', '$itemname')");
        }

        // Delete the product from products table
        mysqli_query($con, "DELETE FROM products WHERE id = '$id'");
        
        // Now remove the item from all users' carts
        mysqli_query($con, "DELETE FROM bag WHERE itemname = '$itemname'");
        
        // Log the deletion (optional)
        $delete_date = date("Y-m-d H:i:s");
        mysqli_query($con, "INSERT INTO deleted_items (item_name, deleted_date) VALUES ('$itemname', '$delete_date')");
        
        echo "<script>alert('Product and all cart instances have been successfully deleted.');</script>";
    } else {
        echo "<script>alert('Product not found.');</script>";
    }
}



// Check if 'id' exists
if (isset($_GET["id"])) {
    $id = $_GET["id"];
    deleteItem($con, $id);
}

// Redirect back to management page
echo "<script>window.location='management.php?pg=manage_items';</script>";
?>