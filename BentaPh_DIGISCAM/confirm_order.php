<?php
if(!isset($_SESSION)) session_start();
if (!isset($_SESSION['username'])) {
    echo '<script>window.location.href = "index.php";</script>';
    exit();
}
include("connect.php");

if (isset($_POST['item_ids']) && isset($_POST['quantities'])) {
    $username = $_SESSION['username'];
    $item_ids = $_POST['item_ids'];
    $quantities = $_POST['quantities'];
    $total_amount = $_POST['total'];
    $shipping_fee = $_POST['shipping_fee'];

    // Get user details
    $user_query = mysqli_query($con, "SELECT * FROM usersaccount WHERE username = '$username'");
    $user = mysqli_fetch_array($user_query);
    $fullname = $user['fullname'];

    // Check stock first
    $out_of_stock = false;
    $out_of_stock_item = "";
    
    for ($i = 0; $i < count($item_ids); $i++) {
        $item_id = $item_ids[$i];
        
        // Get item from bag
        $bag_query = mysqli_query($con, "SELECT * FROM bag WHERE id = $item_id AND username = '$username'");
        $bag_item = mysqli_fetch_array($bag_query);

        if ($bag_item) {
            $itemname = $bag_item['itemname'];
            $order_quantity = $bag_item['quantity'];

            // Check product stock
            $product_query = mysqli_query($con, "SELECT quantity FROM products WHERE item = '$itemname'");
            $product = mysqli_fetch_array($product_query);

            if (!$product || $product['quantity'] < $order_quantity || $product['quantity'] == 0) {
                $out_of_stock = true;
                $out_of_stock_item = $itemname;
                break;
            }
        }
    }

    // Create transaction ID
    $date_prefix = date('mdy');
    $today = date('Y-m-d');
    
    $count_query = mysqli_query($con, "SELECT COUNT(DISTINCT transaction_id) as cnt FROM transaction WHERE DATE(date) = '$today'");
    $count_row = mysqli_fetch_array($count_query);
    $transaction_number = $count_row['cnt'] + 1;
    $transaction_id = $date_prefix . 'T' . $transaction_number;

    // Process each item
    for ($i = 0; $i < count($item_ids); $i++) {
        $item_id = $item_ids[$i];
        
        // Get item from bag
        $bag_query = mysqli_query($con, "SELECT * FROM bag WHERE id = $item_id AND username = '$username'");
        $bag_item = mysqli_fetch_array($bag_query);

        if ($bag_item) {
            $itemname = $bag_item['itemname'];
            $order_quantity = $bag_item['quantity'];

            // Check stock again
            $product_query = mysqli_query($con, "SELECT quantity FROM products WHERE item = '$itemname'");
            $product = mysqli_fetch_array($product_query);

            if ($product && $product['quantity'] >= $order_quantity && $product['quantity'] > 0) {
                // Update stock
                mysqli_query($con, "UPDATE products SET quantity = quantity - $order_quantity WHERE item = '$itemname'");

                $total = $bag_item['total'];
                $image = $bag_item['image'];
                $subtotal = $total;
                $shippingfee = $shipping_fee;
                $totalamount = $subtotal + $shippingfee;
                $price = $total / $order_quantity;
                $status = 'Pending';

                // Add to transaction
                $insert_query = "INSERT INTO transaction (transaction_id, fullname, itemname, quantity, price, image, subtotal, shippingfee, totalamount, status, date) 
                               VALUES ('$transaction_id', '$fullname', '$itemname', $order_quantity, $price, '$image', $subtotal, $shippingfee, $totalamount, '$status', NOW())";
                mysqli_query($con, $insert_query);

                // Remove from bag
                mysqli_query($con, "DELETE FROM bag WHERE id = $item_id AND username = '$username'");
            } else {
                echo "<script>alert('Sorry, \"$itemname\" is out of stock or your order exceeds the available stock. Please update your cart.'); window.location.href = 'main.php?pg=cart';</script>";
                exit();
            }
        }
    }
    echo "<script>alert('Order placed successfully!'); window.location.href = 'main.php?pg=cart';</script>";
} else {
    echo "<script>window.location.href = 'main.php?pg=cart';</script>";
}
mysqli_close($con);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - BentaPH</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: Arial, sans-serif; 
            background: #f5f5f5;
            line-height: 1.6;
        }
        .header {
            background: #2c3338;
            color: white;
            text-align: center;
            padding: 3rem 0;
        }
        .header h1 {
            font-size: 3rem;
            margin-bottom: 0.5rem;
        }
        .header p {
            color: #a7aaad;
            font-size: 1.2rem;
        }
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
        }
        .items-section, .summary-section {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h2 {
            color: #333;
            margin-bottom: 1.5rem;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.5rem;
        }
        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        th {
            background: #f8f9fa;
            font-weight: 500;
        }
        .info-item {
            margin-bottom: 1rem;
        }
        .info-item strong {
            display: inline-block;
            width: 150px;
            color: #666;
        }
        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
            padding: 0.5rem 0;
        }
        .total {
            border-top: 2px solid #dee2e6;
            padding-top: 1rem;
            margin-top: 1rem;
            font-size: 1.2rem;
            font-weight: bold;
        }
        .btn {
            display: inline-block;
            padding: 1rem 2rem;
            background: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            width: 100%;
            text-align: center;
        }
        .empty-cart {
            text-align: center;
            padding: 2rem;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Checkout</h1>
        <p>Review and Complete Your Order</p>
    </div>

    <div class="container">
        <div class="items-section">
            <h2>Items</h2>
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
                        foreach ($_SESSION['cart'] as $item_id => $item) {
                            $name = $item['name'];
                            $price = $item['price'];
                            $quantity = $item['quantity'];
                            $item_subtotal = $price * $quantity;
                            $subtotal += $item_subtotal;
                            ?>
                            <tr>
                                <td><?php echo $name; ?></td>
                                <td>Php <?php echo number_format($price, 2); ?></td>
                                <td><?php echo $quantity; ?></td>
                                <td>Php <?php echo number_format($item_subtotal, 2); ?></td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo '<tr><td colspan="4" class="empty-cart">No items in cart.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="summary-section">
            <h2>Order Summary</h2>
            <div class="info-item">
                <strong>Full Name:</strong> <?php echo $user['fullname']; ?>
            </div>
            <div class="info-item">
                <strong>Delivery Address:</strong> <?php echo $user['address']; ?>
            </div>
            <div class="info-item">
                <strong>Contact Number:</strong> <?php echo $user['contactnumber']; ?>
            </div>
            <hr style="margin: 1.5rem 0;">
            <div class="summary-item">
                <span>Subtotal:</span>
                <span>Php <?php echo number_format($subtotal, 2); ?></span>
            </div>
            <div class="summary-item">
                <span>Shipping Fee:</span>
                <span>Php <?php echo number_format($shipping_fee, 2); ?></span>
            </div>
            <div class="summary-item total">
                <span>Total Amount:</span>
                <span>Php <?php echo number_format($subtotal + $shipping_fee, 2); ?></span>
            </div>
            <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
            <form action="process_order.php" method="POST">
                <input type="hidden" name="total" value="<?php echo $subtotal + $shipping_fee; ?>">
                <input type="hidden" name="shipping_fee" value="<?php echo $shipping_fee; ?>">
                <button type="submit" class="btn">Proceed</button>
            </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>