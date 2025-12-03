<?php
/**
 * Education Management Page
 * Halaman untuk mengelola data pendidikan dengan modal Add & Edit
 */

require_once '../database/config.php';
require_once '../database/functions.php';

// Proteksi halaman - harus login
requireLogin();

// Search functionality
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Pagination settings
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
if ($limit <= 0 || $limit > 100) $limit = 5; // Validation
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Build query with search
$whereClause = '';
$params = [];

if (!empty($search)) {
    $whereClause = "WHERE institution LIKE :search1 OR degree LIKE :search2 OR field_of_study LIKE :search3";
    $params[':search1'] = "%$search%";
    $params[':search2'] = "%$search%";
    $params[':search3'] = "%$search%";
}

// Get total count
$countQuery = "SELECT COUNT(*) FROM education $whereClause";
$countStmt = $pdo->prepare($countQuery);
foreach ($params as $key => $value) {
    $countStmt->bindValue($key, $value);
}
$countStmt->execute();
$totalRecords = $countStmt->fetchColumn();
$totalPages = ceil($totalRecords / $limit);

// Get paginated education
$query = "SELECT * FROM education $whereClause ORDER BY display_order ASC, start_date DESC LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$educations = $stmt->fetchAll();

// Page info
$pageTitle = 'Education';
$currentPage = 'education';

// Include header & sidebar
include '../partials/header.php';
include '../partials/sidebar.php';
?>

<!-- Main Content -->
<div class="main-content">
    
    <!-- Page Header -->
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1><i class="fas fa-graduation-cap me-2"></i> Education Management</h1>
            <p class="text-muted mb-0">Manage your educational background</p>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEducationModal">
            <i class="fas fa-plus-circle me-2"></i> Add New Education
        </button>
    </div>
    
    <!-- Flash Messages -->
    <?php displayFlashMessage(); ?>
    
    <!-- Education Datatable -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i> Education List
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
                                   placeholder="Search education..." 
                                   value="<?php echo htmlspecialchars($search); ?>"
                                   data-autocomplete-url="<?php echo BACKEND_URL; ?>education/education-autocomplete.php"
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
            <?php if (empty($educations)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-graduation-cap" style="font-size: 4rem; color: #D1D5DB; margin-bottom: 20px;"></i>
                    <h5 class="text-muted">No Education Added Yet</h5>
                    <p class="text-muted">Start by adding your educational background</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>Institution</th>
                                <th>Degree</th>
                                <th>Field of Study</th>
                                <th>Period</th>
                                <th style="width: 150px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($educations as $index => $education): ?>
                                <tr>
                                    <td><?php echo $offset + $index + 1; ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($education['institution']); ?></strong>
                                    </td>
                                    <td><?php echo htmlspecialchars($education['degree']); ?></td>
                                    <td><?php echo htmlspecialchars($education['field_of_study']); ?></td>
                                    <td>
                                        <small class="text-muted">
                                            <?php echo date('M Y', strtotime($education['start_date'])); ?> - 
                                            <?php echo $education['end_date'] ? date('M Y', strtotime($education['end_date'])) : 'Present'; ?>
                                        </small>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-warning" onclick="editEducation(<?php echo $education['id']; ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form method="POST" action="education-delete.php" style="display: inline;" class="delete-form">
                                            <input type="hidden" name="id" value="<?php echo $education['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger" 
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus education dari <?php echo htmlspecialchars($education['institution']); ?>?');">
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
                        <nav aria-label="Education pagination">
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
    
</div>

<!-- Add Education Modal -->
<div class="modal fade" id="addEducationModal" tabindex="-1" aria-labelledby="addEducationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addEducationModalLabel">
                    <i class="fas fa-plus-circle me-2"></i> Add New Education
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addEducationForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="add_institution" class="form-label">Institution Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="add_institution" name="institution" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="add_degree" class="form-label">Degree <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="add_degree" name="degree" placeholder="e.g. Bachelor, Master, High School" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="add_field_of_study" class="form-label">Field of Study <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="add_field_of_study" name="field_of_study" placeholder="e.g. Computer Science" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="add_start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="add_start_date" name="start_date" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="add_end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="add_end_date" name="end_date">
                            <small class="text-muted">Leave empty if still studying</small>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label for="add_description" class="form-label">Description</label>
                            <textarea class="form-control" id="add_description" name="description" rows="4" placeholder="Activities, achievements, GPA, etc..."></textarea>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label for="add_display_order" class="form-label">Display Order</label>
                            <input type="number" class="form-control" id="add_display_order" name="display_order" value="0" min="0">
                            <small class="text-muted">Lower numbers appear first</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Save Education
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Education Modal -->
<div class="modal fade" id="editEducationModal" tabindex="-1" aria-labelledby="editEducationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editEducationModalLabel">
                    <i class="fas fa-edit me-2"></i> Edit Education
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editEducationForm" enctype="multipart/form-data">
                <input type="hidden" id="edit_id" name="id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="edit_institution" class="form-label">Institution Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_institution" name="institution" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="edit_degree" class="form-label">Degree <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_degree" name="degree" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="edit_field_of_study" class="form-label">Field of Study <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_field_of_study" name="field_of_study" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="edit_start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="edit_start_date" name="start_date" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="edit_end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="edit_end_date" name="end_date">
                            <small class="text-muted">Leave empty if still studying</small>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label for="edit_description" class="form-label">Description</label>
                            <textarea class="form-control" id="edit_description" name="description" rows="4"></textarea>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label for="edit_display_order" class="form-label">Display Order</label>
                            <input type="number" class="form-control" id="edit_display_order" name="display_order" min="0">
                            <small class="text-muted">Lower numbers appear first</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Update Education
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Add Education Form Handler
document.getElementById('addEducationForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Saving...';
    
    try {
        const response = await fetch('education-create.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Edit Successfully',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                window.location.href = '<?php echo BACKEND_URL; ?>education/';
            });
        } else {
            alert('Error: ' + result.message);
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    } catch (error) {
        alert('An error occurred. Please try again.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }
});

// Edit Education Function
async function editEducation(id) {
    try {
        const response = await fetch('education-get.php?id=' + id);
        const result = await response.json();
        
        if (result.success) {
            const education = result.data;
            
            document.getElementById('edit_id').value = education.id;
            document.getElementById('edit_institution').value = education.institution;
            document.getElementById('edit_degree').value = education.degree;
            document.getElementById('edit_field_of_study').value = education.field_of_study;
            document.getElementById('edit_start_date').value = education.start_date;
            document.getElementById('edit_end_date').value = education.end_date || '';
            document.getElementById('edit_description').value = education.description || '';
            document.getElementById('edit_display_order').value = education.display_order;
            
            const editModal = new bootstrap.Modal(document.getElementById('editEducationModal'));
            editModal.show();
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        alert('An error occurred. Please try again.');
    }
}

// Edit Education Form Handler
document.getElementById('editEducationForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Updating...';
    
    try {
        const response = await fetch('education-update.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Edit Successfully',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                window.location.href = '<?php echo BACKEND_URL; ?>education/';
            });
        } else {
            alert('Error: ' + result.message);
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    } catch (error) {
        alert('An error occurred. Please try again.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }
});
</script>

<?php include '../partials/footer.php'; ?>
