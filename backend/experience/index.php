<?php
/**
 * Experience Management Page
 * Halaman untuk mengelola data pengalaman kerja dengan modal Add & Edit
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
    $whereClause = "WHERE company LIKE :search1 OR position LIKE :search2 OR description LIKE :search3";
    $params[':search1'] = "%$search%";
    $params[':search2'] = "%$search%";
    $params[':search3'] = "%$search%";
}

// Get total count
$countQuery = "SELECT COUNT(*) FROM experience $whereClause";
$countStmt = $pdo->prepare($countQuery);
foreach ($params as $key => $value) {
    $countStmt->bindValue($key, $value);
}
$countStmt->execute();
$totalRecords = $countStmt->fetchColumn();
$totalPages = ceil($totalRecords / $limit);

// Get paginated experience
$query = "SELECT * FROM experience $whereClause ORDER BY display_order ASC, start_date DESC LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$experiences = $stmt->fetchAll();

// Page info
$pageTitle = 'Experience';
$currentPage = 'experience';

// Include header & sidebar
include '../partials/header.php';
include '../partials/sidebar.php';
?>

<!-- Main Content -->
<div class="main-content">
    
    <!-- Page Header -->
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1><i class="fas fa-briefcase me-2"></i> Experience Management</h1>
            <p class="text-muted mb-0">Manage your work experience and internships</p>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addExperienceModal">
            <i class="fas fa-plus-circle me-2"></i> Add New Experience
        </button>
    </div>
    
    <!-- Flash Messages -->
    <?php displayFlashMessage(); ?>
    
    <!-- Experience Datatable -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-briefcase me-2"></i> Work Experience List</h5>
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
                                   placeholder="Search experience..." 
                                   value="<?php echo htmlspecialchars($search); ?>"
                                   data-autocomplete-url="<?php echo BACKEND_URL; ?>experience/experience-autocomplete.php"
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
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Company</th>
                            <th>Position</th>
                            <th>Period</th>
                            <th>Status</th>
                            <th style="width: 100px;">Order</th>
                            <th style="width: 150px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($experiences) > 0): ?>
                            <?php foreach ($experiences as $index => $exp): ?>
                                <tr>
                                    <td><?php echo $offset + $index + 1; ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($exp['company']); ?></strong>
                                    </td>
                                    <td><?php echo htmlspecialchars($exp['position']); ?></td>
                                    <td>
                                        <?php 
                                        $start = date('M Y', strtotime($exp['start_date']));
                                        $end = $exp['is_current'] ? 'Present' : date('M Y', strtotime($exp['end_date']));
                                        echo "$start - $end";
                                        ?>
                                    </td>
                                    <td>
                                        <?php if ($exp['is_current']): ?>
                                            <span class="badge bg-success">Current</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Past</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center"><?php echo $exp['display_order']; ?></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-warning" onclick="editExperience(<?php echo $exp['id']; ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="deleteExperience(<?php echo $exp['id']; ?>, '<?php echo htmlspecialchars($exp['company']); ?>')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No experience records found</p>
                                    <?php if (!empty($search)): ?>
                                        <a href="." class="btn btn-sm btn-outline-primary">Clear Search</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination Info & Controls -->
            <div class="row mt-3">
                <div class="col-sm-12 col-md-5">
                    <div class="datatable-info">
                        <?php if ($totalRecords > 0): ?>
                            Showing <?php echo $offset + 1; ?> to <?php echo min($offset + $limit, $totalRecords); ?> of <?php echo $totalRecords; ?> entries
                        <?php else: ?>
                            Showing 0 to 0 of 0 entries
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-sm-12 col-md-7">
                    <?php if ($totalPages > 1): ?>
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-end mb-0">
                                <!-- Previous Button -->
                                <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo max(1, $page - 1); ?>&limit=<?php echo $limit; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>">
                                        Previous
                                    </a>
                                </li>
                                
                                <?php
                                // Smart pagination - show max 5 page numbers
                                $startPage = max(1, $page - 2);
                                $endPage = min($totalPages, $page + 2);
                                
                                // Show first page
                                if ($startPage > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=1&limit=<?php echo $limit; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>">1</a>
                                    </li>
                                    <?php if ($startPage > 2): ?>
                                        <li class="page-item disabled"><span class="page-link">...</span></li>
                                    <?php endif; ?>
                                <?php endif; ?>
                                
                                <!-- Page Numbers -->
                                <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?>&limit=<?php echo $limit; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>
                                
                                <!-- Show last page -->
                                <?php if ($endPage < $totalPages): ?>
                                    <?php if ($endPage < $totalPages - 1): ?>
                                        <li class="page-item disabled"><span class="page-link">...</span></li>
                                    <?php endif; ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo $totalPages; ?>&limit=<?php echo $limit; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>"><?php echo $totalPages; ?></a>
                                    </li>
                                <?php endif; ?>
                                
                                <!-- Next Button -->
                                <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo min($totalPages, $page + 1); ?>&limit=<?php echo $limit; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>">
                                        Next
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Add Experience Modal -->
<div class="modal fade" id="addExperienceModal" tabindex="-1" aria-labelledby="addExperienceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="addExperienceForm" method="POST" action="experience-create.php">
                <div class="modal-header">
                    <h5 class="modal-title" id="addExperienceModalLabel">
                        <i class="fas fa-plus-circle me-2"></i> Add New Experience
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add_company" class="form-label">Company Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="add_company" name="company" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add_position" class="form-label">Position <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="add_position" name="position" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add_start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="add_start_date" name="start_date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add_end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="add_end_date" name="end_date">
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="add_is_current" name="is_current" value="1" onchange="toggleEndDate('add')">
                                    <label class="form-check-label" for="add_is_current">
                                        Currently working here
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="add_description" class="form-label">Description</label>
                        <textarea class="form-control" id="add_description" name="description" rows="4" placeholder="Describe your responsibilities and achievements..."></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="add_display_order" class="form-label">Display Order</label>
                        <input type="number" class="form-control" id="add_display_order" name="display_order" value="0" min="0">
                        <small class="text-muted">Lower numbers appear first</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i> Save Experience
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Experience Modal -->
<div class="modal fade" id="editExperienceModal" tabindex="-1" aria-labelledby="editExperienceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="editExperienceForm" method="POST" action="experience-update.php">
                <input type="hidden" id="edit_id" name="id">
                <div class="modal-header">
                    <h5 class="modal-title" id="editExperienceModalLabel">
                        <i class="fas fa-edit me-2"></i> Edit Experience
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_company" class="form-label">Company Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_company" name="company" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_position" class="form-label">Position <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_position" name="position" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="edit_start_date" name="start_date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="edit_end_date" name="end_date">
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="edit_is_current" name="is_current" value="1" onchange="toggleEndDate('edit')">
                                    <label class="form-check-label" for="edit_is_current">
                                        Currently working here
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Description</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="4" placeholder="Describe your responsibilities and achievements..."></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_display_order" class="form-label">Display Order</label>
                        <input type="number" class="form-control" id="edit_display_order" name="display_order" value="0" min="0">
                        <small class="text-muted">Lower numbers appear first</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i> Update Experience
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Toggle end date based on "currently working" checkbox
function toggleEndDate(prefix) {
    const checkbox = document.getElementById(prefix + '_is_current');
    const endDateInput = document.getElementById(prefix + '_end_date');
    
    if (checkbox.checked) {
        endDateInput.value = '';
        endDateInput.disabled = true;
        endDateInput.removeAttribute('required');
    } else {
        endDateInput.disabled = false;
    }
}

// Load experience data for editing
function editExperience(id) {
    fetch(`experience-get.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const exp = data.data;
                document.getElementById('edit_id').value = exp.id;
                document.getElementById('edit_company').value = exp.company;
                document.getElementById('edit_position').value = exp.position;
                document.getElementById('edit_start_date').value = exp.start_date;
                document.getElementById('edit_end_date').value = exp.end_date || '';
                document.getElementById('edit_description').value = exp.description || '';
                document.getElementById('edit_display_order').value = exp.display_order;
                document.getElementById('edit_is_current').checked = exp.is_current == 1;
                
                // Toggle end date field
                toggleEndDate('edit');
                
                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('editExperienceModal'));
                modal.show();
            } else {
                alert('Error loading experience data');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading experience data');
        });
}

// Delete experience with confirmation
function deleteExperience(id, company) {
    if (confirm(`Are you sure you want to delete experience at "${company}"?`)) {
        fetch(`experience-delete.php?id=${id}`, {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting experience');
        });
    }
}

// Handle Add Experience Form submission
document.getElementById('addExperienceForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('experience-create.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Experience added successfully',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: data.message || 'Failed to add experience'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Failed to add experience'
        });
    });
});

// Handle Edit Experience Form submission
document.getElementById('editExperienceForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('experience-update.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Experience updated successfully',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: data.message || 'Failed to update experience'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Failed to update experience'
        });
    });
});
</script>

<?php include '../partials/footer.php'; ?>
