<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    echo "<script>
        alert('Please login first.');
        window.location.href = 'index.php';
    </script>";
    exit();
}

// Database connection
include("../connect.php");
if (!$con) {
    echo "<script>
        alert('Database connection failed! Please try again later.');
        window.location.href = 'index.php';
    </script>";
    exit();
}

// Get current page from URL parameter
$pg = isset($_GET['pg']) ? $_GET['pg'] : 'dashboard';

$is_main_file = true;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BentaPH - <?php echo ucfirst($pg); ?></title>
    <style>
        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
        }
        
        body { 
            font-family: Arial, sans-serif;
            background: #f8f9fa;
            display: flex;
            min-height: 100vh;
            color: #333;
            position: relative;
        }

        .mobile-menu-toggle {
            display: none;
            position: fixed;
            top: 10px;
            left: 10px;
            z-index: 1001;
            background: #2c3338;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        .close-sidebar {
            display: none;
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
        }

        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        .overlay.active {
            display: block;
        }

        .sidebar {
            width: 250px;
            background: #2c3338;
            color: #fff;
            padding: 20px 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
            transition: transform 0.3s ease;
        }

        .brand {
            padding: 15px 25px;
            font-size: 24px;
            font-weight: bold;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 20px;
        }

        .nav-section {
            margin-bottom: 25px;
        }

        .nav-title {
            padding: 0 25px;
            font-size: 12px;
            text-transform: uppercase;
            color: #6c757d;
            margin-bottom: 10px;
            letter-spacing: 0.5px;
        }

        .nav-item {
            padding: 12px 25px;
            color: #fff;
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: background 0.3s;
            font-size: 14px;
        }

        .nav-item:hover {
            background: #383f45;
        }

        .nav-item.active {
            background: #383f45;
            border-left: 3px solid #0d6efd;
        }

        .nav-item i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
            opacity: 0.7;
        }

        .main-content {
            flex: 1;
            padding: 30px;
            margin-left: 250px;
            width: calc(100% - 250px);
            transition: margin-left 0.3s ease, width 0.3s ease;
        }

        .page-header {
            margin-bottom: 30px;
        }

        .page-header h1 {
            font-size: 32px;
            color: #2c3338;
            margin-bottom: 10px;
            font-weight: 500;
        }

        .overview-text {
            color: #6c757d;
            font-size: 14px;
        }

        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background: #2c3338;
            color: #fff;
            border-radius: 8px;
            padding: 25px;
            transition: transform 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-title {
            font-size: 16px;
            color: #fff;
            margin-bottom: 15px;
            opacity: 0.9;
        }

        .card-number {
            font-size: 48px;
            font-weight: 600;
            color: #fff;
            margin-bottom: 20px;
        }

        .view-details {
            color: #fff;
            text-decoration: none;
            display: flex;
            align-items: center;
            font-size: 14px;
            opacity: 0.8;
            transition: opacity 0.3s;
        }

        .view-details:hover {
            opacity: 1;
        }

        .view-details::after {
            content: "→";
            margin-left: 5px;
            transition: transform 0.3s;
        }

        .view-details:hover::after {
            transform: translateX(5px);
        }

        .user-section {
            padding: 20px 25px;
            border-top: 1px solid rgba(255,255,255,0.1);
            margin-top: auto;
        }

        .user-role {
            font-size: 12px;
            color: #6c757d;
            margin-bottom: 5px;
        }

        .logout-btn {
            display: block;
            width: 100%;
            padding: 10px;
            background: #383f45;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            margin-top: 10px;
            transition: background 0.3s;
            font-size: 14px;
        }

        .logout-btn:hover {
            background: #444b52;
        }

        .footer {
            margin-top: auto;
            padding: 20px 0;
            color: #6c757d;
            font-size: 12px;
            border-top: 1px solid #dee2e6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .footer a {
            color: #0d6efd;
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer a:hover {
            color: #0a58ca;
        }

        .footer-links a + a {
            margin-left: 15px;
        }

        .content-wrapper {
            display: flex;
            gap: 30px;
            margin-bottom: 30px;
        }

        .add-category-section {
            flex: 0 0 400px;
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            height: fit-content;
        }

        .category-list-section {
            flex: 1;
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .section-title {
            font-size: 24px;
            margin-bottom: 20px;
            color: #2c3338;
            font-weight: 500;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: #0d6efd;
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
        }

        .btn {
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-primary {
            background: #0d6efd;
            color: #fff;
        }

        .btn-primary:hover {
            background: #0b5ed7;
            transform: translateY(-1px);
        }

        .btn-edit {
            background: #ffc107;
            color: #000;
            padding: 6px 12px;
            font-size: 12px;
            margin-right: 5px;
        }

        .btn-edit:hover {
            background: #ffca2c;
        }

        .btn-delete {
            background: #dc3545;
            color: #fff;
            padding: 6px 12px;
            font-size: 12px;
        }

        .btn-delete:hover {
            background: #c82333;
        }

        .categories-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .categories-table th,
        .categories-table td {
            padding: 15px 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .categories-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }

        .categories-table tr:hover {
            background: #f8f9fa;
        }

        .action-buttons {
            display: flex;
            gap: 5px;
            align-items: center;
        }

        .edit-form {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .edit-form input {
            padding: 6px 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .alert {
            padding: 15px;
            margin-bottom: 25px;
            border-radius: 6px;
            font-size: 14px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #6c757d;
        }

        .empty-state h3 {
            margin-bottom: 10px;
            color: #333;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .cards-grid {
                grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .mobile-menu-toggle {
                display: block;
            }

            .close-sidebar {
                display: block;
            }

            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                width: 100%;
                padding: 20px;
                padding-top: 60px;
            }

            .page-header h1 {
                font-size: 24px;
            }

            .card-number {
                font-size: 36px;
            }

            .footer {
                flex-direction: column;
                text-align: center;
            }

            .footer-links {
                display: flex;
                gap: 15px;
                justify-content: center;
            }
        }

        @media (max-width: 480px) {
            .cards-grid {
                grid-template-columns: 1fr;
            }

            .main-content {
                padding: 15px;
                padding-top: 60px;
            }

            .card {
                padding: 20px;
            }

            .nav-item {
                padding: 10px 20px;
                font-size: 13px;
            }
        }
    </style>
</head>
<body>
    <button class="mobile-menu-toggle">☰ Menu</button>
    <div class="overlay"></div>
    <div class="sidebar">
        <div class="brand">
            BentaPH
            <button class="close-sidebar">×</button>
        </div>
        
        <div class="nav-section">
            <div class="nav-title">CORE</div>
            <a href="management.php?pg=dashboard" class="nav-item <?php echo $pg == 'dashboard' ? 'active' : ''; ?>">
                <i>📊</i> Dashboard
            </a>
        </div>
        
        <div class="nav-section">
            <div class="nav-title">TRANSACTIONS</div>
            <a href="management.php?pg=orders" class="nav-item <?php echo $pg == 'orders' ? 'active' : ''; ?>">
                <i>🛍️</i> Orders
            </a>
        </div>
        
        <div class="nav-section">
            <div class="nav-title">MANAGEMENT</div>
            <a href="management.php?pg=categories" class="nav-item <?php echo $pg == 'categories' ? 'active' : ''; ?>">
                <i>📁</i> Categories
            </a>
            <a href="management.php?pg=manage_items" class="nav-item <?php echo $pg == 'items' ? 'active' : ''; ?>">
                <i>📦</i> Items
            </a>
            <a href="management.php?pg=account" class="nav-item <?php echo $pg == 'account' ? 'active' : ''; ?>">
                <i>👤</i> Account
            </a>
        </div>

        <div class="user-section">
            <div class="user-role">Logged in as:</div>
            <div><?php echo isset($_SESSION['is_admin']) && $_SESSION['is_admin'] ? 'Administrator' : 'User'; ?></div>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </div>

    <div class="main-content">
        <?php
        if ($pg == "dashboard") {
            include("dashboard.php");
        } elseif ($pg == "orders") {
            include("orders.php");
        } elseif ($pg == "categories") {
            include("categories.php");
        } elseif ($pg == "manage_items") {
            include("manage_items.php");
        } elseif ($pg == "manage_edit_item") {
            include("manage_edit_item.php");
        } elseif ($pg == "account") {
            include("account.php");
        } elseif ($pg == "transaction_details" && isset($_GET['transaction_id'])) {
            include("transaction_details.php");
        } else {
            echo "<h2>Page Not Found</h2>";
            echo "<p>The page you are looking for does not exist.</p>";
        }
        ?>

        <div class="footer">
            <div>Copyright © BentaPH <?php echo date('Y'); ?></div>
            <div class="footer-links">
                <a href="privacy.php">Privacy Policy</a>
                <a href="terms.php">Terms & Conditions</a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
            const closeSidebarBtn = document.querySelector('.close-sidebar');
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.querySelector('.overlay');

            function toggleSidebar() {
                sidebar.classList.toggle('active');
                overlay.classList.toggle('active');
                document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
            }

            mobileMenuToggle.addEventListener('click', toggleSidebar);
            closeSidebarBtn.addEventListener('click', toggleSidebar);
            overlay.addEventListener('click', toggleSidebar);

            // Close sidebar when clicking a nav item on mobile
            const navItems = document.querySelectorAll('.nav-item');
            navItems.forEach(item => {
                item.addEventListener('click', () => {
                    if (window.innerWidth <= 768) {
                        toggleSidebar();
                    }
                });
            });

            // Handle window resize
            window.addEventListener('resize', () => {
                if (window.innerWidth > 768) {
                    sidebar.classList.remove('active');
                    overlay.classList.remove('active');
                    document.body.style.overflow = '';
                }
            });
        });
    </script>
</body>
</html>
