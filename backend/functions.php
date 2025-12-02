<?php
/**
 * Functions Library
 * Berisi semua fungsi CRUD, validation, file upload, dan security
 * Project: Personal Profile Website - PKL
 */

require_once 'config.php';

// ============================================
// FILE UPLOAD FUNCTIONS
// ============================================

/**
 * Upload file gambar
 * @return array ['success' => bool, 'filename' => string, 'error' => string]
 */
function uploadImage($file, $targetDir = 'profiles') {
    // Validasi jika file ada
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return ['success' => false, 'error' => 'Tidak ada file yang diupload'];
    }
    
    // Validasi error upload
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Error saat mengupload file'];
    }
    
    // Validasi ukuran file
    if ($file['size'] > MAX_IMAGE_SIZE) {
        return ['success' => false, 'error' => 'Ukuran file terlalu besar. Maksimal ' . formatFileSize(MAX_IMAGE_SIZE)];
    }
    
    // Validasi tipe file
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, ALLOWED_IMAGE_TYPES)) {
        return ['success' => false, 'error' => 'Tipe file tidak diizinkan. Hanya JPG, PNG, GIF, WEBP'];
    }
    
    // Generate nama file baru
    $newFilename = generateRandomFilename($file['name']);
    $targetPath = UPLOAD_DIR . $targetDir . '/' . $newFilename;
    
    // Pindahkan file
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return ['success' => true, 'filename' => $newFilename];
    } else {
        return ['success' => false, 'error' => 'Gagal menyimpan file'];
    }
}

/**
 * Upload file PDF
 * @return array ['success' => bool, 'filename' => string, 'error' => string]
 */
function uploadPDF($file, $targetDir = 'resumes') {
    // Validasi jika file ada
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return ['success' => false, 'error' => 'Tidak ada file yang diupload'];
    }
    
    // Validasi error upload
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Error saat mengupload file'];
    }
    
    // Validasi ukuran file
    if ($file['size'] > MAX_PDF_SIZE) {
        return ['success' => false, 'error' => 'Ukuran file terlalu besar. Maksimal ' . formatFileSize(MAX_PDF_SIZE)];
    }
    
    // Validasi tipe file
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, ALLOWED_PDF_TYPES)) {
        return ['success' => false, 'error' => 'Tipe file tidak diizinkan. Hanya PDF'];
    }
    
    // Generate nama file baru
    $newFilename = generateRandomFilename($file['name']);
    $targetPath = UPLOAD_DIR . $targetDir . '/' . $newFilename;
    
    // Pindahkan file
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return ['success' => true, 'filename' => $newFilename];
    } else {
        return ['success' => false, 'error' => 'Gagal menyimpan file'];
    }
}

/**
 * Hapus file
 */
function deleteFile($filename, $directory) {
    $filePath = UPLOAD_DIR . $directory . '/' . $filename;
    if (file_exists($filePath)) {
        return unlink($filePath);
    }
    return false;
}

// ============================================
// PROFILE FUNCTIONS
// ============================================

/**
 * Get profile data (hanya 1 row)
 */
function getProfile() {
    global $db;
    $stmt = $db->query("SELECT * FROM profile LIMIT 1");
    return $stmt->fetch();
}

/**
 * Update profile
 */
function updateProfile($data) {
    global $db;
    
    try {
        $sql = "UPDATE profile SET 
                full_name = ?,
                tagline = ?,
                about_me = ?,
                phone = ?,
                email = ?,
                location = ?";
        
        $params = [
            $data['full_name'],
            $data['tagline'],
            $data['about_me'],
            $data['phone'],
            $data['email'],
            $data['location']
        ];
        
        // Jika ada profile image baru
        if (!empty($data['profile_image'])) {
            $sql .= ", profile_image = ?";
            $params[] = $data['profile_image'];
        }
        
        // Jika ada resume file baru
        if (!empty($data['resume_file'])) {
            $sql .= ", resume_file = ?";
            $params[] = $data['resume_file'];
        }
        
        $sql .= " WHERE id = 1";
        
        $stmt = $db->prepare($sql);
        return $stmt->execute($params);
    } catch (PDOException $e) {
        return false;
    }
}

// ============================================
// SKILLS FUNCTIONS
// ============================================

/**
 * Get all skills
 */
function getAllSkills($category = null) {
    global $db;
    
    if ($category) {
        $stmt = $db->prepare("SELECT * FROM skills WHERE category = ? ORDER BY display_order ASC, id DESC");
        $stmt->execute([$category]);
    } else {
        $stmt = $db->query("SELECT * FROM skills ORDER BY display_order ASC, id DESC");
    }
    
    return $stmt->fetchAll();
}

