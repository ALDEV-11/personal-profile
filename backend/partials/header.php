<?php
/**
 * Header Partial - Admin Panel
 * Berisi HTML head, meta tags, CSS links, dan navbar
 */

$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$pageTitle = ucfirst(str_replace('-', ' ', $currentPage));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo $pageTitle; ?> - <?php echo SITE_NAME; ?></title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/png" href="<?php echo BASE_URL; ?>uploads/favicon.png?v=2">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo BASE_URL; ?>uploads/favicon.png?v=2">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo BASE_URL; ?>uploads/favicon.png?v=2">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo BASE_URL; ?>uploads/Aldev.png?v=2">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    <!-- Custom Admin CSS -->
    <style>
        :root {
            --primary-color: #4F46E5;
            --primary-dark: #4338CA;
            --secondary-color: #10B981;
            --danger-color: #EF4444;
            --warning-color: #F59E0B;
            --info-color: #3B82F6;
            --dark-color: #1F2937;
            --light-color: #F9FAFB;
            --sidebar-width: 260px;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--light-color);
            color: var(--dark-color);
        }
        
        /* Navbar */
        .navbar {
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .navbar-brand {
            font-weight: 700;
            color: var(--primary-color) !important;
            font-size: 1.25rem;
        }
        
        .navbar .dropdown-toggle {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .navbar .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }
        
        /* Notification Badge */
        .navbar .nav-item {
            display: flex;
            align-items: center;
        }
        
        .navbar .nav-link {
            display: flex;
            align-items: center;
            padding: 0.5rem 1rem;
            position: relative;
        }
        
        .notification-badge {
            position: absolute;
            top: -2px;
            right: 2px;
            background: var(--danger-color);
            color: white;
            font-size: 0.6rem;
            font-weight: 700;
            padding: 2px 4px;
            border-radius: 8px;
            min-width: 16px;
            height: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
            box-shadow: 0 2px 4px rgba(239, 68, 68, 0.3);
            border: 2px solid white;
        }
        
        .nav-link i.fa-bell {
            color: #6B7280;
            transition: all 0.3s;
            font-size: 1.25rem;
        }
        
        .nav-link:hover i.fa-bell {
            color: var(--primary-color);
            transform: rotate(15deg);
        }
        
        /* Responsive Bell Icon */
        @media (max-width: 1366px) {
            .nav-link i.fa-bell {
                font-size: 1.125rem;
            }
            
            .notification-badge {
                font-size: 0.55rem;
                min-width: 15px;
                height: 15px;
                padding: 1.5px 3.5px;
            }
        }
        
        @media (max-width: 991px) {
            .nav-link i.fa-bell {
                font-size: 1rem;
            }
            
            .notification-badge {
                font-size: 0.5rem;
                min-width: 14px;
                height: 14px;
                padding: 1.5px 3px;
                top: -1px;
                right: 3px;
            }
        }
        
        @media (max-width: 767px) {
            .nav-link i.fa-bell {
                font-size: 1.375rem;
            }
            
            .notification-badge {
                font-size: 0.6rem;
                min-width: 16px;
                height: 16px;
                padding: 2px 4px;
                top: -2px;
                right: 2px;
            }
        }
        
        @media (max-width: 575px) {
            .nav-link i.fa-bell {
                font-size: 1.25rem;
            }
            
            .notification-badge {
                font-size: 0.55rem;
                min-width: 15px;
                height: 15px;
                padding: 1.5px 3.5px;
            }
        }
        
        @media (max-width: 374px) {
            .nav-link i.fa-bell {
                font-size: 1.125rem;
            }
            
            .notification-badge {
                font-size: 0.5rem;
                min-width: 14px;
                height: 14px;
                padding: 1.5px 3px;
                top: -1px;
                right: 1px;
            }
        }
        
        /* Responsive Add New Button */
        @media (max-width: 1366px) {
            .btn {
                font-size: 0.9rem;
                padding: 9px 18px;
                border-radius: 7px;
            }
        }
        
        @media (max-width: 991px) {
            .btn {
                font-size: 0.85rem;
                padding: 8px 16px;
                border-radius: 6px;
            }
            
            .btn i {
                font-size: 0.85rem;
            }
        }
        
        @media (max-width: 767px) {
            .btn {
                font-size: 0.8rem;
                padding: 7px 14px;
                border-radius: 6px;
            }
            
            .btn i {
                font-size: 0.8rem;
            }
        }
        
        @media (max-width: 575px) {
            .btn {
                font-size: 0.75rem;
                padding: 6px 12px;
                border-radius: 5px;
            }
            
            .btn i {
                font-size: 0.75rem;
            }
        }
        
        @media (max-width: 374px) {
            .btn {
                font-size: 0.7rem;
                padding: 5px 10px;
                border-radius: 4px;
            }
            
            .btn i {
                font-size: 0.7rem;
            }
        }
        
        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 56px;
            left: 0;
            width: var(--sidebar-width);
            height: calc(100vh - 56px);
            background: white;
            box-shadow: 2px 0 4px rgba(0,0,0,0.08);
            overflow-y: auto;
            z-index: 999;
            transition: transform 0.3s ease;
        }
        
        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid #E5E7EB;
        }
        
        .sidebar-menu {
            list-style: none;
            padding: 10px 0;
        }
        
        .sidebar-menu li {
            margin: 5px 0;
        }
        
        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: #6B7280;
            text-decoration: none;
            transition: all 0.3s;
            position: relative;
        }
        
        .sidebar-menu a i {
            width: 24px;
            margin-right: 12px;
            font-size: 1.1rem;
        }
        
        .sidebar-menu a:hover {
            background: var(--light-color);
            color: var(--primary-color);
        }
        
        .sidebar-menu a.active {
            background: linear-gradient(90deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
        }
        
        .sidebar-menu a.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: white;
        }
        
        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 30px;
            min-height: calc(100vh - 56px);
        }
        
        /* Page Header */
        .page-header {
            margin-bottom: 30px;
        }
        
        .page-header h1 {
            font-size: 1.875rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 8px;
        }
        
        .page-header p {
            color: #6B7280;
            font-size: 0.95rem;
        }
        
        /* Cards */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            margin-bottom: 24px;
        }
        
        .card-header {
            background: white;
            border-bottom: 1px solid #E5E7EB;
            padding: 16px 20px;
            font-weight: 600;
            border-radius: 12px 12px 0 0 !important;
        }
        
        /* Buttons */
        .btn {
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(79, 70, 229, 0.3);
        }
        
        /* Table */
        .table {
            background: white;
        }
        
        .table thead th {
            border-bottom: 2px solid #E5E7EB;
            color: var(--dark-color);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            padding: 12px;
        }
        
        .table tbody td {
            padding: 12px;
            vertical-align: middle;
        }
        
        /* Badge */
        .badge {
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.75rem;
        }
        
        /* Form */
        .form-label {
            font-weight: 500;
            color: var(--dark-color);
            margin-bottom: 8px;
        }
        
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #D1D5DB;
            padding: 10px 14px;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
                padding: 20px;
            }
        }
        
        /* Loading */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Admin Footer */
        .admin-footer {
            background: #ffffff;
            border-top: 1px solid #e5e7eb;
            padding: 1.5rem 2rem;
            margin-top: 3rem;
            margin-left: 250px;
            position: relative;
            bottom: 0;
            width: calc(100% - 250px);
        }
        
        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .footer-left,
        .footer-right {
            margin: 0;
        }
        
        .footer-left p,
        .footer-right p {
            margin: 0;
            font-size: 0.875rem;
            color: #6b7280;
        }
        
        .admin-footer .fa-heart {
            color: #ef4444;
            animation: heartbeat 1.5s ease-in-out infinite;
            margin: 0 4px;
        }
        
        @keyframes heartbeat {
            0%, 100% { transform: scale(1); }
            25% { transform: scale(1.15); }
            50% { transform: scale(1); }
        }
        
        /* Responsive footer */
        @media (max-width: 768px) {
            .admin-footer {
                margin-left: 0;
                width: 100%;
            }
            
            .footer-content {
                flex-direction: column;
                text-align: center;
                gap: 0.5rem;
            }
        }
    </style>
