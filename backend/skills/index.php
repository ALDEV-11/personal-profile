<?php
/**
 * Skills List Page
 * Menampilkan daftar semua skills
 */

require_once '../database/config.php';
require_once '../database/functions.php';

requireLogin();

// Search functionality
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Pagination settings
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
if ($limit <= 0 || $limit > 100) $limit = 5; // Validation
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Build query with search
global $pdo;
$whereClause = '';
$params = [];

if (!empty($search)) {
    $whereClause = "WHERE skill_name LIKE :search1 OR category LIKE :search2";
    $params[':search1'] = "%$search%";
    $params[':search2'] = "%$search%";
}

// Get total count
$countQuery = "SELECT COUNT(*) FROM skills $whereClause";
$countStmt = $pdo->prepare($countQuery);
foreach ($params as $key => $value) {
    $countStmt->bindValue($key, $value);
}
$countStmt->execute();
$totalRecords = $countStmt->fetchColumn();
$totalPages = ceil($totalRecords / $limit);

// Get paginated skills
$query = "SELECT * FROM skills $whereClause ORDER BY display_order ASC, id DESC LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$skills = $stmt->fetchAll(PDO::FETCH_ASSOC);

$categories = getSkillCategories();

$pageTitle = 'Skills Management';
include '../partials/header.php';
include '../partials/sidebar.php';
?>

