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
            <a href="<?php echo BACKEND_URL; ?>index.php" class="<?php echo $currentPage === 'dashboard' ? 'active' : ''; ?>">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
        </li>
        
        <li>
            <a href="<?php echo BACKEND_URL; ?>profile/" class="<?php echo $currentPage === 'index' && strpos($_SERVER['REQUEST_URI'], '/profile/') !== false ? 'active' : ''; ?>">
                <i class="fas fa-user-circle"></i>
                <span>Profile</span>
            </a>
        </li>
        
        <li>
            <a href="<?php echo BACKEND_URL; ?>skills/" class="<?php echo $currentPage === 'index' && strpos($_SERVER['REQUEST_URI'], '/skills/') !== false ? 'active' : ''; ?>">
                <i class="fas fa-code"></i>
                <span>Skills</span>
            </a>
        </li>
        
        <li>
            <a href="<?php echo BACKEND_URL; ?>projects/" class="<?php echo $currentPage === 'index' && strpos($_SERVER['REQUEST_URI'], '/projects/') !== false ? 'active' : ''; ?>">
                <i class="fas fa-folder-open"></i>
                <span>Projects</span>
            </a>
        </li>
        
        <li>
            <a href="<?php echo BACKEND_URL; ?>education/" class="<?php echo $currentPage === 'index' && strpos($_SERVER['REQUEST_URI'], '/education/') !== false ? 'active' : ''; ?>">
                <i class="fas fa-graduation-cap"></i>
                <span>Education</span>
            </a>
        </li>
        
        <li>
            <a href="<?php echo BACKEND_URL; ?>experience/" class="<?php echo $currentPage === 'index' && strpos($_SERVER['REQUEST_URI'], '/experience/') !== false ? 'active' : ''; ?>">
                <i class="fas fa-briefcase"></i>
                <span>Experience</span>
            </a>
        </li>
        
        <li>
            <a href="<?php echo BACKEND_URL; ?>social-media/" class="<?php echo $currentPage === 'index' && strpos($_SERVER['REQUEST_URI'], '/social-media/') !== false ? 'active' : ''; ?>">
                <i class="fas fa-share-alt"></i>
                <span>Social Media</span>
            </a>
        </li>
        
        <li>
            <a href="<?php echo BACKEND_URL; ?>messages/" class="<?php echo $currentPage === 'index' && strpos($_SERVER['REQUEST_URI'], '/messages/') !== false ? 'active' : ''; ?>">
                <i class="fas fa-envelope"></i>
                <span>Messages</span>
            </a>
        </li>
    </ul>
    
   
</aside>

<style>
    .sidebar-menu .badge {
        margin-left: auto;
    }
</style>
