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

-- Sample data untuk users (password: admin123 - akan di-hash di PHP)
INSERT INTO users (username, password, email) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@example.com');
-- Password hash untuk 'admin123' menggunakan bcrypt

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

-- Sample data profile
INSERT INTO profile (full_name, tagline, about_me, phone, email, location, profile_image, resume_file) VALUES
(
    'Ahmad Rizki Pratama',
    'Full Stack Web Developer | UI/UX Enthusiast',
    'Saya adalah seorang Full Stack Web Developer dengan pengalaman lebih dari 3 tahun dalam mengembangkan aplikasi web modern. Passionate dalam menciptakan solusi digital yang user-friendly dan memiliki performa tinggi. Mahir dalam berbagai teknologi web seperti PHP, JavaScript, MySQL, dan framework modern. Saya selalu antusias untuk belajar teknologi baru dan berkontribusi dalam project yang challenging.',
    '+62 812-3456-7890',
    'ahmad.rizki@example.com',
    'Jakarta, Indonesia',
    'profile.jpg',
    'cv_ahmad_rizki.pdf'
);

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

-- Sample data skills
INSERT INTO skills (skill_name, skill_level, category, icon, display_order) VALUES
-- Frontend Skills
('HTML5', 95, 'Frontend', 'fab fa-html5', 1),
('CSS3', 90, 'Frontend', 'fab fa-css3-alt', 2),
('JavaScript', 85, 'Frontend', 'fab fa-js', 3),
('React.js', 75, 'Frontend', 'fab fa-react', 4),
('Bootstrap', 90, 'Frontend', 'fab fa-bootstrap', 5),
('Tailwind CSS', 80, 'Frontend', 'fas fa-wind', 6),

-- Backend Skills
('PHP', 90, 'Backend', 'fab fa-php', 7),
('MySQL', 85, 'Backend', 'fas fa-database', 8),
('Node.js', 75, 'Backend', 'fab fa-node-js', 9),
('Laravel', 80, 'Backend', 'fab fa-laravel', 10),
('RESTful API', 85, 'Backend', 'fas fa-server', 11),

-- Tools & Others
('Git', 85, 'Tools', 'fab fa-git-alt', 12),
('GitHub', 85, 'Tools', 'fab fa-github', 13),
('VS Code', 90, 'Tools', 'fas fa-code', 14),
('Figma', 75, 'Tools', 'fab fa-figma', 15),
('Photoshop', 70, 'Tools', 'fas fa-image', 16);

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

-- Sample data projects
INSERT INTO projects (project_title, description, image, project_url, github_url, technologies, start_date, end_date, is_featured, display_order) VALUES
(
    'E-Commerce Platform',
    'Platform e-commerce lengkap dengan fitur shopping cart, payment gateway integration, admin dashboard untuk manage products, orders, dan customers. Dilengkapi dengan real-time notifications dan responsive design.',
    'project1.jpg',
    'https://demo-ecommerce.example.com',
    'https://github.com/username/ecommerce-platform',
    'PHP,MySQL,JavaScript,Bootstrap,PayPal API',
    '2024-01-15',
    '2024-06-30',
    TRUE,
    1
),
(
    'School Management System',
    'Sistem informasi manajemen sekolah yang mencakup modul untuk siswa, guru, kelas, jadwal, nilai, dan absensi. Memiliki 3 level akses: Admin, Guru, dan Siswa dengan dashboard masing-masing.',
    'project2.jpg',
    'https://demo-school.example.com',
    'https://github.com/username/school-management',
    'PHP,MySQL,jQuery,AdminLTE,Chart.js',
    '2023-08-01',
    '2023-12-20',
    TRUE,
    2
),
(
    'Restaurant Reservation App',
    'Aplikasi reservasi restoran online dengan fitur booking meja, menu digital, payment online, dan rating system. Admin dapat manage reservations, menu items, dan view analytics.',
    'project3.jpg',
    'https://demo-restaurant.example.com',
    'https://github.com/username/restaurant-app',
    'PHP,MySQL,JavaScript,Bootstrap,Google Maps API',
    '2024-03-10',
    '2024-07-15',
    TRUE,
    3
),
(
    'Personal Blog CMS',
    'Content Management System untuk personal blog dengan fitur rich text editor, category & tags, comments, search functionality, dan SEO optimization. Responsive design untuk semua devices.',
    'project4.jpg',
    'https://demo-blog.example.com',
    'https://github.com/username/blog-cms',
    'PHP,MySQL,JavaScript,TinyMCE,Bootstrap',
    '2023-05-01',
    '2023-07-30',
    FALSE,
    4
),
(
    'Task Management Dashboard',
    'Dashboard untuk project management dengan fitur kanban board, task assignment, deadline tracking, team collaboration, dan progress reporting. Terintegrasi dengan notifications system.',
    'project5.jpg',
    'https://demo-taskmanager.example.com',
    'https://github.com/username/task-manager',
    'PHP,MySQL,JavaScript,Sortable.js,Bootstrap',
    '2024-02-01',
    '2024-05-15',
    FALSE,
    5
),
(
    'Weather Forecast Application',
    'Aplikasi prakiraan cuaca menggunakan API integration dengan fitur current weather, 7-day forecast, location search, dan weather alerts. Clean UI dengan weather animations.',
    'project6.jpg',
    'https://demo-weather.example.com',
    'https://github.com/username/weather-app',
    'HTML,CSS,JavaScript,OpenWeather API',
    '2023-10-05',
    '2023-11-10',
    FALSE,
    6
);

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

