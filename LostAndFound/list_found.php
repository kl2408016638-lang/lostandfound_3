<?php
session_start();
include 'db_connect.php';

// Check jika user logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'] ?? 0;
$user_role = $_SESSION['role'] ?? 'user';
$user_name = $_SESSION['name'] ?? 'Unknown';
$message = "";

// Handle status update (FOR ADMIN ONLY)

// Dalam update_status section (sekitar line 40-60), UPDATE jadi:

if($user_role == 'admin' && isset($_POST['update_status'])) {
    $item_id = $_POST['item_id'];
    $new_status = $_POST['status'];
    
    // GET OLD STATUS FIRST untuk logging
    $get_old_sql = "SELECT status, type_item FROM found_items WHERE id = '$item_id'";
    $old_result = mysqli_query($connect, $get_old_sql);
    $old_data = mysqli_fetch_assoc($old_result);
    $old_status = $old_data['status'] ?? 'unknown';
    $item_name = $old_data['type_item'] ?? 'Unknown Item';
    
    // Then do the update
    $update_sql = "UPDATE found_items SET status='$new_status', updated_at=NOW() WHERE id='$item_id'";
    
    if(mysqli_query($connect, $update_sql)) {
        $message = "Item status updated successfully!";
        
        // LOGGING dengan OLD STATUS
        include 'admin_logger.php';
        logAdminAction($connect, $user_id, $user_name, 'update_status', 'found_item', $item_id, $item_name, "Updated item #{$item_id} status from {$old_status} to {$new_status}");
    } else {
        $message = "Error: " . mysqli_error($connect);
    }
}


// Search functionality
$search = "";
if(isset($_GET['search'])) {
    $search = mysqli_real_escape_string($connect, $_GET['search']);
}

// Query to get FOUND items (not lost items)
if(!empty($search)) {
    $sql = "SELECT * FROM found_items WHERE type_item LIKE '%$search%' ORDER BY created_at DESC";
} else {
    $sql = "SELECT * FROM found_items ORDER BY created_at DESC";
}

$result = mysqli_query($connect, $sql);
$total_items = mysqli_num_rows($result);

