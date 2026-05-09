<?php
if (isset($_POST['btnSave'])) {
    $item = $_POST['item'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];
    
    $image = "items/" . basename($_FILES["image"]["name"]);
    if(move_uploaded_file($_FILES["image"]["tmp_name"], "../" . $image)) {
        mysqli_query($con, "INSERT INTO products (item, category, description, quantity, price, image) 
            VALUES ('$item', '$category', '$description', $quantity, $price, '$image')");
        echo "<script>window.location.href = 'management.php?pg=manage_items';</script>";
    }
}

// Handle delete action
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $img = mysqli_fetch_array(mysqli_query($con, "SELECT image FROM products WHERE id = $id"));
    if ($img && file_exists("../" . $img['image'])) {
        unlink("../" . $img['image']);
    }
    mysqli_query($con, "DELETE FROM products WHERE id = $id");
    echo "<script>window.location.href = 'management.php?pg=items';</script>";
}

// Get category filter from URL parameter
$filter_category = isset($_GET['filter_category']) ? $_GET['filter_category'] : '';

// Get all products with optional category filter
if (!empty($filter_category)) {
    $items = mysqli_query($con, "SELECT * FROM products WHERE category = '$filter_category' ORDER BY item ASC");
} else {
    $items = mysqli_query($con, "SELECT * FROM products ORDER BY item ASC");
}

$cat_query = mysqli_query($con, "SELECT * FROM classification ORDER BY name");
?>

<style>
.item-image {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 6px;
    border: 1px solid #ddd;
}

.category-badge {
    background: #e9ecef;
    color: #495057;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
}

.price-cell {
    font-weight: 600;
    color: #28a745;
}

.quantity-cell {
    font-weight: 500;
}

.items-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.items-table th,
.items-table td {
    padding: 15px 12px;
    text-align: left;
    border-bottom: 1px solid #eee;
}

.items-table th {
    background: #f8f9fa;
    font-weight: 600;
    color: #333;
    font-size: 14px;
}

.items-table tr:hover {
    background: #f8f9fa;
}

.image-preview {
    margin-top: 10px;
    max-width: 200px;
    max-height: 200px;
    border-radius: 6px;
    border: 1px solid #ddd;
    display: none;
}

textarea.form-control {
    height: 100px;
    resize: vertical;
}

.btn-edit, .btn-delete {
    display: inline-block;
    width: 60px;
    text-align: center;
    padding: 8px 0;
    text-decoration: none;
    border-radius: 4px;
    font-size: 12px;
    margin: 0 5px;
}

.btn-edit {
    background: #ffc107;
    color: #000;
}

.btn-edit:hover {
    background: #ffca2c;
    color: #000;
    text-decoration: none;
}

.btn-delete {
    background: #dc3545;
    color: white;
}

.btn-delete:hover {
    background: #bb2d3b;
    color: white;
    text-decoration: none;
}

.action-buttons {
    display: flex;
    gap: 10px;
    justify-content: center;
}

.btn-primary {
    background: #198754;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
}
.btn-primary:hover {
    background:rgb(52, 179, 120);
    color: white;
    text-decoration: none;
}

.content-wrapper {
    display: flex;
    gap: 30px;
    margin-bottom: 30px;
}

