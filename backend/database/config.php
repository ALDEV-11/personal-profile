<?php
/**
 * Configuration File
 * Berisi database connection, session management, dan constants
 * Project: Personal Profile Website - PKL
 */

// Mulai session dengan security settings
if (session_status() === PHP_SESSION_NONE) {
    // Set session security parameters
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS
    ini_set('session.cookie_samesite', 'Strict');
    
    session_start();
    
    // Regenerate session ID on login to prevent session fixation
    if (!isset($_SESSION['initiated'])) {
        session_regenerate_id(true);
        $_SESSION['initiated'] = true;
    }
}

// ============================================
// DATABASE CONFIGURATION
// ============================================
define('DB_HOST', 'localhost');
define('DB_NAME', 'personal_profile');
define('DB_USER', 'root');
define('DB_PASS', 'root');

// ============================================
// PATH CONFIGURATION
// ============================================
define('BASE_URL', 'http://localhost/personal-profile/');
define('BACKEND_URL', BASE_URL . 'backend/');
define('FRONTEND_URL', BASE_URL . 'frontend/');

// Upload directories
define('UPLOAD_DIR', $_SERVER['DOCUMENT_ROOT'] . '/personal-profile/uploads/');
define('UPLOAD_URL', BASE_URL . 'uploads/');

// Pastikan folder uploads ada
if (!file_exists(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0777, true);
}
if (!file_exists(UPLOAD_DIR . 'profiles/')) {
    mkdir(UPLOAD_DIR . 'profiles/', 0777, true);
}
if (!file_exists(UPLOAD_DIR . 'projects/')) {
    mkdir(UPLOAD_DIR . 'projects/', 0777, true);
}
if (!file_exists(UPLOAD_DIR . 'resumes/')) {
    mkdir(UPLOAD_DIR . 'resumes/', 0777, true);
}

// ============================================
// SITE CONFIGURATION
// ============================================
define('SITE_NAME', 'ALDEV - Admin Panel');
define('SITE_VERSION', '1.0.0');

// ============================================
// FILE UPLOAD CONFIGURATION
// ============================================
define('MAX_IMAGE_SIZE', 5 * 1024 * 1024); // 5MB
define('MAX_PDF_SIZE', 10 * 1024 * 1024); // 10MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp']);
define('ALLOWED_PDF_TYPES', ['application/pdf']);

// ============================================
// DATABASE CONNECTION
// ============================================
function getDBConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";port="  . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
    } catch (PDOException $e) {
        die("Database Connection Error: " . $e->getMessage());
    }
}

// Inisialisasi koneksi database global
$pdo = getDBConnection();
$db = $pdo; // Alias for backward compatibility

// ============================================
// SESSION HELPERS
// ============================================

/**
 * Check jika user sudah login
 */
function isLoggedIn() {
    // Check if session variables exist and are valid
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
        return false;
    }
    
    // Check if session was initiated properly
    if (!isset($_SESSION['initiated']) || $_SESSION['initiated'] !== true) {
        return false;
    }
    
    // Check if logged_out flag exists (user has logged out)
    if (isset($_SESSION['logged_out']) && $_SESSION['logged_out'] === true) {
        return false;
    }
    
    // Additional check: verify session is not expired
    if (isset($_SESSION['last_activity'])) {
        $inactive = 3600; // 1 hour timeout
        if ((time() - $_SESSION['last_activity']) > $inactive) {
            // Session expired
            session_unset();
            session_destroy();
            return false;
        }
    }
    
    // Update last activity time
    $_SESSION['last_activity'] = time();
    
    return true;
}

/**
 * Redirect ke login jika belum login
 */
function requireLogin() {
    // CRITICAL: Prevent ALL forms of caching
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    header("Expires: Mon, 01 Jan 1990 00:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    
    // Check if user is logged in
    if (!isLoggedIn()) {
        // Clear any remaining session data
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = array();
            session_unset();
            session_destroy();
        }
        
        // Prevent back button access
        echo '<script>
            // Disable back button functionality
            history.pushState(null, null, location.href);
            window.onpopstate = function () {
                history.go(1);
            };
        </script>';
        
        header('Location: ' . BACKEND_URL . 'login/login.php');
        exit();
    }
    
    // Additional security: Check if session is marked as logged out
    if (isset($_SESSION['logged_out']) && $_SESSION['logged_out'] === true) {
        $_SESSION = array();
        session_unset();
        session_destroy();
        
        header('Location: ' . BACKEND_URL . 'login/login.php');
        exit();
    }
}

/**
 * Get current logged in user data
 */
function getCurrentUser() {
    global $pdo;
    
    if (!isLoggedIn()) {
        return null;
    }
    
    $stmt = $pdo->prepare("SELECT id, username, email FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

/**
 * Login user
 */
function loginUser($username, $password) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT id, username, password, email FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        // Regenerate session ID to prevent session fixation
        session_regenerate_id(true);
        
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['last_activity'] = time();
        $_SESSION['initiated'] = true;
        
        return true;
    }
    
    return false;
}

/**
 * Logout user
 */