</head>
<body>
    
    <!-- Anti-Back-Button Security Script -->
    <script>
        // Prevent back button after logout
        (function() {
            // Disable back button
            history.pushState(null, null, location.href);
            window.onpopstate = function () {
                history.pushState(null, null, location.href);
            };
            
            // Additional security: reload page if coming from cache
            window.addEventListener('pageshow', function(event) {
                if (event.persisted) {
                    // Page was loaded from cache (back button)
                    window.location.reload();
                }
            });
            
            // Prevent browser from caching this page
            if (window.performance && window.performance.navigation.type === 2) {
                // Page was accessed by navigating into the history (back button)
                window.location.reload();
            }
        })();
    </script>
    
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo BACKEND_URL; ?>index.php">
                <i class="fas fa-rocket"></i> Admin Dashboard
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <!-- Message Notification Bell -->
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="<?php echo BACKEND_URL; ?>messages/">
                            <i class="fas fa-bell fa-lg"></i>
                            <?php 
                            $unreadCount = getUnreadMessagesCount();
                            if ($unreadCount > 0): 
                            ?>
                                <span class="notification-badge"><?php echo $unreadCount > 99 ? '99+' : $unreadCount; ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    
                    <!-- User Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <div class="user-avatar">
                                <?php echo strtoupper(substr($_SESSION['username'] ?? 'A', 0, 1)); ?>
                            </div>
                            <span><?php echo $_SESSION['username'] ?? 'Admin'; ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?php echo BACKEND_URL; ?>profile/"><i class="fas fa-user me-2"></i> Edit Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?php echo BACKEND_URL; ?>login/logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