.add-category-section {
    flex: 0 0 350px;
    background: white;
    padding: 25px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.category-list-section {
    flex: 1;
    background: white;
    padding: 25px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    overflow-x: auto;
}

/* Responsive Design */
@media (max-width: 1024px) {
    .content-wrapper {
        gap: 20px;
    }
    
    .add-category-section {
        flex: 0 0 300px;
    }
}

@media (max-width: 768px) {
    .content-wrapper {
        flex-direction: column;
    }
    
    .add-category-section {
        flex: none;
        width: 100%;
    }
    
    .items-table {
        font-size: 14px;
    }
    
    .item-image {
        width: 50px;
        height: 50px;
    }
    
    .btn-edit, .btn-delete {
        padding: 6px 10px;
        width: auto;
    }
}

@media (max-width: 480px) {
    .items-table th,
    .items-table td {
        padding: 10px 8px;
    }
    
    .category-badge {
        font-size: 11px;
        padding: 3px 6px;
    }
    
    .action-buttons {
        flex-direction: column;
        gap: 5px;
    }
    
    .btn-edit, .btn-delete {
        width: 100%;
        margin: 0;
    }
    
    .image-preview {
        max-width: 100%;
    }
}
</style>

<div class="page-header">
    <h1>Items Management</h1>
    <div class="overview-text">Manage your product inventory</div>
</div>

<div class="content-wrapper">
    <div class="add-category-section">
        <h2 class="section-title">Add New Item</h2>
        <form method="POST" action="management.php?pg=manage_items" enctype="multipart/form-data">
            <div class="form-group">
                <label>Item Name</label>
                <input type="text" name="item" class="form-control" required placeholder="Enter item name">
            </div>

            <div class="form-group">
                <label>Category</label>
                <select name="category" class="form-control" required>
                    <option value="">Select a category</option>
                    <?php while ($cat = mysqli_fetch_array($cat_query)) { ?>
                        <option value="<?php echo htmlspecialchars($cat['name']); ?>">
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control" required placeholder="Enter item description"></textarea>
            </div>

            <div class="form-group">
                <label>Quantity</label>
                <input type="number" name="quantity" class="form-control" required min="0" placeholder="Enter quantity">
            </div>

            <div class="form-group">
                <label>Price (₱)</label>
                <input type="number" step="0.01" min="0" name="price" class="form-control" required placeholder="0.00">
            </div>

            <div class="form-group">
                <label>Product Image</label>
                <input type="file" name="image" class="form-control" required onchange="previewImage(this)">
                <img id="imagePreview" class="image-preview" alt="Image Preview">
            </div>

            <button type="submit" name="btnSave" class="btn btn-primary">Add Item</button>
        </form>
    </div>

    <div class="category-list-section">
        <h2 class="section-title">Items List</h2>
        
        <!-- Category Filter - Added this section only -->
        <div style="margin-bottom: 15px;">
            <form method="GET" action="management.php" style="display: inline-block;">
                <input type="hidden" name="pg" value="manage_items">
                <select name="filter_category" onchange="this.form.submit()" style="padding: 8px; border-radius: 4px; border: 1px solid #ddd;">
                    <option value="">All Categories</option>
                    <?php 
                    // Reset the category query pointer
                    mysqli_data_seek($cat_query, 0);
                    while ($cat = mysqli_fetch_array($cat_query)) { 
                        $selected = ($filter_category == $cat['name']) ? 'selected' : '';
                    ?>
                        <option value="<?php echo htmlspecialchars($cat['name']); ?>" <?php echo $selected; ?>>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php } ?>
                </select>
            </form>
            <?php if (!empty($filter_category)): ?>
                <a href="management.php?pg=manage_items" style="margin-left: 10px; padding: 8px 12px; background: #6c757d; color: white; text-decoration: none; border-radius: 4px; font-size: 14px;">Clear</a>
            <?php endif; ?>
        </div>
        
        <table class="items-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_array($items)) { ?>
                <tr>
                    <td>
                        <img src="../<?php echo $row['image']; ?>" class="item-image">
                    </td>
                    <td><?php echo htmlspecialchars($row['item']); ?></td>
                    <td><span class="category-badge"><?php echo htmlspecialchars($row['category']); ?></span></td>
                    <td><?php echo htmlspecialchars(substr($row['description'], 0, 50)) . (strlen($row['description']) > 50 ? '...' : ''); ?></td>
                    <td class="quantity-cell">
                        <?php
                            if ($row['quantity'] <= 0) {
                                echo "<span style='color:red;font-weight:bold;'>Sold Out</span>";
                            } else {
                                echo htmlspecialchars($row['quantity']);
                            }
                        ?>
                    </td>
                    <td class="price-cell">₱<?php echo number_format($row['price'], 2); ?></td>
                    <td class="action-buttons">
                        <a href="management.php?pg=manage_edit_item&id=<?php echo $row['id']; ?>" class="btn-edit">Edit</a>
                        <a href="manage_delete_item.php?id=<?php echo $row['id']; ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this item?')">Delete</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.style.display = 'none';
    }
}
</script>