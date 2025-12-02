<?php
/**
 * Sidebar Partial - Admin Panel
 * Navigation menu untuk admin panel
 */

$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$unreadCount = getUnreadMessagesCount();
?>

<!-- Sidebar -->
<aside class="sidebar">
    <ul class="sidebar-menu">
        <li>
            <a href="dashboard.php" class="<?php echo $currentPage === 'dashboard' ? 'active' : ''; ?>">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
        </li>
        
        <li>
            <a href="profile.php" class="<?php echo $currentPage === 'profile' ? 'active' : ''; ?>">
                <i class="fas fa-user-circle"></i>
                <span>Profile</span>
            </a>
        </li>
        
        <li>
            <a href="skills.php" class="<?php echo $currentPage === 'skills' || $currentPage === 'skill-form' ? 'active' : ''; ?>">
                <i class="fas fa-code"></i>
                <span>Skills</span>
            </a>
        </li>
        
        <li>
            <a href="projects.php" class="<?php echo $currentPage === 'projects' || $currentPage === 'project-form' ? 'active' : ''; ?>">
                <i class="fas fa-folder-open"></i>
                <span>Projects</span>
            </a>
        </li>
        
        <li>
            <a href="education.php" class="<?php echo $currentPage === 'education' || $currentPage === 'education-form' ? 'active' : ''; ?>">
                <i class="fas fa-graduation-cap"></i>
                <span>Education</span>
            </a>
        </li>
        
        <li>
            <a href="experience.php" class="<?php echo $currentPage === 'experience' || $currentPage === 'experience-form' ? 'active' : ''; ?>">
                <i class="fas fa-briefcase"></i>
                <span>Experience</span>
            </a>
        </li>
        
        <li>
            <a href="social-media.php" class="<?php echo $currentPage === 'social-media' || $currentPage === 'social-form' ? 'active' : ''; ?>">
                <i class="fas fa-share-alt"></i>
                <span>Social Media</span>
            </a>
        </li>
        
        <li>
            <a href="messages.php" class="<?php echo $currentPage === 'messages' ? 'active' : ''; ?>">
                <i class="fas fa-envelope"></i>
                <span>Messages</span>
                <?php if ($unreadCount > 0): ?>
                    <span class="badge bg-danger ms-auto"><?php echo $unreadCount; ?></span>
                <?php endif; ?>
            </a>
        </li>
    </ul>
    
    <div style="padding: 20px; border-top: 1px solid #E5E7EB; margin-top: 20px;">
        <div class="text-muted" style="font-size: 0.75rem;">
            <i class="fas fa-info-circle me-1"></i> Version <?php echo SITE_VERSION; ?>
        </div>
        <div class="text-muted" style="font-size: 0.75rem; margin-top: 5px;">
            © 2025 Personal Profile
        </div>
    </div>
</aside>

<style>
    .sidebar-menu .badge {
        margin-left: auto;
    }
</style>
