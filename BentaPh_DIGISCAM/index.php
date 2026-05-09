<?php
session_start();
include("connect.php");
$page = isset($_GET['pg']) ? $_GET['pg'] : 'login';

if ($page == 'register') {
    include('register.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DigiScam - Login</title>
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

        .highlight {
            color: #fff;
            font-weight: 500;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
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

        .login-container {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 420px;
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

        .password-container {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: none;
            color: #6c757d;
            cursor: pointer;
            padding: 0;
            display: flex;
            align-items: center;
        }

        .btn-login {
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

        .btn-login:hover {
            background-color: #212529;
            transform: translateY(-1px);
        }

        .register-text {
            text-align: center;
            color: #6c757d;
            font-size: 0.95rem;
            margin-top: 1.5rem;
        }

        .register-link {
            color: #343a40;
            text-decoration: none;
            font-weight: 500;
        }

        .register-link:hover {
            text-decoration: underline;
        }

        .alert {
            background-color: #f8d7da;
            color: #721c24;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            font-size: 0.95rem;
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

            .login-container {
                margin: 0 auto;
                justify-self: center;
            }
        }

        @media (max-width: 576px) {
            .welcome-title {
                font-size: 2rem;
            }
            
            .login-container {
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
            <p class="welcome-text">Your premiere destination for online shopping in the Philippines. We are committed to providing a seamless and enjoyable shopping experience for all our customers.</p>
            <p class="welcome-text">Sounds shady, feels amazing. We promise, no actual scamming here.</p>
        </div>

        <div class="login-container">
            <div class="brand-container">
                <img src="logo.png" alt="DigiScam Logo" class="brand-logo">
                <h1 class="brand-name">DigiScam</h1>
                <p class="brand-tagline">Your Hunt for Affordable Digital Cameras</p>
            </div>

            <?php
            if (isset($_POST["btnlogin"])) {
                $username = $_POST["username"];
                $password = $_POST["password"];
                
                $admin = mysqli_query($con, "SELECT * FROM administrators WHERE username='$username' AND BINARY password='$password'");
                
                if (mysqli_num_rows($admin) > 0) {
                    $_SESSION["username"] = $username;
                    $_SESSION["is_admin"] = true;
                    echo "<script>window.location.href = 'administrator/management.php';</script>";
                    exit();
                }
                
                $user = mysqli_query($con, "SELECT * FROM usersaccount WHERE username='$username' AND BINARY password='$password'");
                
                if (mysqli_num_rows($user) > 0) {
                    $_SESSION["username"] = $username;
                    $_SESSION["is_admin"] = false;
                    echo "<script>window.location.href = 'main.php';</script>";
                    exit();
                }
                
                echo '<div class="alert">Invalid username or password. Please try again.</div>';
            }
            ?>

            <form method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" name="username" id="username" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="password-container">
                        <input type="password" name="password" id="password" class="form-control" required>
                        <button type="button" id="togglePassword" class="password-toggle">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 5C7 5 2.73 8.11 1 12c1.73 3.89 6 7 11 7s9.27-3.11 11-7c-1.73-3.89-6-7-11-7zm0 12c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8a3 3 0 100 6 3 3 0 000-6z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" name="btnlogin" class="btn-login">Sign In</button>

                <div class="register-text">
                    Don't have an account? <a href="index.php?pg=register" class="register-link">Register here</a>
                </div>
            </form>
        </div>
    </div>

    <script>
    var pwdToggle = document.getElementById('togglePassword');
    var pwdInput = document.getElementById('password');
    var showPath = 'M12 5C7 5 2.73 8.11 1 12c1.73 3.89 6 7 11 7s9.27-3.11 11-7c-1.73-3.89-6-7-11-7zm0 12c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8a3 3 0 100 6 3 3 0 000-6z';
    var hidePath = 'M12 5C7 5 2.73 8.11 1 12c.58 1.3 1.5 2.47 2.62 3.45l1.42-1.42A7.97 7.97 0 013.07 12c1.61-3.13 5.06-5.5 8.93-5.5 1.61 0 3.13.38 4.45 1.07l1.42-1.42A10.97 10.97 0 0012 5zm0 14c-1.61 0-3.13-.38-4.45-1.07l-1.42 1.42A10.97 10.97 0 0012 19c5 0 9.27-3.11 11-7-1.73-3.89-6-7-11-7-.34 0-.67.02-1 .05l1.45 1.45C13.13 6.02 13.56 6 14 6c2.76 0 5 2.24 5 5 0 .44-.02.87-.05 1.29l1.45 1.45c.03-.33.05-.66.05-1.04 0-4.89-4.27-8-9-8z';
    
    pwdToggle.onclick = function() {
        pwdInput.type = pwdInput.type === 'password' ? 'text' : 'password';
        this.querySelector('svg path').setAttribute('d', pwdInput.type === 'password' ? showPath : hidePath);
    };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