-- Sample data education
INSERT INTO education (institution, degree, field_of_study, start_date, end_date, description, display_order) VALUES
(
    'Universitas Indonesia',
    'Sarjana Komputer (S.Kom)',
    'Teknik Informatika',
    '2018-08-01',
    '2022-07-15',
    'Fokus pada pengembangan software dan web technologies. IPK: 3.75/4.00. Tugas Akhir: Sistem Rekomendasi E-Learning berbasis Machine Learning. Aktif di organisasi kampus sebagai ketua divisi IT HMTC.',
    1
),
(
    'SMK Negeri 1 Jakarta',
    'Diploma',
    'Rekayasa Perangkat Lunak',
    '2015-07-01',
    '2018-06-15',
    'Mempelajari dasar-dasar pemrograman, database, dan networking. Juara 1 Lomba Kompetensi Siswa (LKS) bidang Web Technologies tingkat Provinsi. Praktek Kerja Industri di PT. Tech Solutions Indonesia.',
    2
);

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

-- Sample data experience
INSERT INTO experience (company, position, start_date, end_date, is_current, description, display_order) VALUES
(
    'PT. Digital Kreasi Indonesia',
    'Full Stack Web Developer',
    '2023-08-01',
    NULL,
    TRUE,
    'Mengembangkan dan maintain aplikasi web untuk klien dari berbagai industri. Bertanggung jawab dalam full development cycle dari requirement analysis hingga deployment. Teknologi: PHP, Laravel, MySQL, JavaScript, Vue.js, Git. Berhasil menghandle 10+ project dengan client satisfaction rate 95%.',
    1
),
(
    'PT. Mitra Solusi Technology',
    'Junior Web Developer',
    '2022-08-15',
    '2023-07-30',
    FALSE,
    'Membantu tim development dalam pembuatan website dan aplikasi berbasis web. Fokus pada frontend development menggunakan HTML, CSS, JavaScript, dan Bootstrap. Melakukan bug fixing dan feature enhancement pada existing projects. Kolaborasi dengan designer dan backend developer.',
    2
),
(
    'CV. Tech Startup Hub',
    'Web Developer Intern',
    '2022-02-01',
    '2022-06-30',
    FALSE,
    'Program magang selama 5 bulan sebagai Web Developer. Mempelajari workflow development di industri, version control dengan Git, dan best practices dalam coding. Berkontribusi dalam pembuatan company profile website dan internal dashboard application.',
    3
),
(
    'Freelance Projects',
    'Freelance Web Developer',
    '2020-01-01',
    '2022-07-31',
    FALSE,
    'Mengerjakan berbagai project freelance untuk UMKM dan startup. Membuat website company profile, landing pages, e-commerce, dan custom web applications. Menangani 15+ client dengan repeat order rate 60%. Teknologi: PHP, MySQL, JavaScript, WordPress customization.',
    4
);

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

-- Sample data social media
INSERT INTO social_media (platform, url, icon, display_order) VALUES
('GitHub', 'https://github.com/ahmadrzkipratama', 'fab fa-github', 1),
('LinkedIn', 'https://linkedin.com/in/ahmadrzkipratama', 'fab fa-linkedin', 2),
('Instagram', 'https://instagram.com/ahmad.rzki', 'fab fa-instagram', 3),
('Twitter', 'https://twitter.com/ahmadrzkipratama', 'fab fa-twitter', 4),
('Email', 'mailto:ahmad.rizki@example.com', 'fas fa-envelope', 5),
('WhatsApp', 'https://wa.me/6281234567890', 'fab fa-whatsapp', 6);

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

-- Sample data contact messages
INSERT INTO contact_messages (name, email, subject, message, is_read, created_at) VALUES
(
    'Budi Santoso',
    'budi.santoso@example.com',
    'Inquiry tentang Project Web Development',
    'Halo Ahmad, saya tertarik dengan portfolio Anda dan ingin berdiskusi tentang project web development untuk perusahaan saya. Mohon hubungi saya kembali. Terima kasih.',
    TRUE,
    '2024-11-25 10:30:00'
),
(
    'Siti Rahayu',
    'siti.rahayu@company.com',
    'Collaboration Opportunity',
    'Hi Ahmad, kami dari PT. Digital Solutions mencari Full Stack Developer untuk project jangka panjang. Portfolio Anda sangat impressive. Apakah Anda available untuk discuss lebih lanjut?',
    TRUE,
    '2024-11-26 14:20:00'
),
(
    'Doni Prakoso',
    'doni.p@startup.id',
    'Konsultasi Website E-Commerce',
    'Mas Ahmad, saya ingin membuat website e-commerce untuk bisnis saya. Bisa minta estimasi waktu dan budget? Bisnis saya di bidang fashion. Terima kasih.',
    FALSE,
    '2024-11-28 09:15:00'
),
(
    'Rina Wulandari',
    'rina.wulandari@email.com',
    'Question about Your Skills',
    'Halo, saya sedang belajar web development. Boleh tanya-tanya tentang learning path yang Anda recommend? Terutama untuk backend development. Thanks!',
    FALSE,
    '2024-11-29 16:45:00'
),
(
    'Ahmad Fauzi',
    'ahmad.fauzi@tech.co.id',
    'Job Opportunity',
    'Dear Ahmad, kami sedang membuka posisi Senior Web Developer. Salary dan benefit menarik. Silakan check email detail yang sudah saya kirim. Looking forward to hearing from you.',
    FALSE,
    '2024-11-30 11:00:00'
);

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
