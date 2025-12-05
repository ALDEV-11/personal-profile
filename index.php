<?php
/**
 * Frontend Landing Page
 * Personal Profile Website
 */

// Include backend config untuk ambil data dari database
require_once __DIR__ . '/backend/database/config.php';
require_once __DIR__ . '/backend/database/functions.php';

// Get all data from database
$profile = getProfile();
$skills = getAllSkills();
$projects = getAllProjects();
$education = getAllEducation();
$experience = getAllExperience();
$socialMedia = getAllSocialMedia();

// Group skills by category
$skillsByCategory = [];
foreach ($skills as $skill) {
    $skillsByCategory[$skill['category']][] = $skill;
}

// Handle contact form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_submit'])) {
    $data = [
        'name' => sanitize($_POST['name']),
        'email' => sanitize($_POST['email']),
        'subject' => sanitize($_POST['subject']),
        'message' => sanitize($_POST['message'])
    ];
    
    if (createContactMessage($data)) {
        // Gunakan session flash message
        setFlashMessage('success', 'Pesan berhasil dikirim! Terima kasih sudah menghubungi saya.');
        header('Location: ' . $_SERVER['PHP_SELF'] . '#contact');
        exit;
    } else {
        setFlashMessage('error', 'Gagal mengirim pesan. Silakan coba lagi.');
        header('Location: ' . $_SERVER['PHP_SELF'] . '#contact');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <!-- SEO Meta Tags -->
    <title><?php echo htmlspecialchars($profile['full_name'] ?? 'Personal Profile'); ?> - <?php echo htmlspecialchars($profile['tagline'] ?? ''); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars(substr($profile['about_me'] ?? '', 0, 160)); ?>">
    <meta name="keywords" content="web developer, portfolio, <?php echo htmlspecialchars($profile['full_name'] ?? ''); ?>">
    <meta name="author" content="<?php echo htmlspecialchars($profile['full_name'] ?? ''); ?>">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?php echo htmlspecialchars($profile['full_name'] ?? ''); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars(substr($profile['about_me'] ?? '', 0, 160)); ?>">
    
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/png" href="uploads/favicon.png?v=2">
    <link rel="icon" type="image/png" sizes="32x32" href="uploads/favicon.png?v=2">
    <link rel="icon" type="image/png" sizes="16x16" href="uploads/favicon.png?v=2">
    <link rel="apple-touch-icon" sizes="180x180" href="uploads/Aldev.png?v=2">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="frontend/css/style.css">
</head>
<body>
    
    <!-- Navbar -->
    <nav class="navbar" id="navbar">
        <div class="container">
            <a href="#home" class="navbar-brand"><?php echo htmlspecialchars($profile['full_name'] ?? 'Personal Profile'); ?></a>
            
            <div class="navbar-toggle" id="navbarToggle">
                <span></span>
                <span></span>
                <span></span>
            </div>
            
            <ul class="navbar-menu" id="navbarMenu">
                <li><a href="#home" class="nav-link active">Home</a></li>
                <li><a href="#about" class="nav-link">About</a></li>
                <li><a href="#skills" class="nav-link">Skills</a></li>
                <li><a href="#projects" class="nav-link">Projects</a></li>
                <li><a href="#experience" class="nav-link">Experience</a></li>
                <li><a href="#contact" class="nav-link">Contact</a></li>
            </ul>
        </div>
    </nav>
    
    <!-- Hero Section -->
    <section class="hero-section" id="home">
        <div class="hero-bg"></div>
        <div class="container">
            <div class="hero-content">
                <div class="hero-text" data-animate="fade-up">
                    <h1 class="hero-title">
                        Hi, I'm <span class="gradient-text"><?php echo htmlspecialchars($profile['full_name'] ?? 'Your Name'); ?></span>
                    </h1>
                    <p class="hero-tagline"><?php echo htmlspecialchars($profile['tagline'] ?? ''); ?></p>
                    <p class="hero-description">
                        <?php echo htmlspecialchars(substr($profile['about_me'] ?? '', 0, 200)); ?>...
                    </p>
                    
                    <div class="hero-buttons">
                        <?php if (!empty($profile['resume_file'])): ?>
                            <a href="<?php echo UPLOAD_URL . 'resumes/' . $profile['resume_file']; ?>" class="btn btn-primary" download>
                                <i class="fas fa-download me-2"></i> Download CV
                            </a>
                        <?php endif; ?>
                        <a href="#contact" class="btn btn-outline">
                            <i class="fas fa-envelope me-2"></i> Contact Me
                        </a>
                    </div>
                    
                    <!-- Social Media Links -->
                    <div class="hero-social">
                        <?php foreach ($socialMedia as $social): ?>
                            <a href="<?php echo htmlspecialchars($social['url']); ?>" target="_blank" rel="noopener" title="<?php echo htmlspecialchars($social['platform']); ?>">
                                <i class="<?php echo htmlspecialchars($social['icon']); ?>"></i>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="hero-image">
                    <?php if (!empty($profile['profile_image'])): ?>
                        <img src="<?php echo UPLOAD_URL . 'profiles/' . $profile['profile_image']; ?>" alt="<?php echo htmlspecialchars($profile['full_name']); ?>">
                    <?php else: ?>
                        <div class="hero-image-placeholder">
                            <i class="fas fa-user"></i>
                        </div>
                    <?php endif; ?>
                    <div class="hero-image-bg"></div>
                </div>
            </div>
        </div>
        
        <!-- Scroll Down Indicator -->
        <div class="scroll-indicator">
            <a href="#about">
                <i class="fas fa-chevron-down"></i>
            </a>
        </div>
    </section>
    
    <!-- About Section -->
    <section class="section about-section" id="about">
        <div class="container">
            <div class="section-header" data-animate="fade-up">
                <h2 class="section-title">About Me</h2>
                <p class="section-subtitle">Get to know more about me</p>
            </div>
            
            <div class="about-content">
                <div class="about-text" data-animate="fade-up">
                    <p><?php echo nl2br(htmlspecialchars($profile['about_me'] ?? '')); ?></p>
                    
                    <div class="about-info">
                        <?php if (!empty($profile['email'])): ?>
                            <div class="info-item">
                                <i class="fas fa-envelope"></i>
                                <div>
                                    <strong>Email</strong>
                                    <p><a href="mailto:<?php echo htmlspecialchars($profile['email']); ?>"><?php echo htmlspecialchars($profile['email']); ?></a></p>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($profile['phone'])): ?>
                            <div class="info-item">
                                <i class="fas fa-phone"></i>
                                <div>
                                    <strong>Phone</strong>
                                    <p><a href="tel:<?php echo htmlspecialchars($profile['phone']); ?>"><?php echo htmlspecialchars($profile['phone']); ?></a></p>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($profile['location'])): ?>
                            <div class="info-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <div>
                                    <strong>Location</strong>
                                    <p><?php echo htmlspecialchars($profile['location']); ?></p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Skills Section -->
    <section class="section skills-section" id="skills">
        <div class="container">
            <div class="section-header" data-animate="fade-up">
                <h2 class="section-title">My Skills</h2>
                <p class="section-subtitle">Technologies and tools I work with</p>
            </div>
            
            <div class="skills-container">
                <?php foreach ($skillsByCategory as $category => $categorySkills): ?>
                    <div class="skills-category" data-animate="fade-up">
                        <h3 class="category-title"><?php echo htmlspecialchars($category); ?></h3>
                        <div class="skills-list">
                            <?php foreach ($categorySkills as $skill): 
                                $level = strtolower($skill['skill_level']);
                                $badgeClass = ($level == 'beginner') ? 'skill-badge-beginner' : 
                                              (($level == 'intermediate') ? 'skill-badge-intermediate' : 'skill-badge-advanced');
                            ?>
                                <div class="skill-item">
                                    <div class="skill-header">
                                        <div class="skill-name">
                                            <i class="<?php echo htmlspecialchars($skill['icon']); ?>"></i>
                                            <span><?php echo htmlspecialchars($skill['skill_name']); ?></span>
                                        </div>
                                        <span class="skill-level-badge <?php echo $badgeClass; ?>">
                                            <?php echo ucfirst($level); ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    
    <!-- Projects Section -->
    <section class="section projects-section" id="projects">
        <div class="container">
            <div class="section-header" data-animate="fade-up">
                <h2 class="section-title">My Projects</h2>
                <p class="section-subtitle">Some of my recent work</p>
            </div>
            
            <!-- Project Filter -->
            <div class="project-filter" data-animate="fade-up">
                <button class="filter-btn active" data-filter="all">All Projects</button>
                <button class="filter-btn" data-filter="featured">Featured</button>
            </div>
            
            <div class="projects-grid">
                <?php foreach ($projects as $project): ?>
                    <div class="project-card <?php echo $project['is_featured'] ? 'featured' : ''; ?>" data-animate="fade-up">
                        <?php if (!empty($project['image'])): ?>
                            <div class="project-image">
                                <img src="<?php echo UPLOAD_URL . 'projects/' . $project['image']; ?>" alt="<?php echo htmlspecialchars($project['project_title']); ?>">
                                <div class="project-overlay">
                                    <div class="project-links">
                                        <?php if (!empty($project['project_url'])): ?>
                                            <a href="<?php echo htmlspecialchars($project['project_url']); ?>" target="_blank" class="project-link" title="View Demo">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if (!empty($project['github_url'])): ?>
                                            <a href="<?php echo htmlspecialchars($project['github_url']); ?>" target="_blank" class="project-link" title="View Code">
                                                <i class="fab fa-github"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <div class="project-content">
                            <?php if ($project['is_featured']): ?>
                                <span class="project-badge">Featured</span>
                            <?php endif; ?>
                            
                            <h3 class="project-title"><?php echo htmlspecialchars($project['project_title']); ?></h3>
                            <p class="project-description"><?php echo truncate($project['description'], 120); ?></p>
                            
                            <?php if (!empty($project['technologies'])): ?>
                                <div class="project-tech">
                                    <?php 
                                    $techs = explode(',', $project['technologies']);
                                    foreach ($techs as $tech): 
                                    ?>
                                        <span class="tech-tag"><?php echo htmlspecialchars(trim($tech)); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    
    <!-- Experience Section -->
    <section class="section experience-section" id="experience">
        <div class="container">
            <div class="section-header" data-animate="fade-up">
                <h2 class="section-title">Experience & Education</h2>
                <p class="section-subtitle">My professional journey</p>
            </div>
            
            <div class="timeline-container">
                <!-- Experience Timeline -->
                <div class="timeline-column" data-animate="fade-up">
                    <h3 class="timeline-heading">
                        <i class="fas fa-briefcase me-2"></i> Work Experience
                    </h3>
                    <div class="timeline">
                        <?php foreach ($experience as $exp): ?>
                            <div class="timeline-item">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <h4><?php echo htmlspecialchars($exp['position']); ?></h4>
                                    <h5><?php echo htmlspecialchars($exp['company']); ?></h5>
                                    <p class="timeline-date">
                                        <i class="fas fa-calendar me-1"></i>
                                        <?php echo formatDateIndo($exp['start_date']); ?> - 
                                        <?php echo $exp['is_current'] ? 'Present' : formatDateIndo($exp['end_date']); ?>
                                    </p>
                                    <?php if (!empty($exp['description'])): ?>
                                        <p class="timeline-description"><?php echo nl2br(htmlspecialchars($exp['description'])); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Education Timeline -->
                <div class="timeline-column" data-animate="fade-up" data-delay="200">
                    <h3 class="timeline-heading">
                        <i class="fas fa-graduation-cap me-2"></i> Education
                    </h3>
                    <div class="timeline">
                        <?php foreach ($education as $edu): ?>
                            <div class="timeline-item">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <h4><?php echo htmlspecialchars($edu['degree']); ?></h4>
                                    <h5><?php echo htmlspecialchars($edu['institution']); ?></h5>
                                    <p class="timeline-subtitle"><?php echo htmlspecialchars($edu['field_of_study']); ?></p>
                                    <p class="timeline-date">
                                        <i class="fas fa-calendar me-1"></i>
                                        <?php echo formatDateIndo($edu['start_date']); ?> - 
                                        <?php echo formatDateIndo($edu['end_date']); ?>
                                    </p>
                                    <?php if (!empty($edu['description'])): ?>
                                        <p class="timeline-description"><?php echo nl2br(htmlspecialchars($edu['description'])); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Contact Section -->
    <section class="section contact-section" id="contact">
        <div class="container">
            <div class="section-header" data-animate="fade-up">
                <h2 class="section-title">Get In Touch</h2>
                <p class="section-subtitle">Feel free to contact me</p>
            </div>
            
            <div class="contact-content">
                <div class="contact-info" data-animate="fade-up">
                    <h3>Let's talk about everything!</h3>
                    <p>Don't like forms? Send me an <a href="mailto:<?php echo htmlspecialchars($profile['email']); ?>">email</a>. 👋</p>
                    
                    <div class="contact-details">
                        <?php if (!empty($profile['email'])): ?>
                            <div class="contact-item">
                                <i class="fas fa-envelope"></i>
                                <a href="mailto:<?php echo htmlspecialchars($profile['email']); ?>"><?php echo htmlspecialchars($profile['email']); ?></a>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($profile['phone'])): ?>
                            <div class="contact-item">
                                <i class="fas fa-phone"></i>
                                <a href="tel:<?php echo htmlspecialchars($profile['phone']); ?>"><?php echo htmlspecialchars($profile['phone']); ?></a>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($profile['location'])): ?>
                            <div class="contact-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span><?php echo htmlspecialchars($profile['location']); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="contact-social">
                        <?php foreach ($socialMedia as $social): ?>
                            <a href="<?php echo htmlspecialchars($social['url']); ?>" target="_blank" title="<?php echo htmlspecialchars($social['platform']); ?>">
                                <i class="<?php echo htmlspecialchars($social['icon']); ?>"></i>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="contact-form-wrapper" data-animate="fade-up" data-delay="200">
                    <?php 
                    $flash = getFlashMessage();
                    if ($flash): 
                    ?>
                        <div class="alert alert-<?php echo $flash['type'] === 'success' ? 'success' : 'error'; ?>" id="flashAlert">
                            <?php echo htmlspecialchars($flash['message']); ?>
                        </div>
                    <?php endif; ?>
                    
                    <form class="contact-form" method="POST" id="contactForm">
                        <div class="form-group">
                            <label for="name">Name </label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email </label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="subject">Subject </label>
                            <input type="text" id="subject" name="subject" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="message">Message </label>
                            <textarea id="message" name="message" rows="5" required></textarea>
                        </div>
                        
                        <button type="submit" name="contact_submit" class="btn btn-primary btn-block">
                            <i class="fas fa-paper-plane"></i> Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
    
    <?php include __DIR__ . '/frontend/partials/footer.php'; ?>
    
    <!-- Back to Top Button -->
    <button class="back-to-top" id="backToTop">
        <i class="fas fa-chevron-up"></i>
    </button>
    
    <!-- Custom JavaScript -->
    <script src="frontend/js/main.js"></script>
    
    <!-- Auto-hide flash message -->
    <script>
        // Auto-hide alert setelah 5 detik
        const flashAlert = document.getElementById('flashAlert');
        if (flashAlert) {
            setTimeout(function() {
                flashAlert.style.transition = 'opacity 0.5s ease';
                flashAlert.style.opacity = '0';
                setTimeout(function() {
                    flashAlert.remove();
                }, 500);
            }, 3000); // 3 detik
        }
        
        // Setelah notifikasi hilang, clear hash jika ada
        if (window.location.hash === '#contact') {
            setTimeout(function() {
                if (window.history.replaceState) {
                    window.history.replaceState({}, document.title, window.location.pathname);
                }
            }, 6000); // 6 detik (5 detik tampil + 1 detik buffer)
        }
    </script>
    
</body>
</html>
