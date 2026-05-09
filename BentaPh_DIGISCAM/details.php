<?php
include("connect.php");

if (!$con) {
    echo "<script>alert('Connection failed!'); window.location.href = 'index.php';</script>";
    return;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $result = mysqli_query($con, "SELECT * FROM products WHERE id = $id");
    
    if (mysqli_num_rows($result) > 0) {
        $item = mysqli_fetch_array($result);
        $name = $item['item'];
        $image = $item['image'];
        $category = $item['category'];
        $price = $item['price'];
        $quantity = $item['quantity'];
        $description = $item['description'];
    } else {
        echo "<script>alert('Item not found!'); window.location.href = 'index.php';</script>";
        return;
    }
} else {
    echo "<script>alert('Invalid request!'); window.location.href = 'index.php';</script>";
    return;
}
?>

<div class="container my-4">
    <div class="row bg-white p-4 rounded shadow-sm">
        <div class="col-md-6">
            <img src="<?php echo $image; ?>" 
                 alt="<?php echo $name; ?>" 
                 class="img-fluid rounded" style="max-height: 400px; object-fit: contain;">
        </div>
        <div class="col-md-6">
            <h1 class="mb-3"><?php echo $name; ?></h1>
            <div class="text-muted mb-2">Category: <?php echo $category; ?></div>
            <div class="h4 text-success mb-3">₱<?php echo number_format($price, 2); ?></div>
            <div class="mb-3">In Stock: <?php echo $quantity; ?> units</div>
            <p class="mb-4"><?php echo $description; ?></p>
            
            <form action="add_to_cart.php" method="POST" class="d-flex align-items-center">
                <input type="hidden" name="item_id" value="<?php echo $id; ?>">
                <input type="number" name="quantity" value="1" min="1" 
                       max="<?php echo $quantity; ?>" class="form-control me-2" style="width: 80px;">
                <button type="submit" class="btn btn-dark me-2">Add to Cart</button>
                <a href="main.php" class="btn btn-outline-secondary">Back to Shop</a>
            </form>
        </div>
    </div>
</div>
