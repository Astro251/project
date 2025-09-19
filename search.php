<?php
session_start();
include 'includes/config.php';

// Get search query
$query = isset($_GET['query']) ? mysqli_real_escape_string($conn, $_GET['query']) : '';

// Search books
$search_query = "SELECT b.*, a.name as author_name 
                FROM Books b 
                JOIN Authors a ON b.author_id = a.author_id 
                WHERE b.title LIKE '%$query%' 
                OR a.name LIKE '%$query%' 
                OR b.description LIKE '%$query%'";
$result = mysqli_query($conn, $search_query);
$total_results = mysqli_num_rows($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - E-Book Store</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main>
        <div class="container">
            <div class="search-results-header">
                <h1>Search Results</h1>
                <p>Found <?php echo $total_results; ?> results for "<?php echo htmlspecialchars($query); ?>"</p>
                
                <form action="search.php" method="GET" class="search-form" style="max-width: 600px; margin: 20px 0;">
                    <input type="text" name="query" placeholder="Search by title, author, or genre..." value="<?php echo htmlspecialchars($query); ?>">
                    <button type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>
            
            <?php if ($total_results > 0): ?>
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
            <?php else: ?>
                <div class="no-results">
                    <i class="fas fa-search" style="font-size: 3rem; color: #ccc; margin-bottom: 20px;"></i>
                    <h2>No results found</h2>
                    <p>Try different keywords or browse our categories</p>
                    <a href="books.php" class="btn" style="margin-top: 20px;">Browse All Books</a>
                </div>
            <?php endif; ?>
        </div>
    </main>
    
    <?php include 'includes/footer.php'; ?>
    <script src="js/script.js"></script>
</body>
</html>