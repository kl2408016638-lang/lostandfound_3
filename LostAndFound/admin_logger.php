<?php
// admin_logger.php
// JANGAN include db_connect.php di sini sebab sudah diinclude oleh parent file
// include 'db_connect.php'; // REMOVE THIS LINE

// Dalam admin_logger.php, update logAdminAction function:
function logAdminAction($connect, $admin_id, $admin_name, $action, $target_type = null, $target_id = null, $target_name = null, $description = '') {
    // Get IP address
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    
    // Sanitize inputs untuk avoid NULL issues
    $admin_id = (int)$admin_id;
    $admin_name = $admin_name ?? 'Unknown Admin';
    $target_id = $target_id ? (int)$target_id : null;
    $target_name = $target_name ?? null;
    
    // Prepare the query
    $sql = "INSERT INTO admin_logs (admin_id, admin_name, action, target_type, target_id, target_name, description, ip_address) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($connect, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "isssiiss", 
            $admin_id, 
            $admin_name, 
            $action, 
            $target_type, 
            $target_id, 
            $target_name, 
            $description, 
            $ip_address
        );
        
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return true;
    }
    
    return false;
}


// Helper function untuk get human readable action names
function getActionLabel($action) {
    $labels = [
        'login' => 'Login',
        'logout' => 'Logout',
        'edit_user' => 'Edit User',
        'delete_user' => 'Delete User',
        'update_status' => 'Update Item Status',
        'edit_item' => 'Edit Item',
        'delete_item' => 'Delete Item',
        'add_item' => 'Add New Item'
    ];
    
    return $labels[$action] ?? ucfirst(str_replace('_', ' ', $action));
}

// Helper function untuk get action badge color
function getActionBadgeColor($action) {
    $colors = [
        'login' => 'success',      // Green
        'logout' => 'secondary',   // Gray
        'edit_user' => 'primary',  // Blue
        'delete_user' => 'danger', // Red
        'update_status' => 'info', // Light blue
        'edit_item' => 'warning',  // Orange/Yellow
        'delete_item' => 'danger', // Red
        'add_item' => 'success'    // Green
    ];
    
    return $colors[$action] ?? 'secondary';
}
?>