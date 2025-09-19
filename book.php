<?php
session_start();
include 'includes/config.php';

// Check if book ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: books.php");
    exit();
}

$book_id = (int)$_GET['id'];

// Get book details
$query = "SELECT b.*, a.name as author_name, a.biography as author_bio, p.name as publisher_name 
          FROM Books b 
          JOIN Authors a ON b.author_id = a.author_id 
          JOIN Publishers p ON b.publisher_id = p.publisher_id
          WHERE b.book_id = $book_id";
$result = mysqli_query($conn, $query);

// Check if book exists
if (mysqli_num_rows($result) == 0) {
    header("Location: books.php");
    exit();
}

$book = mysqli_fetch_assoc($result);

// Get book ratings
$rating_query = "SELECT AVG(rating) as avg_rating, COUNT(*) as total_ratings FROM Feedback WHERE book_id = $book_id";
$rating_result = mysqli_query($conn, $rating_query);
$rating_data = mysqli_fetch_assoc($rating_result);
$avg_rating = round($rating_data['avg_rating'], 1);
$total_ratings = $rating_data['total_ratings'];

// Process add to cart
if (isset($_POST['add_to_cart']) && isset($_SESSION['user_id'])) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }
    
    // Add book to cart
    $_SESSION['cart'][$book_id] = array(
        'title' => $book['title'],
        'price' => $book['price'],
        'quantity' => 1
    );
    
    // Redirect to prevent form resubmission
    header("Location: book.php?id=$book_id&added=1");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $book['title']; ?> - E-Book Store</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .book-details {
            display: flex;
            gap: 40px;
            margin-bottom: 40px;
        }
        
        .book-cover-large {
            flex: 0 0 300px;
            height: 450px;
            overflow: hidden;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .book-cover-large img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .book-info-detailed {
            flex: 1;
        }
        
        .book-title {
            font-size: 2rem;
            margin-bottom: 10px;
            color: var(--primary-color);
        }
        
        .book-author {
            font-size: 1.2rem;
            margin-bottom: 20px;
            color: #666;
        }
        
        .book-price-large {
            font-size: 1.8rem;
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 20px;
        }
        
        .book-rating {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .stars {
            color: #ffc107;
            margin-right: 10px;
        }
        
        .book-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        
        .book-actions .btn {
            padding: 12px 25px;
            font-size: 1rem;
        }
        
        .book-meta {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
        }
        
        .book-meta p {
            margin-bottom: 10px;
            color: #666;
        }
        
        .book-description {
            margin-top: 40px;
        }
        
        .book-description h2 {
            font-size: 1.5rem;
            margin-bottom: 20px;
            color: var(--primary-color);
        }
        
        .tabs {
            margin-top: 60px;
        }
        
        .tab-buttons {
            display: flex;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 20px;
        }
        
        .tab-btn {
            padding: 10px 20px;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 500;
            color: #666;
            position: relative;
        }
        
        .tab-btn.active {
            color: var(--primary-color);
        }
        
        .tab-btn.active::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 100%;
            height: 2px;
            background-color: var(--primary-color);
        }
        
        .tab-content {
            display: none;
            padding: 20px 0;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 10px 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main>
        <div class="container">
            <?php if (isset($_GET['added']) && $_GET['added'] == 1): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle"></i> Book added to cart successfully!
                </div>
            <?php endif; ?>
            
            <div class="book-details">
                <div class="book-cover-large">
                    <img src="images/covers/<?php echo $book['book_id']; ?>.jpg" alt="<?php echo $book['title']; ?>" onerror="this.src='images/covers/default.jpg'">
                </div>
                
                <div class="book-info-detailed">
                    <h1 class="book-title"><?php echo $book['title']; ?></h1>
                    <p class="book-author">by <a href="author.php?id=<?php echo $book['author_id']; ?>"><?php echo $book['author_name']; ?></a></p>
                    
                    <div class="book-rating">
                        <div class="stars">
                            <?php
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $avg_rating) {
                                    echo '<i class="fas fa-star"></i>';
                                } elseif ($i - 0.5 <= $avg_rating) {
                                    echo '<i class="fas fa-star-half-alt"></i>';
                                } else {
                                    echo '<i class="far fa-star"></i>';
                                }
                            }
                            ?>
                        </div>
                        <span><?php echo $avg_rating; ?> (<?php echo $total_ratings; ?> reviews)</span>
                    </div>
                    
                    <p class="book-price-large">$<?php echo number_format($book['price'], 2); ?></p>
                    
                    <div class="book-actions">
                        <form action="book.php?id=<?php echo $book_id; ?>" method="POST">
                            <button type="submit" name="add_to_cart" class="btn btn-primary">
                                <i class="fas fa-shopping-cart"></i> Add to Cart
                            </button>
                        </form>
                        <a href="#" class="btn">
                            <i class="far fa-heart"></i> Add to Wishlist
                        </a>
                    </div>
                    
                    <div class="book-meta">
                        <p><strong>Publisher:</strong> <?php echo $book['publisher_name']; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="tabs">
                <div class="tab-buttons">
                    <button class="tab-btn active" data-tab="description">Description</button>
                    <button class="tab-btn" data-tab="author">About the Author</button>
                    <button class="tab-btn" data-tab="reviews">Reviews</button>
                </div>
                
                <div id="description" class="tab-content active">
                    <p><?php echo $book['description']; ?></p>
                </div>
                
                <div id="author" class="tab-content">
                    <h3><?php echo $book['author_name']; ?></h3>
                    <p><?php echo $book['author_bio']; ?></p>
                </div>
                
                <div id="reviews" class="tab-content">
                    <?php
                    // Get book reviews
                    $reviews_query = "SELECT f.*, u.name as user_name 
                                    FROM Feedback f 
                                    JOIN Users u ON f.user_id = u.user_id 
                                    WHERE f.book_id = $book_id 
                                    ORDER BY f.feedback_id DESC";
                    $reviews_result = mysqli_query($conn, $reviews_query);
                    
                    if (mysqli_num_rows($reviews_result) > 0) {
                        while ($review = mysqli_fetch_assoc($reviews_result)) {
                            echo '<div class="review">';
                            echo '<div class="review-header">';
                            echo '<h4>' . $review['user_name'] . '</h4>';
                            echo '<div class="stars">';
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $review['rating']) {
                                    echo '<i class="fas fa-star"></i>';
                                } else {
                                    echo '<i class="far fa-star"></i>';
                                }
                            }
                            echo '</div>';
                            echo '</div>';
                            echo '<p>' . $review['comment'] . '</p>';
                            echo '</div>';
                        }
                    } else {
                        echo '<p>No reviews yet. Be the first to review this book!</p>';
                    }
                    
                    // Review form for logged in users
                    if (isset($_SESSION['user_id'])) {
                        echo '<h3>Write a Review</h3>';
                        echo '<form action="submit_review.php" method="POST">';
                        echo '<input type="hidden" name="book_id" value="' . $book_id . '">';
                        echo '<div class="form-group">';
                        echo '<label>Rating:</label>';
                        echo '<select name="rating" required>';
                        echo '<option value="5">5 - Excellent</option>';
                        echo '<option value="4">4 - Very Good</option>';
                        echo '<option value="3">3 - Good</option>';
                        echo '<option value="2">2 - Fair</option>';
                        echo '<option value="1">1 - Poor</option>';
                        echo '</select>';
                        echo '</div>';
                        echo '<div class="form-group">';
                        echo '<label>Comment:</label>';
                        echo '<textarea name="comment" rows="4" required></textarea>';
                        echo '</div>';
                        echo '<button type="submit" class="btn">Submit Review</button>';
                        echo '</form>';
                    } else {
                        echo '<p><a href="login.php">Login</a> to write a review.</p>';
                    }
                    ?>
                </div>
            </div>
            
            <div class="related-books">
                <h2>You May Also Like</h2>
                <div class="book-grid">
                    <?php
                    // Get related books (same author or similar genre)
                    $related_query = "SELECT b.*, a.name as author_name 
                                    FROM Books b 
                                    JOIN Authors a ON b.author_id = a.author_id 
                                    WHERE b.author_id = {$book['author_id']} AND b.book_id != $book_id 
                                    LIMIT 4";
                    $related_result = mysqli_query($conn, $related_query);
                    
                    while ($related_book = mysqli_fetch_assoc($related_result)) {
                        echo '<div class="book-card">';
                        echo '<div class="book-cover">';
                        echo '<img src="images/covers/' . $related_book['book_id'] . '.jpg" alt="' . $related_book['title'] . '" onerror="this.src=\'images/covers/default.jpg\'">';
                        echo '</div>';
                        echo '<div class="book-info">';
                        echo '<h3>' . $related_book['title'] . '</h3>';
                        echo '<p class="author">by ' . $related_book['author_name'] . '</p>';
                        echo '<p class="price">$' . number_format($related_book['price'], 2) . '</p>';
                        echo '<a href="book.php?id=' . $related_book['book_id'] . '" class="btn">View Details</a>';
                        echo '</div>';
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </main>
    
    <?php include 'includes/footer.php'; ?>
    <script src="js/script.js"></script>
    <script>
        // Tab functionality
        const tabButtons = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');
        
        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                const tabId = button.getAttribute('data-tab');
                
                // Remove active class from all buttons and contents
                tabButtons.forEach(btn => btn.classList.remove('active'));
                tabContents.forEach(content => content.classList.remove('active'));
                
                // Add active class to current button and content
                button.classList.add('active');
                document.getElementById(tabId).classList.add('active');
            });
        });
    </script>
</body>
</html>