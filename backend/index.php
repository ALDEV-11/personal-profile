<?php
/**
 * Dashboard Page
 * Halaman utama admin panel dengan statistics dan recent messages
 */

require_once 'database/config.php';
require_once 'database/functions.php';

// Proteksi halaman - harus login
requireLogin();

// Get statistics
$stats = getDashboardStats();
$recentMessages = getRecentMessages(5);
$currentUser = getCurrentUser();

// Get profile info
$profile = getProfile();

// Page title
$pageTitle = 'Dashboard';

// Include header
include 'partials/header.php';
include 'partials/sidebar.php';
?>

<!-- Main Content -->
<div class="main-content">
    
    <!-- Page Header -->
    <div class="page-header">
        <h1>Dashboard</h1>
        <p>Selamat datang kembali, <strong><?php echo $currentUser['username']; ?></strong>! Berikut adalah ringkasan data Anda.</p>
    </div>
    
    <!-- Flash Messages -->
    <?php displayFlashMessage(); ?>
    
    <!-- Main Statistics & Quick Actions -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <!-- Pie Chart -->
                        <div class="col-lg-6">
                            <h5 class="mb-4">
                                <i class="fas fa-chart-pie me-2"></i> Statistics Overview
                            </h5>
                            <div style="position: relative; height: 400px;">
                                <canvas id="mainStatsChart"></canvas>
                            </div>
                        </div>
                        
                        <!-- Quick Actions -->
                        <div class="col-lg-6">
                            <h5 class="mb-4">
                                <i class="fas fa-bolt me-2"></i> Quick Actions
                            </h5>
                            <div class="d-grid gap-2">
                                <a href="profile/" class="btn btn-outline-primary text-start">
                                    <i class="fas fa-user-circle me-2"></i> Edit Profile
                                </a>
                                <a href="skills/" class="btn btn-outline-primary text-start">
                                    <i class="fas fa-plus-circle me-2"></i> Manage Skills
                                </a>
                                <a href="projects/" class="btn btn-outline-primary text-start">
                                    <i class="fas fa-plus-circle me-2"></i> Manage Projects
                                </a>
                                <a href="education/" class="btn btn-outline-primary text-start">
                                    <i class="fas fa-plus-circle me-2"></i> Manage Education
                                </a>
                                <a href="experience/" class="btn btn-outline-primary text-start">
                                    <i class="fas fa-plus-circle me-2"></i> Manage Experience
                                </a>
                                <a href="social-media/" class="btn btn-outline-primary text-start">
                                    <i class="fas fa-plus-circle me-2"></i> Manage Social Media
                                </a>
                               
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Messages -->
    <div class="row g-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-envelope me-2"></i> Recent Messages
                    </h5>
                    <a href="messages/" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($recentMessages)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-inbox" style="font-size: 3rem; color: #D1D5DB; margin-bottom: 15px;"></i>
                            <p class="text-muted">Belum ada pesan</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive" style="border-bottom-left-radius: 0.375rem; border-bottom-right-radius: 0.375rem; overflow: hidden;">
                            <table id="messagesTable" class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 30px;"></th>
                                        <th>Name</th>
                                        <th>Subject</th>
                                        <th>Date</th>
                                        <th style="width: 100px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentMessages as $message): ?>
                                        <tr>
                                            <td>
                                                <?php if (!$message['is_read']): ?>
                                                    <span class="badge bg-primary" style="width: 8px; height: 8px; padding: 0; border-radius: 50%;"></span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($message['name']); ?></strong>
                                                <br>
                                                <small class="text-muted"><?php echo htmlspecialchars($message['email']); ?></small>
                                            </td>
                                            <td><?php echo truncate($message['subject'], 50); ?></td>
                                            <td>
                                                <small class="text-muted time-ago" data-timestamp="<?php echo strtotime($message['created_at']); ?>">
                                                    <?php echo timeAgo($message['created_at']); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewMessage(<?php echo $message['id']; ?>)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
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
    </div>
    
</div>

