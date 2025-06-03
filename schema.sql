-- Schema SQL untuk Aplikasi Booking Kos
-- Database: kos_db

-- Buat database jika belum ada
CREATE DATABASE IF NOT EXISTS kos_db;
USE kos_db;

-- Tabel Users untuk autentikasi pengguna
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE,
    no_hp VARCHAR(20),
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Rooms untuk menyimpan data kamar kos
CREATE TABLE IF NOT EXISTS rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    capacity INT DEFAULT 1,
    facilities TEXT, -- JSON string untuk fasilitas kamar
    status ENUM('available', 'occupied', 'maintenance') DEFAULT 'available',
    image_url VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Bookings untuk menyimpan data booking kamar
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    room_id INT NOT NULL,
    date DATE, -- untuk booking yang menggunakan date saja
    start_date DATE, -- untuk booking yang menggunakan range tanggal
    end_date DATE, -- untuk booking yang menggunakan range tanggal
    status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
    total_price DECIMAL(10, 2),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE
);

-- Index untuk optimasi query
CREATE INDEX idx_bookings_user_id ON bookings(user_id);
CREATE INDEX idx_bookings_room_id ON bookings(room_id);
CREATE INDEX idx_bookings_dates ON bookings(start_date, end_date);
CREATE INDEX idx_rooms_status ON rooms(status);

-- Insert data default admin
INSERT INTO users (username, password, email, role) VALUES 
('admin', 'admin123', 'admin@kos.com', 'admin')
ON DUPLICATE KEY UPDATE username = username;

-- Insert data sample kamar
INSERT INTO rooms (name, description, price, capacity, facilities, status, image_url) VALUES 
('Kamar Single A1', 'Kamar nyaman dengan AC dan WiFi gratis', 500000.00, 1, '["AC", "WiFi", "Lemari", "Meja Belajar"]', 'available', 'https://example.com/room1.jpg'),
('Kamar Single A2', 'Kamar strategis dekat kampus dengan fasilitas lengkap', 450000.00, 1, '["WiFi", "Lemari", "Meja Belajar", "Kamar Mandi Dalam"]', 'available', 'https://example.com/room2.jpg'),
('Kamar Double B1', 'Kamar untuk 2 orang dengan fasilitas modern', 750000.00, 2, '["AC", "WiFi", "Lemari Ganda", "Meja Belajar", "TV"]', 'available', 'https://example.com/room3.jpg')
ON DUPLICATE KEY UPDATE name = name;

-- Insert data sample user
INSERT INTO users (username, password, email, role) VALUES 
('john_doe', 'password123', 'john@email.com', 'user'),
('jane_smith', 'password456', 'jane@email.com', 'user')
ON DUPLICATE KEY UPDATE username = username;
