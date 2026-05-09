<?php
if(!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['username'])) {
    echo '<script>window.location.href = "index.php";</script>';
    exit();
}

include("connect.php");

if (!$con) {
    echo "<script>alert('Connection failed!'); window.location.href = 'main.php';</script>";
    return;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BentaPH</title>
    <style>
        .navbar {
            background: #fff;
            padding: 1rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .nav-brand {
            font-size: 1.5rem;
            font-weight: bold;
            text-decoration: none;
            color: #333;
        }
        .nav-links a {
            text-decoration: none;
            color: #333;
            margin-left: 1.5rem;
        }
        .hero {
            background: #2c3338;
            color: #fff;
            text-align: center;
            padding: 3rem 0;
            margin-bottom: 2rem;
        }
        .hero h1 {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }
        .hero p {
            color: #adb5bd;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="main.php" class="nav-brand">BentaPH</a>
            <div class="nav-links">
                <a href="main.php">Home</a>
                <a href="about.php">About</a>
                <a href="account.php">Account</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="hero">
        <h1>Shop</h1>
        <p>Everything You Need, All In One Place</p>
    </div>

<?php
if (isset($_POST['item_id']) && isset($_POST['quantity'])) {
    $item_id = intval($_POST['item_id']);
    $quantity = intval($_POST['quantity']);
    $username = $_SESSION['username'];

    if ($item_id <= 0 || $quantity <= 0) {
        echo "<script>alert('Invalid input.'); window.history.back();</script>";
        exit();
    }

    // Get product details
    $product_query = "SELECT * FROM products WHERE id = $item_id";
    $product_result = mysqli_query($con, $product_query);
    
    if (mysqli_num_rows($product_result) > 0) {
        $item = mysqli_fetch_array($product_result);
        
        if ($item['quantity'] < $quantity) {
            echo "<script>
                alert('Not enough stock available. Available: " . $item['quantity'] . "');
                window.history.back();
            </script>";
            exit();
        }

        // Insert into bag table
        $itemname = $item['item'];
        $image = $item['image'];
        $price = $item['price'];
        $total = $price * $quantity;
        $date = date("Y-m-d H:i:s");

        // Check if item already in bag for this user
        $check_bag = mysqli_query($con, "SELECT * FROM bag WHERE username='$username' AND itemname='$itemname'");
        if (mysqli_num_rows($check_bag) > 0) {
            // Update quantity and total
            $update_bag = "UPDATE bag SET quantity = quantity + $quantity, total = total + $total WHERE username='$username' AND itemname='$itemname'";
            mysqli_query($con, $update_bag);
        } else {
            // Insert new row
            $insert_bag = "INSERT INTO bag (username, itemname, quantity, total, image, date)
                        VALUES ('$username', '$itemname', $quantity, $total, '$image', '$date')";
            mysqli_query($con, $insert_bag);
        }

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = array();
        }

        // If item already in cart, increment quantity
        if (isset($_SESSION['cart'][$item_id])) {
            $_SESSION['cart'][$item_id]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$item_id] = array(
                'name' => $item['item'],
                'price' => $item['price'],
                'quantity' => $quantity,
                'image' => $item['image']
            );
        }

        echo "<script>
            alert('Item added to cart!');
            window.location.href = 'main.php?pg=cart';
        </script>";
        exit();
    } else {
        echo "<script>alert('Product not found.'); window.history.back();</script>";
        exit();
    }
}
?>

    <div class="container">
        <h2>Products</h2>
        <div class="product-list">
            <?php
            $result = mysqli_query($con, "SELECT * FROM products");
            while ($row = mysqli_fetch_array($result)) {
                ?>
                <div class="product-item">
                    <img src="<?php echo $row['image']; ?>" alt="<?php echo $row['item']; ?>">
                    <h3><?php echo $row['item']; ?></h3>
                    <p>Price: ₱<?php echo number_format($row['price'], 2); ?></p>
                    <p>Stock: <?php echo $row['quantity']; ?></p>
                    <form method="POST" action="shop.php">
                        <input type="hidden" name="item_id" value="<?php echo $row['id']; ?>">
                        <input type="number" name="quantity" value="1" min="1" max="<?php echo $row['quantity']; ?>">
                        <input type="submit" value="Add to Cart">
                    </form>
                </div>
                <?php
            }
            ?>
        </div>
    </div>

</body>
</html>