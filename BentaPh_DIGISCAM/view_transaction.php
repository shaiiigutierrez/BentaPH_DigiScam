<?php
if (!isset($_SESSION['username'])) {
    echo '<script>window.location.href = "index.php";</script>';
    exit;
}

include("connect.php");
if (!$con) {
    echo '<script>alert("Connection failed!"); window.location.href = "index.php";</script>';
    exit;
}

$username = $_SESSION['username'];
$user = mysqli_fetch_array(mysqli_query($con, "SELECT fullname FROM usersaccount WHERE username = '$username'"));

if (!$user) {
    echo '<script>alert("User not found!"); window.location.href = "main.php?pg=account";</script>';
    exit;
}

if (!isset($_GET['transaction_id'])) {
    echo '<script>window.location.href = "main.php?pg=account";</script>';
    exit;
}

$fullname = $user['fullname'];
$transaction_id = $_GET['transaction_id'];

if (isset($_POST['cancel_order'])) {
    $trans = mysqli_query($con, "SELECT * FROM transaction WHERE transaction_id = '$transaction_id' AND fullname = '$fullname' AND (status = 'Pending' OR status = 'Approved')");
    
    if (mysqli_num_rows($trans) > 0) {
        while ($item = mysqli_fetch_array($trans)) {
            $itemname = $item['itemname'];
            $quantity = $item['quantity'];
            mysqli_query($con, "UPDATE products SET quantity = quantity + $quantity WHERE item = '$itemname'");
        }
        
        mysqli_query($con, "UPDATE transaction SET status = 'Cancelled' WHERE transaction_id = '$transaction_id' AND fullname = '$fullname' AND (status = 'Pending' OR status = 'Approved')");
        echo '<script>window.location.href = "main.php?pg=view_transaction&transaction_id=' . $transaction_id . '&cancelled=1";</script>';
        exit;
    } else {
        echo '<script>alert("Order not found or already cancelled!"); window.location.href = "main.php?pg=account";</script>';
        exit;
    }
}

$transaction = mysqli_fetch_array(mysqli_query($con, "SELECT t.*, u.contactnumber, u.address 
    FROM transaction t 
    LEFT JOIN usersaccount u ON t.fullname = u.fullname 
    WHERE t.transaction_id = '$transaction_id' AND t.fullname = '$fullname' LIMIT 1"));

if (!$transaction) {
    echo '<script>alert("Transaction not found or access denied!"); window.location.href = "main.php?pg=account";</script>';
    exit;
}

$items = mysqli_query($con, "SELECT itemname, image, price, quantity, subtotal FROM transaction 
    WHERE transaction_id = '$transaction_id' AND fullname = '$fullname'");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Details - BentaPH</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body { 
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        .transaction-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            transition: box-shadow 0.3s ease;
            border: none;
        }
        .transaction-card:hover {
            box-shadow: 0 4px 20px rgba(0,0,0,0.12);
        }
        .table {
            margin-bottom: 0;
        }
        .table th {
            font-weight: 600;
            color: #343a40;
            background-color: #f8f9fa;
            border-bottom-width: 1px;
        }
        .table td {
            vertical-align: middle;
            color: #495057;
        }
        .product-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }
        .status-badge {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 500;
        }
        .status-pending { background-color: #fff3cd; color: #856404; }
        .status-approved { background-color: #d1ecf1; color: #0c5460; }
        .status-cancelled { background-color: #f8d7da; color: #721c24; }
        .status-completed { background-color: #d4edda; color: #155724; }
        .btn-outline-dark {
            border: 1.5px solid #343a40;
            color: #343a40;
            padding: 8px 20px;
        }
        .btn-outline-dark:hover {
            background-color: #343a40;
            color: #fff;
        }
        .btn-danger {
            background-color: #dc3545;
            border: none;
            padding: 8px 20px;
            transition: all 0.2s ease;
        }
        .btn-danger:hover {
            background-color: #c82333;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(220, 53, 69, 0.2);
        }
        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            padding: 0.5rem 0;
            border-bottom: 1px solid #e9ecef;
            font-size: 1rem;
        }
        .summary-item.total-price {
            border-bottom: none;
            font-weight: 700;
            font-size: 1.5rem;
            color: #198754;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 2px solid #e9ecef;
        }
        @media (max-width: 768px) {
            .d-flex.gap-2 {
                flex-direction: column;
                gap: 0.5rem !important;
            }
            .btn {
                width: 100%;
            }
            .summary-item.total-price {
                font-size: 1.25rem;
            }
        }
    </style>
</head>
<body data-page="view_transaction">
    <div class="container py-5">
        <?php 
        if (isset($_GET['cancelled'])) {
            echo '<div class="alert alert-success" role="alert">
                <i class="fas fa-check-circle me-2"></i>Order has been cancelled successfully.
            </div>';
        }
        ?>

        <div class="transaction-card p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">Transaction Details</h4>
                <span class="status-badge status-<?php echo strtolower($transaction['status']); ?>">
                    <?php echo $transaction['status']; ?>
                </span>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <h6 class="text-muted mb-3">Customer Information</h6>
                    <p class="mb-2"><strong>Transaction ID:</strong> #<?php echo $transaction['transaction_id']; ?></p>
                    <p class="mb-2"><strong>Name:</strong> <?php echo $transaction['fullname']; ?></p>
                    <p class="mb-2"><strong>Contact:</strong> <?php echo $transaction['contactnumber']; ?></p>
                    <p class="mb-2"><strong>Address:</strong> <?php echo $transaction['address']; ?></p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $grand_subtotal = 0;
                        while ($item = mysqli_fetch_array($items)) {
                            $grand_subtotal += $item['subtotal'];
                            echo "<tr>
                                <td>{$item['itemname']}</td>
                                <td>₱" . number_format($item['price'], 2) . "</td>
                                <td>{$item['quantity']}</td>
                                <td class='text-end'>₱" . number_format($item['subtotal'], 2) . "</td>
                            </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <div class="row mt-4">
                <div class="col-md-6 ms-auto">
                    <div class="summary-item">
                        <span>Subtotal</span>
                        <span>₱<?php echo number_format($grand_subtotal, 2); ?></span>
                    </div>
                    <div class="summary-item">
                        <span>Shipping Fee</span>
                        <span>₱<?php echo number_format($transaction['shippingfee'], 2); ?></span>
                    </div>
                    <div class="summary-item total-price">
                        <span>Total</span>
                        <span>₱<?php echo number_format($grand_subtotal + $transaction['shippingfee'], 2); ?></span>
                    </div>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <a href="main.php?pg=account" class="btn btn-outline-dark">
                    <i class="fas fa-arrow-left me-2"></i>Back to Account
                </a>
                <?php 
                if ($transaction['status'] == 'Pending' || $transaction['status'] == 'Approved') {
                    echo '<form method="POST" style="display: inline;" id="cancelForm">
                        <button type="submit" name="cancel_order" class="btn btn-danger" onclick="return confirmCancel();">
                            <i class="fas fa-times-circle me-2"></i>Cancel Order
                        </button>
                    </form>';
                }
                ?>
            </div>
        </div>
    </div>

    <script>
    function confirmCancel() {
        return confirm('Are you sure you want to cancel this order?');
    }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php mysqli_close($con); ?>