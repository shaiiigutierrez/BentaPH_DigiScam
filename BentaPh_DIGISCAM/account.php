<?php
if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['username'])) {
    echo '<script>window.location.href = "index.php";</script>';
    exit;
}

include("connect.php");
if (!$con) {
    echo '<script>alert("Connection failed!"); window.location.href = "index.php";</script>';
    exit;
}

$username = $_SESSION['username'];

// Fetch user details
$user_query = "SELECT * FROM usersaccount WHERE username = '$username'";
$user_result = mysqli_query($con, $user_query);
if (!$user_result) {
    echo '<script>alert("Error fetching user details!"); window.location.href = "index.php";</script>';
    exit;
}
$user = mysqli_fetch_array($user_result);

// Handle profile update
if (isset($_POST['update_profile'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $new_address = $_POST['address'];
    $new_contactnumber = $_POST['contactnumber'];
    $new_email = $_POST['email'];

    // Password and contact number validation using preg_match
    if (!empty($new_password)) {
        if ($new_password !== $confirm_password) {
            $error = "Passwords do not match!";
            $new_password = $confirm_password = "";
        } elseif (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z\d])[A-Za-z\d\S]{8,}$/', $new_password)) {
            $error = "Password must be at least 8 characters long and include letters, numbers, and symbols.";
            $new_password = $confirm_password = "";
        }
    }

    if (!isset($error)) {
        // Contact number validation using preg_match
        if (!preg_match('/^09\d{9}$/', $new_contactnumber)) {
            $error = "Contact number must be exactly 11 digits and start with '09'.";
        }
    }

    if (!isset($error)) {
        // Check if email already exists for another user
        $email_check = mysqli_query($con, "SELECT * FROM usersaccount WHERE email = '$new_email' AND username != '$username'");
        if (mysqli_num_rows($email_check) > 0) {
            $error = "Email already exists!";
        } else {
            if (!empty($new_password)) {
                // Verify current password
                if ($current_password === $user['password']) {
                    // Update password and other details
                    $update_query = "UPDATE usersaccount SET 
                                   password = '$new_password',
                                   address = '$new_address',
                                   contactnumber = '$new_contactnumber',
                                   email = '$new_email'
                                   WHERE username = '$username'";
                    if (mysqli_query($con, $update_query)) {
                        $success = "Profile updated successfully!";
                        $user['address'] = $new_address;
                        $user['contactnumber'] = $new_contactnumber;
                        $user['email'] = $new_email;
                        $user['password'] = $new_password;
                    } else {
                        $error = "Error updating profile!";
                    }
                } else {
                    $error = "Current password is incorrect";
                }
            } else {
                // Only update address, contact number and email
                $update_query = "UPDATE usersaccount SET 
                               address = '$new_address',
                               contactnumber = '$new_contactnumber',
                               email = '$new_email'
                               WHERE username = '$username'";
                if (mysqli_query($con, $update_query)) {
                    $success = "Profile updated successfully!";
                    $user['address'] = $new_address;
                    $user['contactnumber'] = $new_contactnumber;
                    $user['email'] = $new_email;
                } else {
                    $error = "Error updating profile!";
                }
            }
        }
    }
}

// Fetch transactions
$query = "SELECT t.*, r.fullname, r.contactnumber, r.address 
          FROM transaction t 
          LEFT JOIN usersaccount r ON t.fullname = r.fullname 
          WHERE t.fullname = '{$user['fullname']}' 
          ORDER BY t.date DESC";
$result = mysqli_query($con, $query);
if (!$result) {
    echo '<script>alert("Error fetching transactions!"); window.location.href = "index.php";</script>';
    exit;
}

?>

