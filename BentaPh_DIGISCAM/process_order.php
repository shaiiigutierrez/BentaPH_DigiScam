<?php
if(!isset($_SESSION)) session_start();
if (!isset($_SESSION['username'])) {
    echo '<script>window.location.href = "index.php";</script>';
    exit();
}

if (!isset($_POST['item_ids']) || !isset($_POST['quantities'])) {
    echo "<script>window.location.href = 'main.php?pg=cart';</script>";
    exit();
}

include("connect.php");
$username = $_SESSION['username'];
$item_ids = $_POST['item_ids'];
$quantities = $_POST['quantities'];
$total_amount = $_POST['total'];
$shipping_fee = $_POST['shipping_fee'];
$date = date("Y-m-d H:i:s");

$user = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM usersaccount WHERE username = '$username'"));
$fullname = $user['fullname'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Summary - BentaPH</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: Arial, sans-serif; 
            background: #f5f5f5;
            line-height: 1.6;
        }
        .container {
            max-width: 600px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }
        .success-icon {
            color: #28a745;
            font-size: 48px;
            margin-bottom: 1rem;
        }
        h1 {
            color: #333;
            margin-bottom: 1rem;
        }
        .message {
            color: #666;
            margin-bottom: 2rem;
        }
        .order-details {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 2rem;
        }
        .items-list {
            text-align: left;
            margin: 1rem 0;
        }
        .item {
            padding: 0.5rem 0;
            border-bottom: 1px solid #dee2e6;
        }
        .status {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: #ffc107;
            color: #000;
            border-radius: 4px;
            margin: 1rem 0;
        }
        .btn {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 500;
            border: none;
            cursor: pointer;
        }
        .btn-proceed {
            background: #28a745;
            color: white;
        }
        .btn-cancel {
            background: #dc3545;
            color: white;
            margin-right: 10px;
        }
        .button-group {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            gap: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-icon">✓</div>
        <h1>Order Summary</h1>
        <div class="message">Please review your order, <?php echo $fullname; ?>!</div>
        
        <div class="order-details">
            <p><strong>Order Date:</strong> <?php echo date('F j, Y g:i A', strtotime($date)); ?></p>
            <div class="items-list">
                <strong>Ordered Items:</strong>
                <?php 
                for ($i = 0; $i < count($item_ids); $i++) {
                    $bag = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM bag WHERE id = {$item_ids[$i]} AND username = '$username'"));
                    
                    if ($bag) {
                        $itemname = $bag['itemname'];
                        $image = $bag['image'];
                        $quantity = $quantities[$i];
                        
                        $product = mysqli_fetch_array(mysqli_query($con, "SELECT quantity, price FROM products WHERE item = '$itemname'"));
                        $price = $product['price'];
                        $stock = $product['quantity'];
                        
                        if ($stock == 0) {
                            // Remove item from cart
                            mysqli_query($con, "DELETE FROM bag WHERE id = {$item_ids[$i]} AND username = '$username'");
                            echo "<script>alert('Sorry, \"$itemname\" is out of stock and has been removed from your cart.'); window.location.href = 'main.php?pg=cart';</script>";
                            exit();
                        } elseif ($quantity > $stock) {
                            // Update item to available stock
                            mysqli_query($con, "UPDATE bag SET quantity = $stock, total = $stock * $price WHERE id = {$item_ids[$i]} AND username = '$username'");
                            echo "<script>alert('Sorry, \"$itemname\" has only $stock stocks left. Your cart has been updated to $stock.'); window.location.href = 'main.php?pg=cart';</script>";
                            exit();
                        }
                        

                        $subtotal = $price * $quantity;
                        $total = $subtotal + $shipping_fee;
                        ?>
                        <div class="item">
                            <?php echo $itemname; ?> x <?php echo $quantity; ?> - Php <?php echo number_format($total, 2); ?>
                        </div>
                        <?php 
                    }
                }
                ?>
            </div>
            <p><strong>Subtotal:</strong> Php <?php echo number_format($total_amount, 2); ?></p>
            <p><strong>Shipping Fee:</strong> Php <?php echo number_format($shipping_fee, 2); ?></p>
            <p><strong>Total Amount:</strong> Php <?php echo number_format($total_amount + $shipping_fee, 2); ?></p>
            <p><strong>Delivery Address:</strong> <?php echo $user['address']; ?></p>
        </div>

        <div id="orderStatus" class="status">Your order will be submitted for shop approval</div>
        <p class="message">Click Proceed to submit your order or Cancel to return to cart.</p>
        
        <div class="button-group">
            <form action="main.php?pg=cart" method="POST">
                <button type="submit" class="btn btn-cancel">Cancel Order</button>
            </form>
            
            <form action="confirm_order.php" method="POST" onsubmit="return showConfirmation()">
                <?php foreach($item_ids as $i => $id): ?>
                    <input type="hidden" name="item_ids[]" value="<?php echo $id; ?>">
                    <input type="hidden" name="quantities[]" value="<?php echo $quantities[$i]; ?>">
                <?php endforeach; ?>
                <input type="hidden" name="total" value="<?php echo $total_amount; ?>">
                <input type="hidden" name="shipping_fee" value="<?php echo $shipping_fee; ?>">
                <button type="submit" class="btn btn-proceed">Proceed</button>
            </form>
        </div>
    </div>

    <script>
    function showConfirmation() {
        var status = document.getElementById('orderStatus');
        status.innerHTML = 'Order submitted successfully! Waiting for shop approval...';
        status.style.background = '#28a745';
        status.style.color = 'white';
        return true;
    }
    </script>
</body>
</html>
<?php
mysqli_close($con);
?>