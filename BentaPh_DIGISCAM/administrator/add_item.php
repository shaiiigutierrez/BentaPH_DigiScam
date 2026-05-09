<?php
session_start();

include("../connect.php");
if (!$con) {
    echo "<script>alert('Database connection failed! Please try again later.'); window.location.href = 'manage_items.php';</script>";
    exit();
}

$categories = array();
$cat_query = mysqli_query($con, "SELECT * FROM classification ORDER BY name ASC");
while ($row = mysqli_fetch_array($cat_query)) {
    $categories[] = $row;
}

if (isset($_POST["btnSave"])) {
    $item = $_POST["item"];
    $category = $_POST["category"];
    $description = $_POST["description"];
    $quantity = $_POST["quantity"];
    $price = $_POST["price"];

    // Handle file upload
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        // Create administrator/items directory if it doesn't exist
        $upload_dir = __DIR__ . '/items/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $image = "administrator/items/" . basename($_FILES["image"]["name"]);
        $full_image_path = $upload_dir . basename($_FILES["image"]["name"]);

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $full_path)) {
            mysqli_query($con, "INSERT INTO products (item, category, description, quantity, price, image) 
                VALUES ('$item', '$category', '$description', $quantity, $price, '$image')");

            if ($query) {
                echo "<script>window.location.href = 'management.php?pg=manage_items';</script>";
                exit();
            }
        }
    }
    echo "<script>window.location.href = 'management.php?pg=manage_items&error=1';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Item - BentaPH</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
        }

        form {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #666;
        }

        input[type="text"],
        input[type="number"],
        select,
        textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        textarea {
            height: 100px;
            resize: vertical;
        }

        input[type="submit"] {
            background: #333;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background: #444;
        }

        .error {
            color: red;
            margin-bottom: 15px;
        }

        .item-image {
            max-width: 100%;
            height: auto;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Add Item</h2>
        <?php
        if (isset($_GET['error'])) {
            echo '<div class="error">There was an error adding the item. Please try again.</div>';
        }
        ?>

        <form method="POST" enctype="multipart/form-data">
            <label>Item Name</label>
            <input type="text" name="item" required>

            <label>Category</label>
            <select name="category" required>
                <option value="">Select a category</option>
                <?php
                foreach ($categories as $cat) {
                    echo "<option value='" . $cat['name'] . "'>" . $cat['name'] . "</option>";
                }
                ?>
            </select>

            <label>Description</label>
            <textarea name="description" required></textarea>

            <label>Quantity</label>
            <input type="number" name="quantity" min="0" required>

            <label>Price</label>
            <input type="number" step="0.01" min="0" name="price" required>

            <label>Image</label>
            <input type="file" name="image" accept=".jpg,.png" required>

            <input type="submit" name="btnSave" value="Save">
        </form>

        <?php
        if (isset($row)) {
            echo "<img src='/benta/" . $row['image'] . "' alt='" . $row['item'] . "' class='item-image'>";
        }
        ?>
    </div>
</body>

</html>