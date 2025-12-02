<?php
/**
 * Skills List Page
 * Menampilkan daftar semua skills
 */

require_once 'config.php';
require_once 'functions.php';

requireLogin();

// Get all skills
$skills = getAllSkills();
$categories = getSkillCategories();

$pageTitle = 'Skills Management';
include 'partials/header.php';
include 'partials/sidebar.php';
?>

<div class="main-content">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1>Skills Management</h1>
                <p>Manage your technical skills and proficiency levels</p>
            </div>
            <a href="skill-form.php" class="btn btn-primary">
                <i class="fas fa-plus-circle me-2"></i> Add New Skill
            </a>
        </div>
    </div>
    
    <?php displayFlashMessage(); ?>
    
    <div class="card">
        <div class="card-body">
            <?php if (empty($skills)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-code" style="font-size: 4rem; color: #D1D5DB;"></i>
                    <h5 class="mt-3">Belum ada skill</h5>
                    <p class="text-muted">Mulai tambahkan skill Anda</p>
                    <a href="skill-form.php" class="btn btn-primary mt-2">
                        <i class="fas fa-plus-circle me-2"></i> Add First Skill
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>Skill Name</th>
                                <th>Category</th>
                                <th>Level</th>
                                <th style="width: 100px;">Icon</th>
                                <th style="width: 80px;">Order</th>
                                <th style="width: 150px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($skills as $index => $skill): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><strong><?php echo htmlspecialchars($skill['skill_name']); ?></strong></td>
                                    <td>
                                        <span class="badge bg-secondary"><?php echo htmlspecialchars($skill['category']); ?></span>
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 25px;">
                                            <div class="progress-bar" role="progressbar" style="width: <?php echo $skill['skill_level']; ?>%;" aria-valuenow="<?php echo $skill['skill_level']; ?>" aria-valuemin="0" aria-valuemax="100">
                                                <?php echo $skill['skill_level']; ?>%
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <i class="<?php echo htmlspecialchars($skill['icon']); ?>" style="font-size: 1.5rem;"></i>
                                    </td>
                                    <td><?php echo $skill['display_order']; ?></td>
                                    <td>
                                        <a href="skill-form.php?id=<?php echo $skill['id']; ?>" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="skill-delete.php" style="display: inline;" class="delete-form">
                                            <input type="hidden" name="id" value="<?php echo $skill['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger" 
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus skill <?php echo htmlspecialchars($skill['skill_name']); ?>?');">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
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

<?php include 'partials/footer.php'; ?>
