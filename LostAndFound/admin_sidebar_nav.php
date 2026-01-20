<?php
// admin_sidebar_nav.php
// Check jika session sudah start
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check jika user logged in sebagai admin
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    // Redirect ke login jika bukan admin
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Navigation</title>
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
        
        /* ADMIN SIDEBAR - DARK THEME */
        .admin-sidebar {
            width: 280px;
            background: linear-gradient(180deg, #1a1a2e 0%, #16213e 100%);
            color: white;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            padding: 20px 0;
            box-shadow: 2px 0 15px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            overflow-y: auto;
        }
        
        /* TOP HEADER BAR - ADMIN */
        .admin-top-header {
            position: fixed;
            top: 0;
            left: 280px;
            right: 0;
            height: 70px;
            background: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 40px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.1);
            z-index: 999;
            border-bottom: 3px solid #e74c3c;
        }
        
        .admin-logo {
            text-align: center;
            padding: 20px 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
        }
        
        .admin-logo h2 {
            color: white;
            font-size: 24px;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .admin-logo p {
            color: #6c8bc7;
            font-size: 13px;
            font-weight: 300;
        }
        
        .admin-badge {
            background: #e74c3c;
            color: white;
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
            letter-spacing: 1px;
        }
        
        .admin-nav-menu {
            padding: 0 15px;
        }
        
        .admin-nav-title {
            color: #6c8bc7;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            padding: 20px 15px 10px 15px;
            margin-top: 10px;
            font-weight: 600;
        }
        
        .admin-nav-item {
            display: flex;
            align-items: center;
            padding: 14px 15px;
            color: #b8c7e0;
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 5px;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .admin-nav-item:hover {
            background: rgba(255, 255, 255, 0.08);
            color: white;
            transform: translateX(5px);
        }
        
        .admin-nav-item.active {
            background: linear-gradient(90deg, #e74c3c 0%, #c0392b 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(231, 76, 60, 0.3);
        }
        
        .admin-nav-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: white;
            border-radius: 0 2px 2px 0;
        }
        
        .admin-nav-icon {
            margin-right: 15px;
            font-size: 18px;
            width: 24px;
            text-align: center;
        }
        
        .admin-nav-text {
            font-size: 15px;
            font-weight: 500;
            flex: 1;
        }
        
        .nav-arrow {
            font-size: 12px;
            transition: transform 0.3s;
        }
        
        .nav-arrow.rotated {
            transform: rotate(90deg);
        }
        
        /* SUBMENU STYLES */
        .submenu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 6px;
            margin: 5px 0;
        }
        
        .submenu.open {
            max-height: 300px;
        }
        
        .submenu-item {
            display: flex;
            align-items: center;
            padding: 12px 15px 12px 45px;
            color: #a0b1d0;
            text-decoration: none;
            transition: all 0.3s;
            border-left: 2px solid transparent;
        }
        
        .submenu-item:hover {
            background: rgba(255, 255, 255, 0.05);
            color: white;
            border-left-color: #3498db;
        }
        
        .submenu-item.active {
            background: rgba(52, 152, 219, 0.1);
            color: #3498db;
            border-left-color: #3498db;
        }
        
        .submenu-icon {
            margin-right: 10px;
            font-size: 14px;
            width: 20px;
            text-align: center;
        }
        
        /* TOP HEADER STYLES - ADMIN */
        .admin-page-title {
            font-size: 24px;
            color: #2c3e50;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .admin-title-icon {
            color: #e74c3c;
            font-size: 20px;
        }
        
        .admin-header-right {
            display: flex;
            align-items: center;
            gap: 25px;
        }
        
        .admin-notification-btn {
            position: relative;
            background: none;
            border: none;
            color: #7f8c8d;
            font-size: 22px;
            cursor: pointer;
            padding: 10px;
            border-radius: 50%;
            transition: all 0.3s;
        }
        
        .admin-notification-btn:hover {
            background: #f8f9fa;
            color: #e74c3c;
            transform: rotate(15deg);
        }
        
        .admin-notification-badge {
            position: absolute;
            top: 5px;
            right: 5px;
            background: #e74c3c;
            color: white;
            font-size: 10px;
            padding: 3px 7px;
            border-radius: 10px;
            min-width: 18px;
            text-align: center;
            font-weight: bold;
        }
        
        .admin-user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            padding: 8px 15px;
            border-radius: 25px;
            transition: all 0.3s;
            background: #f8f9fa;
            border: 2px solid transparent;
        }
        
        .admin-user-profile:hover {
            background: white;
            border-color: #e74c3c;
            box-shadow: 0 4px 12px rgba(231, 76, 60, 0.1);
        }
        
        .admin-user-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 18px;
            border: 2px solid white;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.2);
        }
        
        .admin-user-info {
            display: flex;
            flex-direction: column;
        }
        
        .admin-user-name {
            font-weight: 700;
            color: #2c3e50;
            font-size: 15px;
        }
        
        .admin-user-role {
            color: #e74c3c;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        
        .admin-logout-btn {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 10px;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 12px rgba(231, 76, 60, 0.2);
        }
        
        .admin-logout-btn:hover {
            background: linear-gradient(135deg, #c0392b 0%, #a93226 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(231, 76, 60, 0.3);
        }
        
        /* MAIN CONTENT AREA - ADMIN */
        .admin-main-content {
            flex: 1;
            margin-left: 280px;
            margin-top: 70px;
            padding: 40px;
            min-height: calc(100vh - 70px);
            background: #f8f9fa;
        }
        
        /* ADMIN DROPDOWN MENU */
        .admin-dropdown {
            position: relative;
        }
        
        .admin-dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border-radius: 10px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            min-width: 220px;
            display: none;
            z-index: 1001;
            border: 1px solid #eee;
            overflow: hidden;
        }
        
        .admin-dropdown:hover .admin-dropdown-menu {
            display: block;
        }
        
        .admin-dropdown-item {
            display: flex;
            align-items: center;
            padding: 14px 20px;
            color: #2c3e50;
            text-decoration: none;
            transition: all 0.3s;
            border-bottom: 1px solid #f8f9fa;
        }
        
        .admin-dropdown-item:hover {
            background: #f8f9fa;
            color: #e74c3c;
            padding-left: 25px;
        }
        
        .admin-dropdown-item:last-child {
            border-bottom: none;
            background: #fff5f5;
        }
        
        .admin-dropdown-icon {
            margin-right: 12px;
            font-size: 16px;
            width: 20px;
            text-align: center;
        }
        
        /* ADMIN NOTIFICATION PANEL */
        .admin-notification-panel {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border-radius: 10px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            width: 380px;
            max-height: 450px;
            overflow-y: auto;
            display: none;
            z-index: 1001;
            border: 1px solid #eee;
        }
        
        .admin-notification-header {
            padding: 20px;
            border-bottom: 1px solid #f8f9fa;
            font-weight: 700;
            color: #2c3e50;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f8f9fa;
        }
        
        .admin-notification-item {
            padding: 18px 20px;
            border-bottom: 1px solid #f8f9fa;
            transition: all 0.3s;
            cursor: pointer;
        }
        
        .admin-notification-item:hover {
            background: #f8f9fa;
        }
        
        .admin-notification-item.unread {
            background: #ffeaea;
            border-left: 4px solid #e74c3c;
        }
        
        .admin-notification-title {
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .admin-notification-message {
            color: #7f8c8d;
            font-size: 14px;
            margin-bottom: 8px;
            line-height: 1.5;
        }
        
        .admin-notification-time {
            color: #95a5a6;
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .admin-notification-btn:hover .admin-notification-panel {
            display: block;
        }
        
        /* SYSTEM STATS BADGE */
        .system-stats {
            display: flex;
            gap: 15px;
            margin-top: 10px;
        }
        
        .stat-badge {
            background: rgba(255, 255, 255, 0.1);
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 11px;
            color: #6c8bc7;
            border: 1px solid rgba(108, 139, 199, 0.3);
        }
        
        /* RESPONSIVE */
        @media (max-width: 1024px) {
            .admin-sidebar {
                width: 240px;
            }
            
            .admin-top-header {
                left: 240px;
                padding: 0 30px;
            }
            
            .admin-main-content {
                margin-left: 240px;
                padding: 30px;
            }
        }
        
        @media (max-width: 768px) {
            .admin-sidebar {
                width: 70px;
            }
            
            .admin-sidebar .admin-nav-text,
            .admin-sidebar .admin-logo h2,
            .admin-sidebar .admin-logo p,
            .admin-sidebar .admin-nav-title,
            .admin-sidebar .system-stats,
            .admin-sidebar .nav-arrow {
                display: none;
            }
            
            .admin-sidebar .admin-nav-item {
                justify-content: center;
                padding: 18px;
            }
            
            .admin-sidebar .admin-nav-icon {
                margin-right: 0;
                font-size: 20px;
            }
            
            .admin-sidebar .submenu-item {
                padding: 15px;
                justify-content: center;
            }
            
            .admin-sidebar .submenu-icon {
                margin-right: 0;
                font-size: 16px;
            }
            
            .admin-top-header {
                left: 70px;
                padding: 0 20px;
            }
            
            .admin-main-content {
                margin-left: 70px;
                padding: 20px;
            }
            
            .admin-user-info {
                display: none;
            }
            
            .admin-page-title {
                font-size: 20px;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        // JavaScript untuk toggle submenu
        document.addEventListener('DOMContentLoaded', function() {
            const dashboardItem = document.querySelector('.admin-nav-item[data-submenu="dashboard"]');
            const submenu = document.querySelector('.submenu');
            const arrow = dashboardItem.querySelector('.nav-arrow');
            
            dashboardItem.addEventListener('click', function(e) {
                e.preventDefault();
                submenu.classList.toggle('open');
                arrow.classList.toggle('rotated');
            });
            
            // Set active submenu berdasarkan current page
            const currentPage = '<?php echo basename($_SERVER["PHP_SELF"]); ?>';
            const submenuPages = {
                'admin_statistics.php': 'statistics',
                'admin_trail.php': 'trail',
                'list_users.php': 'users'
            };
            
            if (submenuPages[currentPage]) {
                const activeSubItem = document.querySelector(`.submenu-item[data-page="${submenuPages[currentPage]}"]`);
                if (activeSubItem) {
                    activeSubItem.classList.add('active');
                    submenu.classList.add('open');
                    arrow.classList.add('rotated');
                }
            }
        });
    </script>
</head>
<body>
    <!-- ADMIN SIDEBAR NAVIGATION -->
    <div class="admin-sidebar">
        <div class="admin-logo">
            <h2>
                <i class="fas fa-shield-alt"></i>
                Admin Panel
                <span class="admin-badge">ADMIN</span>
            </h2>
            <p>Surau Ismail Lost & Found</p>
            
            <div class="system-stats">
                <span class="stat-badge"><i class="fas fa-users"></i> Online</span>
                <span class="stat-badge"><i class="fas fa-server"></i> Active</span>
            </div>
        </div>
        
        <div class="admin-nav-menu">
            <div class="admin-nav-title">Administration</div>
            
            <!-- Dashboard dengan Submenu -->
            <a href="#" class="admin-nav-item" data-submenu="dashboard">
                <div class="admin-nav-icon"><i class="fas fa-tachometer-alt"></i></div>
                <div class="admin-nav-text">Dashboard</div>
                <div class="nav-arrow"><i class="fas fa-chevron-right"></i></div>
            </a>
            
            <!-- Submenu untuk Dashboard -->
            <div class="submenu">
                <a href="admin_statistics.php" class="submenu-item" data-page="statistics">
                    <div class="submenu-icon"><i class="fas fa-chart-bar"></i></div>
                    <div>Statistics</div>
                </a>
                <a href="admin_trail.php" class="submenu-item" data-page="trail">
                    <div class="submenu-icon"><i class="fas fa-history"></i></div>
                    <div>Admin Trail</div>
                </a>
                <a href="admin_users.php" class="submenu-item" data-page="users">
                    <div class="submenu-icon"><i class="fas fa-user-friends"></i></div>
                    <div>List User Account</div>
                </a>
            </div>
            
            <a href="admin_profile.php" class="admin-nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'admin_profile.php' ? 'active' : ''; ?>">
                <div class="admin-nav-icon"><i class="fas fa-user-cog"></i></div>
                <div class="admin-nav-text">Profile</div>
            </a>
            
            <a href="list_found.php" class="admin-nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'list_found.php' ? 'active' : ''; ?>">
                <div class="admin-nav-icon"><i class="fas fa-search"></i></div>
                <div class="admin-nav-text">List Found Item</div>
            </a>
            
            
        </div>
    </div>
    
    <!-- ADMIN TOP HEADER BAR -->
    <div class="admin-top-header">
        <div class="admin-page-title">
            <i class="fas fa-<?php 
                // Dynamic icon based on page
                $page_icons = [
                    'admin_dashboard.php' => 'tachometer-alt',
                    'admin_profile.php' => 'user-cog',
                    'list_found.php' => 'search',
                    'manage_found_items.php' => 'box-open',
                    'reports.php' => 'chart-pie',
                    'system_settings.php' => 'cogs',
                    'admin_trail.php' => 'history',
                    'list_users.php' => 'user-friends'
                ];
                $current_page = basename($_SERVER['PHP_SELF']);
                echo $page_icons[$current_page] ?? 'cog';
            ?> admin-title-icon"></i>
            <?php 
            // Dynamic page title
            $page_titles = [
                'admin_statistics.php' => 'Dashboard Statistics',
                'admin_profile.php' => 'Admin Profile',
                'list_found.php' => 'Manage Lost Items',
                'manage_found_items.php' => 'Found Items Management',
                'reports.php' => 'System Reports',
                'system_settings.php' => 'System Settings',
                'admin_trail.php' => 'Admin Activity Trail',
                'admin_users.php' => 'User Accounts Management'
            ];
            echo $page_titles[$current_page] ?? 'Admin Panel';
            ?>
        </div>
        
        <div class="admin-header-right">
            <!-- Admin Notification Button -->
            <div class="admin-dropdown">
                <button class="admin-notification-btn">
                    <i class="fas fa-bell"></i>
                    <span class="admin-notification-badge">5</span>
                </button>
                
                <div class="admin-notification-panel">
                    <div class="admin-notification-header">
                        <span>Admin Notifications</span>
                        <span style="font-size: 12px; color: #e74c3c; font-weight: 600;">5 Unread</span>
                    </div>
                    
                    <div class="admin-notification-item unread">
                        <div class="admin-notification-title">
                            <i class="fas fa-exclamation-triangle" style="color: #e74c3c;"></i>
                            System Alert
                        </div>
                        <div class="admin-notification-message">New user registration requires approval</div>
                        <div class="admin-notification-time">
                            <i class="far fa-clock"></i> Just now
                        </div>
                    </div>
                    
                    <div class="admin-notification-item unread">
                        <div class="admin-notification-title">
                            <i class="fas fa-box" style="color: #3498db;"></i>
                            Item Reported
                        </div>
                        <div class="admin-notification-message">3 new lost items reported today</div>
                        <div class="admin-notification-time">
                            <i class="far fa-clock"></i> 2 hours ago
                        </div>
                    </div>
                    
                    <div class="admin-notification-item unread">
                        <div class="admin-notification-title">
                            <i class="fas fa-user-check" style="color: #28a745;"></i>
                            User Activity
                        </div>
                        <div class="admin-notification-message">10 active users in the last hour</div>
                        <div class="admin-notification-time">
                            <i class="far fa-clock"></i> 4 hours ago
                        </div>
                    </div>
                    
                    <div class="admin-notification-item">
                        <div class="admin-notification-title">
                            <i class="fas fa-database" style="color: #6c757d;"></i>
                            Backup Complete
                        </div>
                        <div class="admin-notification-message">System backup completed successfully</div>
                        <div class="admin-notification-time">
                            <i class="far fa-clock"></i> Yesterday
                        </div>
                    </div>
                    
                    <a href="admin_notifications.php" class="admin-dropdown-item" style="text-align: center; color: #e74c3c; font-weight: 600;">
                        <i class="fas fa-list admin-dropdown-icon"></i>
                        View All Notifications
                    </a>
                </div>
            </div>
            
            <!-- Admin User Profile Dropdown -->
            <div class="admin-dropdown">
                <div class="admin-user-profile">
                    <div class="admin-user-avatar">
                        <?php 
                        // Get first letter of admin name
                        $admin_name = $_SESSION['name'] ?? 'Admin';
                        echo strtoupper(substr($admin_name, 0, 1)); 
                        ?>
                    </div>
                    <div class="admin-user-info">
                        <div class="admin-user-name"><?php echo htmlspecialchars($_SESSION['name'] ?? 'Administrator'); ?></div>
                        <div class="admin-user-role">ADMIN</div>
                    </div>
                    <i class="fas fa-chevron-down" style="color: #7f8c8d; font-size: 12px;"></i>
                </div>
                
                <div class="admin-dropdown-menu">
                    
                    <div class="admin-dropdown-item">
                        <a href="logout.php" class="admin-logout-btn" style="width: 100%; justify-content: center;">
                            <i class="fas fa-sign-out-alt"></i>
                            Logout Admin
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- ADMIN MAIN CONTENT AREA -->
    <div class="admin-main-content">
        <!-- Content will be inserted here by other admin pages -->
        <?php
        // This is where the actual admin page content goes
        ?>