/**
 * Get skill by ID
 */
function getSkillById($id) {
    $db = getDBConnection();
    $stmt = $db->prepare("SELECT * FROM skills WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

/**
 * Create skill
 */
function createSkill($data) {
    global $db;
    
    try {
        $sql = "INSERT INTO skills (skill_name, skill_level, category, icon, display_order) 
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            $data['skill_name'],
            $data['skill_level'],
            $data['category'],
            $data['icon'],
            $data['display_order'] ?? 0
        ]);
    } catch (PDOException $e) {
        return false;
    }
}

/**
 * Update skill
 */
function updateSkill($id, $data) {
    global $db;
    
    try {
        $sql = "UPDATE skills SET 
                skill_name = ?,
                skill_level = ?,
                category = ?,
                icon = ?,
                display_order = ?
                WHERE id = ?";
        
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            $data['skill_name'],
            $data['skill_level'],
            $data['category'],
            $data['icon'],
            $data['display_order'] ?? 0,
            $id
        ]);
    } catch (PDOException $e) {
        return false;
    }
}

/**
 * Delete skill
 */
function deleteSkill($id) {
    $db = getDBConnection();
    
    try {
        $stmt = $db->prepare("DELETE FROM skills WHERE id = ?");
        return $stmt->execute([$id]);
    } catch (PDOException $e) {
        error_log("Delete skill error: " . $e->getMessage());
        return false;
    }
}

/**
 * Get skill categories
 */
function getSkillCategories() {
    global $db;
    $stmt = $db->query("SELECT DISTINCT category FROM skills ORDER BY category");
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

// ============================================
// PROJECTS FUNCTIONS
// ============================================

/**
 * Get all projects
 */
function getAllProjects($featured = null) {
    global $db;
    
    if ($featured !== null) {
        $stmt = $db->prepare("SELECT * FROM projects WHERE is_featured = ? ORDER BY display_order ASC, id DESC");
        $stmt->execute([$featured ? 1 : 0]);
    } else {
        $stmt = $db->query("SELECT * FROM projects ORDER BY display_order ASC, id DESC");
    }
    
    return $stmt->fetchAll();
}

/**
 * Get project by ID
 */
function getProjectById($id) {
    global $db;
    $stmt = $db->prepare("SELECT * FROM projects WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

/**
 * Create project
 */
function createProject($data) {
    global $db;
    
    try {
        $sql = "INSERT INTO projects (project_title, description, image, project_url, github_url, 
                technologies, start_date, end_date, is_featured, display_order) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            $data['project_title'],
            $data['description'],
            $data['image'] ?? null,
            $data['project_url'] ?? null,
            $data['github_url'] ?? null,
            $data['technologies'] ?? null,
            $data['start_date'] ?? null,
            $data['end_date'] ?? null,
            $data['is_featured'] ?? 0,
            $data['display_order'] ?? 0
        ]);
    } catch (PDOException $e) {
        return false;
    }
}

/**
 * Update project
 */
function updateProject($id, $data) {
    global $db;
    
    try {
        $sql = "UPDATE projects SET 
                project_title = ?,
                description = ?,
                project_url = ?,
                github_url = ?,
                technologies = ?,
                start_date = ?,
                end_date = ?,
                is_featured = ?,
                display_order = ?";
        
        $params = [
            $data['project_title'],
            $data['description'],
            $data['project_url'] ?? null,
            $data['github_url'] ?? null,
            $data['technologies'] ?? null,
            $data['start_date'] ?? null,
            $data['end_date'] ?? null,
            $data['is_featured'] ?? 0,
            $data['display_order'] ?? 0
        ];
        
        // Jika ada image baru
        if (!empty($data['image'])) {
            $sql .= ", image = ?";
            $params[] = $data['image'];
        }
        
        $sql .= " WHERE id = ?";
        $params[] = $id;
        
        $stmt = $db->prepare($sql);
        return $stmt->execute($params);
    } catch (PDOException $e) {
        return false;
    }
}

/**
 * Delete project
 */
function deleteProject($id) {
    global $db;
    
    try {
        // Get image filename untuk dihapus
        $project = getProjectById($id);
        
        // Delete dari database
        $stmt = $db->prepare("DELETE FROM projects WHERE id = ?");
        $result = $stmt->execute([$id]);
        
        // Delete image file jika ada
        if ($result && !empty($project['image'])) {
            deleteFile($project['image'], 'projects');
        }
        
        return $result;
    } catch (PDOException $e) {
        return false;
    }
}

