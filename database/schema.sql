-- Veritabanını oluştur
CREATE DATABASE IF NOT EXISTS freelancer_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE freelancer_management;

-- Kullanıcılar tablosu
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'freelancer', 'employer') NOT NULL,
    bio TEXT,
    skills TEXT,
    portfolio TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Kategoriler tablosu
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Projeler tablosu
CREATE TABLE IF NOT EXISTS projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT NOT NULL,
    category_id INT NOT NULL,
    min_budget DECIMAL(10,2) NOT NULL,
    max_budget DECIMAL(10,2) NOT NULL,
    deadline DATE NOT NULL,
    status ENUM('active', 'completed', 'canceled') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- Teklifler tablosu
CREATE TABLE IF NOT EXISTS bids (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    user_id INT NOT NULL,
    description TEXT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    delivery_time INT NOT NULL COMMENT 'Teslim süresi (gün)',
    status ENUM('pending', 'accepted', 'rejected') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE(project_id, user_id)
);

-- Sözleşmeler tablosu
CREATE TABLE IF NOT EXISTS contracts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    freelancer_id INT NOT NULL,
    employer_id INT NOT NULL,
    status ENUM('active', 'delivered', 'revision', 'completed') NOT NULL DEFAULT 'active',
    started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (freelancer_id) REFERENCES users(id),
    FOREIGN KEY (employer_id) REFERENCES users(id)
);

-- Faturalar tablosu
CREATE TABLE IF NOT EXISTS invoices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    contract_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'paid') NOT NULL DEFAULT 'pending',
    payment_method VARCHAR(50),
    payment_date TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (contract_id) REFERENCES contracts(id) ON DELETE CASCADE
);

-- Değerlendirmeler tablosu
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL COMMENT 'Değerlendiren kullanıcı',
    target_user_id INT NOT NULL COMMENT 'Değerlendirilen kullanıcı',
    project_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (target_user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    UNIQUE(user_id, target_user_id, project_id)
);

-- Bildirimler tablosu
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN NOT NULL DEFAULT FALSE,
    type VARCHAR(50) NOT NULL,
    related_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- E-posta tercihler tablosu
CREATE TABLE IF NOT EXISTS email_preferences (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    new_bid BOOLEAN NOT NULL DEFAULT TRUE,
    bid_accepted BOOLEAN NOT NULL DEFAULT TRUE,
    bid_rejected BOOLEAN NOT NULL DEFAULT TRUE,
    new_message BOOLEAN NOT NULL DEFAULT TRUE,
    project_completed BOOLEAN NOT NULL DEFAULT TRUE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Örnek kategoriler
INSERT INTO categories (name, description) VALUES
('Web Geliştirme', 'Web siteleri, web uygulamaları geliştirme'),
('Mobil Uygulama', 'iOS, Android uygulama geliştirme'),
('Grafik Tasarım', 'Logo, banner, arayüz tasarımı'),
('İçerik Yazarlığı', 'Blog, makale, içerik üretimi'),
('SEO & Dijital Pazarlama', 'Arama motoru optimizasyonu, dijital pazarlama'),
('Video & Animasyon', 'Video düzenleme, animasyon'),
('Çeviri & Dil Hizmetleri', 'Çeviri, düzeltme, yerelleştirme');

-- Örnek admin kullanıcısı
INSERT INTO users (name, email, password, role, bio) VALUES
('Admin', 'admin@example.com', '$2y$10$LPCzMROYJIu5I9g/S2l6aeO3UJcGlT5C5j1eZJRA5h2aQS.pVN0p.', 'admin', 'Site yöneticisi');
-- Not: Şifre: admin123 