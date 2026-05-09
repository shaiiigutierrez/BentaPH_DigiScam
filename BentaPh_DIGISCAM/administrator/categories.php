<?php
if(isset($_POST["action"])) {
    $action = $_POST["action"];

    if($action == "add" && isset($_POST["name"])) {
        $name = mysqli_real_escape_string($con, $_POST["name"]);

        // Check if category already exists
        $check_query = mysqli_query($con, "SELECT id FROM classification WHERE name = '$name'");
        if(mysqli_num_rows($check_query) > 0) {
            $_SESSION['message'] = "Category Already Exist";
            $_SESSION['message_type'] = 'error';
            header("Location: management.php?pg=categories");
            exit;
        } else {
            $current_time = date("Y-m-d H:i:s");

            mysqli_query($con, "INSERT INTO classification(name, created_at, updated_at) 
                VALUES('$name', '$current_time', '$current_time')");
            $_SESSION['message'] = "Category added successfully!";
            $_SESSION['message_type'] = 'success';
            header("Location: management.php?pg=categories");
            exit;
        }
    }

    if($action == "update" && isset($_POST["id"]) && isset($_POST["name"])) {
        $id = intval($_POST["id"]);
        $name = mysqli_real_escape_string($con, $_POST["name"]);

        $check_query = mysqli_query($con, "SELECT id FROM classification WHERE name = '$name' AND id != $id");
        if(mysqli_num_rows($check_query) > 0) {
            $_SESSION['message'] = "Category Already Exist";
            $_SESSION['message_type'] = 'error';
            header("Location: management.php?pg=categories");
            exit;
        } else {
            $current_time = date("Y-m-d H:i:s");

            $result = mysqli_query($con, "SELECT name FROM classification WHERE id = $id");
            if ($row = mysqli_fetch_assoc($result)) {
                $old_name = mysqli_real_escape_string($con, $row["name"]);

                mysqli_query($con, "UPDATE classification SET name = '$name', updated_at = '$current_time' WHERE id = $id");
                mysqli_query($con, "UPDATE products SET category = '$name' WHERE category = '$old_name'");
            }

            $_SESSION['message'] = "Category updated successfully!";
            $_SESSION['message_type'] = 'success';
            header("Location: management.php?pg=categories");
            exit;
        }
    }

    if($action == "delete" && isset($_POST["id"])) {
        $id = intval($_POST["id"]);
    
        $result = mysqli_query($con, "SELECT name FROM classification WHERE id = $id");
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_array($result);
            $cat_name = $row["name"];
    
            $products_result = mysqli_query($con, "SELECT id, item FROM products WHERE category = '$cat_name'");
            while ($product = mysqli_fetch_array($products_result)) {
                $product_id = $product["id"];
                $item_name = $product["item"];
    
                // Get all users who have this item in bag before deletion
                $users = mysqli_query($con, "SELECT DISTINCT username FROM bag WHERE itemname = '$item_name'");
                while ($user = mysqli_fetch_array($users)) {
                    $username = $user['username'];
                    mysqli_query($con, "INSERT INTO item_deletion (username, itemname) VALUES ('$username', '$item_name')");
                }
    
                // Delete the item from bag
                mysqli_query($con, "DELETE FROM bag WHERE itemname = '$item_name'");
    
                // Delete product
                mysqli_query($con, "DELETE FROM products WHERE id = $product_id");

            }
    
            // Delete the category
            mysqli_query($con, "DELETE FROM classification WHERE id = $id");
        }
    
        echo "<script>window.location.href = 'management.php?pg=categories';</script>";
    }
}

$q = mysqli_query($con, "SELECT * FROM classification ORDER BY name ASC");
?>

<style>
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

.section-title {
    font-size: 24px;
    margin-bottom: 20px;
    color: #2c3338;
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
}

.btn-primary {
    width: 100%;
    padding: 12px;
    background: #2c3338;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    transition: background 0.3s;
}