<!-- View Message Modal -->
<div class="modal fade" id="viewMessageModal" tabindex="-1" aria-labelledby="viewMessageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewMessageModalLabel">
                    <i class="fas fa-envelope me-2"></i> Message Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="messageContent">
                    <div class="text-center py-4">
                        <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                        <p class="mt-2">Loading message...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i> Close
                </button>
                <a href="messages/" class="btn btn-primary">
                    <i class="fas fa-envelope me-2"></i> Go to Messages
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    .stat-card {
        transition: transform 0.3s, box-shadow 0.3s;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
    }
</style>

<script>
// View Message Function
async function viewMessage(id) {
    const modal = new bootstrap.Modal(document.getElementById('viewMessageModal'));
    const contentDiv = document.getElementById('messageContent');
    
    // Show modal with loading
    modal.show();
    contentDiv.innerHTML = `
        <div class="text-center py-4">
            <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
            <p class="mt-2">Loading message...</p>
        </div>
    `;
    
    try {
        // Fetch message data from messages folder
        const response = await fetch('messages/message-get.php?id=' + id);
        const result = await response.json();
        
        if (result.success) {
            const message = result.data;
            
            // Format date
            const date = new Date(message.created_at);
            const formattedDate = date.toLocaleDateString('id-ID', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
            
            // Display message
            contentDiv.innerHTML = `
                <div class="mb-3">
                    <label class="form-label fw-bold text-muted">From:</label>
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" 
                             style="width: 40px; height: 40px; font-size: 1.2rem;">
                            ${message.name.charAt(0).toUpperCase()}
                        </div>
                        <div>
                            <div class="fw-bold">${message.name}</div>
                            <small class="text-muted">${message.email}</small>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold text-muted">Subject:</label>
                    <div>${message.subject}</div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold text-muted">Message:</label>
                    <div class="p-3 bg-light rounded" style="white-space: pre-wrap;">${message.message}</div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold text-muted">Date:</label>
                    <div><i class="fas fa-clock me-2"></i>${formattedDate}</div>
                </div>
                
                ${!message.is_read ? `
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-2"></i> This message will be marked as read.
                    </div>
                ` : ''}
            `;
            
            // Mark as read if unread
            if (!message.is_read) {
                fetch('messages/mark-read.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'id=' + id
                });
            }
        } else {
            contentDiv.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i> ${result.message}
                </div>
            `;
        }
    } catch (error) {
        contentDiv.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i> An error occurred while loading the message.
            </div>
        `;
    }
}

// Initialize Pie Charts
document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTable for Messages
    <?php if (!empty($recentMessages)): ?>
  
    <?php endif; ?>
    
    // Common chart options
    const chartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 15,
                    font: {
                        size: 11
                    }
                }
            }
        }
    };

    // Main Statistics Pie Chart - Unified View
    const mainStatsCtx = document.getElementById('mainStatsChart');
    if (mainStatsCtx) {
        new Chart(mainStatsCtx, {
            type: 'pie',
            data: {
                labels: ['Skills', 'Projects', 'Education', 'Experience', 'Messages'],
                datasets: [{
                    data: [
                        <?php echo $stats['total_skills']; ?>,
                        <?php echo $stats['total_projects']; ?>,
                        <?php echo $stats['total_education']; ?>,
                        <?php echo $stats['total_experience']; ?>,
                        <?php echo $stats['total_messages']; ?>
                    ],
                    backgroundColor: [
                        '#667eea', // Skills - Purple
                        '#10B981', // Projects - Green
                        '#F59E0B', // Education - Orange
                        '#8B5CF6', // Experience - Violet
                        '#EF4444'  // Messages - Red
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            font: {
                                size: 13,
                                weight: '500'
                            },
                            usePointStyle: true,
                            pointStyle: 'circle',
                            generateLabels: function(chart) {
                                const data = chart.data;
                                if (data.labels.length && data.datasets.length) {
                                    return data.labels.map((label, i) => {
                                        const value = data.datasets[0].data[i];
                                        return {
                                            text: label + ': ' + value,
                                            fillStyle: data.datasets[0].backgroundColor[i],
                                            hidden: false,
                                            index: i
                                        };
                                    });
                                }
                                return [];
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13
                        },
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return label + ': ' + value + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>

<?php include 'partials/footer.php'; ?>
