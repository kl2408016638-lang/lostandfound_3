<?php
session_start();
include 'db_connect.php';

// Check jika user logged in dan dia admin
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

$admin_id = $_SESSION['user_id'];
$admin_name = $_SESSION['name'];

// Handle message actions
$action_message = "";

// Mark as read
if(isset($_GET['mark_read']) && is_numeric($_GET['mark_read'])) {
    $message_id = $_GET['mark_read'];
    $sql = "UPDATE user_messages SET status = 'read' WHERE id = '$message_id'";
    if(mysqli_query($connect, $sql)) {
        $action_message = "<div class='alert success'>Message marked as read!</div>";
    }
}

// Mark as replied
if(isset($_GET['mark_replied']) && is_numeric($_GET['mark_replied'])) {
    $message_id = $_GET['mark_replied'];
    $sql = "UPDATE user_messages SET status = 'replied' WHERE id = '$message_id'";
    if(mysqli_query($connect, $sql)) {
        $action_message = "<div class='alert success'>Message marked as replied!</div>";
    }
}

// Delete message
if(isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $message_id = $_GET['delete'];
    $sql = "DELETE FROM user_messages WHERE id = '$message_id'";
    if(mysqli_query($connect, $sql)) {
        $action_message = "<div class='alert success'>Message deleted successfully!</div>";
    }
}

// Update admin notes
if(isset($_POST['update_notes'])) {
    $message_id = $_POST['message_id'];
    $admin_notes = mysqli_real_escape_string($connect, $_POST['admin_notes']);
    
    $sql = "UPDATE user_messages SET admin_notes = '$admin_notes', updated_at = NOW() WHERE id = '$message_id'";
    if(mysqli_query($connect, $sql)) {
        $action_message = "<div class='alert success'>Notes updated successfully!</div>";
    }
}

// Filter parameters
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$search = isset($_GET['search']) ? mysqli_real_escape_string($connect, $_GET['search']) : '';

// Build query
$sql = "SELECT * FROM user_messages WHERE 1=1";

// Apply status filter
if($status_filter != 'all') {
    $sql .= " AND status = '$status_filter'";
}

// Apply search filter
if(!empty($search)) {
    $sql .= " AND (subject LIKE '%$search%' OR message LIKE '%$search%' OR sender_name LIKE '%$search%')";
}

// Order by latest first
$sql .= " ORDER BY 
    CASE status 
        WHEN 'unread' THEN 1
        WHEN 'read' THEN 2
        WHEN 'replied' THEN 3
        ELSE 4
    END,
    created_at DESC";

$result = mysqli_query($connect, $sql);
$total_messages = mysqli_num_rows($result);

// Count messages by status
$count_unread_sql = "SELECT COUNT(*) as count FROM user_messages WHERE status = 'unread'";
$count_read_sql = "SELECT COUNT(*) as count FROM user_messages WHERE status = 'read'";
$count_replied_sql = "SELECT COUNT(*) as count FROM user_messages WHERE status = 'replied'";

$unread_result = mysqli_query($connect, $count_unread_sql);
$read_result = mysqli_query($connect, $count_read_sql);
$replied_result = mysqli_query($connect, $count_replied_sql);

$unread_count = mysqli_fetch_assoc($unread_result)['count'];
$read_count = mysqli_fetch_assoc($read_result)['count'];
$replied_count = mysqli_fetch_assoc($replied_result)['count'];
$total_count = $unread_count + $read_count + $replied_count;

