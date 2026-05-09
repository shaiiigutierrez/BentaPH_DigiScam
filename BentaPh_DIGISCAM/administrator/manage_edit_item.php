<?php
include("../connect.php");
$id = $_GET["id"];

$update_success = false;
if(isset($_POST["btnUpdate"])) {
    // Get the original item details before update
    $original_query = mysqli_query($con, "SELECT item FROM products WHERE id = '$id'");
    $original_item = mysqli_fetch_array($original_query);
    $original_item_name = $original_item['item'];
    
    // Get the new values from the form
    $new_item = mysqli_real_escape_string($con, $_POST["item"]);
    $category = mysqli_real_escape_string($con, $_POST["category"]);
    $description = mysqli_real_escape_string($con, $_POST["description"]);
    $quantity = intval($_POST["quantity"]);
    $price = floatval($_POST["price"]);

    if($_FILES["image"]["name"]) {
        $image = "items/" . basename($_FILES["image"]["name"]);
        if(move_uploaded_file($_FILES["image"]["tmp_name"], "../items/" . basename($_FILES["image"]["name"]))) {
            mysqli_query($con, "UPDATE products SET 
                item = '$new_item',
                category = '$category',
                description = '$description',
                quantity = '$quantity',
                price = '$price',
                image = '$image'
                WHERE id = '$id'");
            $update_success = true;
        }
    } else {
        $current_q = mysqli_query($con, "SELECT image FROM products WHERE id = $id");
        $current_item = mysqli_fetch_array($current_q);
        $image = $current_item["image"];
        
        mysqli_query($con, "UPDATE products SET 
            item = '$new_item',
            category = '$category',
            description = '$description',
            quantity = '$quantity',
            price = '$price',
            image = '$image'
            WHERE id = '$id'");
        $update_success = true;
    }
    
    // Update all cart entries (bag table) if the item name has changed
    if ($update_success && $original_item_name != $new_item) {
        // Update the bag table for all users who have this item
        mysqli_query($con, "UPDATE bag SET 
            itemname = '$new_item', 
            total = quantity * $price,
            image = '$image'
            WHERE itemname = '$original_item_name'");
            
        // Log the change for reference
        $change_date = date("Y-m-d H:i:s");
        mysqli_query($con, "INSERT INTO item_changes (original_name, new_name, change_date) 
                           VALUES ('$original_item_name', '$new_item', '$change_date')");
    }
    
    // Update price in bag table even if name hasn't changed
    if ($update_success) {
        mysqli_query($con, "UPDATE bag SET 
            total = quantity * $price
            WHERE itemname = '$new_item'");
    }
}

$q = mysqli_query($con, "SELECT * FROM products WHERE id = $id");
$item = mysqli_fetch_array($q);

$categories_query = mysqli_query($con, "SELECT name FROM classification ORDER BY name ASC");
$categories = array();
while ($category = mysqli_fetch_array($categories_query)) {
    $categories[] = $category;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Item - BentaPH</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .edit-container {
            width: 100%;
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        .edit-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.06);
            padding: 2.5rem;
            border: none;
        }
        .page-title {
            color: #2c3e50;
            font-size: 1.75rem;
            font-weight: 600;
            margin-bottom: 2rem;
            text-align: center;
        }
        .form-label {
            color: #4a5568;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        .form-control {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 0.75rem;
            transition: all 0.2s;
        }
        .form-control:focus {
            border-color: #4a5568;
            box-shadow: 0 0 0 2px rgba(74, 85, 104, 0.1);
        }
        .item-image-container {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            text-align: center;
        }
        .item-image {
            max-width: 200px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .btn {
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.2s;
        }
        .btn-primary {
            background-color: #2c3e50;
            border: none;
        }
        .btn-primary:hover {
            background-color: #34495e;
            transform: translateY(-1px);
        }
        .btn-secondary {
            background-color: #718096;
            border: none;
        }
        .btn-secondary:hover {
            background-color: #4a5568;
            transform: translateY(-1px);
        }
        .alert {
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 2rem;
        }
        .alert-success {
            background-color: #def7ec;
            border: 1px solid #31c48d;
            color: #046c4e;
        }
        .form-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        @media (max-width: 768px) {
            .edit-card {
                padding: 1.5rem;
            }
            .btn {
                width: 100%;
                margin-bottom: 0.5rem;
            }
            .button-group {
                flex-direction: column;
            }
        }
        
        /* Add styles for the select dropdown */
        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 16px 12px;
            padding-right: 2.5rem;
        }
        
        select.form-control:focus {
            border-color: #4a5568;
            box-shadow: 0 0 0 2px rgba(74, 85, 104, 0.1);
        }
    </style>
</head>
<body>
    <div class="edit-container">
        <div class="edit-card">
            <h1 class="page-title">Edit Item</h1>
            
            <?php if ($update_success): ?>
                <div class='alert alert-success'>
                    <strong>Success!</strong> Item has been updated successfully.
                    <?php if (isset($original_item_name) && $original_item_name != $new_item): ?>
                        All cart entries have also been updated.
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="form-section">
                    <div class="mb-4">
                        <label class="form-label">Item Name</label>
                        <input type="text" name="item" class="form-control" value="<?php echo htmlspecialchars($item['item']); ?>" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Category</label>
                        <select name="category" class="form-control" required>
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat['name']); ?>" 
                                        <?php echo ($item['category'] == $cat['name']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="4" required><?php echo htmlspecialchars($item['description']); ?></textarea>
                </div>

                <div class="form-section">
                    <div class="mb-4">
                        <label class="form-label">Quantity</label>
                        <input type="number" name="quantity" class="form-control" value="<?php echo htmlspecialchars($item['quantity']); ?>" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Price (PHP)</label>
                        <input type="text" name="price" class="form-control" value="<?php echo htmlspecialchars($item['price']); ?>" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Current Image</label>
                    <div class="item-image-container">
                        <img src="../<?php echo htmlspecialchars($item['image']); ?>" 
                             alt="<?php echo htmlspecialchars($item['item']); ?>" 
                             class="item-image">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">New Image (optional)</label>
                    <input type="file" name="image" class="form-control" accept=".jpg,.png">
                </div>

                <div class="d-flex justify-content-between align-items-center button-group">
                    <button type="submit" name="btnUpdate" class="btn btn-primary">Update Item</button>
                    <a href="management.php?pg=manage_items" class="btn btn-secondary">Back to Item List</a>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>