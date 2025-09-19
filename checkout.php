<?php
session_start();
include 'includes/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

// Get user information
$user_id = $_SESSION['user_id'];
$user_query = "SELECT * FROM Users WHERE user_id = $user_id";
$user_result = mysqli_query($conn, $user_query);
$user = mysqli_fetch_assoc($user_result);

// Calculate cart total
$cart_total = 0;
foreach ($_SESSION['cart'] as $item) {
    $cart_total += $item['price'] * $item['quantity'];
}
$tax = $cart_total * 0.05;
$total = $cart_total + $tax;

// Process checkout
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);
    
    // Create order
    $order_date = date('Y-m-d');
    $order_time = date('H:i:s');
    
    $order_query = "INSERT INTO Orders (user_id, order_date, order_time, total_cost, payment_method) 
                   VALUES ($user_id, '$order_date', '$order_time', $total, '$payment_method')";
    
    if (mysqli_query($conn, $order_query)) {
        $order_id = mysqli_insert_id($conn);
        
        // Clear cart
        $_SESSION['cart'] = array();
        
        // Redirect to order confirmation
        header("Location: order_confirmation.php?order_id=$order_id");
        exit();
    } else {
        $error = "Error processing your order: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - E-Book Store</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .checkout-container {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 30px;
        }
        
        .checkout-form {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }
        
        .checkout-summary {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
            align-self: start;
        }
        
        .checkout-section {
            margin-bottom: 30px;
        }
        
        .checkout-section h2 {
            color: var(--primary-color);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border-color);
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            font-size: 16px;
        }
        
        .payment-methods {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        
        .payment-method {
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .payment-method:hover {
            border-color: var(--primary-color);
        }
        
        .payment-method.selected {
            border-color: var(--primary-color);
            background-color: rgba(74, 111, 165, 0.1);
        }
        
        .payment-method i {
            font-size: 2rem;
            margin-bottom: 10px;
            color: var(--primary-color);
        }
        
        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }
        
        .summary-total {
            font-size: 1.2rem;
            font-weight: bold;
            color: var(--primary-color);
            border-top: 1px solid var(--border-color);
            padding-top: 15px;
            margin-top: 15px;
        }
        
        .cart-items-preview {
            margin-top: 20px;
        }
        
        .cart-item-preview {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .cart-item-preview img {
            width: 50px;
            height: 75px;
            object-fit: cover;
            border-radius: 4px;
            margin-right: 15px;
        }
        
        .cart-item-preview-details {
            flex: 1;
        }
        
        .cart-item-preview-title {
            font-weight: 500;
            margin-bottom: 5px;
        }
        
        .cart-item-preview-price {
            color: var(--primary-color);
        }
        
        @media (max-width: 768px) {
            .checkout-container {
                grid-template-columns: 1fr;
            }
            
            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main>
        <div class="container">
            <h1>Checkout</h1>
            
            <?php if (isset($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <div class="checkout-container">
                <div class="checkout-form">
                    <form action="checkout.php" method="POST">
                        <div class="checkout-section">
                            <h2>Billing Information</h2>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="name">Full Name</label>
                                    <input type="text" id="name" name="name" value="<?php echo $user['name']; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email Address</label>
                                    <input type="email" id="email" name="email" value="<?php echo $user['email']; ?>" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="tel" id="phone" name="phone" value="<?php echo $user['phone']; ?>" required>
                            </div>
                        </div>
                        
                        <div class="checkout-section">
                            <h2>Payment Method</h2>
                            <div class="payment-methods">
                                <div class="payment-method selected" data-method="Credit Card">
                                    <i class="fas fa-credit-card"></i>
                                    <p>Credit Card</p>
                                </div>
                                <div class="payment-method" data-method="PayPal">
                                    <i class="fab fa-paypal"></i>
                                    <p>PayPal</p>
                                </div>
                                <div class="payment-method" data-method="Bank Transfer">
                                    <i class="fas fa-university"></i>
                                    <p>Bank Transfer</p>
                                </div>
                            </div>
                            <input type="hidden" name="payment_method" id="payment_method" value="Credit Card">
                        </div>
                        
                        <button type="submit" class="btn btn-primary" style="width: 100%;">Complete Purchase</button>
                    </form>
                </div>
                
                <div class="checkout-summary">
                    <h2>Order Summary</h2>
                    
                    <div class="cart-items-preview">
                        <?php foreach ($_SESSION['cart'] as $book_id => $item): ?>
                            <div class="cart-item-preview">
                                <img src="images/covers/<?php echo $book_id; ?>.jpg" alt="<?php echo $item['title']; ?>" onerror="this.src='images/covers/default.jpg'">
                                <div class="cart-item-preview-details">
                                    <p class="cart-item-preview-title"><?php echo $item['title']; ?></p>
                                    <p class="cart-item-preview-price">$<?php echo number_format($item['price'], 2); ?> x <?php echo $item['quantity']; ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="summary-item">
                        <span>Subtotal:</span>
                        <span>$<?php echo number_format($cart_total, 2); ?></span>
                    </div>
                    <div class="summary-item">
                        <span>Tax (5%):</span>
                        <span>$<?php echo number_format($tax, 2); ?></span>
                    </div>
                    <div class="summary-item summary-total">
                        <span>Total:</span>
                        <span>$<?php echo number_format($total, 2); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <?php include 'includes/footer.php'; ?>
    <script src="js/script.js"></script>
    <script>
        // Payment method selection
        const paymentMethods = document.querySelectorAll('.payment-method');
        const paymentMethodInput = document.getElementById('payment_method');
        
        paymentMethods.forEach(method => {
            method.addEventListener('click', () => {
                // Remove selected class from all methods
                paymentMethods.forEach(m => m.classList.remove('selected'));
                
                // Add selected class to clicked method
                method.classList.add('selected');
                
                // Update hidden input value
                paymentMethodInput.value = method.getAttribute('data-method');
            });
        });
    </script>
</body>
</html>