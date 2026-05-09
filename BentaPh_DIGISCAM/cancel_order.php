<?php
if(!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['username'])) {
    echo '<script>window.location.href = "index.php";</script>';
    exit();
}

// No deletion here!
echo "<script>
    alert('Order cancelled. Returning to cart.');
    window.location.href = 'main.php?pg=cart';
</script>";
?>