<?php
/**
 * Messages Page
 * View all contact messages
 */

require_once '../database/config.php';
require_once '../database/functions.php';

requireLogin();

// Get all messages
$messages = getAllMessages();

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
        <div class="card-body">
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
                                <th style="width: 30px;"></th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Subject</th>
                                <th>Date</th>
                                <th style="width: 150px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($messages as $message): ?>
                                <tr class="<?php echo !$message['is_read'] ? 'table-primary' : ''; ?>" id="message-row-<?php echo $message['id']; ?>">
                                    <td>
                                        <?php if (!$message['is_read']): ?>
                                            <span class="badge bg-primary unread-indicator" style="width: 8px; height: 8px; padding: 0; border-radius: 50%;" title="Unread"></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><strong><?php echo htmlspecialchars($message['name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($message['email']); ?></td>
                                    <td><?php echo truncate($message['subject'], 50); ?></td>
                                    <td>
                                        <small class="text-muted"><?php echo timeAgo($message['created_at']); ?></small>
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
                    
                    // Update counter unread di sidebar (jika ada)
                    updateUnreadCounter();
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
    function updateUnreadCounter() {
        // Hitung jumlah badge unread yang tersisa
        const remainingUnread = document.querySelectorAll('.unread-indicator').length;
        
        // Update semua counter yang ada
        const counters = document.querySelectorAll('.badge-danger, .unread-count');
        counters.forEach(counter => {
            if (remainingUnread > 0) {
                counter.textContent = remainingUnread;
                counter.style.display = '';
            } else {
                counter.style.display = 'none';
            }
        });
    }
});
</script>

<?php include '../partials/footer.php'; ?>
