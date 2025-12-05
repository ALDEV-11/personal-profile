<?php
/**
 * Messages Page
 * View all contact messages
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
    $whereClause = "WHERE name LIKE :search1 OR email LIKE :search2 OR subject LIKE :search3 OR message LIKE :search4";
    $params[':search1'] = "%$search%";
    $params[':search2'] = "%$search%";
    $params[':search3'] = "%$search%";
    $params[':search4'] = "%$search%";
}

// Get total count
$countQuery = "SELECT COUNT(*) FROM contact_messages $whereClause";
$countStmt = $pdo->prepare($countQuery);
foreach ($params as $key => $value) {
    $countStmt->bindValue($key, $value);
}
$countStmt->execute();
$totalRecords = $countStmt->fetchColumn();
$totalPages = ceil($totalRecords / $limit);

// Get paginated messages
$query = "SELECT * FROM contact_messages $whereClause ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'Contact Messages';
include '../partials/header.php';
include '../partials/sidebar.php';
?>

<div class="main-content">
    <div class="page-header">
        <h1>Contact Messages</h1>
        <p>View and manage messages from your website visitors</p>
    </div>
    
    <?php displayFlashMessage(); ?>
    
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-envelope me-2"></i> Messages List
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
                                   placeholder="Search messages..." 
                                   value="<?php echo htmlspecialchars($search); ?>"
                                   data-autocomplete-url="<?php echo BACKEND_URL; ?>messages/message-autocomplete.php"
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
            <?php if (empty($messages)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-inbox" style="font-size: 4rem; color: #D1D5DB;"></i>
                    <h5 class="mt-3">Belum ada pesan</h5>
                    <p class="text-muted">Pesan dari contact form akan muncul di sini</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th style="width: 30px;"></th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Subject</th>
                                <th>Date</th>
                                <th style="width: 150px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($messages as $index => $message): ?>
                                <tr class="<?php echo !$message['is_read'] ? 'table-primary' : ''; ?>" id="message-row-<?php echo $message['id']; ?>">
                                    <td><?php echo $offset + $index + 1; ?></td>
                                    <td>
                                        <?php if (!$message['is_read']): ?>
                                            <span class="badge bg-primary unread-indicator" style="width: 8px; height: 8px; padding: 0; border-radius: 50%;" title="Unread"></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><strong><?php echo htmlspecialchars($message['name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($message['email']); ?></td>
                                    <td><?php echo truncate($message['subject'], 50); ?></td>
                                    <td>
                                        <small class="text-muted time-ago" data-timestamp="<?php echo strtotime($message['created_at']); ?>">
                                            <?php echo timeAgo($message['created_at']); ?>
                                        </small>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary btn-view-message" 
                                                data-message-id="<?php echo $message['id']; ?>"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#messageModal<?php echo $message['id']; ?>">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <form method="POST" action="message-delete.php" style="display: inline;" class="delete-form">
                                            <input type="hidden" name="id" value="<?php echo $message['id']; ?>">
                                            <input type="hidden" name="name" value="<?php echo htmlspecialchars($message['name']); ?>">
                                            <button type="button" class="btn btn-sm btn-danger btn-delete-confirm" 
                                                    data-name="<?php echo htmlspecialchars($message['name']); ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                
                                <!-- Message Modal -->
                                <div class="modal fade" id="messageModal<?php echo $message['id']; ?>" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Message Details</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <strong>From:</strong> <?php echo htmlspecialchars($message['name']); ?>
                                                    <br>
                                                    <strong>Email:</strong> <a href="mailto:<?php echo htmlspecialchars($message['email']); ?>"><?php echo htmlspecialchars($message['email']); ?></a>
                                                    <br>
                                                    <strong>Date:</strong> <?php echo formatDateIndo($message['created_at']); ?>
                                                </div>
                                                <hr>
                                                <div class="mb-3">
                                                    <strong>Subject:</strong>
                                                    <h6><?php echo htmlspecialchars($message['subject']); ?></h6>
                                                </div>
                                                <div>
                                                    <strong>Message:</strong>
                                                    <p style="white-space: pre-wrap;"><?php echo htmlspecialchars($message['message']); ?></p>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <a href="mailto:<?php echo htmlspecialchars($message['email']); ?>" class="btn btn-primary">
                                                    <i class="fas fa-reply me-1"></i> Reply via Email
                                                </a>
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
                        <nav aria-label="Messages pagination">
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
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Mark message as read when modal is opened
document.addEventListener('DOMContentLoaded', function() {
    console.log('Messages page loaded');
    
    const viewButtons = document.querySelectorAll('.btn-view-message');
    console.log('Found ' + viewButtons.length + ' view buttons');
    
    viewButtons.forEach(button => {
        button.addEventListener('click', function() {
            const messageId = this.getAttribute('data-message-id');
            const row = document.getElementById('message-row-' + messageId);
            
            console.log('Marking message as read:', messageId);
            
            // Send AJAX request to mark as read
            fetch('mark-read.php?id=' + messageId, {
                method: 'GET',
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                console.log('Mark read response:', data);
                if (data.success) {
                    // Hapus badge unread indicator
                    const badge = row.querySelector('.unread-indicator');
                    if (badge) {
                        badge.remove();
                    }
                    
                    // Hapus highlight row (class table-primary)
                    if (row.classList.contains('table-primary')) {
                        row.classList.remove('table-primary');
                    }
                    
                    // Update counter unread di navbar dengan nilai dari server
                    updateUnreadCounter(data.unreadCount);
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });
    
    // Handle delete confirmation
    const deleteButtons = document.querySelectorAll('.btn-delete-confirm');
    console.log('Found ' + deleteButtons.length + ' delete buttons');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Delete button clicked');
            
            const name = this.getAttribute('data-name');
            const form = this.closest('form');
            
            console.log('Showing confirm for:', name);
            console.log('Form action:', form.action);
            console.log('Form method:', form.method);
            
            if (confirm('Apakah Anda yakin ingin menghapus pesan dari ' + name + '?')) {
                console.log('User confirmed, submitting form');
                console.log('Form data:', new FormData(form));
                
                // Log form fields
                const formData = new FormData(form);
                for (let pair of formData.entries()) {
                    console.log(pair[0] + ': ' + pair[1]);
                }
                
                // Submit form
                form.submit();
                console.log('Form submitted!');
            } else {
                console.log('User cancelled');
            }
        });
    });
    
    // Function untuk update counter
    function updateUnreadCounter(serverCount) {
        // Update badge di navbar (bell icon)
        const navBadge = document.querySelector('.notification-badge');
        if (navBadge) {
            if (serverCount > 0) {
                navBadge.textContent = serverCount > 99 ? '99+' : serverCount;
                navBadge.style.display = '';
            } else {
                // Sembunyikan badge jika tidak ada pesan unread
                navBadge.style.display = 'none';
            }
        }
        
        // Update counter lain jika ada (di sidebar atau tempat lain)
        const otherCounters = document.querySelectorAll('.badge-danger, .unread-count');
        otherCounters.forEach(counter => {
            if (serverCount > 0) {
                counter.textContent = serverCount > 99 ? '99+' : serverCount;
                counter.style.display = '';
            } else {
                counter.style.display = 'none';
            }
        });
    }
    
    // Realtime update untuk timestamp
    function timeAgoRealtime(timestamp) {
        const now = Math.floor(Date.now() / 1000);
        const difference = now - timestamp;
        
        // Pastikan tidak ada nilai negatif
        if (difference < 0) return '0 detik yang lalu';
        
        // Kurang dari 60 detik
        if (difference < 60) {
            return difference + ' detik yang lalu';
        }
        
        // Kurang dari 60 menit
        if (difference < 3600) {
            const minutes = Math.floor(difference / 60);
            return minutes + ' menit yang lalu';
        }
        
        // Kurang dari 24 jam
        if (difference < 86400) {
            const hours = Math.floor(difference / 3600);
            return hours + ' jam yang lalu';
        }
        
        // Kurang dari 30 hari
        if (difference < 2592000) {
            const days = Math.floor(difference / 86400);
            return days + ' hari yang lalu';
        }
        
        // Lebih dari 30 hari, kembalikan null (akan tetap tampilkan teks original)
        return null;
    }
    
    // Update semua timestamp dengan animasi
    function updateAllTimestamps() {
        const timeElements = document.querySelectorAll('.time-ago');
        timeElements.forEach(element => {
            const timestamp = parseInt(element.getAttribute('data-timestamp'));
            const oldTime = element.textContent;
            const newTime = timeAgoRealtime(timestamp);
            
            if (newTime && newTime !== oldTime) {
                // Tambahkan animasi fade saat berubah
                element.style.transition = 'opacity 0.3s ease, color 0.3s ease';
                element.style.opacity = '0.5';
                
                setTimeout(() => {
                    element.textContent = newTime;
                    element.style.opacity = '1';
                    
                    // Flash effect untuk perubahan signifikan
                    if (shouldHighlightChange(oldTime, newTime)) {
                        element.style.color = '#4F46E5';
                        element.style.fontWeight = '600';
                        
                        setTimeout(() => {
                            element.style.color = '';
                            element.style.fontWeight = '';
                        }, 1000);
                    }
                }, 300);
            } else if (newTime) {
                element.textContent = newTime;
            }
        });
    }
    
    // Tentukan apakah perubahan signifikan (detik ke menit, menit ke jam, dll)
    function shouldHighlightChange(oldText, newText) {
        const oldUnit = oldText.match(/(detik|menit|jam|hari)/);
        const newUnit = newText.match(/(detik|menit|jam|hari)/);
        
        if (!oldUnit || !newUnit) return false;
        
        const units = ['detik', 'menit', 'jam', 'hari'];
        const oldIndex = units.indexOf(oldUnit[0]);
        const newIndex = units.indexOf(newUnit[0]);
        
        // Highlight jika unit berubah (detik->menit, menit->jam, etc)
        return newIndex > oldIndex;
    }
    
    // Update setiap 10 detik
    setInterval(updateAllTimestamps, 10000);
    
    // Update awal
    updateAllTimestamps();
    
    // Tambahkan pulse animation untuk waktu yang baru (< 1 menit)
    function addPulseAnimation() {
        const timeElements = document.querySelectorAll('.time-ago');
        timeElements.forEach(element => {
            const timestamp = parseInt(element.getAttribute('data-timestamp'));
            const now = Math.floor(Date.now() / 1000);
            const difference = now - timestamp;
            
            if (difference < 60) {
                element.classList.add('pulse-animation');
            } else {
                element.classList.remove('pulse-animation');
            }
        });
    }
    
    // Jalankan pulse animation check setiap 5 detik
    setInterval(addPulseAnimation, 5000);
    addPulseAnimation();
});
</script>

<style>
/* Animasi pulse untuk waktu yang baru */
@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.6;
    }
}

.pulse-animation {
    animation: pulse 2s ease-in-out infinite;
    color: #4F46E5 !important;
    font-weight: 600 !important;
}

/* Smooth transition untuk semua time elements */
.time-ago {
    display: inline-block;
    transition: all 0.3s ease;
}

/* Hover effect */
.time-ago:hover {
    color: #4F46E5 !important;
    cursor: help;
    transform: scale(1.05);
}
</style>

<?php include '../partials/footer.php'; ?>