// ============================================
// EDUCATION FUNCTIONS
// ============================================

/**
 * Get all education
 */
function getAllEducation() {
    global $db;
    $stmt = $db->query("SELECT * FROM education ORDER BY display_order ASC, start_date DESC");
    return $stmt->fetchAll();
}

/**
 * Get education by ID
 */
function getEducationById($id) {
    global $db;
    $stmt = $db->prepare("SELECT * FROM education WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

/**
 * Create education
 */
function createEducation($data) {
    global $db;
    
    try {
        $sql = "INSERT INTO education (institution, degree, field_of_study, start_date, 
                end_date, description, display_order) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            $data['institution'],
            $data['degree'],
            $data['field_of_study'],
            $data['start_date'],
            $data['end_date'] ?? null,
            $data['description'] ?? null,
            $data['display_order'] ?? 0
        ]);
    } catch (PDOException $e) {
        return false;
    }
}

/**
 * Update education
 */
function updateEducation($id, $data) {
    global $db;
    
    try {
        $sql = "UPDATE education SET 
                institution = ?,
                degree = ?,
                field_of_study = ?,
                start_date = ?,
                end_date = ?,
                description = ?,
                display_order = ?
                WHERE id = ?";
        
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            $data['institution'],
            $data['degree'],
            $data['field_of_study'],
            $data['start_date'],
            $data['end_date'] ?? null,
            $data['description'] ?? null,
            $data['display_order'] ?? 0,
            $id
        ]);
    } catch (PDOException $e) {
        return false;
    }
}

/**
 * Delete education
 */
function deleteEducation($id) {
    global $db;
    
    try {
        $stmt = $db->prepare("DELETE FROM education WHERE id = ?");
        return $stmt->execute([$id]);
    } catch (PDOException $e) {
        return false;
    }
}

// ============================================
// EXPERIENCE FUNCTIONS
// ============================================

/**
 * Get all experience
 */
function getAllExperience() {
    global $db;
    $stmt = $db->query("SELECT * FROM experience ORDER BY display_order ASC, start_date DESC");
    return $stmt->fetchAll();
}

/**
 * Get experience by ID
 */
function getExperienceById($id) {
    global $db;
    $stmt = $db->prepare("SELECT * FROM experience WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

/**
 * Create experience
 */
function createExperience($data) {
    global $db;
    
    try {
        $sql = "INSERT INTO experience (company, position, start_date, end_date, 
                is_current, description, display_order) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            $data['company'],
            $data['position'],
            $data['start_date'],
            $data['end_date'] ?? null,
            $data['is_current'] ?? 0,
            $data['description'] ?? null,
            $data['display_order'] ?? 0
        ]);
    } catch (PDOException $e) {
        return false;
    }
}

/**
 * Update experience
 */
function updateExperience($id, $data) {
    global $db;
    
    try {
        $sql = "UPDATE experience SET 
                company = ?,
                position = ?,
                start_date = ?,
                end_date = ?,
                is_current = ?,
                description = ?,
                display_order = ?
                WHERE id = ?";
        
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            $data['company'],
            $data['position'],
            $data['start_date'],
            $data['end_date'] ?? null,
            $data['is_current'] ?? 0,
            $data['description'] ?? null,
            $data['display_order'] ?? 0,
            $id
        ]);
    } catch (PDOException $e) {
        return false;
    }
}

/**
 * Delete experience
 */
function deleteExperience($id) {
    global $db;
    
    try {
        $stmt = $db->prepare("DELETE FROM experience WHERE id = ?");
        return $stmt->execute([$id]);
    } catch (PDOException $e) {
        return false;
    }
}

// ============================================
// SOCIAL MEDIA FUNCTIONS
// ============================================

/**
 * Get all social media
 */
function getAllSocialMedia() {
    global $db;
    $stmt = $db->query("SELECT * FROM social_media ORDER BY display_order ASC, id DESC");
    return $stmt->fetchAll();
}

/**
 * Get social media by ID
 */
function getSocialMediaById($id) {
    global $db;
    $stmt = $db->prepare("SELECT * FROM social_media WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

/**
 * Create social media
 */
function createSocialMedia($data) {
    global $db;
    
    try {
        $sql = "INSERT INTO social_media (platform, url, icon, display_order) 
                VALUES (?, ?, ?, ?)";
        
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            $data['platform'],
            $data['url'],
            $data['icon'],
            $data['display_order'] ?? 0
        ]);
    } catch (PDOException $e) {
        return false;
    }
}

