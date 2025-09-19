<?php
session_start();
include 'includes/config.php';

// Pagination setup
$books_per_page = 12;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $books_per_page;

// Get total number of books
$count_query = "SELECT COUNT(*) as total FROM Books";
$count_result = mysqli_query($conn, $count_query);
$total_books = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_books / $books_per_page);

// Get books for current page
$query = "SELECT b.*, a.name as author_name, p.name as publisher_name 
          FROM Books b 
          JOIN Authors a ON b.author_id = a.author_id 
          JOIN Publishers p ON b.publisher_id = p.publisher_id
          ORDER BY b.title ASC
          LIMIT $offset, $books_per_page";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Books - E-Book Store</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .books-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .filter-container {
            display: flex;
            gap: 15px;
        }
        
        .filter-container select {
            padding: 8px 15px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            background-color: #fff;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 40px;
            gap: 10px;
        }
        
        .pagination a {
            display: inline-block;
            padding: 8px 15px;
            background-color: #fff;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            color: var(--text-color);
        }
        
        .pagination a.active {
            background-color: var(--primary-color);
            color: #fff;
            border-color: var(--primary-color);
        }
        
        .pagination a:hover:not(.active) {
            background-color: var(--light-bg);
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main>
        <div class="container">
            <div class="books-header">
                <h1>Browse Books</h1>
                <div class="filter-container">
                    <select id="sort-by">
                        <option value="title-asc">Title (A-Z)</option>
                        <option value="title-desc">Title (Z-A)</option>
                        <option value="price-asc">Price (Low to High)</option>
                        <option value="price-desc">Price (High to Low)</option>
                    </select>
                </div>
            </div>
            
            <div class="book-grid">
                <?php while ($book = mysqli_fetch_assoc($result)): ?>
                <div class="book-card">
                    <div class="book-cover">
                        <img src="images/covers/<?php echo $book['book_id']; ?>.jpg" alt="<?php echo $book['title']; ?>" onerror="this.src='images/covers/default.jpg'">
                    </div>
                    <div class="book-info">
                        <h3><?php echo $book['title']; ?></h3>
                        <p class="author">by <?php echo $book['author_name']; ?></p>
                        <p class="price">$<?php echo number_format($book['price'], 2); ?></p>
                        <a href="book.php?id=<?php echo $book['book_id']; ?>" class="btn">View Details</a>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            
            <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>"><i class="fas fa-chevron-left"></i></a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?php echo $i; ?>" <?php echo ($i == $page) ? 'class="active"' : ''; ?>><?php echo $i; ?></a>
                <?php endfor; ?>
                
                <?php if ($page < $total_pages): ?>
                <a href="?page=<?php echo $page + 1; ?>"><i class="fas fa-chevron-right"></i></a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </main>
    
    <?php include 'includes/footer.php'; ?>
    <script src="js/script.js"></script>
    <script>
        document.getElementById('sort-by').addEventListener('change', function() {
            const sortValue = this.value;
            const currentUrl = new URL(window.location.href);
            
            currentUrl.searchParams.set('sort', sortValue);
            window.location.href = currentUrl.toString();
        });
    </script>
</body>
</html>