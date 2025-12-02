<?php
/**
 * Dashboard Page
 * Halaman utama admin panel dengan statistics dan recent messages
 */

require_once 'config.php';
require_once 'functions.php';

// Proteksi halaman - harus login
requireLogin();

// Get statistics
$stats = getDashboardStats();
$recentMessages = getRecentMessages(5);
$currentUser = getCurrentUser();

// Get profile info
$profile = getProfile();

// Page title
$pageTitle = 'Dashboard';

// Include header
include 'partials/header.php';
include 'partials/sidebar.php';
?>

<!-- Main Content -->
<div class="main-content">
    
    <!-- Page Header -->
    <div class="page-header">
        <h1>Dashboard</h1>
        <p>Selamat datang kembali, <strong><?php echo $currentUser['username']; ?></strong>! Berikut adalah ringkasan data Anda.</p>
    </div>
    
    <!-- Flash Messages -->
    <?php displayFlashMessage(); ?>
    
    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <!-- Skills -->
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted text-uppercase" style="font-size: 0.75rem; font-weight: 600; letter-spacing: 0.05em;">
                                Total Skills
                            </div>
                            <h2 class="mb-0 mt-2" style="font-size: 2rem; font-weight: 700; color: var(--primary-color);">
                                <?php echo $stats['total_skills']; ?>
                            </h2>
                        </div>
                        <div class="stat-icon" style="width: 60px; height: 60px; border-radius: 12px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-code" style="font-size: 1.5rem; color: white;"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="skills.php" class="text-decoration-none" style="font-size: 0.875rem; color: var(--primary-color); font-weight: 500;">
                            Manage Skills <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Projects -->
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted text-uppercase" style="font-size: 0.75rem; font-weight: 600; letter-spacing: 0.05em;">
                                Total Projects
                            </div>
                            <h2 class="mb-0 mt-2" style="font-size: 2rem; font-weight: 700; color: #10B981;">
                                <?php echo $stats['total_projects']; ?>
                            </h2>
                            <div class="text-muted" style="font-size: 0.75rem; margin-top: 4px;">
                                <i class="fas fa-star text-warning"></i> <?php echo $stats['featured_projects']; ?> Featured
                            </div>
                        </div>
                        <div class="stat-icon" style="width: 60px; height: 60px; border-radius: 12px; background: linear-gradient(135deg, #10B981 0%, #059669 100%); display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-folder-open" style="font-size: 1.5rem; color: white;"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="projects.php" class="text-decoration-none" style="font-size: 0.875rem; color: #10B981; font-weight: 500;">
                            Manage Projects <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Education & Experience -->
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted text-uppercase" style="font-size: 0.75rem; font-weight: 600; letter-spacing: 0.05em;">
                                Education & Experience
                            </div>
                            <h2 class="mb-0 mt-2" style="font-size: 2rem; font-weight: 700; color: #F59E0B;">
                                <?php echo $stats['total_education'] + $stats['total_experience']; ?>
                            </h2>
                            <div class="text-muted" style="font-size: 0.75rem; margin-top: 4px;">
                                <i class="fas fa-graduation-cap"></i> <?php echo $stats['total_education']; ?> Education • 
                                <i class="fas fa-briefcase"></i> <?php echo $stats['total_experience']; ?> Experience
                            </div>
                        </div>
                        <div class="stat-icon" style="width: 60px; height: 60px; border-radius: 12px; background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%); display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-graduation-cap" style="font-size: 1.5rem; color: white;"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="education.php" class="text-decoration-none me-3" style="font-size: 0.875rem; color: #F59E0B; font-weight: 500;">
                            Education <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                        <a href="experience.php" class="text-decoration-none" style="font-size: 0.875rem; color: #F59E0B; font-weight: 500;">
                            Experience <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Messages -->
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted text-uppercase" style="font-size: 0.75rem; font-weight: 600; letter-spacing: 0.05em;">
                                Messages
                            </div>
                            <h2 class="mb-0 mt-2" style="font-size: 2rem; font-weight: 700; color: #EF4444;">
                                <?php echo $stats['total_messages']; ?>
                            </h2>
                            <div class="text-muted" style="font-size: 0.75rem; margin-top: 4px;">
                                <?php if ($stats['unread_messages'] > 0): ?>
                                    <span class="badge bg-danger"><?php echo $stats['unread_messages']; ?> Unread</span>
                                <?php else: ?>
                                    <i class="fas fa-check-circle text-success"></i> All Read
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="stat-icon" style="width: 60px; height: 60px; border-radius: 12px; background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%); display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-envelope" style="font-size: 1.5rem; color: white;"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="messages.php" class="text-decoration-none" style="font-size: 0.875rem; color: #EF4444; font-weight: 500;">
                            View Messages <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row g-4">
        <!-- Recent Messages -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-envelope me-2"></i> Recent Messages
                    </h5>
                    <a href="messages.php" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($recentMessages)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-inbox" style="font-size: 3rem; color: #D1D5DB; margin-bottom: 15px;"></i>
                            <p class="text-muted">Belum ada pesan</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 30px;"></th>
                                        <th>Name</th>
                                        <th>Subject</th>
                                        <th>Date</th>
                                        <th style="width: 100px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentMessages as $message): ?>
                                        <tr>
                                            <td>
                                                <?php if (!$message['is_read']): ?>
                                                    <span class="badge bg-primary" style="width: 8px; height: 8px; padding: 0; border-radius: 50%;"></span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($message['name']); ?></strong>
                                                <br>
                                                <small class="text-muted"><?php echo htmlspecialchars($message['email']); ?></small>
                                            </td>
                                            <td><?php echo truncate($message['subject'], 50); ?></td>
                                            <td>
                                                <small class="text-muted"><?php echo timeAgo($message['created_at']); ?></small>
                                            </td>
                                            <td>
                                                <a href="messages.php?view=<?php echo $message['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt me-2"></i> Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="profile.php" class="btn btn-outline-primary text-start">
                            <i class="fas fa-user-circle me-2"></i> Edit Profile
                        </a>
                        <a href="skill-form.php" class="btn btn-outline-primary text-start">
                            <i class="fas fa-plus-circle me-2"></i> Add New Skill
                        </a>
                        <a href="project-form.php" class="btn btn-outline-primary text-start">
                            <i class="fas fa-plus-circle me-2"></i> Add New Project
                        </a>
                        <a href="education-form.php" class="btn btn-outline-primary text-start">
                            <i class="fas fa-plus-circle me-2"></i> Add Education
                        </a>
                        <a href="experience-form.php" class="btn btn-outline-primary text-start">
                            <i class="fas fa-plus-circle me-2"></i> Add Experience
                        </a>
                        <a href="social-form.php" class="btn btn-outline-primary text-start">
                            <i class="fas fa-plus-circle me-2"></i> Add Social Media
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Profile Info -->
            <?php if ($profile): ?>
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-user me-2"></i> Profile Info
                    </h5>
                </div>
                <div class="card-body text-center">
                    <?php if (!empty($profile['profile_image'])): ?>
                        <img src="<?php echo UPLOAD_URL . 'profiles/' . $profile['profile_image']; ?>" alt="Profile" class="rounded-circle mb-3" style="width: 100px; height: 100px; object-fit: cover; border: 3px solid var(--primary-color);">
                    <?php else: ?>
                        <div class="rounded-circle mb-3 d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px; background: var(--primary-color); color: white; font-size: 2.5rem; font-weight: 700;">
                            <?php echo strtoupper(substr($profile['full_name'], 0, 1)); ?>
                        </div>
                    <?php endif; ?>
                    
                    <h5 class="mb-1"><?php echo htmlspecialchars($profile['full_name']); ?></h5>
                    <p class="text-muted mb-3" style="font-size: 0.9rem;"><?php echo htmlspecialchars($profile['tagline']); ?></p>
                    
                    <div class="text-start" style="font-size: 0.875rem;">
                        <div class="mb-2">
                            <i class="fas fa-envelope text-muted me-2"></i> <?php echo htmlspecialchars($profile['email']); ?>
                        </div>
                        <?php if (!empty($profile['phone'])): ?>
                        <div class="mb-2">
                            <i class="fas fa-phone text-muted me-2"></i> <?php echo htmlspecialchars($profile['phone']); ?>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($profile['location'])): ?>
                        <div class="mb-2">
                            <i class="fas fa-map-marker-alt text-muted me-2"></i> <?php echo htmlspecialchars($profile['location']); ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <a href="profile.php" class="btn btn-primary btn-sm mt-3">
                        <i class="fas fa-edit me-1"></i> Edit Profile
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
</div>

<style>
    .stat-card {
        transition: transform 0.3s, box-shadow 0.3s;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
    }
</style>

<?php include 'partials/footer.php'; ?>