<div class="main-content">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1>Skills Management</h1>
                <p>Manage your technical skills and proficiency levels</p>
            </div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSkillModal">
                <i class="fas fa-plus-circle me-2"></i> Add New Skill
            </button>
        </div>
    </div>
    
    <?php displayFlashMessage(); ?>
    
    <!-- Skills Datatable -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i> Skills List
            </h5>
        </div>
        <div class="card-body">
            <!-- Datatable Controls -->
            <div class="row mb-3">
                <div class="col-sm-12 col-md-6">
                    <div class="d-flex align-items-center">
                        <label class="me-2 mb-0">Show</label>
                        <select name="limit" class="form-select form-select-sm" style="width: 80px;" onchange="window.location.href='?limit=' + this.value + '<?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>'">
                            <option value="5" <?php echo $limit == 5 ? 'selected' : ''; ?>>5</option>
                            <option value="10" <?php echo $limit == 10 ? 'selected' : ''; ?>>10</option>
                            <option value="25" <?php echo $limit == 25 ? 'selected' : ''; ?>>25</option>
                            <option value="50" <?php echo $limit == 50 ? 'selected' : ''; ?>>50</option>
                        </select>
                        <label class="ms-2 mb-0">entries</label>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6">
                    <form method="GET" action="" class="d-flex justify-content-md-end mt-2 mt-md-0">
                        <?php if (!empty($_GET['limit'])): ?>
                            <input type="hidden" name="limit" value="<?php echo (int)$_GET['limit']; ?>">
                        <?php endif; ?>
                        <div class="position-relative" style="max-width: 300px; width: 100%;">
                            <input type="text" 
                                   class="form-control" 
                                   name="search" 
                                   placeholder="Search skills..." 
                                   value="<?php echo htmlspecialchars($search); ?>"
                                   data-autocomplete-url="<?php echo BACKEND_URL; ?>skills/skill-autocomplete.php"
                                   style="padding-right: <?php echo !empty($search) ? '35px' : '12px'; ?>;">
                            <?php if (!empty($search)): ?>
                                <button type="button" 
                                        class="btn btn-link position-absolute top-50 end-0 translate-middle-y text-muted" 
                                        style="padding: 0; margin-right: 10px; text-decoration: none; border: none; background: none;"
                                        onclick="window.location.href='.<?php echo !empty($_GET['limit']) ? '?limit=' . (int)$_GET['limit'] : ''; ?>'">
                                    <i class="fas fa-times"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Table -->
            <?php if (empty($skills)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-code" style="font-size: 4rem; color: #D1D5DB;"></i>
                    <h5 class="mt-3">Belum ada skill</h5>
                    <p class="text-muted">Mulai tambahkan skill Anda</p>
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
                                    <td><?php echo $offset + $index + 1; ?></td>
                                    <td><strong><?php echo htmlspecialchars($skill['skill_name']); ?></strong></td>
                                    <td>
                                        <span class="badge bg-secondary"><?php echo htmlspecialchars($skill['category']); ?></span>
                                    </td>
                                    <td>
                                        <?php
                                        $level = strtolower($skill['skill_level']);
                                        $badgeClass = '';
                                        $badgeText = ucfirst($level);
                                        
                                        switch($level) {
                                            case 'beginner':
                                                $badgeClass = 'bg-info';
                                                break;
                                            case 'intermediate':
                                                $badgeClass = 'bg-warning text-dark';
                                                break;
                                            case 'advanced':
                                                $badgeClass = 'bg-success';
                                                break;
                                            default:
                                                $badgeClass = 'bg-secondary';
                                        }
                                        ?>
                                        <span class="badge <?php echo $badgeClass; ?>" style="font-size: 0.9rem; padding: 0.5rem 1rem;">
                                            <?php echo $badgeText; ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <i class="<?php echo htmlspecialchars($skill['icon']); ?>" style="font-size: 1.5rem;"></i>
                                    </td>
                                    <td><?php echo $skill['display_order']; ?></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-warning btn-edit-skill" 
                                                data-id="<?php echo $skill['id']; ?>"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editSkillModal">
                                            <i class="fas fa-edit"></i>
                                        </button>
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
                
                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                <?php 
                // Build query string for pagination
                $queryParams = [];
                if (!empty($search)) $queryParams[] = 'search=' . urlencode($search);
                if (isset($_GET['limit'])) $queryParams[] = 'limit=' . (int)$_GET['limit'];
                $queryString = !empty($queryParams) ? '&' . implode('&', $queryParams) : '';
                ?>
                <div class="row mt-3">
                    <div class="col-sm-12 col-md-5">
                        <div class="text-muted">
                            Showing <?php echo $offset + 1; ?> to <?php echo min($offset + $limit, $totalRecords); ?> of <?php echo $totalRecords; ?> entries
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-7">
                        <nav aria-label="Skills pagination">
                            <ul class="pagination justify-content-md-end mb-0 mt-3 mt-md-0">
                                <!-- Previous Button -->
                                <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo $queryString; ?>" aria-label="Previous">
                                        Previous
                                    </a>
                                </li>
                                
                                <!-- Page Numbers -->
                                <?php 
                                $startPage = max(1, $page - 2);
                                $endPage = min($totalPages, $page + 2);
                                
                                if ($startPage > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=1<?php echo $queryString; ?>">1</a>
                                    </li>
                                    <?php if ($startPage > 2): ?>
                                        <li class="page-item disabled"><span class="page-link">...</span></li>
                                    <?php endif; ?>
                                <?php endif; ?>
                                
                                <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?><?php echo $queryString; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>
                                
                                <?php if ($endPage < $totalPages): ?>
                                    <?php if ($endPage < $totalPages - 1): ?>
                                        <li class="page-item disabled"><span class="page-link">...</span></li>
                                    <?php endif; ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo $totalPages; ?><?php echo $queryString; ?>"><?php echo $totalPages; ?></a>
                                    </li>
                                <?php endif; ?>
                                
                                <!-- Next Button -->
                                <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo $queryString; ?>" aria-label="Next">
                                        Next
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
                <?php else: ?>
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="text-muted">
                            Showing <?php echo $offset + 1; ?> to <?php echo min($offset + $limit, $totalRecords); ?> of <?php echo $totalRecords; ?> entries
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Add Skill Modal -->
<div class="modal fade" id="addSkillModal" tabindex="-1" aria-labelledby="addSkillModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSkillModalLabel">
                    <i class="fas fa-plus-circle me-2"></i> Add New Skill
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addSkillForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="add_skill_name" class="form-label">
                            Skill Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="add_skill_name" name="skill_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="add_category" class="form-label">
                            Category <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="add_category" name="category" required>
                        <small class="text-muted">Ex: Frontend, Backend, Tools</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="add_skill_level" class="form-label">
                            Skill Level <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="add_skill_level" name="skill_level" required>
                            <option value="">-- Select Level --</option>
                            <option value="beginner">Beginner</option>
                            <option value="intermediate" selected>Intermediate</option>
                            <option value="advanced">Advanced</option>
                        </select>
                        <small class="text-muted">Choose your proficiency level</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="add_icon" class="form-label">Icon (Font Awesome)</label>
                        <input type="text" class="form-control" id="add_icon" name="icon" placeholder="fab fa-html5">
                        <small class="text-muted">
                            <a href="https://fontawesome.com/icons" target="_blank">Browse icons</a>
                        </small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="add_display_order" class="form-label">Display Order</label>
                        <input type="number" class="form-control" id="add_display_order" name="display_order" value="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i> Create Skill
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Skill Modal -->
<div class="modal fade" id="editSkillModal" tabindex="-1" aria-labelledby="editSkillModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editSkillModalLabel">
                    <i class="fas fa-edit me-2"></i> Edit Skill
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editSkillForm">
                <div class="modal-body">
                    <input type="hidden" id="edit_skill_id" name="id">
                    
                    <div class="mb-3">
                        <label for="edit_skill_name" class="form-label">
                            Skill Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="edit_skill_name" name="skill_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_category" class="form-label">
                            Category <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="edit_category" name="category" required>
                        <small class="text-muted">Ex: Frontend, Backend, Tools</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_skill_level" class="form-label">
                            Skill Level <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="edit_skill_level" name="skill_level" required>
                            <option value="">-- Select Level --</option>
                            <option value="beginner">Beginner</option>
                            <option value="intermediate">Intermediate</option>
                            <option value="advanced">Advanced</option>
                        </select>
                        <small class="text-muted">Choose your proficiency level</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_icon" class="form-label">Icon (Font Awesome)</label>
                        <input type="text" class="form-control" id="edit_icon" name="icon" placeholder="fab fa-html5">
                        <small class="text-muted">
                            <a href="https://fontawesome.com/icons" target="_blank">Browse icons</a>
                        </small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_display_order" class="form-label">Display Order</label>
                        <input type="number" class="form-control" id="edit_display_order" name="display_order" value="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i> Update Skill
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Load skill data into modal when edit button clicked
document.addEventListener('DOMContentLoaded', function() {
    const editButtons = document.querySelectorAll('.btn-edit-skill');
    
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const skillId = this.getAttribute('data-id');
            
            // Fetch skill data
            fetch('skill-get.php?id=' + skillId)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const skill = data.skill;
                        
                        // Fill form
                        document.getElementById('edit_skill_id').value = skill.id;
                        document.getElementById('edit_skill_name').value = skill.skill_name;
                        document.getElementById('edit_category').value = skill.category;
                        document.getElementById('edit_skill_level').value = skill.skill_level;
                        document.getElementById('edit_icon').value = skill.icon || '';
                        document.getElementById('edit_display_order').value = skill.display_order;
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Gagal memuat data skill');
                });
        });
    });
    
    // Handle form submission (Edit)
    document.getElementById('editSkillForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('skill-update.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Edit Successfully',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    location.reload();
                });
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Gagal mengupdate skill');
        });
    });
    
    // Handle Add Skill Form submission
    document.getElementById('addSkillForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('skill-create.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Skill Added Successfully',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    location.reload();
                });
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Gagal menambahkan skill');
        });
    });
});
</script>

<?php include '../partials/footer.php'; ?>
