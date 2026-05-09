<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['username'])) {
    echo '<script>window.location.href = "index.php";</script>';
    exit;
}

include("connect.php");
$username = $_SESSION['username'];
$_SESSION['cart'] = array();

$bag = mysqli_query($con, "SELECT * FROM bag WHERE username = '$username'");
while ($row = mysqli_fetch_array($bag)) {
    $_SESSION['cart'][$row['id']] = array(
        'name' => $row['itemname'],
        'price' => ($row['quantity'] != 0) ? $row['total'] / $row['quantity'] : 0,
        'quantity' => $row['quantity'],
        'image' => $row['image']
    );
}

$is_main_file = true;
$pg = isset($_GET["pg"]) ? $_GET["pg"] : "main";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BentaPH - Online Shopping</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
      <style>
        .navbar {
            background: #fff !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07) !important;
        }
        .navbar-brand {
            color: #343a40 !important;
            font-weight: bold !important;
        }
        .nav-link {
            color: #343a40 !important;
            font-weight: 500 !important;
            margin-right: 1rem !important;
        }
        .nav-link:hover {
            color: #6c757d !important;
            text-decoration: underline !important;
        }
        .cart-icon {
            font-size: 1.3rem !important;
            margin-left: 1rem !important;
        }
        
        html, body {
            height: 100%;
            min-height: 100vh;
        }
        body {
            background: #fff;
            min-height: 100vh;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Arial, sans-serif;
            display: flex;
            flex-direction: column;
        }
        .banner-header {
            width: 100%;
            min-height: 300px;
            height: 40vw;
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('banner.png') center center/cover no-repeat;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .banner-header .overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.2) 0%, rgba(0, 0, 0, 0) 100%);
            opacity: 1;
            animation: subtleFloat 20s ease-in-out infinite;
        }
        .banner-header .banner-content {
            position: relative;
            z-index: 2;
            color: #fff;
            text-align: center;
        }
        .banner-header h4 {
            font-size: 1.5rem;
            font-weight: 400;
            margin-bottom: 0.5rem;
            animation: slideUp 1s ease-out;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }
        .banner-header h1 {
            font-size: 50px;
            font-weight: 700;
            letter-spacing: 2px;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
            background: linear-gradient(45deg, #fff, #e0e0e0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: slideUp 1s ease-out 0.2s forwards;
            opacity: 0;
            transform: translateY(20px);
            line-height: 1.3;
            padding: 0.1em 0;
            margin: 0;
        }
        .banner-header p {
            font-size: 20px;
            font-weight: 400;
            text-shadow: 0 1px 4px rgba(0, 0, 0, 0.2);
            animation: slideUp 1s ease-out 0.4s forwards;
            opacity: 0;
            transform: translateY(20px);
        }
        .banner-header .emphasis {
            font-style: italic;
        }
        @keyframes subtleFloat {
            0%, 100% { transform: translateY(0) scale(1); }
            50% { transform: translateY(-20px) scale(1.05); }
        }
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .navbar {
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
        }
        .nav-link {
            color: #343a40 !important;
            font-weight: 500;
            margin-right: 1rem;
        }
        .nav-link:hover {
            color: #6c757d !important;
            text-decoration: underline;
        }
        .cart-icon {
            font-size: 1.3rem;
            margin-left: 1rem;
        }
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 2rem;
            padding: 2rem 0;
        }
        .product-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 16px rgba(60,102,241,0.07);
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
            position: relative;
        }
        .product-card:hover {
            transform: translateY(-8px) scale(1.03);
            box-shadow: 0 8px 32px rgba(60,102,241,0.13);
        }
        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            transition: transform 0.3s, filter 0.3s;
        }
        .product-card:hover .product-image {
            transform: scale(1.08);
            filter: brightness(0.93) saturate(1.2);
        }
        .product-info {
            padding: 1.2rem;
        }
        .product-name {
            font-size: 1.15em;
            font-weight: 600;
            margin-bottom: 8px;
            color: #343a40;
        }
        .product-price {
            color: #000 !important;
            font-weight: bold;
            font-size: 1.15em;
            margin-bottom: 8px;
        }
        .product-quantity {
            color: #6c757d;
            font-size: 0.95em;
            margin-bottom: 15px;
        }
        .view-details {
            display: inline-block;
            background: #fff !important;
            color: #343a40 !important;
            padding: 8px 18px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            border: 1.5px solid #adb5bd !important;
            transition: background 0.3s, box-shadow 0.3s, border 0.3s;
            box-shadow: 0 2px 8px rgba(60,102,241,0.08);
        }
        .view-details:hover {
            background: #f8f9fa !important;
            color: #212529 !important;
            border: 1.5px solid #343a40 !important;
        }
        /* Button styles only for main page and details page */
        body:not([data-page="account"]):not([data-page="cart"]):not([data-page="checkout"]):not([data-page="about"]):not([data-page="view_transaction"]) .btn,
        body:not([data-page="account"]):not([data-page="cart"]):not([data-page="checkout"]):not([data-page="about"]):not([data-page="view_transaction"]) .btn-primary,
        body:not([data-page="account"]):not([data-page="cart"]):not([data-page="checkout"]):not([data-page="about"]):not([data-page="view_transaction"]) .btn-secondary {
            background: #fff !important;
            color: #343a40 !important;
            border: 1.5px solid #adb5bd !important;
            box-shadow: none !important;
        }
        
        body:not([data-page="account"]):not([data-page="cart"]):not([data-page="checkout"]):not([data-page="about"]):not([data-page="view_transaction"]) .btn:hover,
        body:not([data-page="account"]):not([data-page="cart"]):not([data-page="checkout"]):not([data-page="about"]):not([data-page="view_transaction"]) .btn-primary:hover,
        body:not([data-page="account"]):not([data-page="cart"]):not([data-page="checkout"]):not([data-page="about"]):not([data-page="view_transaction"]) .btn-secondary:hover {
            background: #f8f9fa !important;
            color: #212529 !important;
            border: 1.5px solid #343a40 !important;
        }
        .card.p-3 {
            border: none !important;
            box-shadow: none !important;
        }
        .card.p-3::-webkit-scrollbar,
        .d-flex.flex-row::-webkit-scrollbar {
            display: none;
        }
        .card.p-3, .d-flex.flex-row {
            -ms-overflow-style: none;  
            scrollbar-width: none;   
        }
        @media (max-width: 768px) {
            .banner-header {
                min-height: 250px;
                height: auto;
                padding: 3rem 1rem;
            }
            .banner-header h4 {
                font-size: 1.2rem;
            }
            .banner-header h1 {
                font-size: 2.5rem;
                margin: 0.5rem 0;
            }
            .banner-header p {
                font-size: 1rem;
            }
            .products-grid {
                grid-template-columns: 1fr;
                gap: 1.2rem;
            }
            .product-image {
                height: 140px;
            }
        }
        @media (max-width: 480px) {
            .banner-header {
                min-height: 200px;
                padding: 2rem 1rem;
            }
            .banner-header h4 {
                font-size: 1rem;
            }
            .banner-header h1 {
                font-size: 2rem;
            }
            .banner-header p {
                font-size: 0.9rem;
            }
            .product-info {
                padding: 0.7rem;
            }
        }
    </style>
