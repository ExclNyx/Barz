-- Barbershop Booking System Database Schema
-- Created: 2026-09-24

CREATE DATABASE IF NOT EXISTS barz_booking CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE barz_booking;

-- Users Table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'customer') DEFAULT 'customer',
    email_verified_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Services Table
CREATE TABLE services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    duration INT NOT NULL COMMENT 'Duration in minutes',
    image VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Barbers Table
CREATE TABLE barbers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    photo VARCHAR(255),
    specialization VARCHAR(255),
    experience_years INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bookings Table
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    service_id INT NOT NULL,
    barber_id INT NULL,
    booking_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled', 'no_show') DEFAULT 'pending',
    total_price DECIMAL(10, 2) NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE RESTRICT,
    FOREIGN KEY (barber_id) REFERENCES barbers(id) ON DELETE SET NULL,
    INDEX idx_customer (customer_id),
    INDEX idx_date (booking_date),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Schedules Table
CREATE TABLE schedules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    barber_id INT NULL,
    date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    is_available BOOLEAN DEFAULT TRUE,
    type ENUM('working_hours', 'break', 'blocked') DEFAULT 'working_hours',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (barber_id) REFERENCES barbers(id) ON DELETE CASCADE,
    INDEX idx_date (date),
    INDEX idx_barber_date (barber_id, date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Time Slots Table
CREATE TABLE time_slots (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    barber_id INT NULL,
    booking_id INT NULL,
    is_available BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (barber_id) REFERENCES barbers(id) ON DELETE CASCADE,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE SET NULL,
    INDEX idx_date (date),
    INDEX idx_available (is_available),
    INDEX idx_barber_date (barber_id, date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Chats Table
CREATE TABLE chats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    admin_id INT NULL,
    status ENUM('active', 'closed') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_customer (customer_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Chat Messages Table
CREATE TABLE chat_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chat_id INT NOT NULL,
    sender_id INT NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (chat_id) REFERENCES chats(id) ON DELETE CASCADE,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_chat (chat_id),
    INDEX idx_read (is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Settings Table
CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_key (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin user (password: admin123)
INSERT INTO users (name, email, phone, password, role) VALUES
('Admin', 'admin@barz.com', '081234567890', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert sample services
INSERT INTO services (name, description, price, duration, is_active) VALUES
('Haircut Basic', 'Potong rambut standar dengan teknik dasar', 35000, 30, TRUE),
('Haircut Premium', 'Potong rambut dengan styling modern dan konsultasi', 75000, 45, TRUE),
('Haircut + Shave', 'Paket lengkap potong rambut dan cukur jenggot', 85000, 60, TRUE),
('Kids Haircut', 'Potong rambut khusus anak-anak', 30000, 25, TRUE),
('Beard Trim', 'Rapikan jenggot dan kumis', 25000, 20, TRUE),
('Hair Coloring', 'Pewarnaan rambut dengan produk premium', 150000, 90, TRUE);

-- Insert sample barbers
INSERT INTO barbers (name, specialization, experience_years, is_active) VALUES
('Andi', 'Classic & Modern Cuts', 5, TRUE),
('Budi', 'Fade & Undercut Specialist', 3, TRUE),
('Cecep', 'Beard & Styling Expert', 7, TRUE);

-- Insert default settings
INSERT INTO settings (setting_key, setting_value) VALUES
('shop_name', 'Barz Barbershop'),
('shop_address', 'Jl. Contoh No. 123, Jakarta'),
('shop_phone', '021-12345678'),
('shop_email', 'info@barz.com'),
('opening_time', '09:00'),
('closing_time', '21:00'),
('slot_duration', '30'),
('booking_advance_days', '30'),
('whatsapp_number', '6281234567890');
