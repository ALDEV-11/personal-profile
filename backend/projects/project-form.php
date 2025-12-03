<?php
/**
 * Project Form Page
 * Halaman untuk tambah/edit project
 */

require_once '../database/config.php';
require_once '../database/functions.php';

requireLogin();

$pageTitle = 'Project Form';
$isEdit = false;
$project = null;
$errors = [];

// Check if edit mode
if (isset($_GET['id'])) {
    $isEdit = true;
    $project = getProjectById($_GET['id']);
    
    if (!$project) {
        setFlashMessage('error', 'Project tidak ditemukan!');
        redirect('./');
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'project_title' => sanitize($_POST['project_title'] ?? ''),
        'description' => sanitize($_POST['description'] ?? ''),
        'technologies' => sanitize($_POST['technologies'] ?? ''),
        'project_url' => sanitize($_POST['project_url'] ?? ''),
        'github_url' => sanitize($_POST['github_url'] ?? ''),
        'is_featured' => isset($_POST['is_featured']) ? 1 : 0
    ];
    
    // Validasi
    if (empty($data['project_title'])) {
        $errors[] = 'Project title harus diisi!';
    }
    if (empty($data['description'])) {
        $errors[] = 'Description harus diisi!';
    }
    
    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadResult = uploadImage($_FILES['image'], 'projects');
        if ($uploadResult['success']) {
            $data['image'] = $uploadResult['filename'];
            
            // Delete old image if edit mode
            if ($isEdit && !empty($project['image'])) {
                $oldImagePath = UPLOAD_DIR . 'projects/' . $project['image'];
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
        } else {
            $errors[] = $uploadResult['message'];
        }
    } elseif ($isEdit && !empty($project['image'])) {
        // Keep existing image
        $data['image'] = $project['image'];
    }
    
    // Save if no errors
    if (empty($errors)) {
        if ($isEdit) {
            if (updateProject($_GET['id'], $data)) {
                setFlashMessage('success', 'Project berhasil diupdate!');
                redirect('./');
            } else {
                $errors[] = 'Gagal update project!';
            }
        } else {
            if (createProject($data)) {
                setFlashMessage('success', 'Project berhasil ditambahkan!');
                redirect('./');
            } else {
                $errors[] = 'Gagal menambahkan project!';
            }
        }
    }
}

include 'partials/header.php';
?>

<div class="main-content">
    <div class="content-header">
        <div>
            <h1>
                <i class="fas fa-<?php echo $isEdit ? 'edit' : 'plus'; ?> me-2"></i> 
                <?php echo $isEdit ? 'Edit' : 'Add New'; ?> Project
            </h1>
            <p class="text-muted">
                <?php echo $isEdit ? 'Update informasi project' : 'Tambahkan project portfolio baru'; ?>
            </p>
        </div>
        
    </div>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <strong>Error!</strong>
            <ul class="mb-0 mt-2">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <div class="card">
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                <div class="row">
                    <!-- Left Column -->
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="project_title" class="form-label">
                                Project Title <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="project_title" 
                                   name="project_title" 
                                   value="<?php echo htmlspecialchars($project['project_title'] ?? $_POST['project_title'] ?? ''); ?>"
                                   required
                                   placeholder="e.g. E-Commerce Website">
                            <div class="invalid-feedback">Project title harus diisi!</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">
                                Description <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" 
                                      id="description" 
                                      name="description" 
                                      rows="6" 
                                      required
                                      maxlength="1000"
                                      placeholder="Jelaskan tentang project ini..."><?php echo htmlspecialchars($project['description'] ?? $_POST['description'] ?? ''); ?></textarea>
                            <div class="invalid-feedback">Description harus diisi!</div>
                            <small class="text-muted">Maksimal 1000 karakter</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="technologies" class="form-label">
                                Technologies Used
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="technologies" 
                                   name="technologies" 
                                   value="<?php echo htmlspecialchars($project['technologies'] ?? $_POST['technologies'] ?? ''); ?>"
                                   placeholder="PHP, MySQL, Bootstrap (pisahkan dengan koma)">
                            <small class="text-muted">Pisahkan dengan koma (,)</small>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="project_url" class="form-label">
                                        <i class="fas fa-external-link-alt me-1"></i> Project URL (Demo)
                                    </label>
                                    <input type="url" 
                                           class="form-control" 
                                           id="project_url" 
                                           name="project_url" 
                                           value="<?php echo htmlspecialchars($project['project_url'] ?? $_POST['project_url'] ?? ''); ?>"
                                           placeholder="https://demo.example.com">
                                    <small class="text-muted">Link ke project live/demo</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="github_url" class="form-label">
                                        <i class="fab fa-github me-1"></i> GitHub URL
                                    </label>
                                    <input type="url" 
                                           class="form-control" 
                                           id="github_url" 
                                           name="github_url" 
                                           value="<?php echo htmlspecialchars($project['github_url'] ?? $_POST['github_url'] ?? ''); ?>"
                                           placeholder="https://github.com/username/repo">
                                    <small class="text-muted">Link ke repository GitHub</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Column -->
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="image" class="form-label">
                                Project Image
                            </label>
                            <input type="file" 
                                   class="form-control" 
                                   id="image" 
                                   name="image" 
                                   accept="image/*">
                            <small class="text-muted">Max 5MB (JPG, PNG)</small>
                            
                            <?php if ($isEdit && !empty($project['image'])): ?>
                                <div class="mt-3">
                                    <p class="mb-2"><strong>Current Image:</strong></p>
                                    <img src="<?php echo UPLOAD_URL . 'projects/' . $project['image']; ?>" 
                                         alt="Current" 
                                         class="img-thumbnail"
                                         id="imagePreview"
                                         style="max-width: 100%; height: auto;">
                                </div>
                            <?php else: ?>
                                <div class="mt-3" style="display: none;">
                                    <p class="mb-2"><strong>Preview:</strong></p>
                                    <img id="imagePreview" 
                                         src="" 
                                         alt="Preview" 
                                         class="img-thumbnail"
                                         style="max-width: 100%; height: auto;">
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="card bg-light">
                            <div class="card-body">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="is_featured" 
                                           name="is_featured"
                                           <?php echo ($project['is_featured'] ?? 0) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="is_featured">
                                        <i class="fas fa-star text-warning me-1"></i>
                                        <strong>Featured Project</strong>
                                    </label>
                                </div>
                                <small class="text-muted d-block mt-2">
                                    Tandai sebagai project unggulan untuk ditampilkan di halaman utama
                                </small>
                            </div>
                        </div>
                        
                        <div class="card bg-info bg-opacity-10 border-info mt-3">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="fas fa-info-circle me-1"></i> Tips
                                </h6>
                                <ul class="small mb-0 ps-3">
                                    <li>Gunakan gambar dengan rasio 16:9</li>
                                    <li>Resolusi minimal 800x600px</li>
                                    <li>Jelaskan fitur utama project</li>
                                    <li>Sertakan link demo atau GitHub</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <hr class="my-4">
                
                <div class="d-flex justify-content-between">
                    <a href="./" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Back
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i> 
                        <?php echo $isEdit ? 'Update' : 'Create'; ?> Project
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Image preview
document.getElementById('image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('imagePreview');
            preview.src = e.target.result;
            preview.parentElement.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
});
</script>

<?php include 'partials/footer.php'; ?>
