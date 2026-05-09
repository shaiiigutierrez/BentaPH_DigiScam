<?php
session_start();

// Destroy all session data
session_destroy();

// Redirect to index.php outside the admin folder
echo '<script>window.location.href = "../index.php";</script>';
exit;
?>