<div class="account-container">
    <div class="account-header">
        <h1>My Account</h1>
        <?php if (isset($success)) { ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php } ?>

        <?php if (isset($error)) { ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php } ?>
    </div>

    <div class="account-grid">
        <div class="profile-card">
            <div class="card-header">
                <h2>Profile Information</h2>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="profile-grid">
                        <!-- Personal Information Column -->
                        <div class="form-section">
                            <h3>Personal Information</h3>
                            <div class="form-group">
                                <label>Full Name</label>
                                <div class="readonly-field"><?php echo htmlspecialchars($user['fullname']); ?></div>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Contact Number</label>
                                <input type="number" name="contactnumber" class="form-control" value="<?php echo htmlspecialchars($user['contactnumber']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Address</label>
                                <input type="text" name="address" class="form-control" value="<?php echo htmlspecialchars($user['address']); ?>" required>
                            </div>
                        </div>

                        <!-- Password Change Column -->
                        <div class="form-section">
                            <h3>Change Password</h3>
                            <div class="form-group">
                                <label>Current Password</label>
                                <div class="password-input-group">
                                    <input type="password" name="current_password" class="form-control" id="current_password">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>New Password</label>
                                <div class="password-input-group">
                                    <input type="password" name="new_password" class="form-control" id="new_password" onkeyup="validatePassword()">
                                </div>
                                <span id="passwordError" class="error-message"></span>
                            </div>
                            <div class="form-group">
                                <label>Confirm Password</label>
                                <div class="password-input-group">
                                    <input type="password" name="confirm_password" class="form-control" id="confirm_password">
                                </div>
                            </div>
                            <div class="form-group show-password">
                                <label class="checkbox-label">
                                    <input type="checkbox" id="togglePassword" onclick="togglePasswordVisibility()">
                                    Show Password
                                </label>
                            </div>
                            <small class="help-text">Leave password fields empty if you only want to update other information</small>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="orders-card">
            <div class="card-header">
                <h2>Order History</h2>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="orders-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Full Name</th>
                                <th>Delivery Address</th>
                                <th>Contact Number</th>
                                <th>Subtotal</th>
                                <th>Shipping Fee</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $transactions = array();
                            $subtotals = array();
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_array($result)) {
                                    $tid = $row['transaction_id'];
                                    if (!isset($transactions[$tid])) {
                                        $transactions[$tid] = $row;
                                        $subtotals[$tid] = 0;
                                    }
                                    $subtotals[$tid] += $row['subtotal'];
                                }
                                foreach ($transactions as $tid => $row) { ?>
                                    <tr>
                                        <td><?php echo $row['date']; ?></td>
                                        <td><?php echo $row['fullname']; ?></td>
                                        <td><?php echo $row['address']; ?></td>
                                        <td><?php echo $row['contactnumber']; ?></td>
                                        <td>₱<?php echo number_format($subtotals[$tid], 2); ?></td>
                                        <td>₱<?php echo number_format($row['shippingfee'], 2); ?></td>
                                        <td>₱<?php echo number_format($subtotals[$tid] + $row['shippingfee'], 2); ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo strtolower($row['status']); ?>">
                                                <?php echo $row['status']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="main.php?pg=view_transaction&transaction_id=<?php echo $row['transaction_id']; ?>" class="btn btn-view">View Details</a>
                                        </td>
                                    </tr>
                                <?php }
                            } else { ?>
                                <tr>
                                    <td colspan="9" class="no-records">No transactions found.</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
:root {
    --primary-color: #4a90e2;
    --success-color: #28a745;
    --danger-color: #dc3545;
    --warning-color: #ffc107;
    --info-color: #17a2b8;
    --text-color: #333;
    --border-color: #e0e0e0;
    --background-color: #f8f9fa;
    --card-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    --input-focus: #edf2ff;
}

.account-container {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.account-header {
    margin-bottom: 2rem;
}

.account-header h1 {
    color: var(--text-color);
    font-size: 2rem;
    margin-bottom: 1rem;
}

.account-grid {
    display: grid;
    gap: 2rem;
    grid-template-columns: 1fr;
}

.profile-card,
.orders-card {
    background: white;
    border-radius: 8px;
    box-shadow: var(--card-shadow);
    overflow: hidden;
}

.card-header {
    background: var(--background-color);
    padding: 1.5rem;
    border-bottom: 1px solid var(--border-color);
}

.card-header h2 {
    margin: 0;
    color: var(--text-color);
    font-size: 1.5rem;
}

.card-body {
    padding: 1.5rem;
}

.profile-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
}

.form-section {
    padding: 0;
    border-bottom: none;
}

.form-section h3 {
    font-size: 1.2rem;
    color: var(--text-color);
    margin-bottom: 1.5rem;
    font-weight: 500;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid var(--border-color);
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: var(--text-color);
    font-weight: 500;
    font-size: 0.95rem;
}

.readonly-field {
    padding: 0.75rem;
    background: var(--background-color);
    border-radius: 4px;
    color: var(--text-color);
    font-size: 0.95rem;
}

.form-control {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid var(--border-color);
    border-radius: 4px;
    font-size: 0.95rem;
    transition: all 0.2s ease;
    background: white;
}

.form-control:focus {
    border-color: var(--primary-color);
    background: var(--input-focus);
    outline: none;
    box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
}

.password-input-group {
    position: relative;
}

.help-text {
    display: block;
    margin-top: 0.75rem;
    color: #666;
    font-size: 0.85rem;
}

.show-password {
    margin-top: 0.5rem;
    display: flex;
    align-items: center;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    font-weight: normal;
    margin: 0;
    color: #666;
    font-size: 0.9rem;
}

.checkbox-label input[type="checkbox"] {
    margin: 0;
    cursor: pointer;
}

.form-actions {
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid var(--border-color);
}

.account-container .btn-primary {
    background: #28a745;
    color: white;
    padding: 0.75rem 2rem;
    font-size: 0.95rem;
    border-radius: 4px;
    border: none;
    transition: all 0.2s ease;
}

.account-container .btn-primary:hover {
    background: #218838;
    transform: translateY(-1px);
}

.account-container .btn-primary:active {
    transform: translateY(0);
}

.table-responsive {
    overflow-x: auto;
    margin: 0 -1.5rem;
    padding: 0 1.5rem;
}

.orders-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 1rem;
}

