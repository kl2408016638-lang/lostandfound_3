<?php
// sidebar_nav.php
// Check jika session sudah start, kalau belum start
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check jika user logged in
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    // Redirect ke login jika bukan user
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Navigation</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            display: flex;
            min-height: 100vh;
            background-color: #f5f5f5;
        }
        
        /* SIDEBAR NAVIGATION */
        .sidebar {
            width: 250px;
            background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
            color: white;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            padding: 20px 0;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            overflow-y: auto;
        }
        
        /* TOP HEADER BAR */
        .top-header {
            position: fixed;
            top: 0;
            left: 250px;
            right: 0;
            height: 60px;
            background: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            z-index: 999;
        }
        
        .logo {
            text-align: center;
            padding: 20px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
        }
        
        .logo h2 {
            color: white;
            font-size: 22px;
            margin-bottom: 5px;
        }
        
        .logo p {
            color: #bdc3c7;
            font-size: 12px;
        }
        
        .nav-menu {
            padding: 0 15px;
        }
        
        .nav-title {
            color: #95a5a6;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 15px 15px 10px 15px;
            margin-top: 10px;
        }
        
        .nav-item {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: #ecf0f1;
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 5px;
            transition: all 0.3s ease;
        }
        
        .nav-item:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            transform: translateX(5px);
        }
        
        .nav-item.active {
            background: #3498db;
            color: white;
        }
        
        .nav-icon {
            margin-right: 12px;
            font-size: 18px;
            width: 24px;
            text-align: center;
        }
        
        .nav-text {
            font-size: 15px;
            font-weight: 500;
        }
        
        /* TOP HEADER STYLES */
        .page-title {
            font-size: 20px;
            color: #2c3e50;
            font-weight: 600;
        }
        
        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .notification-btn {
            position: relative;
            background: none;
            border: none;
            color: #7f8c8d;
            font-size: 20px;
            cursor: pointer;
            padding: 8px;
            border-radius: 50%;
            transition: all 0.3s;
        }
        
        .notification-btn:hover {
            background: #f8f9fa;
            color: #3498db;
        }
        
        .notification-badge {
            position: absolute;
            top: 0;
            right: 0;
            background: #e74c3c;
            color: white;
            font-size: 10px;
            padding: 2px 5px;
            border-radius: 10px;
            min-width: 16px;
            text-align: center;
        }
        
        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            padding: 5px 10px;
            border-radius: 20px;
            transition: all 0.3s;
        }
        
        .user-profile:hover {
            background: #f8f9fa;
        }
        
        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 16px;
        }
        
        .user-info {
            display: flex;
            flex-direction: column;
        }
        
        .user-name {
            font-weight: 600;
            color: #2c3e50;
            font-size: 14px;
        }
        
        .user-role {
            color: #7f8c8d;
            font-size: 12px;
        }
        
        .logout-btn {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .logout-btn:hover {
            background: #c0392b;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(231, 76, 60, 0.3);
        }
        
        /* MAIN CONTENT AREA */
        .main-content {
            flex: 1;
            margin-left: 250px;
            margin-top: 60px;
            padding: 30px;
            min-height: calc(100vh - 60px);
        }
        
        /* DROPDOWN MENU */
        .dropdown {
            position: relative;
        }
        
        .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border-radius: 8px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
            min-width: 200px;
            display: none;
            z-index: 1001;
        }
        
        .dropdown:hover .dropdown-menu {
            display: block;
        }
        
        .dropdown-item {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: #2c3e50;
            text-decoration: none;
            transition: all 0.3s;
            border-bottom: 1px solid #f8f9fa;
        }
        
        .dropdown-item:hover {
            background: #f8f9fa;
            color: #3498db;
        }
        
        .dropdown-item:last-child {
            border-bottom: none;
        }
        
        .dropdown-icon {
            margin-right: 10px;
            font-size: 16px;
        }
        
        /* NOTIFICATION PANEL */
        .notification-panel {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border-radius: 8px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
            width: 350px;
            max-height: 400px;
            overflow-y: auto;
            display: none;
            z-index: 1001;
        }
        
        .notification-header {
            padding: 15px;
            border-bottom: 1px solid #f8f9fa;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .notification-item {
            padding: 15px;
            border-bottom: 1px solid #f8f9fa;
            transition: all 0.3s;
        }
        
        .notification-item:hover {
            background: #f8f9fa;
        }
        
        .notification-item.unread {
            background: #e8f4fc;
        }
        
        .notification-title {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .notification-message {
            color: #7f8c8d;
            font-size: 14px;
            margin-bottom: 5px;
        }
        
        .notification-time {
            color: #95a5a6;
            font-size: 12px;
        }
        
        .notification-btn:hover .notification-panel {
            display: block;
        }
        
        /* RESPONSIVE */
        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
            }
            
            .sidebar .nav-text,
            .sidebar .logo h2,
            .sidebar .logo p,
            .sidebar .nav-title {
                display: none;
            }
            
            .sidebar .nav-item {
                justify-content: center;
                padding: 15px;
            }
            
            .sidebar .nav-icon {
                margin-right: 0;
                font-size: 20px;
            }
            
            .top-header {
                left: 70px;
            }
            
            .main-content {
                margin-left: 70px;
            }
            
            .user-info {
                display: none;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- SIDEBAR NAVIGATION -->
    <div class="sidebar">
        <div class="logo">
            <h2>Surau Ismail</h2>
            <p>Lost & Found System</p>
        </div>
        
        <div class="nav-menu">
            <div class="nav-title">Main Menu</div>
            
            
            <a href="user_profile.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'user_profile.php' ? 'active' : ''; ?>">
                <div class="nav-icon"><i class="fas fa-user"></i></div>
                <div class="nav-text">Profile</div>
            </a>
            
            <a href="list_found.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'list_found.php' ? 'active' : ''; ?>">
                <div class="nav-icon"><i class="fas fa-search"></i></div>
                <div class="nav-text">List Found Item</div>
            </a>
            
            <a href="form_found.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'form_found.php' ? 'active' : ''; ?>">
                <div class="nav-icon"><i class="fas fa-plus-circle"></i></div>
                <div class="nav-text">Form Found Item</div>
            </a>
            
            <a href="contact_admin.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'contact_admin.php' ? 'active' : ''; ?>">
                <div class="nav-icon"><i class="fas fa-file-alt"></i></div>
                <div class="nav-text">Contact Admin</div>
            </a>
            

            
           
        </div>
    </div>
    
    <!-- TOP HEADER BAR -->
    <div class="top-header">
        <div class="page-title">
            <?php 
            // Dynamic page title based on current page
            $page_titles = [
                'user_dashboard.php' => 'Dashboard',
                'user_profile.php' => 'My Profile',
                'list_found.php' => 'Found Items List',
                'form_found.php' => 'Report Found Item',
                'contact_admin.php' => 'Contact Admin',
                'help.php' => 'Help & Support'
            ];
            $current_page = basename($_SERVER['PHP_SELF']);
            echo $page_titles[$current_page] ?? 'User Panel';
            ?>
        </div>
        
        <div class="header-right">
            <!-- Notification Button -->
            <div class="dropdown">
                <button class="notification-btn">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">3</span>
                </button>
                
                <div class="notification-panel">
                    <div class="notification-header">Notifications</div>
                    
                    <div class="notification-item unread">
                        <div class="notification-title">New Lost Item Reported</div>
                        <div class="notification-message">Wallet found in main prayer hall</div>
                        <div class="notification-time">10 minutes ago</div>
                    </div>
                    
                    <div class="notification-item unread">
                        <div class="notification-title">Status Updated</div>
                        <div class="notification-message">Your reported item has been claimed</div>
                        <div class="notification-time">2 hours ago</div>
                    </div>
                    
                    <div class="notification-item">
                        <div class="notification-title">Welcome Message</div>
                        <div class="notification-message">Welcome to Surau Ismail Lost & Found System</div>
                        <div class="notification-time">1 day ago</div>
                    </div>
                    
                    <a href="notifications.php" class="dropdown-item" style="text-align: center; color: #3498db;">
                        <i class="fas fa-eye dropdown-icon"></i>
                        View All Notifications
                    </a>
                </div>
            </div>
            
            <!-- User Profile Dropdown -->
            <div class="dropdown">
                <div class="user-profile">
                    <div class="user-avatar">
                        <?php 
                        // Get first letter of name
                        $name = $_SESSION['name'] ?? 'User';
                        echo strtoupper(substr($name, 0, 1)); 
                        ?>
                    </div>
                    <div class="user-info">
                        <div class="user-name"><?php echo htmlspecialchars($_SESSION['name'] ?? 'User'); ?></div>
                        <div class="user-role"><?php echo ucfirst($_SESSION['role'] ?? 'user'); ?></div>
                    </div>
                    <i class="fas fa-chevron-down" style="color: #7f8c8d; font-size: 12px;"></i>
                </div>
                
                <div class="dropdown-menu">
                    
                    <div class="dropdown-item" style="border-top: 1px solid #f8f9fa; margin-top: 5px;">
                        <a href="logout.php" class="logout-btn" style="width: 100%; justify-content: center;">
                            <i class="fas fa-sign-out-alt"></i>
                            Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- MAIN CONTENT AREA -->
    <div class="main-content">
        <!-- Content will be inserted here by other pages -->
        <?php
        // This is where the actual page content goes
        // Each page will include this sidebar_nav.php and put its content after
        ?>