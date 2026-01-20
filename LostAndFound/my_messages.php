<?php
session_start();
include 'db_connect.php';

// Check jika user logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['name'];
$user_role = $_SESSION['role'];

// Get user's messages
$sql = "SELECT * FROM user_messages 
        WHERE sender_id = '$user_id' 
        ORDER BY created_at DESC";
        
$result = mysqli_query($connect, $sql);
$total_messages = mysqli_num_rows($result);

// Count messages by status
$count_unread_sql = "SELECT COUNT(*) as count FROM user_messages 
                     WHERE sender_id = '$user_id' AND status = 'unread'";
$count_read_sql = "SELECT COUNT(*) as count FROM user_messages 
                   WHERE sender_id = '$user_id' AND status = 'read'";
$count_replied_sql = "SELECT COUNT(*) as count FROM user_messages 
                      WHERE sender_id = '$user_id' AND status = 'replied'";

$unread_result = mysqli_query($connect, $count_unread_sql);
$read_result = mysqli_query($connect, $count_read_sql);
$replied_result = mysqli_query($connect, $count_replied_sql);

$unread_count = mysqli_fetch_assoc($unread_result)['count'];
$read_count = mysqli_fetch_assoc($read_result)['count'];
$replied_count = mysqli_fetch_assoc($replied_result)['count'];