.orders-table th,
.orders-table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid var(--border-color);
}

.orders-table th {
    background: var(--background-color);
    font-weight: 500;
    color: var(--text-color);
}

.status-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 50px;
    font-size: 0.875rem;
    font-weight: 500;
}

.status-pending {
    background: #fff3cd;
    color: #856404;
}

.status-approved {
    background: #d1ecf1;
    color: #0c5460;
}

.status-cancelled {
    background: #f8d7da;
    color: #721c24;
}

.status-completed {
    background: #d4edda;
    color: #155724;
}

.alert {
    padding: 1rem;
    border-radius: 4px;
    margin-bottom: 1rem;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.no-records {
    text-align: center;
    color: #666;
    padding: 2rem !important;
}

@media (max-width: 768px) {
    .account-grid {
        grid-template-columns: 1fr;
    }

    .profile-grid {
        grid-template-columns: 1fr;
        gap: 2rem;
    }

    .form-section {
        padding: 1.5rem 0;
        border-bottom: 1px solid var(--border-color);
    }

    .form-section:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .orders-table {
        display: block;
        overflow-x: auto;
        white-space: nowrap;
    }
}

.account-container .btn-view {
    background: rgb(35, 139, 168);
    color: white;
    padding: 0.4rem 0.8rem;
    font-size: 0.8rem;
    border-radius: 4px;
    text-decoration: none;
    transition: all 0.2s ease;
    white-space: nowrap;
}

.account-container .btn-view:hover {
    background: rgb(92, 165, 194);
    transform: translateY(-1px);
}

.account-container .btn-view:active {
    transform: translateY(0);
}
</style>

<script>
    function validatePassword() {
        const passwordField = document.getElementById("new_password");
        const errorSpan = document.getElementById("passwordError");
        const password = passwordField.value;

        // At least 8 chars, 1 letter, 1 number, 1 symbol
        const regex = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z\d])[A-Za-z\d\S]{8,}$/;
        if (password.length > 0 && !regex.test(password)) {
            errorSpan.textContent = "Password must be at least 8 characters long and include letters, numbers, and symbols.";
        } else {
            errorSpan.textContent = "";
        }
    }

    function togglePasswordVisibility() {
        const currentPasswordField = document.getElementById("current_password");
        const newPasswordField = document.getElementById("new_password");
        const confirmPasswordField = document.getElementById("confirm_password");
        const toggleCheckbox = document.getElementById("togglePassword");

        const type = toggleCheckbox.checked ? "text" : "password";
        currentPasswordField.type = type;
        newPasswordField.type = type;
        confirmPasswordField.type = type;
    }
</script>
</body>

</html>