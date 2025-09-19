<?php
session_start();
include 'includes/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Pagination setup
$records_per_page = 10;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $records_per_page;

// Get total number of orders
$count_query = "SELECT COUNT(*) as total FROM Orders WHERE user_id = $user_id";
$count_result = mysqli_query($conn, $count_query);
$count_row = mysqli_fetch_assoc($count_result);
$total_records = $count_row['total'];
$total_pages = ceil($total_records / $records_per_page);

// Get orders with pagination
$orders_query = "SELECT * FROM Orders WHERE user_id = $user_id 
                ORDER BY order_date DESC, order_time DESC 
                LIMIT $offset, $records_per_page";
$orders_result = mysqli_query($conn, $orders_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History - E-Book Store</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .order-history-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }
        
        .orders-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .orders-table th,
        .orders-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }
        
        .orders-table th {
            background-color: #f9f9f9;
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .orders-table tr:hover {
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
        
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 30px;
            list-style: none;
            padding: 0;
        }
        
        .pagination li {
            margin: 0 5px;
        }
        
        .pagination a {
            display: block;
            padding: 8px 15px;
            background-color: #f9f9f9;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            color: #333;
            text-decoration: none;
            transition: var(--transition);
        }
        
        .pagination a:hover {
            background-color: var(--primary-color);
            color: #fff;
        }
        
        .pagination .active a {
            background-color: var(--primary-color);
            color: #fff;
            border-color: var(--primary-color);
        }
        
        .pagination .disabled a {
            color: #999;
            cursor: not-allowed;
        }
        
        .empty-orders {
            text-align: center;
            padding: 50px 0;
        }
        
        .empty-orders i {
            font-size: 4rem;
            color: #ddd;
            margin-bottom: 20px;
        }
        
        @media (max-width: 768px) {
            .orders-table {
                display: block;
                overflow-x: auto;
            }
            
            .orders-table th,
            .orders-table td {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main>
        <div class="container">
            <h1>Order History</h1>
            
            <div class="order-history-container">
                <?php if (mysqli_num_rows($orders_result) > 0): ?>
                    <table class="orders-table">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Total</th>
                                <th>Payment Method</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($order = mysqli_fetch_assoc($orders_result)): ?>
                                <tr>
                                    <td>#<?php echo str_pad($order['order_id'], 6, '0', STR_PAD_LEFT); ?></td>
                                    <td><?php echo date('M j, Y', strtotime($order['order_date'])); ?></td>
                                    <td><?php echo date('g:i A', strtotime($order['order_time'])); ?></td>
                                    <td>$<?php echo number_format($order['total_cost'], 2); ?></td>
                                    <td><?php echo $order['payment_method']; ?></td>
                                    <td><span class="order-status status-completed">Completed</span></td>
                                    <td><a href="order_details.php?order_id=<?php echo $order['order_id']; ?>">View Details</a></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    
                    <?php if ($total_pages > 1): ?>
                        <ul class="pagination">
                            <li class="<?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                                <a href="<?php echo ($page <= 1) ? '#' : 'order_history.php?page='.($page-1); ?>">Previous</a>
                            </li>
                            
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="<?php echo ($page == $i) ? 'active' : ''; ?>">
                                    <a href="order_history.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <li class="<?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                                <a href="<?php echo ($page >= $total_pages) ? '#' : 'order_history.php?page='.($page+1); ?>">Next</a>
                            </li>
                        </ul>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="empty-orders">
                        <i class="fas fa-shopping-bag"></i>
                        <h2>No Orders Found</h2>
                        <p>You haven't placed any orders yet.</p>
                        <a href="books.php" class="btn btn-primary">Browse Books</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>