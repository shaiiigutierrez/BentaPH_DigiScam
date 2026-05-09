<?php

if (!isset($_SESSION['username']) || $_SESSION['is_admin'] != true) {
    echo "<script>window.location.href = 'index.php';</script>";
    exit();
}

include("../connect.php");
if (!$con) {
    echo "<script>alert('Database connection failed!');</script>";
    exit();
}

$message = "";
$username = $_SESSION['username'];
$pass_query = mysqli_query($con, "SELECT password FROM administrators WHERE username='$username'");
$pass_row = mysqli_fetch_array($pass_query);
$current_password = $pass_row['password'];

if (isset($_POST['update'])) {
    $new_password = $_POST['password'];
    if (mysqli_query($con, "UPDATE administrators SET password='$new_password' WHERE username='$username'")) {
        $message = "Password updated successfully!";
        $current_password = $new_password;
    } else {
        $message = "Error updating password. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account - BentaPH Admin</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f8f9fa;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 24px;
            color: #2c3338;
            margin-bottom: 20px;
        }

        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: center;
        }

        .success {
            background: #d4edda;
            color: #155724;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
        }

        .note {
            color: #6c757d;
            font-size: 14px;
            margin-bottom: 20px;
            font-style: italic;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #2c3338;
            font-weight: 500;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        input[type="text"]:focus {
            outline: none;
            border-color: #0d6efd;
        }

        button {
            background: #2c3338;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.3s;
        }

        button:hover {
            background: #383f45;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #6c757d;
            text-decoration: none;
            font-size: 14px;
        }

        .back-link:hover {
            color: #0d6efd;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Account</h1>

        <?php
        if ($message) {
            $class = strpos($message, 'Error') !== false ? 'error' : 'success';
            echo "<div class='message $class'>$message</div>";
        }
        ?>

        <form method="POST">
            <div class="form-group">
                <label for="password">Password</label>
                <input type="text" id="password" name="password" value="<?php echo $current_password; ?>" required>
            </div>
            <button type="submit" name="update">Update</button>
        </form>
    </div>
</body>

</html>