/**
 * Update social media
 */
function updateSocialMedia($id, $data) {
    global $db;
    
    try {
        $sql = "UPDATE social_media SET 
                platform = ?,
                url = ?,
                icon = ?,
                display_order = ?
                WHERE id = ?";
        
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            $data['platform'],
            $data['url'],
            $data['icon'],
            $data['display_order'] ?? 0,
            $id
        ]);
    } catch (PDOException $e) {
        return false;
    }
}

/**
 * Delete social media
 */
function deleteSocialMedia($id) {
    global $db;
    
    try {
        $stmt = $db->prepare("DELETE FROM social_media WHERE id = ?");
        return $stmt->execute([$id]);
    } catch (PDOException $e) {
        return false;
    }
}

// ============================================
// CONTACT MESSAGES FUNCTIONS
// ============================================

/**
 * Get all contact messages
 */
function getAllMessages($unreadOnly = false) {
    $db = getDBConnection();
    
    if ($unreadOnly) {
        $stmt = $db->query("SELECT * FROM contact_messages WHERE is_read = 0 ORDER BY created_at DESC");
    } else {
        $stmt = $db->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
    }
    
    return $stmt->fetchAll();
}

/**
 * Get message by ID
 */
function getMessageById($id) {
    $db = getDBConnection();
    $stmt = $db->prepare("SELECT * FROM contact_messages WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

/**
 * Create contact message (dari frontend)
 */
function createContactMessage($data) {
    global $db;
    
    try {
        $sql = "INSERT INTO contact_messages (name, email, subject, message) 
                VALUES (?, ?, ?, ?)";
        
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            $data['name'],
            $data['email'],
            $data['subject'],
            $data['message']
        ]);
    } catch (PDOException $e) {
        return false;
    }
}

/**
 * Mark message as read
 */
function markMessageAsRead($id) {
    $db = getDBConnection();
    
    try {
        $stmt = $db->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?");
        return $stmt->execute([$id]);
    } catch (PDOException $e) {
        error_log("Mark as read error: " . $e->getMessage());
        return false;
    }
}

/**
 * Delete message
 */
function deleteMessage($id) {
    $db = getDBConnection();
    
    try {
        $stmt = $db->prepare("DELETE FROM contact_messages WHERE id = ?");
        return $stmt->execute([$id]);
    } catch (PDOException $e) {
        error_log("Delete message error: " . $e->getMessage());
        return false;
    }
}

/**
 * Get unread messages count
 */
function getUnreadMessagesCount() {
    $db = getDBConnection();
    $stmt = $db->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0");
    return $stmt->fetchColumn();
}

// ============================================
// DASHBOARD STATISTICS
// ============================================

/**
 * Get dashboard statistics
 */
function getDashboardStats() {
    global $db;
    
    $stats = [];
    
    // Total skills
    $stmt = $db->query("SELECT COUNT(*) FROM skills");
    $stats['total_skills'] = $stmt->fetchColumn();
    
    // Total projects
    $stmt = $db->query("SELECT COUNT(*) FROM projects");
    $stats['total_projects'] = $stmt->fetchColumn();
    
    // Featured projects
    $stmt = $db->query("SELECT COUNT(*) FROM projects WHERE is_featured = 1");
    $stats['featured_projects'] = $stmt->fetchColumn();
    
    // Total education
    $stmt = $db->query("SELECT COUNT(*) FROM education");
    $stats['total_education'] = $stmt->fetchColumn();
    
    // Total experience
    $stmt = $db->query("SELECT COUNT(*) FROM experience");
    $stats['total_experience'] = $stmt->fetchColumn();
    
    // Total social media
    $stmt = $db->query("SELECT COUNT(*) FROM social_media");
    $stats['total_social'] = $stmt->fetchColumn();
    
    // Total messages
    $stmt = $db->query("SELECT COUNT(*) FROM contact_messages");
    $stats['total_messages'] = $stmt->fetchColumn();
    
    // Unread messages
    $stmt = $db->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0");
    $stats['unread_messages'] = $stmt->fetchColumn();
    
    return $stats;
}

/**
 * Get recent messages untuk dashboard
 */
function getRecentMessages($limit = 5) {
    global $db;
    $stmt = $db->prepare("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT ?");
    $stmt->execute([$limit]);
    return $stmt->fetchAll();
}