// Include user sidebar
if(file_exists('sidebar_nav.php')) {
    include 'sidebar_nav.php';
} else {
    echo '<div style="padding:20px;background:#f8d7da;color:#721c24;">Sidebar not found.</div>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Messages</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
            color: #333;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .main-content {
            margin-left: 250px;
            transition: all 0.3s;
        }
        
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }
        }
        
        .page-card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 25px;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .page-title {
            color: #2c3e50;
            font-size: 24px;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .page-title i {
            color: #3498db;
        }
        
        .btn {
            padding: 10px 25px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }
        
        .btn-primary {
            background: #3498db;
            color: white;
        }
        
        .btn-primary:hover {
            background: #2980b9;
        }
        
        /* Stats Overview */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }
        
        @media (max-width: 768px) {
            .stats-container {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 576px) {
            .stats-container {
                grid-template-columns: 1fr;
            }
        }
        
        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            border-left: 4px solid;
            transition: all 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .stat-card.total { border-left-color: #3498db; }
        .stat-card.unread { border-left-color: #e74c3c; }
        .stat-card.read { border-left-color: #f39c12; }
        .stat-card.replied { border-left-color: #27ae60; }
        
        .stat-number {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: #7f8c8d;
            font-size: 14px;
        }
        
        /* Messages List */
        .messages-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .message-item {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            border-left: 4px solid;
            transition: all 0.3s;
            cursor: pointer;
        }
        
        .message-item:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .message-item.unread { border-left-color: #e74c3c; background: #fef9e7; }
        .message-item.read { border-left-color: #f39c12; }
        .message-item.replied { border-left-color: #27ae60; background: #e8f8ef; }
        
        .message-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }
        
        .message-subject {
            font-size: 18px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .message-status {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }
        
        .status-unread { background: #f8d7da; color: #721c24; }
        .status-read { background: #fff3cd; color: #856404; }
        .status-replied { background: #d4edda; color: #155724; }
        
        .timestamp {
            font-size: 13px;
            color: #95a5a6;
            white-space: nowrap;
        }
        
        .message-content {
            color: #5d6d7e;
            line-height: 1.6;
            margin-bottom: 15px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .message-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
        
        .admin-notes {
            background: #f8f9fa;
            border-radius: 6px;
            padding: 15px;
            margin-top: 15px;
            border-left: 3px solid #3498db;
            display: none;
        }
        
        .admin-notes.show {
            display: block;
        }
        
        .notes-label {
            font-weight: 700;
            color: #3498db;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .notes-content {
            color: #5d6d7e;
            line-height: 1.6;
            white-space: pre-wrap;
        }
        
        .view-details {
            color: #3498db;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
        }
        
        .view-details:hover {
            text-decoration: underline;
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #7f8c8d;
            background: #f8f9fa;
            border-radius: 8px;
            border: 2px dashed #dee2e6;
        }
        
        .empty-state i {
            font-size: 64px;
            margin-bottom: 20px;
            color: #bdc3c7;
        }
        
        .empty-state h3 {
            color: #6c757d;
            margin-bottom: 10px;
        }
        
        .empty-state p {
            color: #95a5a6;
            margin-bottom: 25px;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.6;
        }
        
        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .modal-content {
            background: white;
            border-radius: 10px;
            width: 90%;
            max-width: 700px;
            max-height: 90vh;
            overflow-y: auto;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .modal-title {
            font-size: 20px;
            color: #2c3e50;
        }
        
        .close-modal {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #7f8c8d;
        }
        
        .message-details {
            margin-bottom: 25px;
        }
        
        .detail-row {
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        
        .detail-label {
            font-weight: 700;
            color: #34495e;
            margin-bottom: 8px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .detail-value {
            color: #5d6d7e;
            line-height: 1.6;
            white-space: pre-wrap;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .message-header {
                flex-direction: column;
                gap: 10px;
            }
            
            .message-status {
                align-self: flex-start;
            }
            
            .timestamp {
                align-self: flex-start;
            }
            
            .message-footer {
                flex-direction: column;
                gap: 10px;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="main-content">
            <div class="page-card">
                <!-- Page Header -->
                <div class="page-header">
                    <h1 class="page-title">
                        <i class="fas fa-envelope-open-text"></i>
                        My Messages
                    </h1>
                    <a href="contact_admin.php" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> New Message
                    </a>
                </div>
                
                <!-- Stats Overview -->
                <div class="stats-container">
                    <div class="stat-card total">
                        <div class="stat-number"><?php echo $total_messages; ?></div>
                        <div class="stat-label">Total Messages</div>
                    </div>
                    
                    <div class="stat-card unread">
                        <div class="stat-number"><?php echo $unread_count; ?></div>
                        <div class="stat-label">Waiting Response</div>
                    </div>
                    
                    <div class="stat-card read">
                        <div class="stat-number"><?php echo $read_count; ?></div>
                        <div class="stat-label">Viewed by Admin</div>
                    </div>
                    
                    <div class="stat-card replied">
                        <div class="stat-number"><?php echo $replied_count; ?></div>
                        <div class="stat-label">Admin Replied</div>
                    </div>
                </div>
                
                <!-- Messages List -->
                <?php if($total_messages > 0): ?>
                    <div class="messages-list">
                        <?php while($message = mysqli_fetch_assoc($result)): ?>
                        <div class="message-item <?php echo $message['status']; ?>" 
                             onclick="viewMessage(<?php echo htmlspecialchars(json_encode($message)); ?>)">
                            <div class="message-header">
                                <div>
                                    <div class="message-subject">
                                        <?php echo htmlspecialchars($message['subject']); ?>
                                    </div>
                                    <div class="timestamp">
                                        <i class="far fa-clock"></i>
                                        <?php echo date('d/m/Y H:i', strtotime($message['created_at'])); ?>
                                    </div>
                                </div>
                                
                                <div class="message-status">
                                    <span class="status-badge status-<?php echo $message['status']; ?>">
                                        <?php 
                                        $status_labels = [
                                            'unread' => 'Waiting Response',
                                            'read' => 'Viewed by Admin',
                                            'replied' => 'Admin Replied'
                                        ];
                                        echo $status_labels[$message['status']] ?? ucfirst($message['status']);
                                        ?>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="message-content">
                                <?php echo htmlspecialchars(substr($message['message'], 0, 200)); ?>
                                <?php if(strlen($message['message']) > 200): ?>...<?php endif; ?>
                            </div>
                            
                            <div class="message-footer">
                                <span class="view-details" onclick="event.stopPropagation(); viewMessage(<?php echo htmlspecialchars(json_encode($message)); ?>)">
                                    <i class="fas fa-eye"></i> View Details
                                </span>
                                
                                <?php if($message['admin_notes']): ?>
                                <span class="view-details" onclick="event.stopPropagation(); toggleNotes(this)">
                                    <i class="fas fa-comment-alt"></i> View Admin Notes
                                </span>
                                <?php endif; ?>
                            </div>
                            
                            <?php if($message['admin_notes']): ?>
                            <div class="admin-notes" id="notes-<?php echo $message['id']; ?>">
                                <div class="notes-label">
                                    <i class="fas fa-user-shield"></i>
                                    Admin Notes
                                </div>
                                <div class="notes-content">
                                    <?php echo htmlspecialchars($message['admin_notes']); ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endwhile; ?>
                    </div>
                    
                    <!-- Summary -->
                    <div style="margin-top: 25px; padding: 15px; background: #f8f9fa; border-radius: 5px; font-size: 14px; color: #7f8c8d;">
                        <i class="fas fa-info-circle"></i>
                        Showing <?php echo $total_messages; ?> message(s) sent to admin
                    </div>
                    
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-envelope-open"></i>
                        <h3>No messages yet</h3>
                        <p>
                            You haven't sent any messages to the admin yet. 
                            If you have questions or need assistance, click the button below to send your first message.
                        </p>
                        <a href="contact_admin.php" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Send Your First Message
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Message Details Modal -->
    <div id="messageModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Message Details</h2>
                <button class="close-modal" onclick="closeModal()">&times;</button>
            </div>
            
            <div class="message-details" id="messageDetails">
                <!-- Dynamic content will be inserted here -->
            </div>
        </div>
    </div>
    
    <script>
        // View message details
        function viewMessage(message) {
            const modal = document.getElementById('messageModal');
            const details = document.getElementById('messageDetails');
            
            // Format dates
            const createdDate = new Date(message.created_at);
            const formattedDate = createdDate.toLocaleDateString('en-GB', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
            
            // Format updated date if exists
            let updatedDateHtml = '';
            if(message.updated_at && message.updated_at !== message.created_at) {
                const updatedDate = new Date(message.updated_at);
                const formattedUpdated = updatedDate.toLocaleDateString('en-GB', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
                updatedDateHtml = `
                    <div class="detail-row">
                        <div class="detail-label">
                            <i class="fas fa-sync-alt"></i> Last Updated
                        </div>
                        <div class="detail-value">${formattedUpdated}</div>
                    </div>
                `;
            }
            
            // Status label
            const statusLabels = {
                'unread': 'Waiting Response',
                'read': 'Viewed by Admin', 
                'replied': 'Admin Replied'
            };
            
            // Build details HTML
            details.innerHTML = `
                <div class="detail-row">
                    <div class="detail-label">
                        <i class="fas fa-tag"></i> Subject
                    </div>
                    <div class="detail-value"><strong>${escapeHtml(message.subject)}</strong></div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">
                        <i class="fas fa-envelope"></i> Your Message
                    </div>
                    <div class="detail-value" style="white-space: pre-wrap;">${escapeHtml(message.message)}</div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">
                        <i class="fas fa-info-circle"></i> Status
                    </div>
                    <div class="detail-value">
                        <span class="status-badge status-${message.status}" style="font-size: 14px;">
                            ${statusLabels[message.status] || message.status}
                        </span>
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">
                        <i class="fas fa-calendar-alt"></i> Sent On
                    </div>
                    <div class="detail-value">${formattedDate}</div>
                </div>
                
                ${updatedDateHtml}
                
                ${message.admin_notes ? `
                <div class="detail-row">
                    <div class="detail-label">
                        <i class="fas fa-user-shield"></i> Admin Notes
                    </div>
                    <div class="detail-value" style="white-space: pre-wrap; background: #f8f9fa; padding: 15px; border-radius: 6px; border-left: 4px solid #3498db;">
                        ${escapeHtml(message.admin_notes)}
                    </div>
                </div>
                ` : `
                <div class="detail-row">
                    <div class="detail-label">
                        <i class="fas fa-info-circle"></i> Note
                    </div>
                    <div class="detail-value" style="color: #7f8c8d;">
                        <i class="fas fa-clock"></i> Admin will respond to your message within 24-48 hours.
                    </div>
                </div>
                `}
            `;
            
            // Show modal
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
        
        // Toggle admin notes
        function toggleNotes(button) {
            event.stopPropagation();
            const messageId = button.closest('.message-item').querySelector('.admin-notes').id.split('-')[1];
            const notesDiv = document.getElementById('notes-' + messageId);
            notesDiv.classList.toggle('show');
            
            if(notesDiv.classList.contains('show')) {
                button.innerHTML = '<i class="fas fa-chevron-up"></i> Hide Admin Notes';
            } else {
                button.innerHTML = '<i class="fas fa-comment-alt"></i> View Admin Notes';
            }
        }
        
        // Close modal
        function closeModal() {
            document.getElementById('messageModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('messageModal');
            if (event.target == modal) {
                closeModal();
            }
        }
        
        // Escape HTML to prevent XSS
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        // Auto-refresh page every 2 minutes for status updates
        setInterval(function() {
            if(!document.hidden) {
                const modal = document.getElementById('messageModal');
                if(modal.style.display !== 'flex') {
                    location.reload();
                }
            }
        }, 120000);
        
        // Highlight new messages
        document.addEventListener('DOMContentLoaded', function() {
            const unreadMessages = document.querySelectorAll('.message-item.unread');
            unreadMessages.forEach(msg => {
                msg.style.animation = 'pulse 2s infinite';
            });
            
            // Add CSS animation for pulse effect
            const style = document.createElement('style');
            style.textContent = `
                @keyframes pulse {
                    0% { box-shadow: 0 2px 8px rgba(231, 76, 60, 0.1); }
                    50% { box-shadow: 0 2px 15px rgba(231, 76, 60, 0.3); }
                    100% { box-shadow: 0 2px 8px rgba(231, 76, 60, 0.1); }
                }
            `;
            document.head.appendChild(style);
        });
    </script>
</body>
</html>