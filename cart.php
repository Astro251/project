<?php
session_start();
include 'includes/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

// Process cart actions
if (isset($_POST['action'])) {
    $book_id = isset($_POST['book_id']) ? (int)$_POST['book_id'] : 0;
    
    // Remove item from cart
    if ($_POST['action'] == 'remove' && isset($_SESSION['cart'][$book_id])) {
        unset($_SESSION['cart'][$book_id]);
    }
    
    // Update quantity
    if ($_POST['action'] == 'update' && isset($_SESSION['cart'][$book_id]) && isset($_POST['quantity'])) {
        $quantity = (int)$_POST['quantity'];
        if ($quantity > 0) {
            $_SESSION['cart'][$book_id]['quantity'] = $quantity;
        } else {
            unset($_SESSION['cart'][$book_id]);
        }
    }
    
    // Clear cart
    if ($_POST['action'] == 'clear') {
        $_SESSION['cart'] = array();
    }
    
    // Redirect to prevent form resubmission
    header("Location: cart.php");
    exit();
}

// Calculate cart total
$cart_total = 0;
foreach ($_SESSION['cart'] as $item) {
    $cart_total += $item['price'] * $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - E-Book Store</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .cart-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-bottom: 40px;
        }
        
        .cart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border-color);
        }
        
        .cart-header h1 {
            color: var(--primary-color);
            margin: 0;
        }
        
        .cart-items {
            margin-bottom: 30px;
        }
        
        .cart-item {
            display: flex;
            align-items: center;
            padding: 20px 0;
            border-bottom: 1px solid var(--border-color);
        }
        
        .cart-item-image {
            width: 100px;
            height: 150px;
            overflow: hidden;
            border-radius: 4px;
            margin-right: 20px;
        }
        
        .cart-item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .cart-item-details {
            flex: 1;
        }
        
        .cart-item-title {
            font-size: 1.2rem;
            margin-bottom: 5px;
        }
        
        .cart-item-price {
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 10px;
        }
        
        .cart-item-actions {
            display: flex;
            align-items: center;
        }
        
        .quantity-input {
            width: 60px;
            padding: 5px;
            text-align: center;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            margin: 0 10px;
        }
        
        .cart-summary {
            background-color: var(--light-bg);
            padding: 20px;
            border-radius: 8px;
        }
        
        .cart-summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }
        
        .cart-total {
            font-size: 1.2rem;
            font-weight: bold;
            color: var(--primary-color);
        }
        
        .empty-cart {
            text-align: center;
            padding: 50px 0;
        }
        
        .empty-cart i {
            font-size: 4rem;
            color: #ccc;
            margin-bottom: 20px;
        }
        
        .empty-cart h2 {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main>
        <div class="container">
            <div class="cart-container">
                <div class="cart-header">
                    <h1>Shopping Cart</h1>
                    <?php if (!empty($_SESSION['cart'])): ?>
                        <form action="cart.php" method="POST">
                            <input type="hidden" name="action" value="clear">
                            <button type="submit" class="btn">Clear Cart</button>
                        </form>
                    <?php endif; ?>
                </div>
                
                <?php if (empty($_SESSION['cart'])): ?>
                    <div class="empty-cart">
                        <i class="fas fa-shopping-cart"></i>
                        <h2>Your cart is empty</h2>
                        <p>Looks like you haven't added any books to your cart yet.</p>
                        <a href="books.php" class="btn">Browse Books</a>
                    </div>
                <?php else: ?>
                    <div class="cart-items">
                        <?php foreach ($_SESSION['cart'] as $book_id => $item): ?>
                            <div class="cart-item">
                                <div class="cart-item-image">
                                    <img src="images/covers/<?php echo $book_id; ?>.jpg" alt="<?php echo $item['title']; ?>" onerror="this.src='images/covers/default.jpg'">
                                </div>
                                <div class="cart-item-details">
                                    <h3 class="cart-item-title"><?php echo $item['title']; ?></h3>
                                    <p class="cart-item-price">$<?php echo number_format($item['price'], 2); ?></p>
                                    <div class="cart-item-actions">
                                        <form action="cart.php" method="POST" class="update-form">
                                            <input type="hidden" name="action" value="update">
                                            <input type="hidden" name="book_id" value="<?php echo $book_id; ?>">
                                            <label>Quantity:</label>
                                            <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" class="quantity-input">
                                            <button type="submit" class="btn">Update</button>
                                        </form>
                                        <form action="cart.php" method="POST" class="remove-form">
                                            <input type="hidden" name="action" value="remove">
                                            <input type="hidden" name="book_id" value="<?php echo $book_id; ?>">
                                            <button type="submit" class="btn">Remove</button>
                                        </form>
                                    </div>
                                </div>
                                <div class="cart-item-subtotal">
                                    <p>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="cart-summary">
                        <div class="cart-summary-row">
                            <span>Subtotal:</span>
                            <span>$<?php echo number_format($cart_total, 2); ?></span>
                        </div>
                        <div class="cart-summary-row">
                            <span>Tax (5%):</span>
                            <span>$<?php echo number_format($cart_total * 0.05, 2); ?></span>
                        </div>
                        <div class="cart-summary-row cart-total">
                            <span>Total:</span>
                            <span>$<?php echo number_format($cart_total * 1.05, 2); ?></span>
                        </div>
                        <a href="checkout.php" class="btn btn-primary" style="width: 100%; text-align: center;">Proceed to Checkout</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
    
    <?php include 'includes/footer.php'; ?>
    <script src="js/script.js"></script>
</body>
</html>