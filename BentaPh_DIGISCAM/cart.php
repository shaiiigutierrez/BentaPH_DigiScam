<?php
if(!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['username'])) {
    echo '<script>window.location.href = "index.php";</script>';
    exit();
}

include("connect.php");

// Check for cart deletion alert for this user
$alert_query = mysqli_query($con, "SELECT * FROM item_deletion WHERE username = '{$_SESSION['username']}'");

if (mysqli_num_rows($alert_query) > 0) {
    while ($row = mysqli_fetch_assoc($alert_query)) {
        $itemname = $row['itemname'];
        echo "<script>alert('The item \"$itemname\" was removed from your cart because it is no longer available.');</script>";
    }

    // After displaying, remove the alert so it doesn't show again
    mysqli_query($con, "DELETE FROM item_deletion WHERE username = '{$_SESSION['username']}'");
}

?>
<div class="container py-5">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <h2 class="mb-4 fw-bold" style="color: #343a40;">Shopping Cart</h2>
            <form id="checkoutForm" action="process_order.php" method="POST">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="selectAll" onchange="toggleAll(this)">
                                        <label class="form-check-label" for="selectAll">Select All</label>
                                    </div>
                                </th>
                                <th scope="col">Item</th>
                                <th scope="col">Price</th>
                                <th scope="col">Quantity</th>
                                <th scope="col">Subtotal</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($_SESSION['cart'])) {
                                foreach ($_SESSION['cart'] as $item_id => $item) {
                                    $name = $item['name'];
                                    $price = $item['price'];
                                    $quantity = $item['quantity'];
                                    $image = $item['image'];
                                    $subtotal = $price * $quantity;
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input item-checkbox" 
                                                       data-id="<?php echo $item_id; ?>"
                                                       data-price="<?php echo $price; ?>"
                                                       onchange="updateSelectedTotal()">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="<?php echo $image; ?>" alt="<?php echo $name; ?>" 
                                                     class="rounded" style="width: 60px; height: 60px; object-fit: cover;">
                                                <div class="ms-3">
                                                    <span><?php echo $name; ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>₱<?php echo number_format($price, 2); ?></td>
                                        <td>
                                            <div class="input-group" style="width: 130px;">
                                                <button type="button" class="btn btn-outline-secondary" onclick="updateQuantity(<?php echo $item_id; ?>, 'decrease')">-</button>
                                                <input type="number" class="form-control text-center quantity-input" value="<?php echo $quantity; ?>" 
                                                       min="1" onchange="updateQuantity(<?php echo $item_id; ?>, 'input', this.value)"
                                                       data-id="<?php echo $item_id; ?>">
                                                <button type="button" class="btn btn-outline-secondary" onclick="updateQuantity(<?php echo $item_id; ?>, 'increase')">+</button>
                                            </div>
                                        </td>
                                        <td>₱<span class="subtotal" data-id="<?php echo $item_id; ?>"><?php echo number_format($subtotal, 2); ?></span></td>
                                        <td>
                                            <button type="button" onclick="removeItem(<?php echo $item_id; ?>)" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash-alt"></i> Remove
                                            </button>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                echo '<tr><td colspan="6" class="text-center py-4">Your cart is empty</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="selected-total">
                        <h5 class="mb-0">Selected Total: <span class="fw-bold text-success">₱<span id="selectedTotal">0.00</span></span></h5>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="main.php" class="btn btn-outline-dark">
                            <i class="fas fa-arrow-left me-2"></i>Continue Shopping
                        </a>
                        <button type="button" onclick="proceedToCheckout()" class="btn btn-success">
                            <i class="fas fa-shopping-cart me-2"></i>Checkout
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.card {
    background: #fff;
    border-radius: 12px;
    transition: box-shadow 0.3s ease;
}
.card:hover {
    box-shadow: 0 8px 16px rgba(0,0,0,0.1) !important;
}
.table th {
    font-weight: 600;
    color: #343a40;
    border-bottom-width: 1px;
}
.table td {
    vertical-align: middle;
    color: #495057;
}
.form-check-input:checked {
    background-color: #198754;
    border-color: #198754;
}
.btn-outline-secondary {
    border-color: #dee2e6;
    color: #495057;
}
.btn-outline-secondary:hover {
    background-color: #f8f9fa;
    color: #343a40;
    border-color: #dee2e6;
}
.quantity-input {
    border-left: 0;
    border-right: 0;
    background-color: #fff !important;
}
.quantity-input:focus {
    box-shadow: none;
    border-color: #dee2e6;
}
.btn-danger {
    background-color: #dc3545;
    border-color: #dc3545;
}
.btn-success {
    background-color: #198754;
    border-color: #198754;
}
.btn-outline-dark {
    color: #343a40;
    border-color: #343a40;
}
.btn-outline-dark:hover {
    background-color: #343a40;
    color: #fff;
}
.alert-danger {
    background-color: #f8d7da;
    border-color: #f5c6cb;
    color: #721c24;
    border-radius: 8px;
    padding: 0.75rem 1.25rem;
    margin-bottom: 0.5rem;
}
.stock-warnings {
    animation: fadeIn 0.3s ease-in-out;
}
.text-danger.small {
    font-size: 0.8rem;
    margin-top: 0.25rem;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
@media (max-width: 768px) {
    .d-flex.gap-2 {
        flex-direction: column;
        gap: 0.5rem !important;
    }
    .btn {
        width: 100%;
    }
}
</style>

<script>
    function toggleAll(source) {
        const checkboxes = document.getElementsByClassName('item-checkbox');
        for (let checkbox of checkboxes) {
            checkbox.checked = source.checked;
        }
        updateSelectedTotal();
    }

    function updateSelectedTotal() {
        let total = 0;
        const checkboxes = document.getElementsByClassName('item-checkbox');
        
        for (let checkbox of checkboxes) {
            if (checkbox.checked) {
                const itemId = checkbox.getAttribute('data-id');
                const price = parseFloat(checkbox.getAttribute('data-price'));
                const quantity = parseInt(document.querySelector(`.quantity-input[data-id="${itemId}"]`).value);
                total += price * quantity;
            }
        }
        document.getElementById('selectedTotal').textContent = total.toFixed(2);
    }

    function updateQuantity(itemId, action, value = null) {
        let input = document.querySelector(`.quantity-input[data-id="${itemId}"]`);
        let checkbox = document.querySelector(`.item-checkbox[data-id="${itemId}"]`);
        let currentQty = parseInt(input.value);
        let newQty = currentQty;

        if (action === 'increase') {
            newQty = currentQty + 1;
        } else if (action === 'decrease' && currentQty > 1) {
            newQty = currentQty - 1;
        } else if (action === 'input') {
            newQty = parseInt(value);
            if (newQty < 1) newQty = 1;
        }

        input.value = newQty;

        // Get the fixed price from the data attribute
        const fixedPrice = parseFloat(checkbox.getAttribute('data-price'));
        const subtotal = fixedPrice * newQty;
        document.querySelector(`.subtotal[data-id="${itemId}"]`).textContent = subtotal.toFixed(2);

        // Update selected total if item is checked
        if (checkbox.checked) {
            updateSelectedTotal();
        }

        // Update the cart in the session via AJAX
        fetch('update_cart_quantity.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `item_id=${itemId}&quantity=${newQty}`
        });
    }

    function removeItem(itemId) {
        if (confirm('Are you sure you want to remove this item?')) {
            fetch('remove_from_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `item_id=${itemId}`
            }).then(() => {
                location.reload();
            });
        }
    }

    function proceedToCheckout() {
        const selectedItems = [];
        const checkboxes = document.getElementsByClassName('item-checkbox');
        let hasSelected = false;
        let total = 0;
        
        for (let checkbox of checkboxes) {
            if (checkbox.checked) {
                hasSelected = true;
                const itemId = checkbox.getAttribute('data-id');
                const quantity = document.querySelector(`.quantity-input[data-id="${itemId}"]`).value;
                const price = parseFloat(checkbox.getAttribute('data-price'));
                total += price * parseInt(quantity);
                
                // Create hidden inputs for each selected item
                const itemIdInput = document.createElement('input');
                itemIdInput.type = 'hidden';
                itemIdInput.name = 'item_ids[]';
                itemIdInput.value = itemId;
                document.getElementById('checkoutForm').appendChild(itemIdInput);
                
                const quantityInput = document.createElement('input');
                quantityInput.type = 'hidden';
                quantityInput.name = 'quantities[]';
                quantityInput.value = quantity;
                document.getElementById('checkoutForm').appendChild(quantityInput);
            }
        }

        if (!hasSelected) {
            alert('Please select at least one item to checkout.');
            return;
        }

        // Add total amount
        const totalInput = document.createElement('input');
        totalInput.type = 'hidden';
        totalInput.name = 'total';
        totalInput.value = total;
        document.getElementById('checkoutForm').appendChild(totalInput);

        // Add shipping fee
        const shippingInput = document.createElement('input');
        shippingInput.type = 'hidden';
        shippingInput.name = 'shipping_fee';
        shippingInput.value = '100';  // Fixed shipping fee
        document.getElementById('checkoutForm').appendChild(shippingInput);

        document.getElementById('checkoutForm').submit();
    }
</script>
