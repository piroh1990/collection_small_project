-- URL Shortener Database Schema
-- Create this database and run this SQL to set up the tables

CREATE DATABASE IF NOT EXISTS url_shortener;
USE url_shortener;

-- Main URLs table
CREATE TABLE shortener_urls (
    id INT AUTO_INCREMENT PRIMARY KEY,
    short_code VARCHAR(50) NOT NULL UNIQUE,
    original_url TEXT NOT NULL,
    custom_alias VARCHAR(100) UNIQUE,
    title VARCHAR(255),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE,
    click_count INT DEFAULT 0,
    last_clicked TIMESTAMP NULL,
    created_by_ip VARCHAR(45),
    INDEX idx_short_code (short_code),
    INDEX idx_custom_alias (custom_alias),
    INDEX idx_active (is_active)
);

-- Click tracking table
CREATE TABLE shortener_clicks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    url_id INT NOT NULL,
    clicked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    user_agent TEXT,
    referrer TEXT,
    country VARCHAR(2),
    city VARCHAR(100),
    FOREIGN KEY (url_id) REFERENCES shortener_urls(id) ON DELETE CASCADE,
    INDEX idx_url_id (url_id),
    INDEX idx_clicked_at (clicked_at)
);

-- Admin users table (simple token-based auth)
CREATE TABLE shortener_admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    token VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE
);

-- Insert default admin user
INSERT INTO shortener_admin_users (username, token) VALUES ('admin', 'yxt');

-- Optional: QR code cache table for performance
CREATE TABLE shortener_qr_cache (
    id INT AUTO_INCREMENT PRIMARY KEY,
    url_id INT NOT NULL,
    qr_data LONGBLOB,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (url_id) REFERENCES shortener_urls(id) ON DELETE CASCADE,
    UNIQUE KEY unique_url (url_id)
);