</head>
<body data-page="<?php echo $pg; ?>">
     <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light sticky-top">
        <div class="container p-2">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="main.php" style="color:#343a40;">
                <img src="logo.png" alt="Logo" style="height:38px; margin-right:10px;">
                DigiScam
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="main.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="main.php?pg=about">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="main.php?pg=account">Account</a></li>
                    <li class="nav-item"><a class="nav-link" href="administrator/logout.php">Logout</a></li>
                    <li class="nav-item">
                        <a class="nav-link cart-icon" href="main.php?pg=cart" title="Cart">🛒</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>


    <!-- Banner Header -->
   <?php if ($pg == "main") { ?>
    <div class="banner-header">
        <div class="overlay"></div>
        <div class="banner-content">
            <h4>Your Hunt For</h4>
            <h1  style="font-size: 50px;">Affordable Digital Cameras Ends Here</h1><br>
            <p style="font-size: 20px; font-style: italic;">Scam? Nah. Just Snappy Deals on DigiCams!</p>
        </div>
    </div>
    <?php } ?>

    <?php if ($pg == "main") { ?>
    <!-- Category Section -->
    <div class="container my-4"><br><br>
        <h4 class="mb-3 fw-bold" style="color:#343a40;">Select for Categories</h4>
        <div class="category-scroll d-flex flex-row gap-4" style="overflow-x:auto;">
            <!-- All Cameras Card -->
            <?php
            // Always get the first camera image for "All Cameras"
            $all_cam = mysqli_fetch_array(mysqli_query($con, "SELECT image, item FROM products WHERE category LIKE '%camera%' OR item LIKE '%camera%' ORDER BY id ASC LIMIT 1"));
            $all_cam_img = $all_cam ? $all_cam['image'] : 'placeholder.png';
            $all_cam_name = $all_cam ? $all_cam['item'] : 'Camera';
            $is_selected = !isset($_GET['category']);
            ?>
            <a href="main.php" style="text-decoration:none;">
                <div class="card shadow-sm border-0 text-center" style="width: 230px; cursor:pointer; background:<?php echo $is_selected ? '#e9ecef' : '#fff'; ?>">
                    <img src="<?php echo $all_cam_img; ?>" alt="All Cameras"
                         style="width:100%;height:150px;object-fit:cover;border-radius:12px 12px 0 0;">
                    <div class="card-body p-2">
                        <div class="fw-bold" style="color:#343a40;">All Cameras</div>
                    </div>
                </div>
            </a>
            <?php
            // Category cards
            $categories = mysqli_query($con, "SELECT * FROM classification ORDER BY name ASC");
            while ($cat = mysqli_fetch_array($categories)):
                $prod = mysqli_fetch_array(mysqli_query($con, "SELECT image, item FROM products WHERE category='$cat[name]' LIMIT 1"));
                $img = $prod ? $prod['image'] : 'placeholder.png';
                $item_name = $prod ? $prod['item'] : $cat['name'];
                $is_selected = isset($_GET['category']) && $_GET['category'] == $cat['name'];
            ?>
            <a href="main.php?category=<?php echo urlencode($cat['name']); ?>" style="text-decoration:none;">
                <div class="card shadow-sm border-0 text-center" style="width: 230px; cursor:pointer; background:<?php echo $is_selected ? '#e9ecef' : '#fff'; ?>">
                    <img src="<?php echo $img; ?>" alt="<?php echo $item_name; ?>"
                         style="width:100%;height:150px;object-fit:cover;border-radius:12px 12px 0 0;">
                    <div class="card-body p-2">
                        <div class="fw-bold" style="color:#343a40;"><?php echo $cat['name']; ?></div>
                    </div>
                </div>
            </a>
            <?php endwhile; ?>
        </div>
        <br><br>
        <h4 class="mb-3 fw-bold" style="color:#343a40;">Explore our Cameras</h4>
    </div>
    <?php } ?>
    <!-- Main Content -->
    <div class="container flex-grow-1 py-4">
        
        <?php
        if ($pg == "main") {
            $category = isset($_GET['category']) ? $_GET['category'] : '';
            $query = $category 
                ? "SELECT * FROM products WHERE category='$category' ORDER BY item ASC"
                : "SELECT * FROM products ORDER BY item ASC";
            
            $products = mysqli_query($con, $query);
            echo '<div class="products-grid">';
            
            while ($product = mysqli_fetch_array($products)) {
                $sold_out = $product['quantity'] <= 0;
                $price = number_format($product['price'], 2);
                $stock_text = $sold_out ? "<span style='color:red;font-weight:bold;'>Sold Out</span>" : "In Stock: {$product['quantity']}";
                $button_text = $sold_out ? "Sold Out" : "View Details";
                $button_style = $sold_out ? " style='pointer-events:none;opacity:0.5;'" : "";
                
                echo "<div class='product-card'>
                    <img src='{$product['image']}' alt='{$product['item']}' class='product-image'>
                    <div class='product-info'>
                        <h3 class='product-name'>{$product['item']}</h3>
                        <div class='product-price'>₱{$price}</div>
                        <div class='product-quantity'>{$stock_text}</div>
                        <a href='main.php?pg=details&id={$product['id']}' class='view-details'{$button_style}>{$button_text}</a>
                    </div>
                </div>";
            }
            echo '</div>';
        } elseif ($pg == "details" && isset($_GET['id'])) {
            include("details.php");
        } elseif ($pg == "cart") {
            include("cart.php");
        } elseif ($pg == "checkout") {
            include("checkout.php");
        } elseif ($pg == "about") {
            include("about.php");
        } elseif ($pg == "account") {
            include("account.php");
        } elseif ($pg == "view_transaction" && isset($_GET['transaction_id'])) {
            include("view_transaction.php");
        } else {
            echo "<h2>Page Not Found</h2>";
            echo "<p>The page you are looking for does not exist.</p>";
        }
        ?>
    </div>

    <!-- Footer -->
    <footer class="mt-auto py-4" style="background:#f8f9fa; border-top:1px solid #e9ecef;">
        <div class="container">
            <div class="row text-center text-md-start">
                <!-- Developers -->
                <div class="col-12 col-md-3 mb-3 mb-md-0">
                    <div style="font-weight:600; color:#343a40;">Developers</div>
                    <div style="color:#6c757d; font-size:1em;">
                        Lhinden Shein Mandocdoc<br>
                        Shaira Glen Gutierrez<br>
                        Sophia Suralta<br>
                        Klarence Cate Virtucio<br>
                        John Peter Mendoza<br>
                        James Brylle Sanares<br>
                    </div>
                </div>
                <!-- Our Company -->
                <div class="col-12 col-md-3 mb-3 mb-md-0">
                    <div style="font-weight:600; color:#343a40;">Our Company</div>
                    <div style="color:#6c757d; font-size:1em;">
                        SHFI<br>
                        <span style="font-size:0.97em;">Sir Harold and Friends Inc.</span>
                    </div>
                    <div style="color:#adb5bd; font-size:0.95em;">
                        &copy; <?php echo date('Y'); ?> SHFI
                    </div>
                </div>
                <!-- Contact Us -->
                <div class="col-12 col-md-3 mb-3 mb-md-0">
                    <div style="font-weight:600; color:#343a40;">Contact Us</div>
                    <div style="color:#6c757d; font-size:1em;">
                        contactshfi@gmail.com<br>
                        +63 912 345 6789
                    </div>
                </div>
                <!-- Follow Us -->
                <div class="col-12 col-md-3">
                    <div style="font-weight:600; color:#343a40;">Follow Us</div>
                    <div class="mt-2">
                        <a href="https://facebook.com" target="_blank" style="color:#343a40; margin:0 8px; font-size:1.3em;"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://twitter.com" target="_blank" style="color:#343a40; margin:0 8px; font-size:1.3em;"><i class="fab fa-twitter"></i></a>
                        <a href="https://instagram.com" target="_blank" style="color:#343a40; margin:0 8px; font-size:1.3em;"><i class="fab fa-instagram"></i></a>
                        <a href="mailto:contact@shfi.com" style="color:#343a40; margin:0 8px; font-size:1.3em;"><i class="fas fa-envelope"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- Font Awesome CDN for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>