<?php
session_start();
include 'includes/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Book Store - Home</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main>
        <section class="hero">
            <div class="container">
                <div class="hero-content">
                    <h1>Discover Your Next Favorite Book</h1>
                    <p>Explore thousands of e-books across all genres</p>
                    <form action="search.php" method="GET" class="search-form">
                        <input type="text" name="query" placeholder="Search by title, author, or genre...">
                        <button type="submit"><i class="fas fa-search"></i></button>
                    </form>
                </div>
            </div>
        </section>

        <section class="featured-books">
            <div class="container">
                <h2>Featured Books</h2>
                <div class="book-grid">
                    <?php
                    $query = "SELECT b.*, a.name as author_name FROM Books b 
                              JOIN Authors a ON b.author_id = a.author_id 
                              ORDER BY b.book_id DESC LIMIT 6";
                    $result = mysqli_query($conn, $query);
                    
                    while ($book = mysqli_fetch_assoc($result)) {
                        echo '<div class="book-card">';
                        echo '<div class="book-cover">';
                        echo '<img src="images/covers/' . $book['book_id'] . '.jpg" alt="' . $book['title'] . '" onerror="this.src=\'images/covers/default.jpg\'">';
                        echo '</div>';
                        echo '<div class="book-info">';
                        echo '<h3>' . $book['title'] . '</h3>';
                        echo '<p class="author">by ' . $book['author_name'] . '</p>';
                        echo '<p class="price">$' . number_format($book['price'], 2) . '</p>';
                        echo '<a href="book.php?id=' . $book['book_id'] . '" class="btn">View Details</a>';
                        echo '</div>';
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>
        </section>

        <section class="categories">
            <div class="container">
                <h2>Browse Categories</h2>
                <div class="category-grid">
                    <a href="category.php?cat=fiction" class="category-card">
                        <i class="fas fa-book"></i>
                        <h3>Fiction</h3>
                    </a>
                    <a href="category.php?cat=non-fiction" class="category-card">
                        <i class="fas fa-landmark"></i>
                        <h3>Non-Fiction</h3>
                    </a>
                    <a href="category.php?cat=science" class="category-card">
                        <i class="fas fa-atom"></i>
                        <h3>Science</h3>
                    </a>
                    <a href="category.php?cat=business" class="category-card">
                        <i class="fas fa-chart-line"></i>
                        <h3>Business</h3>
                    </a>
                </div>
            </div>
        </section>

        <section class="newsletter">
            <div class="container">
                <h2>Subscribe to Our Newsletter</h2>
                <p>Get updates on new releases and exclusive offers</p>
                <form action="#" method="POST" class="newsletter-form">
                    <input type="email" name="email" placeholder="Your email address" required>
                    <button type="submit" class="btn">Subscribe</button>
                </form>
            </div>
        </section>
    </main>
    
    <?php include 'includes/footer.php'; ?>
    <script src="js/script.js"></script>
</body>
</html>