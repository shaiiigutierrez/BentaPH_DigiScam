<?php
include("../connect.php");
$transaction_id = $_GET["transaction_id"];

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: management.php");
    exit;
}

// Check if user is admin (only admins can view transaction details)
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: management.php");
    exit;
}


// Use transaction_id instead of id
if (!isset($_GET['transaction_id'])) {
    header("Location: management.php?pg=orders");
    exit;
}

if(isset($_POST["action"])) {
    $new_status = "";
    
    if($_POST["action"] == "approve") {
        $new_status = "Approved";
    }
    
    if($_POST["action"] == "cancel") {
        $new_status = "Cancelled";
        $q = mysqli_query($con, "SELECT itemname, quantity FROM transaction WHERE transaction_id = '$transaction_id'");
        while($r = mysqli_fetch_array($q)) {
            $item = $r["itemname"];
            $qty = $r["quantity"];
            mysqli_query($con, "UPDATE products SET quantity = quantity + $qty WHERE item = '$item'");
        }
    }
    
    if($_POST["action"] == "complete") {
        $new_status = "Completed";
    }
    
    if($new_status != "") {
        mysqli_query($con, "UPDATE transaction SET status = '$new_status' WHERE transaction_id = '$transaction_id'");
        echo "<script>window.location='transaction_details.php?transaction_id=$transaction_id&status_updated=1';</script>";
    }
}

