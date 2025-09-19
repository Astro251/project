<?php
session_start();
include 'includes/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if order ID is provided
if (!isset($_GET['order_id'])) {
    header("Location: index.php");
    exit();
}

$order_id = intval($_GET['order_id']);
$user_id = $_SESSION['user_id'];

// Get order details
$order_query = "SELECT * FROM Orders WHERE order_id = $order_id AND user_id = $user_id";
$order_result = mysqli_query($conn, $order_query);

if (mysqli_num_rows($order_result) == 0) {
    header("Location: index.php");
    exit();
}

$order = mysqli_fetch_assoc($order_result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - E-Book Store</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .confirmation-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 40px;
            text-align: center;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .confirmation-icon {
            font-size: 5rem;
            color: #4CAF50;
            margin-bottom: 20px;
        }
        
        .confirmation-title {
            font-size: 2rem;
            color: var(--primary-color);
            margin-bottom: 20px;
        }
        
        .order-details {
            background-color: #f9f9f9;
            border-radius: 8px;
            padding: 20px;
            margin: 30px 0;
            text-align: left;
        }
        
        .order-detail-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border-color);
        }
        
        .order-detail-item:last-child {
            border-bottom: none;
        }
        
        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
        }
        
        @media (max-width: 768px) {
            .confirmation-container {
                padding: 20px;
            }
            
            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main>
        <div class="container">
            <div class="confirmation-container">
                <div class="confirmation-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h1 class="confirmation-title">Order Confirmed!</h1>
                <p>Thank you for your purchase. Your order has been successfully placed.</p>
                
                <div class="order-details">
                    <h2>Order Details</h2>
                    <div class="order-detail-item">
                        <span>Order Number:</span>
                        <span>#<?php echo str_pad($order['order_id'], 6, '0', STR_PAD_LEFT); ?></span>
                    </div>
                    <div class="order-detail-item">
                        <span>Date:</span>
                        <span><?php echo date('F j, Y', strtotime($order['order_date'])); ?></span>
                    </div>
                    <div class="order-detail-item">
                        <span>Time:</span>
                        <span><?php echo date('g:i A', strtotime($order['order_time'])); ?></span>
                    </div>
                    <div class="order-detail-item">
                        <span>Payment Method:</span>
                        <span><?php echo $order['payment_method']; ?></span>
                    </div>
                    <div class="order-detail-item">
                        <span>Total Amount:</span>
                        <span>$<?php echo number_format($order['total_cost'], 2); ?></span>
                    </div>
                </div>
                
                <p>An email confirmation has been sent to your registered email address.</p>
                <p>You can view your order details anytime in your account's order history.</p>
                
                <div class="action-buttons">
                    <a href="index.php" class="btn btn-secondary">Continue Shopping</a>
                    <a href="profile.php" class="btn btn-primary">View Your Account</a>
                </div>
            </div>
        </div>
    </main>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>