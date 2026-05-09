<?php
include("connect.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - DigiScam</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('login_bg.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Segoe UI', Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 20px;
            margin: 0;
        }

        .page-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            width: 100%;
            max-width: 1400px;
            margin: 0 auto;
            align-items: center;
        }

        .welcome-section {
            color: #fff;
            padding: 2rem;
            opacity: 0;
            animation: fadeIn 1s ease-out forwards;
        }

        .welcome-title {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            line-height: 1.2;
            background: linear-gradient(45deg, #fff, #e0e0e0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            transform: translateY(20px);
            opacity: 0;
            animation: slideUp 0.8s ease-out 0.2s forwards;
        }

        .welcome-text {
            font-size: 1.2rem;
            line-height: 1.8;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 2rem;
            transform: translateY(20px);
            opacity: 0;
        }

        .welcome-text:nth-child(2) {
            animation: slideUp 0.8s ease-out 0.4s forwards;
        }

        .welcome-text:nth-child(3) {
            animation: slideUp 0.8s ease-out 0.6s forwards;
        }

        .register-container {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
            padding: 3rem;
            margin: 0 8%;
            justify-self: end;
        }

        .brand-container {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .brand-logo {
            width: 80px;
            height: 80px;
            margin-bottom: 1rem;
        }

        .brand-name {
            font-size: 1.8rem;
            font-weight: 600;
            color: #343a40;
            margin: 0;
        }

        .brand-tagline {
            color: #6c757d;
            font-size: 0.95rem;
            margin-top: 0.5rem;
        }

        .form-label {
            color: #495057;
            font-weight: 500;
            font-size: 0.95rem;
            margin-bottom: 0.5rem;
        }

        .form-control {
            border: 1.5px solid #dee2e6;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            border-color: #343a40;
            box-shadow: none;
        }

        .btn-register {
            background-color: #343a40;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 0.75rem;
            font-size: 1rem;
            font-weight: 500;
            width: 100%;
            margin-top: 1.5rem;
            transition: all 0.2s ease;
        }

        .btn-register:hover {
            background-color: #212529;
            transform: translateY(-1px);
        }

        .login-text {
            text-align: center;
            color: #6c757d;
            font-size: 0.95rem;
            margin-top: 1.5rem;
        }

        .login-link {
            color: #343a40;
            text-decoration: none;
            font-weight: 500;
        }

        .login-link:hover {
            text-decoration: underline;
        }

        .alert {
            padding: 0.75rem 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            font-size: 0.95rem;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .form-check {
            margin-top: 1rem;
        }

        .form-check-input:checked {
            background-color: #343a40;
            border-color: #343a40;
        }

        .text-danger {
            color: #dc3545;
            font-size: 0.85rem;
            margin-top: 0.25rem;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @media (max-width: 992px) {
            .page-container {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .welcome-section {
                text-align: center;
                padding: 1rem;
            }

            .welcome-title {
                font-size: 2.5rem;
            }

            .register-container {
                margin: 0 auto;
                justify-self: center;
            }
        }

        @media (max-width: 576px) {
            .welcome-title {
                font-size: 2rem;
            }
            
            .register-container {
                border-radius: 12px;
                padding: 2rem;
            }
        }
    </style>
</head>

<body>
    <div class="page-container">
        <div class="welcome-section">
            <h1 class="welcome-title">Welcome to DigiScam</h1>
            <p class="welcome-text">Create your account and become part of our growing community. Experience hassle-free shopping with our wide selection of digital cameras.</p>
            <p class="welcome-text">Your journey to amazing deals starts here.</p>
        </div>

        <div class="register-container">
            <div class="brand-container">
                <img src="logo.png" alt="DigiScam Logo" class="brand-logo">
                <h1 class="brand-name">DigiScam</h1>
                <p class="brand-tagline">Your Hunt for Affordable Digital Cameras</p>
            </div>

            <?php
            $message = "";
            $message_class = "";
            // Initialize variables for sticky form
            $fullname = $email = $address = $contactnumber = $username = "";

            if (isset($_POST["register"])) {
                $fullname = $_POST["fullname"];
                $email = $_POST["email"];
                $address = $_POST["address"];
                $contactnumber = $_POST["contactnumber"];
                $username = $_POST["username"];
                $password = $_POST["password"];
                $confirmpassword = $_POST["confirmpassword"];

                if ($password !== $confirmpassword) {
                    $message = "Passwords do not match!";
                    $message_class = "alert alert-danger";
                    // Clear only password fields
                    $password = $confirmpassword = "";
                }  elseif (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z\d])[A-Za-z\d\S]{8,}$/', $password)) {
                    $message = "Password must be at least 8 characters long and include letters, numbers, and symbols.";
                    $message_class = "alert alert-danger";
                    // Clear only password fields
                    $password = $confirmpassword = "";
                } elseif (!preg_match('/^09\d{9}$/', $contactnumber)) {
                    $message = "Contact number must be exactly 11 digits and start with '09'.";
                    $message_class = "alert alert-danger";
                } else {
                    $count = mysqli_num_rows(mysqli_query($con, "select * from usersaccount where username='$username'"));
                    if ($count > 0) {   
                        $message = "Username already exists!";
                        $message_class = "alert alert-danger";
                        // Clear only username field
                        $username = "";
                    } else {
                        mysqli_query($con, "insert into usersaccount(fullname, email, address, contactnumber, username, password)
                        values('$fullname','$email','$address','$contactnumber','$username','$password')");
                        $message = "Registration successful!";
                        $message_class = "alert alert-success";
                        // Clear all fields
                        $fullname = $email = $address = $contactnumber = $username = $password = $confirmpassword = "";
                    }
                }
            }
            ?>

            <?php if (!empty($message)): ?>
                <div class="<?php echo $message_class; ?>" role="alert">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label for="fullname" class="form-label">Full Name:</label>
                    <input type="text" name="fullname" class="form-control" required value="<?php echo htmlspecialchars($fullname); ?>">
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email:</label>
                    <input type="email" name="email" class="form-control" required value="<?php echo htmlspecialchars($email); ?>">
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label">Address:</label>
                    <input type="text" name="address" class="form-control" required value="<?php echo htmlspecialchars($address); ?>">
                </div>

                <div class="mb-3">
                    <label for="contact_number" class="form-label">Contact Number:</label>
                    <input type="number" name="contactnumber" class="form-control" required value="<?php echo htmlspecialchars($contactnumber); ?>">
                </div>

                <div class="mb-3">
                    <label for="username" class="form-label">Username:</label>
                    <input type="text" name="username" class="form-control" required value="<?php echo htmlspecialchars($username); ?>">
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password:</label>
                    <input type="password" name="password" class="form-control" oninput="validatePassword()" required>
                    <span id="passwordError" class="text-danger"></span>
                </div>

                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm Password:</label>
                    <input type="password" name="confirmpassword" class="form-control" required>
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" class="form-check-input" id="togglePassword" onclick="togglePasswordVisibility()">
                    <label for="togglePassword" class="form-check-label">Show Password</label>
                </div>

                <button type="submit" name="register" class="btn-register">Create Account</button>

                <div class="login-text">
                    Already have an account? <a href="index.php?pg=login" class="login-link">Sign in here</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function validatePassword() {
            const passwordField = document.getElementsByName("password")[0];
            const errorSpan = document.getElementById("passwordError");
            const password = passwordField.value;

            // Check if the password meets the criteria
            const regex = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z\d])[A-Za-z\d\S]{8,}$/;
            if (!regex.test(password)) {
                errorSpan.textContent = "Password must be at least 8 characters long and include letters, numbers, and symbols.";
            } else {
                errorSpan.textContent = ""; // Clear the error message
            }
        }

        function togglePasswordVisibility() {
            const passwordField = document.getElementsByName("password")[0];
            const confirmPasswordField = document.getElementsByName("confirmpassword")[0];
            const toggleCheckbox = document.getElementById("togglePassword");

            const type = toggleCheckbox.checked ? "text" : "password";
            passwordField.type = type;
            confirmPasswordField.type = type;
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>