function logoutUser() {
    // Start session if not started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Mark session as logged out BEFORE clearing
    $_SESSION['logged_out'] = true;
    
    // Unset all session variables
    $_SESSION = array();
    
    // Delete session cookie from browser with secure parameters
    if (isset($_COOKIE[session_name()])) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], 
            $params["domain"],
            $params["secure"], 
            $params["httponly"]
        );
        
        // Also try to unset the cookie directly
        setcookie(session_name(), '', time() - 42000, '/');
    }
    
    // Destroy session file on server
    session_destroy();
    
    // Start new empty session and mark as logged out
    session_start();
    session_regenerate_id(true);
    $_SESSION['logged_out'] = true;
    
    // CRITICAL: Maximum cache prevention headers
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    header("Expires: Mon, 01 Jan 1990 00:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    
    // Clear browser history with JavaScript before redirect
    echo '<script>
        // Clear forward history
        if (window.history && window.history.pushState) {
            window.history.pushState(null, null, window.location.href);
            window.onpopstate = function () {
                window.history.go(1);
            };
        }
        
        // Clear any cached data
        if ("caches" in window) {
            caches.keys().then(function(names) {
                for (let name of names) caches.delete(name);
            });
        }
        
        // Redirect to login
        window.location.href = "' . BACKEND_URL . 'login/login.php";
    </script>';
    
    // PHP redirect as fallback
    header('Location: ' . BACKEND_URL . 'login/login.php');
    exit();
}

// ============================================
// FLASH MESSAGE HELPERS
// ============================================

/**
 * Set flash message
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash_type'] = $type; // success, error, warning, info
    $_SESSION['flash_message'] = $message;
}

/**
 * Get dan hapus flash message
 */
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $type = $_SESSION['flash_type'];
        $message = $_SESSION['flash_message'];
        
        unset($_SESSION['flash_type']);
        unset($_SESSION['flash_message']);
        
        return [
            'type' => $type,
            'message' => $message
        ];
    }
    
    return null;
}

/**
 * Display flash message HTML
 */
function displayFlashMessage() {
    $flash = getFlashMessage();
    
    if ($flash) {
        $alertClass = '';
        $icon = '';
        
        switch ($flash['type']) {
            case 'success':
                $alertClass = 'alert-success';
                $icon = 'fa-check-circle';
                break;
            case 'error':
                $alertClass = 'alert-danger';
                $icon = 'fa-times-circle';
                break;
            case 'warning':
                $alertClass = 'alert-warning';
                $icon = 'fa-exclamation-triangle';
                break;
            case 'info':
                $alertClass = 'alert-info';
                $icon = 'fa-info-circle';
                break;
        }
        
        echo "<div class='alert {$alertClass} alert-dismissible fade show' role='alert'>
                <i class='fas {$icon} me-2'></i>
                {$flash['message']}
                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
              </div>";
    }
}

// ============================================
// SECURITY HELPERS
// ============================================

/**
 * Sanitize input untuk mencegah XSS
 */
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Validate URL
 */
function isValidUrl($url) {
    return filter_var($url, FILTER_VALIDATE_URL);
}

/**
 * Generate CSRF Token
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF Token
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// ============================================
// UTILITY HELPERS
// ============================================

/**
 * Format tanggal Indonesia
 */
function formatDateIndo($date) {
    if (empty($date)) return 'Present';
    
    // Jika hanya tahun (numeric 4 digit)
    if (is_numeric($date) && strlen($date) == 4) {
        return $date;
    }
    
    // Jika format date lengkap
    $months = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mai', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    
    $timestamp = strtotime($date);
    $day = date('d', $timestamp);
    $month = $months[(int)date('m', $timestamp)];
    $year = date('Y', $timestamp);
    
    return "{$day} {$month} {$year}";
}

/**
 * Time ago function
 * Menampilkan waktu relatif dalam bahasa Indonesia
 */
function timeAgo($datetime) {
    // Validasi input
    if (empty($datetime) || $datetime == '0000-00-00 00:00:00') {
        return 'Tidak diketahui';
    }
    
    $timestamp = strtotime($datetime);
    
    // Jika strtotime gagal parse
    if ($timestamp === false || $timestamp === -1) {
        return 'Tanggal tidak valid';
    }
    
    $now = time();
    $difference = $now - $timestamp;
    
    // Jika tanggal di masa depan (negatif), set ke 0
    if ($difference < 0) {
        $difference = 0;
    }
    
    // Debug: untuk data yang sangat lama, cek apakah timestamp terlalu kecil
    if ($difference > 31536000) { // > 1 tahun
        return formatDateIndo($datetime);
    }
    
    // Kurang dari 60 detik
    if ($difference < 60) {
        $seconds = max(0, floor($difference));
        return $seconds . ' detik yang lalu';
    }
    
    // Kurang dari 60 menit (3600 detik)
    if ($difference < 3600) {
        $minutes = floor($difference / 60);
        return $minutes . ' menit yang lalu';
    }
    
    // Kurang dari 24 jam (86400 detik)
    if ($difference < 86400) {
        $hours = floor($difference / 3600);
        return $hours . ' jam yang lalu';
    }
    
    // Kurang dari 30 hari (2592000 detik)
    if ($difference < 2592000) {
        $days = floor($difference / 86400);
        return $days . ' hari yang lalu';
    }
    
    // Lebih dari 30 hari, tampilkan format tanggal lengkap
    return formatDateIndo($datetime);
}

/**
 * Truncate text
 */
function truncate($text, $length = 100, $suffix = '...') {
    if (strlen($text) > $length) {
        return substr($text, 0, $length) . $suffix;
    }
    return $text;
}

/**
 * Get file extension
 */
function getFileExtension($filename) {
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

/**
 * Generate random filename
 */
function generateRandomFilename($originalFilename) {
    $extension = getFileExtension($originalFilename);
    return uniqid() . '_' . time() . '.' . $extension;
}

/**
 * Format file size
 */
function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}

/**
 * Redirect helper
 */
function redirect($url, $statusCode = 302) {
    header('Location: ' . $url, true, $statusCode);
    exit();
}

/**
 * Debug helper (hanya untuk development)
 */
function dd($data) {
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    die();
}

// ============================================
// ERROR HANDLING
// ============================================

// Set error reporting untuk development
// Ubah ke 0 untuk production
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Custom error handler bisa ditambahkan di sini
