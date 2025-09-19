
-- Create Database
CREATE DATABASE IF NOT EXISTS ebook_store;
USE ebook_store;

-- Drop existing tables if they exist
DROP TABLE IF EXISTS Feedback;
DROP TABLE IF EXISTS Orders;
DROP TABLE IF EXISTS Books;
DROP TABLE IF EXISTS Authors;
DROP TABLE IF EXISTS Publishers;
DROP TABLE IF EXISTS Users;

-- Users Table
CREATE TABLE Users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(15) UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Authors Table
CREATE TABLE Authors (
    author_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    biography TEXT
);

-- Publishers Table
CREATE TABLE Publishers (
    publisher_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

-- Books Table
CREATE TABLE Books (
    book_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    author_id INT,
    publisher_id INT,
    FOREIGN KEY (author_id) REFERENCES Authors(author_id),
    FOREIGN KEY (publisher_id) REFERENCES Publishers(publisher_id)
);

-- Orders Table
CREATE TABLE Orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    order_date DATE NOT NULL,
    order_time TIME NOT NULL,
    total_cost DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES Users(user_id)
);

-- Feedback Table
CREATE TABLE Feedback (
    feedback_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    book_id INT,
    comment VARCHAR(255),
    rating INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES Users(user_id),
    FOREIGN KEY (book_id) REFERENCES Books(book_id)
);

-- Insert Dummy Data into Users
INSERT INTO Users (name, email, phone, password) VALUES
('Alice Johnson', 'alice@example.com', '9876543210', 'hashed_password1'),
('Bob Smith', 'bob@example.com', '9876543211', 'hashed_password2'),
('Charlie Brown', 'charlie@example.com', '9876543212', 'hashed_password3');

-- Insert Dummy Data into Authors
INSERT INTO Authors (name, biography) VALUES
('J.K. Rowling', 'Author of the Harry Potter series'),
('George R.R. Martin', 'Author of A Song of Ice and Fire'),
('Paulo Coelho', 'Author of The Alchemist');

-- Insert Dummy Data into Publishers
INSERT INTO Publishers (name) VALUES
('Bloomsbury Publishing'),
('Bantam Books'),
('HarperCollins');

-- Insert Dummy Data into Books
INSERT INTO Books (title, description, price, author_id, publisher_id) VALUES
('Harry Potter and the Sorcerer''s Stone', 'Fantasy novel about a young wizard.', 299.99, 1, 1),
('A Game of Thrones', 'First book in A Song of Ice and Fire.', 399.99, 2, 2),
('The Alchemist', 'Novel about following your dreams.', 199.99, 3, 3);

-- Insert Dummy Data into Orders
INSERT INTO Orders (user_id, order_date, order_time, total_cost, payment_method) VALUES
(1, '2025-09-01', '10:30:00', 299.99, 'Credit Card'),
(2, '2025-09-05', '15:45:00', 399.99, 'PayPal'),
(3, '2025-09-10', '09:15:00', 199.99, 'Debit Card');

-- Insert Dummy Data into Feedback
INSERT INTO Feedback (user_id, book_id, comment, rating) VALUES
(1, 1, 'Amazing book, loved it!', 5),
(2, 2, 'Very detailed and interesting.', 4),
(3, 3, 'Inspirational read.', 5);