// Include admin sidebar
if(file_exists('admin_sidebar_nav.php')) {
    include 'admin_sidebar_nav.php';
} else {
    echo '<div style="padding:20px;background:#f8d7da;color:#721c24;">Admin sidebar not found.</div>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - User Messages</title>
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
            max-width: 1400px;
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
        
        .dashboard-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 25px;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .page-title {
            color: #2c3e50;
            font-size: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .page-title i {
            color: #3498db;
        }
        
        /* Stats Overview */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }
        
        @media (max-width: 1024px) {
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
            cursor: pointer;
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
        
        .stat-card.active {
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        }
        
        .stat-number {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: #7f8c8d;
            font-size: 14px;
            font-weight: 600;
        }
        
        /* Filters Section */
        .filters-container {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
            flex-wrap: wrap;
            align-items: flex-end;
        }
        
        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        .filter-label {
            color: #34495e;
            font-weight: 600;
            font-size: 14px;
        }
        
        .filter-select, .search-input {
            padding: 8px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            min-width: 150px;
        }
        
        .search-input {
            min-width: 250px;
            padding-left: 40px;
        }
        
        .search-container {
            position: relative;
        }
        
        .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #95a5a6;
        }
        
        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-primary {
            background: #3498db;
            color: white;
        }
        
        .btn-primary:hover {
            background: #2980b9;
        }
        
        .btn-success {
            background: #27ae60;
            color: white;
        }
        
        .btn-success:hover {
            background: #219653;
        }
        
        .btn-danger {
            background: #e74c3c;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c0392b;
        }
        
        .btn-warning {
            background: #f39c12;
            color: white;
        }
        
        .btn-warning:hover {
            background: #d35400;
        }
        
        .btn-secondary {
            background: #95a5a6;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #7f8c8d;
        }
        
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 25px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .alert.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        /* Messages Table */
        .messages-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        th {
            background: #f8f9fa;
            color: #2c3e50;
            font-weight: 700;
            padding: 15px;
            text-align: left;
            border-bottom: 2px solid #eee;
        }
        
        td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            vertical-align: top;
        }
        
        tr:hover {
            background: #f9f9f9;
        }
        
        tr.unread {
            background: #fef9e7;
        }
        
        tr.unread:hover {
            background: #fcf3cf;
        }
        
        tr.replied {
            background: #e8f8ef;
        }
        
        tr.replied:hover {
            background: #d4edda;
        }
        
        /* Status badges */
        .status-badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }
        
        .status-unread { background: #f8d7da; color: #721c24; }
        .status-read { background: #fff3cd; color: #856404; }
        .status-replied { background: #d4edda; color: #155724; }
        
        /* Message content */
        .message-subject {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .message-preview {
            color: #7f8c8d;
            font-size: 13px;
            line-height: 1.4;
            max-height: 60px;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
        }
        
        .sender-info {
            font-size: 13px;
            color: #3498db;
        }
        
        .timestamp {
            font-size: 12px;
            color: #95a5a6;
            white-space: nowrap;
        }
        
        .actions-cell {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
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
            max-width: 800px;
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
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .detail-label {
            font-weight: 600;
            color: #34495e;
            margin-bottom: 5px;
            font-size: 14px;
        }
        
        .detail-value {
            color: #5d6d7e;
            line-height: 1.6;
            white-space: pre-wrap;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            color: #34495e;
            font-weight: 600;
            font-size: 14px;
        }
        
        .form-textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            min-height: 100px;
            resize: vertical;
        }
        
        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 25px;
        }
        
        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 50px 20px;
            color: #7f8c8d;
        }
        
        .empty-state i {
            font-size: 48px;
            margin-bottom: 20px;
            color: #bdc3c7;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .filters-container {
                flex-direction: column;
                align-items: stretch;
            }
            
            .filter-group {
                width: 100%;
            }
            
            .filter-select, .search-input {
                width: 100%;
            }
            
            .messages-table {
                display: block;
                overflow-x: auto;
            }
            
            .actions-cell {
                flex-direction: column;
                gap: 5px;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
                font-size: 12px;
                padding: 6px 10px;
            }
            
            .timestamp {
                white-space: normal;
                font-size: 11px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="main-content">
            <!-- Dashboard Card -->
            <div class="dashboard-card">
                <!-- Page Header -->
                <div class="page-header">
                    <h1 class="page-title">
                        <i class="fas fa-envelope"></i>
                        User Messages
                    </h1>
                    <div>
                        Logged in as: <strong><?php echo htmlspecialchars($admin_name); ?></strong>
                    </div>
                </div>
                
                <!-- Stats Overview -->
                <div class="stats-container">
                    <a href="?status=all" class="stat-card total <?php echo $status_filter == 'all' ? 'active' : ''; ?>">
                        <div class="stat-number"><?php echo $total_count; ?></div>
                        <div class="stat-label">Total Messages</div>
                    </a>
                    
                    <a href="?status=unread" class="stat-card unread <?php echo $status_filter == 'unread' ? 'active' : ''; ?>">
                        <div class="stat-number"><?php echo $unread_count; ?></div>
                        <div class="stat-label">Unread Messages</div>
                    </a>
                    
                    <a href="?status=read" class="stat-card read <?php echo $status_filter == 'read' ? 'active' : ''; ?>">
                        <div class="stat-number"><?php echo $read_count; ?></div>
                        <div class="stat-label">Read Messages</div>
                    </a>
                    
                    <a href="?status=replied" class="stat-card replied <?php echo $status_filter == 'replied' ? 'active' : ''; ?>">
                        <div class="stat-number"><?php echo $replied_count; ?></div>
                        <div class="stat-label">Replied Messages</div>
                    </a>
                </div>
                
                <!-- Action Messages -->
                <?php if($action_message != ""): ?>
                    <?php echo $action_message; ?>
                <?php endif; ?>
                
                <!-- Filters Section -->
                <form method="GET" action="" class="filters-container">
                    <div class="filter-group">
                        <span class="filter-label">Search:</span>
                        <div class="search-container">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" name="search" class="search-input" 
                                   placeholder="Search by sender, subject, or message" 
                                   value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                    </div>
                    
                    <div class="filter-group">
                        <span class="filter-label">Status:</span>
                        <select name="status" class="filter-select" onchange="this.form.submit()">
                            <option value="all" <?php echo $status_filter == 'all' ? 'selected' : ''; ?>>All Messages</option>
                            <option value="unread" <?php echo $status_filter == 'unread' ? 'selected' : ''; ?>>Unread Only</option>
                            <option value="read" <?php echo $status_filter == 'read' ? 'selected' : ''; ?>>Read Only</option>
                            <option value="replied" <?php echo $status_filter == 'replied' ? 'selected' : ''; ?>>Replied Only</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> Apply Filters
                        </button>
                    </div>
                    
                    <?php if(!empty($search) || $status_filter != 'all'): ?>
                    <div class="filter-group">
                        <a href="admin_messages.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Clear Filters
                        </a>
                    </div>
                    <?php endif; ?>
                </form>
                
                <!-- Messages Table -->
                <?php if($total_messages > 0): ?>
                    <div style="overflow-x: auto;">
                        <table class="messages-table">
                            <thead>
                                <tr>
                                    <th>From</th>
                                    <th>Subject</th>
                                    <th>Message</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($msg = mysqli_fetch_assoc($result)): 
                                    $is_unread = $msg['status'] == 'unread';
                                    $is_replied = $msg['status'] == 'replied';
                                ?>
                                <tr class="<?php echo $msg['status']; ?>">
                                    <td>
                                        <div class="sender-info">
                                            <?php echo htmlspecialchars($msg['sender_name']); ?>
                                        </div>
                                        <div class="timestamp">
                                            ID: <?php echo $msg['sender_id']; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="message-subject">
                                            <?php echo htmlspecialchars($msg['subject']); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="message-preview">
                                            <?php echo htmlspecialchars(substr($msg['message'], 0, 150)); ?>
                                            <?php if(strlen($msg['message']) > 150): ?>...<?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="timestamp">
                                            <?php echo date('d/m/Y', strtotime($msg['created_at'])); ?><br>
                                            <?php echo date('H:i', strtotime($msg['created_at'])); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?php echo $msg['status']; ?>">
                                            <?php echo ucfirst($msg['status']); ?>
                                        </span>
                                    </td>
                                    <td class="actions-cell">
                                        <button class="btn btn-primary" 
                                                onclick="viewMessage(<?php echo htmlspecialchars(json_encode($msg)); ?>)">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                        
                                        <?php if($is_unread): ?>
                                        <a href="?mark_read=<?php echo $msg['id']; ?>" 
                                           class="btn btn-success" 
                                           title="Mark as Read">
                                            <i class="fas fa-check"></i> Read
                                        </a>
                                        <?php elseif(!$is_replied): ?>
                                        <a href="?mark_replied=<?php echo $msg['id']; ?>" 
                                           class="btn btn-warning" 
                                           title="Mark as Replied">
                                            <i class="fas fa-reply"></i> Replied
                                        </a>
                                        <?php endif; ?>
                                        
                                        <a href="?delete=<?php echo $msg['id']; ?>" 
                                           class="btn btn-danger" 
                                           onclick="return confirm('Are you sure you want to delete this message?')"
                                           title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Summary -->
                    <div style="margin-top: 25px; padding: 15px; background: #f8f9fa; border-radius: 5px; font-size: 14px; color: #7f8c8d;">
                        <i class="fas fa-info-circle"></i>
                        Showing <?php echo $total_messages; ?> message(s)
                        <?php if(!empty($search)): ?> matching "<?php echo htmlspecialchars($search); ?>"<?php endif; ?>
                        <?php if($status_filter != 'all'): ?> with status "<?php echo $status_filter; ?>"<?php endif; ?>
                    </div>
                    
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-envelope-open"></i>
                        <h3>No messages found</h3>
                        <p>
                            <?php if(!empty($search) || $status_filter != 'all'): ?>
                                Try changing your search or filter criteria.
                            <?php else: ?>
                                No user messages have been received yet.
                            <?php endif; ?>
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- View Message Modal -->
    <div id="messageModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Message Details</h2>
                <button class="close-modal" onclick="closeModal()">&times;</button>
            </div>
            
            <div class="message-details" id="messageDetails">
                <!-- Dynamic content will be inserted here -->
            </div>
            
            <form id="notesForm" method="POST" action="">
                <input type="hidden" name="message_id" id="notes_message_id">
                
                <div class="form-group">
                    <label class="form-label" for="admin_notes">Admin Notes</label>
                    <textarea id="admin_notes" name="admin_notes" class="form-textarea" 
                              placeholder="Add internal notes or reply details..."></textarea>
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Close</button>
                    <button type="submit" name="update_notes" class="btn btn-primary">Save Notes</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        // View message modal
        function viewMessage(message) {
            const modal = document.getElementById('messageModal');
            const details = document.getElementById('messageDetails');
            
            // Format created date
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
                        <div class="detail-label">Last Updated</div>
                        <div class="detail-value">${formattedUpdated}</div>
                    </div>
                `;
            }
            
            // Build details HTML
            details.innerHTML = `
                <div class="detail-row">
                    <div class="detail-label">From</div>
                    <div class="detail-value">
                        <strong>${escapeHtml(message.sender_name)}</strong> (User ID: ${message.sender_id})
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Subject</div>
                    <div class="detail-value"><strong>${escapeHtml(message.subject)}</strong></div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Message</div>
                    <div class="detail-value" style="white-space: pre-wrap;">${escapeHtml(message.message)}</div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Status</div>
                    <div class="detail-value">
                        <span class="status-badge status-${message.status}">
                            ${message.status.charAt(0).toUpperCase() + message.status.slice(1)}
                        </span>
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Received</div>
                    <div class="detail-value">${formattedDate}</div>
                </div>
                
                ${updatedDateHtml}
                
                ${message.admin_notes ? `
                <div class="detail-row">
                    <div class="detail-label">Admin Notes</div>
                    <div class="detail-value" style="white-space: pre-wrap; background: #f8f9fa; padding: 10px; border-radius: 5px;">
                        ${escapeHtml(message.admin_notes)}
                    </div>
                </div>
                ` : ''}
            `;
            
            // Set form values
            document.getElementById('notes_message_id').value = message.id;
            document.getElementById('admin_notes').value = message.admin_notes || '';
            
            // Show modal
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
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
        
        // Auto-submit filter on status change (already in select)
        
        // Auto-refresh page every 60 seconds for new messages
        setInterval(function() {
            if(!document.hidden) {
                const modal = document.getElementById('messageModal');
                if(modal.style.display !== 'flex') {
                    location.reload();
                }
            }
        }, 60000);
        
        // Quick action confirmation
        document.querySelectorAll('a.btn-danger').forEach(link => {
            link.addEventListener('click', function(e) {
                if(!confirm('Are you sure you want to delete this message?')) {
                    e.preventDefault();
                }
            });
        });
        
        // Mark message as read when viewing
        let currentMessageId = null;
        
        function markAsRead(messageId) {
            if(!messageId) return;
            
            fetch(`?mark_read=${messageId}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(response => {
                // Update UI if needed
                const statusBadge = document.querySelector(`tr[data-id="${messageId}"] .status-badge`);
                if(statusBadge) {
                    statusBadge.className = 'status-badge status-read';
                    statusBadge.textContent = 'Read';
                }
            });
        }
        
        // Update viewMessage function to mark as read
        const originalViewMessage = viewMessage;
        viewMessage = function(message) {
            originalViewMessage(message);
            if(message.status === 'unread') {
                markAsRead(message.id);
            }
        };
    </script>
</body>
</html>