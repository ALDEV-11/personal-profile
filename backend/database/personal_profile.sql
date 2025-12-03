-- ============================================
-- Personal Profile Database Schema
-- Project PKL - Personal Website with Admin Panel
-- Created: December 2025
-- ============================================

-- Buat database
CREATE DATABASE IF NOT EXISTS personal_profile CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE personal_profile;

-- ============================================
-- Tabel: users
-- Deskripsi: Menyimpan data admin/user yang dapat login ke backend
-- ============================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabel: profile
-- Deskripsi: Data profil personal (hanya 1 row)
-- ============================================
CREATE TABLE profile (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    tagline VARCHAR(255) NOT NULL,
    about_me TEXT NOT NULL,
    phone VARCHAR(20),
    email VARCHAR(100) NOT NULL,
    location VARCHAR(100),
    profile_image VARCHAR(255),
    resume_file VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabel: skills
-- Deskripsi: Keahlian/skills dengan level dan kategori
-- ============================================
CREATE TABLE skills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    skill_name VARCHAR(100) NOT NULL,
    skill_level INT NOT NULL CHECK (skill_level >= 0 AND skill_level <= 100),
    category VARCHAR(50) NOT NULL,
    icon VARCHAR(100),
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabel: projects
-- Deskripsi: Portfolio project yang pernah dikerjakan
-- ============================================
CREATE TABLE projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    image VARCHAR(255),
    project_url VARCHAR(255),
    github_url VARCHAR(255),
    technologies TEXT,
    start_date DATE,
    end_date DATE,
    is_featured BOOLEAN DEFAULT FALSE,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================
-- Tabel: education
-- Deskripsi: Riwayat pendidikan
-- ============================================
CREATE TABLE education (
    id INT AUTO_INCREMENT PRIMARY KEY,
    institution VARCHAR(200) NOT NULL,
    degree VARCHAR(100) NOT NULL,
    field_of_study VARCHAR(100) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE,
    description TEXT,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabel: experience
-- Deskripsi: Pengalaman kerja/magang
-- ============================================
CREATE TABLE experience (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company VARCHAR(200) NOT NULL,
    position VARCHAR(100) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE,
    is_current BOOLEAN DEFAULT FALSE,
    description TEXT,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabel: social_media
-- Deskripsi: Link social media profiles
-- ============================================
CREATE TABLE social_media (
    id INT AUTO_INCREMENT PRIMARY KEY,
    platform VARCHAR(50) NOT NULL,
    url VARCHAR(255) NOT NULL,
    icon VARCHAR(100) NOT NULL,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabel: contact_messages
-- Deskripsi: Pesan dari contact form di frontend
-- ============================================
CREATE TABLE contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Indexes untuk performa
-- ============================================
CREATE INDEX idx_skills_category ON skills(category);
CREATE INDEX idx_projects_featured ON projects(is_featured);
CREATE INDEX idx_messages_read ON contact_messages(is_read);
CREATE INDEX idx_experience_current ON experience(is_current);

-- ============================================
-- End of Database Schema
-- ============================================