// INCLUDE NAVBAR BERDASARKAN ROLE
if($user_role == 'admin') {
    include 'admin_sidebar_nav.php';
} else {
    include 'sidebar_nav.php';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Found Items - Surau Ismail Kharofa</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            margin: 0;
            padding: 20px;
            min-height: 100vh;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 20px;
            transition: all 0.3s;
        }
        
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Main Content for Found Items -->
        <div style="background: white; border-radius: 15px; padding: 30px; box-shadow: 0 5px 20px rgba(0,0,0,0.05);">
            
            <!-- Page Header -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <div>
                    <h1 style="color: #2c3e50; margin-bottom: 10px; font-size: 28px;">
                        <i class="fas fa-hand-holding-heart" style="color: #27ae60; margin-right: 10px;"></i>
                        Found Items List
                    </h1>
                    <p style="color: #7f8c8d;">
                        <?php echo $user_role == 'admin' ? 'Manage and update found items status' : 'Browse and search for found items'; ?>
                    </p>
                </div>
                
                <div style="background: #e8f8ef; color: #27ae60; padding: 10px 20px; border-radius: 10px; font-weight: 600;">
                    <i class="fas fa-box" style="margin-right: 8px;"></i>
                    Total Found Items: <?php echo $total_items; ?>
                </div>
            </div>
            
            <?php if($message != ""): ?>
                <div style="padding: 15px; border-radius: 10px; margin-bottom: 25px; text-align: center; 
                            background: <?php echo strpos($message, 'Error') !== false ? '#f8d7da' : '#d4edda'; ?>;
                            color: <?php echo strpos($message, 'Error') !== false ? '#721c24' : '#155724'; ?>;
                            border: 1px solid <?php echo strpos($message, 'Error') !== false ? '#f5c6cb' : '#c3e6cb'; ?>;">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <!-- Search Form -->
            <form method="GET" action="" style="margin-bottom: 30px; display: flex; gap: 10px;">
                <div style="flex: 1; position: relative;">
                    <i class="fas fa-search" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #7f8c8d;"></i>
                    <input type="text" name="search" placeholder="Search by item type (e.g., wallet, phone, keys)" 
                           value="<?php echo htmlspecialchars($search); ?>"
                           style="width: 100%; padding: 12px 15px 12px 45px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 16px; transition: all 0.3s;">
                </div>
                <button type="submit" style="background: #27ae60; color: white; padding: 12px 25px; border: none; 
                                            border-radius: 8px; font-size: 16px; cursor: pointer; font-weight: 600; transition: all 0.3s;">
                    <i class="fas fa-search" style="margin-right: 8px;"></i> Search
                </button>
                <?php if(!empty($search)): ?>
                    <a href="list_found.php" style="padding: 12px 20px; background: #6c757d; color: white; 
                                                    border-radius: 8px; text-decoration: none; display: flex; 
                                                    align-items: center; gap: 8px;">
                        <i class="fas fa-times"></i> Clear
                    </a>
                <?php endif; ?>
            </form>
            
            <?php if(!empty($search)): ?>
                <div style="background: #e7f3fe; padding: 15px; border-radius: 8px; margin-bottom: 20px; 
                            border-left: 4px solid #2196F3; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        Search results for: <strong style="color: #2196F3;"><?php echo htmlspecialchars($search); ?></strong>
                        | Found: <strong><?php echo $total_items; ?></strong> items
                    </div>
                    <?php if($user_role == 'admin'): ?>
                        <button onclick="window.print()" style="background: #6c757d; color: white; padding: 8px 15px; 
                                                                border: none; border-radius: 5px; cursor: pointer; font-size: 14px;">
                            <i class="fas fa-print" style="margin-right: 5px;"></i> Print
                        </button>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <!-- Found Items Table -->
            <?php if(mysqli_num_rows($result) > 0): ?>
                <div style="overflow-x: auto; border-radius: 10px; border: 1px solid #eee;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f8f9fa;">
                                <th style="padding: 15px; text-align: left; color: #2c3e50; font-weight: 700; border-bottom: 2px solid #ddd;">Type</th>
                                <th style="padding: 15px; text-align: left; color: #2c3e50; font-weight: 700; border-bottom: 2px solid #ddd;">Date Found</th>
                                <th style="padding: 15px; text-align: left; color: #2c3e50; font-weight: 700; border-bottom: 2px solid #ddd;">Location</th>
                                <th style="padding: 15px; text-align: left; color: #2c3e50; font-weight: 700; border-bottom: 2px solid #ddd;">Picture</th>
                                <th style="padding: 15px; text-align: left; color: #2c3e50; font-weight: 700; border-bottom: 2px solid #ddd;">Description</th>
                                <th style="padding: 15px; text-align: left; color: #2c3e50; font-weight: 700; border-bottom: 2px solid #ddd;">Found By</th>
                                <th style="padding: 15px; text-align: left; color: #2c3e50; font-weight: 700; border-bottom: 2px solid #ddd;">Status</th>
                                <?php if($user_role == 'admin'): ?>
                                    <th style="padding: 15px; text-align: left; color: #2c3e50; font-weight: 700; border-bottom: 2px solid #ddd;">Change Status</th>
                                    <th style="padding: 15px; text-align: left; color: #2c3e50; font-weight: 700; border-bottom: 2px solid #ddd;">Actions</th>
                                <?php endif; ?>
                                <th style="padding: 15px; text-align: left; color: #2c3e50; font-weight: 700; border-bottom: 2px solid #ddd;">Reported On</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($item = mysqli_fetch_assoc($result)): ?>
                            <tr style="border-bottom: 1px solid #eee; transition: all 0.3s;" onmouseover="this.style.backgroundColor='#f9f9f9'" onmouseout="this.style.backgroundColor='white'">
                                <td style="padding: 15px; color: #333;">
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <span style="width: 12px; height: 12px; border-radius: 50%; 
                                                    background: <?php 
                                                        $colors = ['pending' => '#ffc107', 'approved' => '#007bff', 'matched' => '#17a2b8', 'claimed' => '#28a745', 'closed' => '#6c757d'];
                                                        echo $colors[$item['status']] ?? '#6c757d';
                                                    ?>;"></span>
                                        <?php echo htmlspecialchars(ucfirst($item['type_item'])); ?>
                                    </div>
                                </td>
                                <td style="padding: 15px; color: #333;">
                                    <?php echo htmlspecialchars($item['date']); ?><br>
                                    <small style="color: #7f8c8d;"><?php echo htmlspecialchars($item['time']); ?></small>
                                </td>
                                <td style="padding: 15px; color: #333;">
                                    <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $item['location']))); ?>
                                </td>
                                <td style="padding: 15px;">
                                    <?php if(!empty($item['picture']) && file_exists('uploads/' . $item['picture'])): ?>
                                        <img src="uploads/<?php echo htmlspecialchars($item['picture']); ?>" 
                                             alt="Item Picture" 
                                             style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px; border: 2px solid #eee; cursor: pointer;"
                                             onclick="openImage('uploads/<?php echo htmlspecialchars($item['picture']); ?>')">
                                    <?php else: ?>
                                        <div style="width: 80px; height: 80px; background: #f8f9fa; border-radius: 8px; 
                                                    display: flex; align-items: center; justify-content: center; 
                                                    color: #6c757d; border: 2px dashed #ddd;">
                                            <i class="fas fa-image" style="font-size: 20px;"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 15px; color: #333; max-width: 250px;">
                                    <?php echo htmlspecialchars(substr($item['description'], 0, 100)); ?>
                                    <?php if(strlen($item['description']) > 100): ?>...<?php endif; ?>
                                </td>
                                <td style="padding: 15px; color: #333;">
                                    <?php echo htmlspecialchars($item['user_name']); ?>
                                    <?php if($user_role == 'admin'): ?>
                                        <br><small style="color: #7f8c8d;">ID: <?php echo $item['user_id']; ?></small>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 15px;">
                                    <span style="padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; 
                                                background: <?php 
                                                    $bg_colors = ['pending' => '#fff3cd', 'approved' => '#d1ecf1', 'matched' => '#d1ecf1', 'claimed' => '#d4edda', 'closed' => '#e2e3e5'];
                                                    echo $bg_colors[$item['status']] ?? '#e2e3e5';
                                                ?>; 
                                                color: <?php 
                                                    $text_colors = ['pending' => '#856404', 'approved' => '#007bff', 'matched' => '#0c5460', 'claimed' => '#155724', 'closed' => '#383d41'];
                                                    echo $text_colors[$item['status']] ?? '#383d41';
                                                ?>;">
                                        <?php echo ucfirst($item['status']); ?>
                                    </span>
                                </td>
                                
                                <?php if($user_role == 'admin'): ?>
                                    <!-- ADMIN: Change Status Form -->
                                    <td style="padding: 15px;">
                                        <form method="POST" action="" style="display: flex; gap: 5px; align-items: center;">
                                            <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                            <select name="status" style="padding: 8px; border: 1px solid #ddd; border-radius: 5px; 
                                                                        font-size: 14px; background: white; min-width: 100px;" required>
                                                <option value="pending" <?php echo $item['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                <option value="approved" <?php echo $item['status'] == 'approved' ? 'selected' : ''; ?>>Approved</option>
                                                <option value="matched" <?php echo $item['status'] == 'matched' ? 'selected' : ''; ?>>Matched</option>
                                                <option value="claimed" <?php echo $item['status'] == 'claimed' ? 'selected' : ''; ?>>Claimed</option>
                                                <option value="closed" <?php echo $item['status'] == 'closed' ? 'selected' : ''; ?>>Closed</option>
                                            </select>
                                            <button type="submit" name="update_status" 
                                                    style="background: #28a745; color: white; padding: 8px 12px; border: none; 
                                                            border-radius: 5px; cursor: pointer; font-size: 12px; transition: all 0.3s;">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    </td>
                                    <td style="padding: 15px;">
                                        <div style="display: flex; gap: 5px;">
                                            <a href="view_found_item.php?id=<?php echo $item['id']; ?>" 
                                               style="padding: 6px 12px; background: #17a2b8; color: white; border-radius: 5px; 
                                                      text-decoration: none; font-size: 12px; display: flex; align-items: center; gap: 5px;">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <a href="edit_found_item.php?id=<?php echo $item['id']; ?>" 
                                               style="padding: 6px 12px; background: #ffc107; color: black; border-radius: 5px; 
                                                      text-decoration: none; font-size: 12px; display: flex; align-items: center; gap: 5px;">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                        </div>
                                    </td>
                                <?php endif; ?>
                                
                                <td style="padding: 15px; color: #666; font-size: 14px;">
                                    <?php echo date('d/m/Y H:i', strtotime($item['created_at'])); ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 60px 20px; color: #666; font-size: 18px; background: #f8f9fa; border-radius: 10px;">
                    <i class="fas fa-hand-holding-heart" style="font-size: 48px; color: #bdc3c7; margin-bottom: 20px;"></i>
                    <h3 style="color: #7f8c8d; margin-bottom: 10px;">
                        <?php if(!empty($search)): ?>
                            No found items matching "<?php echo htmlspecialchars($search); ?>"
                        <?php else: ?>
                            No found items have been reported yet.
                        <?php endif; ?>
                    </h3>
                    <p style="color: #95a5a6; font-size: 14px;">
                        <?php if($user_role == 'admin'): ?>
                            Check back later or encourage users to report found items.
                        <?php else: ?>
                            <a href="form_found.php" style="color: #27ae60; text-decoration: none; font-weight: 600;">
                                <i class="fas fa-plus-circle"></i> Report a Found Item
                            </a>
                        <?php endif; ?>
                    </p>
                </div>
            <?php endif; ?>
            
            <!-- Additional Info -->
            <div style="margin-top: 40px; padding: 20px; background: #e8f8ef; 
                        border-radius: 10px; border-left: 4px solid #27ae60;">
                <h4 style="color: #27ae60; margin-bottom: 10px; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-info-circle"></i>
                    <?php echo $user_role == 'admin' ? 'Admin Information' : 'User Information'; ?>
                </h4>
                <p style="color: #666; line-height: 1.6; font-size: 14px;">
                    <?php if($user_role == 'admin'): ?>
                        • As an administrator, you can update found item status and manage all reports.<br>
                        • Use the search function to quickly find specific items.<br>
                        • Click "View" to see complete details or "Edit" to modify item information.
                    <?php else: ?>
                        • Browse through all found items in the surau.<br>
                        • Use the search bar to find specific types of items.<br>
                        • If you lost an item, check if it has been found and reported here.<br>
                        • To report a found item, use the "Form Found Item" in the sidebar.
                    <?php endif; ?>
                </p>
            </div>
            
        </div>
        
    </div>
    
    <script>
        // Function to open image in new tab
        function openImage(src) {
            window.open(src, '_blank');
        }
        
        // Auto-refresh page for admin every 30 seconds to see updates
        <?php if($user_role == 'admin'): ?>
        setTimeout(function() {
            if(!document.hidden) {
                location.reload();
            }
        }, 30000); // 30 seconds
        <?php endif; ?>
    </script>
</body>
</html>