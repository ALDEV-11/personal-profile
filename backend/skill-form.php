<?php
/**
 * Skill Form - Add/Edit
 */

require_once 'config.php';
require_once 'functions.php';

requireLogin();

$isEdit = false;
$skill = null;

// Check if editing
if (isset($_GET['id'])) {
    $isEdit = true;
    $skill = getSkillById($_GET['id']);
    
    if (!$skill) {
        setFlashMessage('error', 'Skill tidak ditemukan');
        redirect(BACKEND_URL . 'skills.php');
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'skill_name' => sanitize($_POST['skill_name']),
        'skill_level' => intval($_POST['skill_level']),
        'category' => sanitize($_POST['category']),
        'icon' => sanitize($_POST['icon']),
        'display_order' => intval($_POST['display_order'] ?? 0)
    ];
    
    // Validasi
    if (empty($data['skill_name']) || $data['skill_level'] < 0 || $data['skill_level'] > 100) {
        setFlashMessage('error', 'Data tidak valid');
    } else {
        if ($isEdit) {
            if (updateSkill($_GET['id'], $data)) {
                setFlashMessage('success', 'Skill berhasil diupdate!');
                redirect(BACKEND_URL . 'skills.php');
            } else {
                setFlashMessage('error', 'Gagal mengupdate skill');
            }
        } else {
            if (createSkill($data)) {
                setFlashMessage('success', 'Skill berhasil ditambahkan!');
                redirect(BACKEND_URL . 'skills.php');
            } else {
                setFlashMessage('error', 'Gagal menambahkan skill');
            }
        }
    }
}

$pageTitle = $isEdit ? 'Edit Skill' : 'Add New Skill';
include 'partials/header.php';
include 'partials/sidebar.php';
?>

<div class="main-content">
    <div class="page-header">
        <h1><?php echo $pageTitle; ?></h1>
        <p><?php echo $isEdit ? 'Edit existing skill' : 'Add a new skill to your profile'; ?></p>
    </div>
    
    <?php displayFlashMessage(); ?>
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form method="POST" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="skill_name" class="form-label">Skill Name *</label>
                            <input type="text" class="form-control" id="skill_name" name="skill_name" value="<?php echo $skill ? htmlspecialchars($skill['skill_name']) : ''; ?>" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="category" class="form-label">Category *</label>
                                <input type="text" class="form-control" id="category" name="category" value="<?php echo $skill ? htmlspecialchars($skill['category']) : ''; ?>" placeholder="Ex: Frontend, Backend, Tools" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="skill_level" class="form-label">Skill Level (0-100) *</label>
                                <input type="number" class="form-control" id="skill_level" name="skill_level" value="<?php echo $skill ? $skill['skill_level'] : 50; ?>" min="0" max="100" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="icon" class="form-label">Icon Class (Font Awesome)</label>
                                <input type="text" class="form-control" id="icon" name="icon" value="<?php echo $skill ? htmlspecialchars($skill['icon']) : ''; ?>" placeholder="Ex: fab fa-html5">
                                <small class="text-muted">Visit <a href="https://fontawesome.com/icons" target="_blank">fontawesome.com</a> for icon codes</small>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="display_order" class="form-label">Display Order</label>
                                <input type="number" class="form-control" id="display_order" name="display_order" value="<?php echo $skill ? $skill['display_order'] : 0; ?>">
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="skills.php" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Back
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> <?php echo $isEdit ? 'Update' : 'Save'; ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'partials/footer.php'; ?>
