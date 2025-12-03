<?php
/**
 * Profile Edit Page
 * Update profile data (hanya ada 1 row)
 */

require_once '../database/config.php';
require_once '../database/functions.php';

// Proteksi halaman
requireLogin();

// Get current profile
$profile = getProfile();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'full_name' => sanitize($_POST['full_name']),
        'tagline' => sanitize($_POST['tagline']),
        'about_me' => sanitize($_POST['about_me']),
        'phone' => sanitize($_POST['phone']),
        'email' => sanitize($_POST['email']),
        'location' => sanitize($_POST['location'])
    ];
    
    // Validasi
    $errors = [];
    
    if (empty($data['full_name'])) {
        $errors[] = 'Nama lengkap harus diisi';
    }
    
    if (empty($data['email']) || !isValidEmail($data['email'])) {
        $errors[] = 'Email tidak valid';
    }
    
    // Handle profile image upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $uploadResult = uploadImage($_FILES['profile_image'], 'profiles');
        
        if ($uploadResult['success']) {
            // Delete old image
            if (!empty($profile['profile_image'])) {
                deleteFile($profile['profile_image'], 'profiles');
            }
            $data['profile_image'] = $uploadResult['filename'];
        } else {
            $errors[] = $uploadResult['error'];
        }
    }
    
    // Handle resume upload
    if (isset($_FILES['resume_file']) && $_FILES['resume_file']['error'] !== UPLOAD_ERR_NO_FILE) {
        $uploadResult = uploadPDF($_FILES['resume_file'], 'resumes');
        
        if ($uploadResult['success']) {
            // Delete old file
            if (!empty($profile['resume_file'])) {
                deleteFile($profile['resume_file'], 'resumes');
            }
            $data['resume_file'] = $uploadResult['filename'];
        } else {
            $errors[] = $uploadResult['error'];
        }
    }
    
    // Update jika tidak ada error
    if (empty($errors)) {
        if (updateProfile($data)) {
            redirect(BACKEND_URL . 'profile/?success=1');
        } else {
            setFlashMessage('error', 'Gagal mengupdate profile');
        }
    } else {
        setFlashMessage('error', implode('<br>', $errors));
    }
}

$pageTitle = 'Edit Profile';
include '../partials/header.php';
include '../partials/sidebar.php';
?>

<div class="main-content">
    <div class="page-header">
        <h1>Edit Profile</h1>
        <p>Update informasi profile Anda</p>
    </div>
    
    <?php displayFlashMessage(); ?>
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-user-edit me-2"></i> Profile Information</h5>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="full_name" class="form-label">Full Name *</label>
                                <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($profile['full_name']); ?>" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($profile['email']); ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="tagline" class="form-label">Tagline *</label>
                            <input type="text" class="form-control" id="tagline" name="tagline" value="<?php echo htmlspecialchars($profile['tagline']); ?>" placeholder="Ex: Full Stack Web Developer | UI/UX Enthusiast" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="about_me" class="form-label">About Me *</label>
                            <textarea class="form-control" id="about_me" name="about_me" rows="6" required><?php echo htmlspecialchars($profile['about_me']); ?></textarea>
                            <small class="text-muted">Ceritakan tentang diri Anda, pengalaman, dan passion Anda</small>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($profile['phone'] ?? ''); ?>" placeholder="+62 812-3456-7890">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" class="form-control" id="location" name="location" value="<?php echo htmlspecialchars($profile['location'] ?? ''); ?>" placeholder="Jakarta, Indonesia">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="profile_image" class="form-label">Profile Image</label>
                                <input type="file" class="form-control" id="profile_image" name="profile_image" accept="image/*">
                                <small class="text-muted">Max 5MB - JPG, PNG, GIF, WEBP</small>
                                <?php if (!empty($profile['profile_image'])): ?>
                                    <div class="mt-2">
                                        <img src="<?php echo UPLOAD_URL . 'profiles/' . $profile['profile_image']; ?>" alt="Current" style="max-width: 150px; border-radius: 8px;">
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="resume_file" class="form-label">Resume/CV (PDF)</label>
                                <input type="file" class="form-control" id="resume_file" name="resume_file" accept=".pdf">
                                <small class="text-muted">Max 10MB - PDF only</small>
                                <?php if (!empty($profile['resume_file'])): ?>
                                    <div class="mt-2">
                                        <a href="<?php echo UPLOAD_URL . 'resumes/' . $profile['resume_file']; ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-file-pdf me-1"></i> View Current CV
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="<?php echo BACKEND_URL; ?>index.php" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Back
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i> Tips</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0" style="line-height: 1.8;">
                        <li><i class="fas fa-check-circle text-success me-2"></i> Gunakan foto profile professional</li>
                        <li><i class="fas fa-check-circle text-success me-2"></i> Buat tagline yang menarik dan deskriptif</li>
                        <li><i class="fas fa-check-circle text-success me-2"></i> Tulis About Me yang engaging</li>
                        <li><i class="fas fa-check-circle text-success me-2"></i> Upload CV terbaru dalam format PDF</li>
                        <li><i class="fas fa-check-circle text-success me-2"></i> Lengkapi semua informasi kontak</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Show success alert if redirected with success parameter
const urlParams = new URLSearchParams(window.location.search);
if (urlParams.get('success') === '1') {
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: 'Edit Successfully',
        showConfirmButton: false,
        timer: 1500
    });
    // Remove query parameter from URL
    window.history.replaceState({}, document.title, window.location.pathname);
}
</script>

<?php include '../partials/footer.php'; ?>
