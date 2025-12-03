<?php
/**
 * Projects Management Page
 * Halaman untuk kelola projects
 */

require_once '../database/config.php';
require_once '../database/functions.php';

requireLogin();

$pageTitle = 'Projects Management';

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
    $whereClause = "WHERE project_title LIKE :search1 OR description LIKE :search2 OR technologies LIKE :search3";
    $params[':search1'] = "%$search%";
    $params[':search2'] = "%$search%";
    $params[':search3'] = "%$search%";
}

// Get total count
$countQuery = "SELECT COUNT(*) FROM projects $whereClause";
$countStmt = $pdo->prepare($countQuery);
foreach ($params as $key => $value) {
    $countStmt->bindValue($key, $value);
}
$countStmt->execute();
$totalRecords = $countStmt->fetchColumn();
$totalPages = ceil($totalRecords / $limit);

// Get paginated projects
$query = "SELECT * FROM projects $whereClause ORDER BY display_order ASC, created_at DESC LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get flash message
$flash = getFlashMessage();

include '../partials/header.php';
include '../partials/sidebar.php';
?>

<div class="main-content">
    <div class="content-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1><i class="fas fa-folder-open me-2"></i> Projects Management</h1>
                <p class="text-muted">Kelola semua projects dan portfolio Anda</p>
            </div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProjectModal">
                <i class="fas fa-plus-circle me-2"></i> Add New Project
            </button>
        </div>
    </div>
    
    <?php if ($flash): ?>
        <div class="alert alert-<?php echo $flash['type'] === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
            <i class="fas fa-<?php echo $flash['type'] === 'success' ? 'check' : 'exclamation'; ?>-circle me-2"></i>
            <?php echo htmlspecialchars($flash['message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <!-- Projects Datatable -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i> All Projects (<?php echo $totalRecords; ?>)
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
                                   placeholder="Search projects..." 
                                   value="<?php echo htmlspecialchars($search); ?>"
                                   data-autocomplete-url="<?php echo BACKEND_URL; ?>projects/project-autocomplete.php"
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
            <?php if (empty($projects)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-folder-open fa-4x text-muted mb-3"></i>
                    <h5>Belum ada project</h5>
                    <p class="text-muted">Mulai tambahkan project portfolio Anda</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="60">Image</th>
                                <th>Project Title</th>
                                <th>Technologies</th>
                                <th width="100" class="text-center">Featured</th>
                                <th width="100" class="text-center">Status</th>
                                <th width="180" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($projects as $project): ?>
                                <tr>
                                    <td>
                                        <?php if (!empty($project['image'])): ?>
                                            <img src="<?php echo UPLOAD_URL . 'projects/' . $project['image']; ?>" 
                                                 alt="<?php echo htmlspecialchars($project['project_title']); ?>" 
                                                 class="rounded"
                                                 style="width: 50px; height: 50px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="bg-secondary rounded d-flex align-items-center justify-content-center" 
                                                 style="width: 50px; height: 50px;">
                                                <i class="fas fa-image text-white"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($project['project_title']); ?></strong>
                                        <br>
                                        <small class="text-muted">
                                            <?php echo truncate($project['description'], 80); ?>
                                        </small>
                                        <br>
                                        <div class="mt-1">
                                            <?php if (!empty($project['project_url'])): ?>
                                                <a href="<?php echo htmlspecialchars($project['project_url']); ?>" 
                                                   target="_blank" 
                                                   class="badge bg-info text-decoration-none me-1"
                                                   title="View Demo">
                                                    <i class="fas fa-external-link-alt"></i> Demo
                                                </a>
                                            <?php endif; ?>
                                            <?php if (!empty($project['github_url'])): ?>
                                                <a href="<?php echo htmlspecialchars($project['github_url']); ?>" 
                                                   target="_blank" 
                                                   class="badge bg-dark text-decoration-none"
                                                   title="View Code">
                                                    <i class="fab fa-github"></i> GitHub
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php 
                                        if (!empty($project['technologies'])) {
                                            $techs = explode(',', $project['technologies']);
                                            $displayTechs = array_slice($techs, 0, 3);
                                            foreach ($displayTechs as $tech): 
                                        ?>
                                            <span class="badge bg-primary me-1 mb-1"><?php echo htmlspecialchars(trim($tech)); ?></span>
                                        <?php 
                                            endforeach;
                                            if (count($techs) > 3) {
                                                echo '<span class="badge bg-secondary">+' . (count($techs) - 3) . ' more</span>';
                                            }
                                        } else {
                                            echo '<span class="text-muted">-</span>';
                                        }
                                        ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($project['is_featured']): ?>
                                            <span class="badge bg-warning">
                                                <i class="fas fa-star"></i> Featured
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle"></i> Active
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-warning btn-edit-project" 
                                                data-id="<?php echo $project['id']; ?>"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editProjectModal">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form method="POST" action="project-delete.php" style="display: inline;" class="delete-form">
                                            <input type="hidden" name="id" value="<?php echo $project['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger" 
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus project <?php echo htmlspecialchars($project['project_title']); ?>?');">
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
                        <nav aria-label="Projects pagination">
                            <ul class="pagination justify-content-md-end mb-0 mt-3 mt-md-0">
                                <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo $queryString; ?>">Previous</a>
                                </li>
                                
                                <?php 
                                $startPage = max(1, $page - 2);
                                $endPage = min($totalPages, $page + 2);
                                
                                if ($startPage > 1): ?>
                                    <li class="page-item"><a class="page-link" href="?page=1<?php echo $queryString; ?>">1</a></li>
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
                                    <li class="page-item"><a class="page-link" href="?page=<?php echo $totalPages; ?><?php echo $queryString; ?>"><?php echo $totalPages; ?></a></li>
                                <?php endif; ?>
                                
                                <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo $queryString; ?>">Next</a>
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
    
    <!-- Projects Statistics -->
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="fas fa-folder-open fa-2x text-primary mb-3"></i>
                    <h3 class="mb-1"><?php echo $totalRecords; ?></h3>
                    <p class="text-muted mb-0">Total Projects</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="fas fa-star fa-2x text-warning mb-3"></i>
                    <h3 class="mb-1">
                        <?php echo count(array_filter($projects, function($p) { return $p['is_featured']; })); ?>
                    </h3>
                    <p class="text-muted mb-0">Featured Projects</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="fab fa-github fa-2x text-dark mb-3"></i>
                    <h3 class="mb-1">
                        <?php echo count(array_filter($projects, function($p) { return !empty($p['github_url']); })); ?>
                    </h3>
                    <p class="text-muted mb-0">With GitHub</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Project Modal -->
<div class="modal fade" id="addProjectModal" tabindex="-1" aria-labelledby="addProjectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addProjectModalLabel">
                    <i class="fas fa-plus-circle me-2"></i> Add New Project
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addProjectForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="add_project_title" class="form-label">
                                    Project Title <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="add_project_title" name="project_title" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="add_description" class="form-label">
                                    Description <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control" id="add_description" name="description" rows="4" required></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="add_technologies" class="form-label">Technologies Used</label>
                                <input type="text" class="form-control" id="add_technologies" name="technologies" placeholder="PHP, MySQL, Bootstrap">
                                <small class="text-muted">Pisahkan dengan koma</small>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="add_project_url" class="form-label">
                                            <i class="fas fa-external-link-alt me-1"></i> Project URL
                                        </label>
                                        <input type="url" class="form-control" id="add_project_url" name="project_url">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="add_github_url" class="form-label">
                                            <i class="fab fa-github me-1"></i> GitHub URL
                                        </label>
                                        <input type="url" class="form-control" id="add_github_url" name="github_url">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="add_image" class="form-label">Project Image</label>
                                <input type="file" class="form-control" id="add_image" name="image" accept="image/*">
                                <small class="text-muted">Max 5MB (JPG, PNG)</small>
                                
                                <div class="mt-3" id="add_image_preview_container" style="display: none;">
                                    <p class="mb-2"><strong>Preview:</strong></p>
                                    <img id="add_image_preview" src="" alt="Preview" class="img-thumbnail" style="max-width: 100%;">
                                </div>
                            </div>
                            
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="add_is_featured" name="is_featured">
                                <label class="form-check-label" for="add_is_featured">
                                    <i class="fas fa-star text-warning me-1"></i> Featured Project
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i> Create Project
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Project Modal -->
<div class="modal fade" id="editProjectModal" tabindex="-1" aria-labelledby="editProjectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProjectModalLabel">
                    <i class="fas fa-edit me-2"></i> Edit Project
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editProjectForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="edit_project_id" name="id">
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="edit_project_title" class="form-label">
                                    Project Title <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="edit_project_title" name="project_title" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="edit_description" class="form-label">
                                    Description <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control" id="edit_description" name="description" rows="4" required></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="edit_technologies" class="form-label">Technologies Used</label>
                                <input type="text" class="form-control" id="edit_technologies" name="technologies" placeholder="PHP, MySQL, Bootstrap">
                                <small class="text-muted">Pisahkan dengan koma</small>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="edit_project_url" class="form-label">
                                            <i class="fas fa-external-link-alt me-1"></i> Project URL
                                        </label>
                                        <input type="url" class="form-control" id="edit_project_url" name="project_url">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="edit_github_url" class="form-label">
                                            <i class="fab fa-github me-1"></i> GitHub URL
                                        </label>
                                        <input type="url" class="form-control" id="edit_github_url" name="github_url">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit_image" class="form-label">Project Image</label>
                                <input type="file" class="form-control" id="edit_image" name="image" accept="image/*">
                                <small class="text-muted">Max 5MB (JPG, PNG)</small>
                                
                                <div class="mt-3" id="edit_image_preview_container">
                                    <p class="mb-2"><strong>Current Image:</strong></p>
                                    <img id="edit_image_preview" src="" alt="Preview" class="img-thumbnail" style="max-width: 100%;">
                                </div>
                            </div>
                            
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="edit_is_featured" name="is_featured">
                                <label class="form-check-label" for="edit_is_featured">
                                    <i class="fas fa-star text-warning me-1"></i> Featured Project
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i> Update Project
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Load project data into modal when edit button clicked
document.addEventListener('DOMContentLoaded', function() {
    const editButtons = document.querySelectorAll('.btn-edit-project');
    
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const projectId = this.getAttribute('data-id');
            
            // Fetch project data
            fetch('project-get.php?id=' + projectId)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const project = data.project;
                        
                        // Fill form
                        document.getElementById('edit_project_id').value = project.id;
                        document.getElementById('edit_project_title').value = project.project_title;
                        document.getElementById('edit_description').value = project.description;
                        document.getElementById('edit_technologies').value = project.technologies || '';
                        document.getElementById('edit_project_url').value = project.project_url || '';
                        document.getElementById('edit_github_url').value = project.github_url || '';
                        document.getElementById('edit_is_featured').checked = project.is_featured == 1;
                        
                        // Show current image
                        if (project.image) {
                            document.getElementById('edit_image_preview').src = '<?php echo UPLOAD_URL; ?>projects/' + project.image;
                            document.getElementById('edit_image_preview_container').style.display = 'block';
                        } else {
                            document.getElementById('edit_image_preview_container').style.display = 'none';
                        }
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Gagal memuat data project');
                });
        });
    });
    
    // Handle form submission
    document.getElementById('editProjectForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('project-update.php', {
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
            alert('Gagal mengupdate project');
        });
    });
    
    // Image preview on file select (Edit)
    document.getElementById('edit_image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('edit_image_preview').src = e.target.result;
                document.getElementById('edit_image_preview_container').style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });
    
    // Handle Add Project Form submission
    document.getElementById('addProjectForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('project-create.php', {
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
            alert('Gagal menambahkan project');
        });
    });
    
    // Image preview on file select (Add)
    document.getElementById('add_image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('add_image_preview').src = e.target.result;
                document.getElementById('add_image_preview_container').style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });
});
</script>

<?php include '../partials/footer.php'; ?>
