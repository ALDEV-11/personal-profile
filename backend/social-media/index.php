<?php
/**
 * Social Media Management Page
 * Halaman untuk mengelola link social media dengan modal Add & Edit
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
    $whereClause = "WHERE platform LIKE :search1 OR url LIKE :search2 OR icon LIKE :search3";
    $params[':search1'] = "%$search%";
    $params[':search2'] = "%$search%";
    $params[':search3'] = "%$search%";
}

// Get total count
$countQuery = "SELECT COUNT(*) FROM social_media $whereClause";
$countStmt = $pdo->prepare($countQuery);
foreach ($params as $key => $value) {
    $countStmt->bindValue($key, $value);
}
$countStmt->execute();
$totalRecords = $countStmt->fetchColumn();
$totalPages = ceil($totalRecords / $limit);

// Get paginated social media
$query = "SELECT * FROM social_media $whereClause ORDER BY display_order ASC, created_at DESC LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$socialMedias = $stmt->fetchAll();

// Page info
$pageTitle = 'Social Media';
$currentPage = 'social-media';

// Include header & sidebar
include '../partials/header.php';
include '../partials/sidebar.php';
?>

<!-- Main Content -->
<div class="main-content">
    
    <!-- Page Header -->
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1><i class="fas fa-share-alt me-2"></i> Social Media Management</h1>
            <p class="text-muted mb-0">Manage your social media profiles and links</p>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSocialMediaModal">
            <i class="fas fa-plus-circle me-2"></i> Add Social Media
        </button>
    </div>
    
    <!-- Flash Messages -->
    <?php displayFlashMessage(); ?>
    
    <!-- Social Media Datatable -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-share-alt me-2"></i> Social Media Links</h5>
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
                                   placeholder="Search social media..." 
                                   value="<?php echo htmlspecialchars($search); ?>"
                                   data-autocomplete-url="<?php echo BACKEND_URL; ?>social-media/social-media-autocomplete.php"
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
                            <th style="width: 80px;">Icon</th>
                            <th>Platform</th>
                            <th>URL</th>
                            <th style="width: 100px;">Order</th>
                            <th style="width: 150px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($socialMedias) > 0): ?>
                            <?php foreach ($socialMedias as $index => $social): ?>
                                <tr>
                                    <td><?php echo $offset + $index + 1; ?></td>
                                    <td class="text-center">
                                        <i class="<?php echo htmlspecialchars($social['icon']); ?> fa-2x"></i>
                                    </td>
                                    <td><strong><?php echo htmlspecialchars($social['platform']); ?></strong></td>
                                    <td>
                                        <a href="<?php echo htmlspecialchars($social['url']); ?>" target="_blank" class="text-decoration-none">
                                            <?php echo htmlspecialchars($social['url']); ?>
                                            <i class="fas fa-external-link-alt ms-1 small"></i>
                                        </a>
                                    </td>
                                    <td class="text-center"><?php echo $social['display_order']; ?></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-warning" onclick="editSocialMedia(<?php echo $social['id']; ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="deleteSocialMedia(<?php echo $social['id']; ?>, '<?php echo htmlspecialchars($social['platform']); ?>')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No social media links found</p>
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
                                // Smart pagination
                                $startPage = max(1, $page - 2);
                                $endPage = min($totalPages, $page + 2);
                                
                                if ($startPage > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=1&limit=<?php echo $limit; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>">1</a>
                                    </li>
                                    <?php if ($startPage > 2): ?>
                                        <li class="page-item disabled"><span class="page-link">...</span></li>
                                    <?php endif; ?>
                                <?php endif; ?>
                                
                                <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?>&limit=<?php echo $limit; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>
                                
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

<!-- Add Social Media Modal -->
<div class="modal fade" id="addSocialMediaModal" tabindex="-1" aria-labelledby="addSocialMediaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="addSocialMediaForm" method="POST" action="social-media-create.php">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSocialMediaModalLabel">
                        <i class="fas fa-plus-circle me-2"></i> Add Social Media
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="add_platform" class="form-label">Platform Name <span class="text-danger">*</span></label>
                        <select class="form-select" id="add_platform" name="platform" required onchange="updateIcon('add')">
                            <option value="">Select Platform</option>
                            <option value="Email" data-icon="fas fa-envelope">Email</option>
                            <option value="Facebook" data-icon="fab fa-facebook">Facebook</option>
                            <option value="Twitter" data-icon="fab fa-twitter">Twitter</option>
                            <option value="Instagram" data-icon="fab fa-instagram">Instagram</option>
                            <option value="LinkedIn" data-icon="fab fa-linkedin">LinkedIn</option>
                            <option value="GitHub" data-icon="fab fa-github">GitHub</option>
                            <option value="YouTube" data-icon="fab fa-youtube">YouTube</option>
                            <option value="TikTok" data-icon="fab fa-tiktok">TikTok</option>
                            <option value="WhatsApp" data-icon="fab fa-whatsapp">WhatsApp</option>
                            <option value="Telegram" data-icon="fab fa-telegram">Telegram</option>
                            <option value="Discord" data-icon="fab fa-discord">Discord</option>
                            <option value="Other" data-icon="fas fa-link">Other</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="add_url" class="form-label">Profile URL <span class="text-danger">*</span></label>
                        <input type="url" class="form-control" id="add_url" name="url" placeholder="https://..." required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="add_icon" class="form-label">Icon Class (Font Awesome) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i id="add_icon_preview" class="fas fa-link"></i>
                            </span>
                            <input type="text" class="form-control" id="add_icon" name="icon" placeholder="fab fa-facebook" required>
                        </div>
                        <small class="text-muted">Example: fab fa-facebook, fab fa-twitter, fab fa-github</small>
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
                        <i class="fas fa-save me-2"></i> Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Social Media Modal -->
<div class="modal fade" id="editSocialMediaModal" tabindex="-1" aria-labelledby="editSocialMediaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editSocialMediaForm" method="POST" action="social-media-update.php">
                <input type="hidden" id="edit_id" name="id">
                <div class="modal-header">
                    <h5 class="modal-title" id="editSocialMediaModalLabel">
                        <i class="fas fa-edit me-2"></i> Edit Social Media
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_platform" class="form-label">Platform Name <span class="text-danger">*</span></label>
                        <select class="form-select" id="edit_platform" name="platform" required onchange="updateIcon('edit')">
                            <option value="">Select Platform</option>
                            <option value="Email" data-icon="fas fa-envelope">Email</option>
                            <option value="Facebook" data-icon="fab fa-facebook">Facebook</option>
                            <option value="Twitter" data-icon="fab fa-twitter">Twitter</option>
                            <option value="Instagram" data-icon="fab fa-instagram">Instagram</option>
                            <option value="LinkedIn" data-icon="fab fa-linkedin">LinkedIn</option>
                            <option value="GitHub" data-icon="fab fa-github">GitHub</option>
                            <option value="YouTube" data-icon="fab fa-youtube">YouTube</option>
                            <option value="TikTok" data-icon="fab fa-tiktok">TikTok</option>
                            <option value="WhatsApp" data-icon="fab fa-whatsapp">WhatsApp</option>
                            <option value="Telegram" data-icon="fab fa-telegram">Telegram</option>
                            <option value="Discord" data-icon="fab fa-discord">Discord</option>
                            <option value="Other" data-icon="fas fa-link">Other</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_url" class="form-label">Profile URL <span class="text-danger">*</span></label>
                        <input type="url" class="form-control" id="edit_url" name="url" placeholder="https://..." required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_icon" class="form-label">Icon Class (Font Awesome) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i id="edit_icon_preview" class="fas fa-link"></i>
                            </span>
                            <input type="text" class="form-control" id="edit_icon" name="icon" placeholder="fab fa-facebook" required>
                        </div>
                        <small class="text-muted">Example: fab fa-facebook, fab fa-twitter, fab fa-github</small>
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
                        <i class="fas fa-save me-2"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Update icon based on platform selection
function updateIcon(prefix) {
    const select = document.getElementById(prefix + '_platform');
    const iconInput = document.getElementById(prefix + '_icon');
    const iconPreview = document.getElementById(prefix + '_icon_preview');
    
    const selectedOption = select.options[select.selectedIndex];
    const icon = selectedOption.getAttribute('data-icon');
    
    if (icon) {
        iconInput.value = icon;
        iconPreview.className = icon;
    }
}

// Update icon preview on input change
document.addEventListener('DOMContentLoaded', function() {
    ['add_icon', 'edit_icon'].forEach(id => {
        document.getElementById(id).addEventListener('input', function() {
            const prefix = id.split('_')[0];
            const iconPreview = document.getElementById(prefix + '_icon_preview');
            iconPreview.className = this.value || 'fas fa-link';
        });
    });
});

// Load social media data for editing
function editSocialMedia(id) {
    fetch(`social-media-get.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const social = data.data;
                document.getElementById('edit_id').value = social.id;
                document.getElementById('edit_platform').value = social.platform;
                document.getElementById('edit_url').value = social.url;
                document.getElementById('edit_icon').value = social.icon;
                document.getElementById('edit_display_order').value = social.display_order;
                document.getElementById('edit_icon_preview').className = social.icon;
                
                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('editSocialMediaModal'));
                modal.show();
            } else {
                alert('Error loading social media data');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading social media data');
        });
}

// Delete social media with confirmation
function deleteSocialMedia(id, platform) {
    if (confirm(`Are you sure you want to delete ${platform}?`)) {
        fetch(`social-media-delete.php?id=${id}`, {
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
            alert('Error deleting social media');
        });
    }
}

// Handle Add Social Media Form submission
document.getElementById('addSocialMediaForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('social-media-create.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Social media added successfully',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: data.message || 'Failed to add social media'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Failed to add social media'
        });
    });
});

// Handle Edit Social Media Form submission
document.getElementById('editSocialMediaForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('social-media-update.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Social media updated successfully',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: data.message || 'Failed to update social media'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Failed to update social media'
        });
    });
});
</script>

<?php include '../partials/footer.php'; ?>
