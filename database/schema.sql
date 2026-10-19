
CREATE DATABASE IF NOT EXISTS salon_booking;
USE salon_booking;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','user') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL DEFAULT 0,
    duration INT NOT NULL DEFAULT 30,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL DEFAULT 0,
    stock INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    service_id INT NOT NULL,
    booking_date DATE NOT NULL,
    booking_time TIME NOT NULL,
    notes TEXT,
    status ENUM('Pending','Confirmed','Completed','Cancelled')
        NOT NULL DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (service_id) REFERENCES services(id)
);

INSERT INTO services (name, description, price, duration) VALUES
('Haircut & Styling', 'A fresh cut and beautiful styling.', 350, 45),
('Hair Coloring', 'Color refresh or a new look.', 1200, 120),
('Manicure', 'Nail shaping and polish.', 250, 30),
('Pedicure', 'Relaxing foot and nail care.', 350, 45),
('Facial Treatment', 'A refreshing facial experience.', 600, 60),
('Hair Treatment', 'Nourishing hair care treatment.', 800, 60);

INSERT INTO products (name, description, price, stock) VALUES
('Silky Hair Serum', 'Smooth and shine-enhancing hair serum.', 299, 20),
('Rose Hand Cream', 'Moisturizing cream for soft hands.', 149, 30),
('Pink Nail Polish', 'A lovely pink polish for everyday wear.', 99, 40),
('Repair Hair Mask', 'Deep conditioning hair mask.', 349, 15),
('Gentle Facial Cleanser', 'A gentle daily facial cleanser.', 249, 25);