.btn-primary:hover {
    background: #383f45;
}

.categories-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 600px;
}

.categories-table th,
.categories-table td {
    padding: 15px;
    text-align: left;
    border-bottom: 1px solid #eee;
}

.categories-table th {
    background: #f8f9fa;
    font-weight: 600;
    color: #333;
    font-size: 14px;
}

.action-buttons {
    display: flex;
    gap: 10px;
    align-items: center;
}

.edit-form {
    display: flex;
    gap: 10px;
    align-items: center;
    flex: 1;
}

.edit-form input {
    flex: 1;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    min-width: 0;
}

.btn {
    padding: 8px 16px;
    border-radius: 4px;
    font-size: 14px;
    cursor: pointer;
    border: none;
    transition: background 0.3s;
}

.btn-edit {
    background: #ffc107;
    color: #000;
}

.btn-edit:hover {
    background: #ffca2c;
}

.btn-delete {
    background: #dc3545;
    color: white;
}

.btn-delete:hover {
    background: #bb2d3b;
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

    .section-title {
        font-size: 20px;
    }

    .categories-table {
        font-size: 14px;
    }

    .action-buttons {
        flex-wrap: wrap;
    }

    .edit-form {
        min-width: 200px;
    }
}

@media (max-width: 480px) {
    .content-wrapper > div {
        padding: 15px;
    }

    .section-title {
        font-size: 18px;
    }

    .categories-table th,
    .categories-table td {
        padding: 10px;
    }

    .action-buttons {
        flex-direction: column;
        width: 100%;
    }

    .edit-form {
        width: 100%;
        flex-direction: column;
    }

    .edit-form input {
        width: 100%;
    }

    .btn {
        width: 100%;
        text-align: center;
    }
}

.alert {
    padding: 15px;
    margin-bottom: 20px;
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
</style>

<div class="page-header">
    <h1>Categories</h1>
    <div class="overview-text">Manage product categories</div>
</div>

<?php
if (isset($_SESSION['message'])) {
    $alertClass = $_SESSION['message_type'] == 'success' ? 'alert-success' : 'alert-error';
    echo "<div class='alert {$alertClass}'>" . htmlspecialchars($_SESSION['message']) . "</div>";
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}
?>

<div class="content-wrapper">
    <div class="add-category-section">
        <h2 class="section-title">Add Category</h2>
        <form method="POST" action="management.php?pg=categories">
            <input type="hidden" name="action" value="add">
            <div class="form-group">
                <label>Category Name</label>
                <input type="text" name="name" class="form-control" required placeholder="Enter category name">
            </div>
            <button type="submit" class="btn btn-primary">Add Category</button>
        </form>
    </div>

    <div class="category-list-section">
        <h2 class="section-title">Category List</h2>
        <table class="categories-table">
            <thead>
                <tr>
                    <th>Category Name</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if(mysqli_num_rows($q) > 0) {
                    while($r = mysqli_fetch_array($q)) {
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($r["name"]); ?></td>
                        <td><?php echo date("M j, Y g:i A", strtotime($r["created_at"])); ?></td>
                        <td><?php echo date("M j, Y g:i A", strtotime($r["updated_at"])); ?></td>
                        <td>
                            <div class="action-buttons">
                                <form method="POST" action="management.php?pg=categories" class="edit-form">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="id" value="<?php echo $r["id"]; ?>">
                                    <input type="text" name="name" value="<?php echo htmlspecialchars($r["name"]); ?>" required>
                                    <button type="submit" class="btn btn-edit">Update</button>
                                </form>
                                <form method="POST" action="management.php?pg=categories">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $r["id"]; ?>">
                                    <button type="submit" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this category? This will delete all the items under this category and remove them from shopping carts.')">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php
                    }
                } else {
                    echo "<tr><td colspan='4' class='empty-state'>No categories found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
