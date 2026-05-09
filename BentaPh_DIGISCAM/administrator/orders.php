<?php
include("../connect.php");
$status = "";
if(isset($_GET["status"])) {
    $status = $_GET["status"];
}

$status_bg = "#6c757d";
if(!isset($_GET["status"])) {
    $status_bg = "#2c3338";
} else if($status == "Pending") {
    $status_bg = "#856404";
} else if($status == "Approved") {
    $status_bg = "#0c5460";
} else if($status == "Completed") {
    $status_bg = "#155724";
} else if($status == "Cancelled") {
    $status_bg = "#721c24";
}
?>
<style>
.status-filters {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 20px;
}

.status-link {
    display: inline-block;
    padding: 8px 16px;
    color: white;
    text-decoration: none;
    border-radius: 4px;
    font-size: 14px;
    transition: opacity 0.3s;
}

.status-link:hover {
    opacity: 0.9;
}

.table-container {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    overflow-x: auto;
}

.orders-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 1000px;
}

.orders-table th,
.orders-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #eee;
}

.orders-table th {
    background: #f8f9fa;
    font-weight: 600;
    color: #333;
    font-size: 14px;
}

.status-badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 4px;
    font-size: 14px;
}

.view-btn {
    display: inline-block;
    padding: 6px 12px;
    background: #2c3338;
    color: white;
    text-decoration: none;
    border-radius: 4px;
    font-size: 14px;
    transition: background 0.3s;
}

.view-btn:hover {
    background: #383f45;
}

/* Responsive Design */
@media (max-width: 768px) {
    .status-filters {
        justify-content: center;
    }

    .status-link {
        padding: 6px 12px;
        font-size: 13px;
    }

    .table-container {
        padding: 15px;
        margin: 0 -15px;
        border-radius: 0;
    }
}

@media (max-width: 480px) {
    .status-filters {
        flex-direction: column;
    }

    .status-link {
        text-align: center;
    }

    .table-container {
        padding: 10px;
    }
}
</style>

<div class="page-header">
    <h1>Transactions</h1>
    <div class="overview-text">
        <div class="status-filters">
            <a href="management.php?pg=orders" class="status-link" style="background: <?php echo !isset($_GET["status"]) ? "#2c3338" : "#6c757d"; ?>">All</a>
            <a href="management.php?pg=orders&status=Pending" class="status-link" style="background: <?php echo $status == "Pending" ? "#856404" : "#6c757d"; ?>">Pending</a>
            <a href="management.php?pg=orders&status=Approved" class="status-link" style="background: <?php echo $status == "Approved" ? "#0c5460" : "#6c757d"; ?>">Approved</a>
            <a href="management.php?pg=orders&status=Completed" class="status-link" style="background: <?php echo $status == "Completed" ? "#155724" : "#6c757d"; ?>">Completed</a>
            <a href="management.php?pg=orders&status=Cancelled" class="status-link" style="background: <?php echo $status == "Cancelled" ? "#721c24" : "#6c757d"; ?>">Cancelled</a>
        </div>
    </div>
</div>

<div class="table-container">
    <table class="orders-table">
        <thead>
            <tr style="border-bottom: 2px solid #eee;">
                <th style="padding: 12px; text-align: left;">Transaction ID</th>
                <th style="padding: 12px; text-align: left;">Full Name</th>
                <th style="padding: 12px; text-align: left;">Address</th>
                <th style="padding: 12px; text-align: left;">Contact</th>
                <th style="padding: 12px; text-align: left;">Subtotal</th>
                <th style="padding: 12px; text-align: left;">Shipping Fee</th>
                <th style="padding: 12px; text-align: left;">Total Amount</th>
                <th style="padding: 12px; text-align: left;">Status</th>
                <th style="padding: 12px; text-align: left;">Date</th>
                <th style="padding: 12px; text-align: left;">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $q = "SELECT 
                t.transaction_id,
                t.fullname,
                u.address,
                u.contactnumber,
                SUM(t.subtotal) as subtotal_sum,
                t.shippingfee,
                t.status,
                t.date,
                SUM(t.totalamount) as totalamount_sum
                FROM transaction t
                LEFT JOIN usersaccount u ON t.fullname = u.fullname";

            if($status != "") {
                $q .= " WHERE t.status='$status'";
            }
            
            $q .= " GROUP BY t.transaction_id ORDER BY 
                CASE 
                    WHEN t.status = 'Pending' THEN 1
                    WHEN t.status = 'Approved' THEN 2
                    WHEN t.status = 'Completed' THEN 3
                    WHEN t.status = 'Cancelled' THEN 4
                END,
                t.date DESC";

            $r = mysqli_query($con, $q);
            
            if(mysqli_num_rows($r) > 0) {
                while($row = mysqli_fetch_array($r)) {
                    $status_color = "";
                    if($row["status"] == "Completed") {
                        $status_color = "background: #d4edda; color: #155724;";
                    } else if($row["status"] == "Cancelled") {
                        $status_color = "background: #f8d7da; color: #721c24;";
                    } else if($row["status"] == "Approved") {
                        $status_color = "background: #d1ecf1; color: #0c5460;";
                    } else {
                        $status_color = "background: #fff3cd; color: #856404;";
                    }
                    
                    $total = $row["subtotal_sum"] + $row["shippingfee"];
                    ?>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 12px;"><?php echo $row["transaction_id"]; ?></td>
                        <td style="padding: 12px;"><?php echo $row["fullname"]; ?></td>
                        <td style="padding: 12px;"><?php echo $row["address"]; ?></td>
                        <td style="padding: 12px;"><?php echo $row["contactnumber"]; ?></td>
                        <td style="padding: 12px;">₱<?php echo number_format($row["subtotal_sum"], 2); ?></td>
                        <td style="padding: 12px;">₱<?php echo number_format($row["shippingfee"], 2); ?></td>
                        <td style="padding: 12px;">₱<?php echo number_format($total, 2); ?></td>
                        <td style="padding: 12px;">
                            <span style="display: inline-block; padding: 6px 12px; border-radius: 4px; font-size: 14px; <?php echo $status_color; ?>">
                                <?php echo $row["status"]; ?>
                            </span>
                        </td>
                        <td style="padding: 12px;"><?php echo date("M j, Y g:i A", strtotime($row["date"])); ?></td>
                        <td style="padding: 12px;">
                            <a href="management.php?pg=transaction_details&transaction_id=<?php echo $row["transaction_id"]; ?>" 
                               style="display: inline-block; padding: 6px 12px; background: #2c3338; color: white; 
                                      text-decoration: none; border-radius: 4px; font-size: 14px;"
                               title="View transaction details">View</a>
                        </td>
                    </tr>
                    <?php
                }
            } else {
                echo "<tr><td colspan='10' style='padding: 20px; text-align: center;'>No transactions found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>
