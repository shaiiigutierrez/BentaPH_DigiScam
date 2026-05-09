<?php
include("../connect.php");

// Count unique transaction_id for each status
$pending_query = mysqli_query($con, "SELECT COUNT(DISTINCT transaction_id) as count FROM transaction WHERE status='Pending'");
$row = mysqli_fetch_array($pending_query);
$pending_count = $row['count'];

$approved_query = mysqli_query($con, "SELECT COUNT(DISTINCT transaction_id) as count FROM transaction WHERE status='Approved'");
$row = mysqli_fetch_array($approved_query);
$approved_count = $row['count'];

$cancelled_query = mysqli_query($con, "SELECT COUNT(DISTINCT transaction_id) as count FROM transaction WHERE status='Cancelled'");
$row = mysqli_fetch_array($cancelled_query);
$cancelled_count = $row['count'];

$completed_query = mysqli_query($con, "SELECT COUNT(DISTINCT transaction_id) as count FROM transaction WHERE status='Completed'");
$row = mysqli_fetch_array($completed_query);
$completed_count = $row['count'];
?>

<style>
.cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 24px;
    margin-top: 24px;
}

.card {
    padding: 24px;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.07);
    text-align: left;
    transition: transform 0.3s;
}

.card:hover {
    transform: translateY(-5px);
}

.card-title {
    font-size: 18px;
    margin-bottom: 15px;
    opacity: 0.9;
}

.card-number {
    font-size: 42px;
    font-weight: 600;
    margin-bottom: 20px;
}

.view-details {
    display: inline-flex;
    align-items: center;
    text-decoration: none;
    font-size: 14px;
    opacity: 0.8;
    transition: opacity 0.3s;
}

.view-details:hover {
    opacity: 1;
    text-decoration: none;
}

.view-details::after {
    content: "→";
    margin-left: 5px;
    transition: transform 0.3s;
}

.view-details:hover::after {
    transform: translateX(5px);
}

/* Responsive Design */
@media (max-width: 1024px) {
    .cards-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .cards-grid {
        grid-template-columns: 1fr;
        gap: 16px;
    }

    .card {
        padding: 20px;
    }

    .card-number {
        font-size: 36px;
    }
}

@media (max-width: 480px) {
    .cards-grid {
        margin-top: 16px;
    }

    .card {
        padding: 16px;
    }

    .card-title {
        font-size: 16px;
    }

    .card-number {
        font-size: 32px;
        margin-bottom: 15px;
    }
}
</style>

<div class="page-header">
    <h1>Dashboard</h1>  
    <div class="overview-text">Overview</div>
</div>

<div class="cards-grid">
    <div class="card" style="background:rgb(209, 192, 64); color: #fffbe6;">
        <div class="card-title">Pending Orders</div>
        <div class="card-number"><?php echo htmlspecialchars($pending_count); ?></div>
        <a href="management.php?pg=orders&status=Pending" class="view-details">
            View Details
        </a>
    </div>

    <div class="card" style="background:rgb(44, 129, 186); color: #e3f2fd;">
        <div class="card-title">Approved Orders</div>
        <div class="card-number"><?php echo htmlspecialchars($approved_count); ?></div>
        <a href="management.php?pg=orders&status=Approved" class="view-details">
            View Details
        </a>
    </div>

    <div class="card" style="background:rgb(185, 76, 74); color: #fbeee6;">
        <div class="card-title">Cancelled Orders</div>
        <div class="card-number"><?php echo htmlspecialchars($cancelled_count); ?></div>
        <a href="management.php?pg=orders&status=Cancelled" class="view-details">
            View Details
        </a>
    </div>

    <div class="card" style="background:rgb(63, 168, 94); color: #e6fbe9;">
        <div class="card-title">Completed Orders</div>
        <div class="card-number"><?php echo htmlspecialchars($completed_count); ?></div>
        <a href="management.php?pg=orders&status=Completed" class="view-details">
            View Details
        </a>
    </div>
</div>
