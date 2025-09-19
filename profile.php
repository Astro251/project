<?php
session_start();
include 'includes/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success_message = '';
$error_message = '';

// Get user information
$user_query = "SELECT * FROM Users WHERE user_id = $user_id";
$user_result = mysqli_query($conn, $user_query);
$user = mysqli_fetch_assoc($user_result);

// Process profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    
    // Check if email already exists for another user
    $email_check_query = "SELECT * FROM Users WHERE email = '$email' AND user_id != $user_id";
    $email_check_result = mysqli_query($conn, $email_check_query);
    
    if (mysqli_num_rows($email_check_result) > 0) {
        $error_message = "Email already in use by another account.";
    } else {
        // Update user information
        $update_query = "UPDATE Users SET name = '$name', email = '$email', phone = '$phone' WHERE user_id = $user_id";
        
        if (mysqli_query($conn, $update_query)) {
            $success_message = "Profile updated successfully!";
            // Refresh user data
            $user_result = mysqli_query($conn, $user_query);
            $user = mysqli_fetch_assoc($user_result);
        } else {
            $error_message = "Error updating profile: " . mysqli_error($conn);
        }
    }
}

// Process password change
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Verify current password
    if (password_verify($current_password, $user['password'])) {
        // Check if new passwords match
        if ($new_password === $confirm_password) {
            // Hash new password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            // Update password
            $update_password_query = "UPDATE Users SET password = '$hashed_password' WHERE user_id = $user_id";
            
            if (mysqli_query($conn, $update_password_query)) {
                $success_message = "Password changed successfully!";
            } else {
                $error_message = "Error changing password: " . mysqli_error($conn);
            }
        } else {
            $error_message = "New passwords do not match.";
        }
    } else {
        $error_message = "Current password is incorrect.";
    }
}

// Get user's recent orders
$orders_query = "SELECT * FROM Orders WHERE user_id = $user_id ORDER BY order_date DESC, order_time DESC LIMIT 5";
$orders_result = mysqli_query($conn, $orders_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - E-Book Store</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .profile-container {
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 30px;
        }
        
        .profile-sidebar {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        
        .profile-main {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }
        
        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background-color: var(--primary-color);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            margin: 0 auto 20px;
        }
        
        .profile-name {
            text-align: center;
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .profile-email {
            text-align: center;
            color: #666;
            margin-bottom: 20px;
        }
        
        .profile-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .profile-menu li {
            margin-bottom: 10px;
        }
        
        .profile-menu a {
            display: flex;
            align-items: center;
            padding: 10px;
            border-radius: 4px;
            color: #333;
            text-decoration: none;
            transition: var(--transition);
        }
        
        .profile-menu a:hover,
        .profile-menu a.active {
            background-color: rgba(74, 111, 165, 0.1);
            color: var(--primary-color);
        }
        
        .profile-menu i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .profile-section {
            margin-bottom: 40px;
        }
        
        .profile-section h2 {
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
        
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            font-size: 16px;
        }
        
        .alert {
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .recent-orders {
            width: 100%;
            border-collapse: collapse;
        }
        
        .recent-orders th,
        .recent-orders td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }
        
        .recent-orders th {
            background-color: #f9f9f9;
            font-weight: 600;
        }
        
        .recent-orders tr:hover {
            background-color: #f9f9f9;
        }
        
        .order-status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.9rem;
            display: inline-block;
        }
        
        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        @media (max-width: 768px) {
            .profile-container {
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
            <h1>My Profile</h1>
            
            <?php if ($success_message): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php endif; ?>
            
            <?php if ($error_message): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php endif; ?>
            
            <div class="profile-container">
                <div class="profile-sidebar">
                    <div class="profile-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <h2 class="profile-name"><?php echo $user['name']; ?></h2>
                    <p class="profile-email"><?php echo $user['email']; ?></p>
                    
                    <ul class="profile-menu">
                        <li><a href="#" class="tab-link active" data-tab="profile"><i class="fas fa-user"></i> Profile Information</a></li>
                        <li><a href="#" class="tab-link" data-tab="password"><i class="fas fa-lock"></i> Change Password</a></li>
                        <li><a href="#" class="tab-link" data-tab="orders"><i class="fas fa-shopping-bag"></i> Recent Orders</a></li>
                        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    </ul>
                </div>
                
                <div class="profile-main">
                    <div id="profile" class="tab-content active">
                        <div class="profile-section">
                            <h2>Profile Information</h2>
                            <form action="profile.php" method="POST">
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
                                    <input type="tel" id="phone" name="phone" value="<?php echo $user['phone']; ?>">
                                </div>
                                <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
                            </form>
                        </div>
                    </div>
                    
                    <div id="password" class="tab-content">
                        <div class="profile-section">
                            <h2>Change Password</h2>
                            <form action="profile.php" method="POST">
                                <div class="form-group">
                                    <label for="current_password">Current Password</label>
                                    <input type="password" id="current_password" name="current_password" required>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="new_password">New Password</label>
                                        <input type="password" id="new_password" name="new_password" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="confirm_password">Confirm New Password</label>
                                        <input type="password" id="confirm_password" name="confirm_password" required>
                                    </div>
                                </div>
                                <button type="submit" name="change_password" class="btn btn-primary">Change Password</button>
                            </form>
                        </div>
                    </div>
                    
                    <div id="orders" class="tab-content">
                        <div class="profile-section">
                            <h2>Recent Orders</h2>
                            <?php if (mysqli_num_rows($orders_result) > 0): ?>
                                <table class="recent-orders">
                                    <thead>
                                        <tr>
                                            <th>Order #</th>
                                            <th>Date</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($order = mysqli_fetch_assoc($orders_result)): ?>
                                            <tr>
                                                <td>#<?php echo str_pad($order['order_id'], 6, '0', STR_PAD_LEFT); ?></td>
                                                <td><?php echo date('M j, Y', strtotime($order['order_date'])); ?></td>
                                                <td>$<?php echo number_format($order['total_cost'], 2); ?></td>
                                                <td><span class="order-status status-completed">Completed</span></td>
                                                <td><a href="order_details.php?order_id=<?php echo $order['order_id']; ?>">View Details</a></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                                <p style="margin-top: 20px;"><a href="order_history.php">View All Orders</a></p>
                            <?php else: ?>
                                <p>You haven't placed any orders yet.</p>
                                <a href="books.php" class="btn btn-primary">Browse Books</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <?php include 'includes/footer.php'; ?>
    <script>
        // Tab functionality
        const tabLinks = document.querySelectorAll('.tab-link');
        const tabContents = document.querySelectorAll('.tab-content');
        
        tabLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                
                // Remove active class from all links and contents
                tabLinks.forEach(l => l.classList.remove('active'));
                tabContents.forEach(c => c.classList.remove('active'));
                
                // Add active class to clicked link
                link.classList.add('active');
                
                // Show corresponding content
                const tabId = link.getAttribute('data-tab');
                document.getElementById(tabId).classList.add('active');
            });
        });
    </script>
</body>
</html>