$q = mysqli_query($con, "SELECT DISTINCT
    t.transaction_id,
    t.fullname,
    u.address,
    u.contactnumber,
    t.shippingfee,
    t.totalamount,
    t.status,
    t.date
    FROM transaction t
    LEFT JOIN usersaccount u ON t.fullname = u.fullname
    WHERE t.transaction_id = '$transaction_id'
    LIMIT 1");

$transaction = mysqli_fetch_array($q);

// Store items in an array first
$items_array = array();
$items_q = mysqli_query($con, "SELECT 
    itemname,
    quantity,
    image,
    subtotal
    FROM transaction 
    WHERE transaction_id = '$transaction_id'");

// Calculate subtotal and store items
$grand_subtotal = 0;
while ($item = mysqli_fetch_array($items_q)) {
    $items_array[] = $item;  // Store each item in array
    $grand_subtotal += $item['subtotal'];
}
$total_amount = number_format($grand_subtotal + $transaction['shippingfee'], 2);
$grand_subtotal_fmt = number_format($grand_subtotal, 2);

// Use the transaction info
$customer_name = $transaction['fullname'];
$delivery_address = $transaction['address'];
$contact_number = $transaction['contactnumber'];
$status = strtolower($transaction['status']);
$is_pending = ($transaction['status'] === 'Pending');
$is_approved = ($transaction['status'] === 'Approved');
$can_cancel = ($transaction['status'] === 'Pending' || $transaction['status'] === 'Approved');

// Prepare formatted values
$shipping_fee = number_format($transaction['shippingfee'], 2);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Details - BentaPH</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: Arial, sans-serif; 
            background: #f5f5f5; 
            color: #333;
        }
        .container {
            max-width: 1000px;
            margin: 30px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        .header h1 {
            font-size: 24px;
            margin-bottom: 10px;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: bold;
        }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-approved { background: #d1ecf1; color: #0c5460; }
        .status-cancelled { background: #f8d7da; color: #721c24; }
        .status-completed { background: #d4edda; color: #155724; }
        .section {
            margin-bottom: 30px;
        }
        .section h2 {
            font-size: 18px;
            margin-bottom: 15px;
            color: #555;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        .info-item {
            margin-bottom: 15px;
        }
        .info-label {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }
        .info-value {
            font-size: 16px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th,
        .items-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .items-table th {
            background: #f8f9fa;
            font-weight: 500;
        }
        .amount-section {
            margin-top: 20px;
            text-align: right;
        }
        .amount-row {
            margin-bottom: 10px;
            font-size: 16px;
        }
        .amount-row.total {
            font-size: 20px;
            font-weight: bold;
            padding-top: 10px;
            border-top: 2px solid #eee;
        }
        .actions {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.3s ease;
        }
        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        .btn-approve { 
            background: #28a745; 
            color: white; 
        }
        .btn-approve:hover {
            background: #218838;
        }
        .btn-cancel { 
            background: #dc3545; 
            color: white; 
        }
        .btn-cancel:hover {
            background: #c82333;
        }
        .btn-complete { 
            background: #17a2b8; 
            color: white; 
        }
        .btn-complete:hover {
            background: #138496;
        }
        .btn-back { 
            background: #6c757d; 
            color: white; 
        }
        .btn-back:hover {
            background: #5a6268;
        }
        .alert {
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .action-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .stock-info {
            font-size: 12px;
            color: #6c757d;
            margin-top: 5px;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        .btn-approve {
            position: relative;
            overflow: hidden;
        }
        .btn-approve:before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, rgba(255,255,255,0) 0%, rgba(255,255,255,0.2) 50%, rgba(255,255,255,0) 100%);
            animation: shine 2s infinite;
        }
        @keyframes shine {
            100% { left: 100%; }
        }
        .warning-note {
            margin-top: 20px; 
            padding: 15px; 
            background: #fff3cd; 
            border: 1px solid #ffeeba; 
            border-radius: 4px; 
            color: #856404;
        }
        .danger-note {
            margin-top: 20px; 
            padding: 15px; 
            background: #f8d7da; 
            border: 1px solid #f5c6cb; 
            border-radius: 4px; 
            color: #721c24;
        }
        .success-note {
            margin-top: 20px; 
            padding: 15px; 
            background: #d4edda; 
            border: 1px solid #c3e6cb; 
            border-radius: 4px; 
            color: #155724;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if (isset($_GET['status_updated'])): ?>
            <div class="alert alert-success">
                Transaction status has been updated successfully.
            </div>
        <?php elseif (isset($_GET['update_error'])): ?>
            <div class="alert alert-error">
                Error updating transaction status. Please try again.
            </div>
        <?php endif; ?>

        <div class="header">
            <h1>Transaction Details</h1>
            <p>Transaction ID: #<?php echo htmlspecialchars($transaction['transaction_id']); ?></p>
            <div style="margin-top: 10px;">
                <span class="status-badge status-<?php echo $status; ?>">
                    <?php echo htmlspecialchars($transaction['status']); ?>
                </span>
            </div>
        </div>

        <div class="section">
            <h2>Customer Information</h2>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Full Name</div>
                    <div class="info-value"><?php echo htmlspecialchars($customer_name); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Contact Number</div>
                    <div class="info-value"><?php echo htmlspecialchars($contact_number); ?></div>
                </div>
                <div class="info-item" style="grid-column: 1 / -1;">
                    <div class="info-label">Delivery Address</div>
                    <div class="info-value"><?php echo htmlspecialchars($delivery_address); ?></div>
                </div>
            </div>
        </div>

        <div class="section">
            <h2>Order Details</h2>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items_array as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['itemname']); ?></td>
                        <td><?php echo (int)$item['quantity']; ?></td>
                        <td>₱<?php echo number_format($item['subtotal'] / max(1, $item['quantity']), 2); ?></td>
                        <td>₱<?php echo number_format($item['subtotal'], 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="amount-section">
                <div class="amount-row">
                    <span style="display: inline-block; width: 150px;">Subtotal:</span>
                    <span>₱<?php echo $grand_subtotal_fmt; ?></span>
                </div>
                <div class="amount-row">
                    <span style="display: inline-block; width: 150px;">Shipping Fee:</span>
                    <span>₱<?php echo $shipping_fee; ?></span>
                </div>
                <div class="amount-row total">
                    <span style="display: inline-block; width: 150px;">Total Amount:</span>
                    <span>₱<?php echo $total_amount; ?></span>
                </div>
            </div>
        </div>

        <div class="actions">
            <a href="management.php?pg=orders" class="btn btn-back">Back to Transactions</a>
            <div class="action-buttons">
                <form method="POST" style="display: inline;">
                    <?php if ($is_pending): ?>
                        <button type="submit" name="action" value="approve" class="btn btn-approve"
                                onclick="return confirm('Are you sure you want to approve this transaction?');"
                                style="background: #28a745; font-weight: bold;">
                            ✓ Approve Order
                        </button>
                    <?php endif; ?>

                    <?php if ($is_approved): ?>
                        <button type="submit" name="action" value="complete" class="btn btn-complete"
                                onclick="return confirm('Are you sure you want to mark this transaction as completed?');">
                            ✓ Complete Order
                        </button>
                    <?php endif; ?>

                    <?php if ($can_cancel): ?>
                        <button type="submit" name="action" value="cancel" class="btn btn-cancel"
                                onclick="return confirm('⚠️ Cancel Order?\n\nThis will:\n• Cancel the transaction\n• Restore all items to inventory\n• Cannot be undone\n\nProceed?');">
                            ✕ Cancel Order
                        </button>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <?php if ($is_pending): ?>
            <div class="warning-note">
                <strong>Note:</strong> This order is pending approval. You can approve or cancel this transaction.
                <?php if ($can_cancel): ?>
                    <br><strong>Cancellation:</strong> Will restore all items in this transaction back to inventory.
                <?php endif; ?>
            </div>
        <?php elseif ($is_approved): ?>
            <div class="warning-note">
                <strong>Note:</strong> This order has been approved. You can complete or cancel this transaction.
                <?php if ($can_cancel): ?>
                    <br><strong>Cancellation:</strong> Will restore all items in this transaction back to inventory.
                <?php endif; ?>
            </div>
        <?php elseif ($transaction['status'] === 'Cancelled'): ?>
            <div class="danger-note">
                <strong>Note:</strong> This order has been cancelled and cannot be modified.
                <br><strong>Inventory:</strong> All items have been restored to stock.
            </div>
        <?php elseif ($transaction['status'] === 'Completed'): ?>
            <div class="success-note">
                <strong>Note:</strong> This order has been completed successfully.
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
<?php mysqli_close($con); ?>
