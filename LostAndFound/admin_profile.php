<?php
session_start();
include 'db_connect.php';

// Check jika user logged in DAN role admin
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

$message = "";
$user_id = $_SESSION['user_id'];

// Get admin data
$sql = "SELECT * FROM accounts WHERE id='$user_id' AND role='admin'";
$result = mysqli_query($connect, $sql);
$admin = mysqli_fetch_assoc($result);

if(isset($_POST['update'])) {
    $name = mysqli_real_escape_string($connect, $_POST['name'] ?? $admin['name']);
    $email = mysqli_real_escape_string($connect, $_POST['email'] ?? $admin['email']);
    $password = $_POST['password'] ?? '';
    
    // Determine new password
    if(!empty($password)) {
        // Untuk DEBUG: Simpan sebagai PLAIN TEXT
        $new_password = $password;
    } else {
        $new_password = $admin['password']; // Keep current
    }
    
    $update_sql = "UPDATE accounts SET 
                   name='$name', 
                   email='$email', 
                   password='$new_password' 
                   WHERE id='$user_id' AND role='admin'";
    
    if(mysqli_query($connect, $update_sql)) {
        $message = "Profile updated successfully!";
        // Refresh admin data
        $result = mysqli_query($connect, $sql);
        $admin = mysqli_fetch_assoc($result);
        $_SESSION['name'] = $admin['name'];
    } else {
        $message = "Error: " . mysqli_error($connect);
    }
}

// Include admin sidebar navigation
include 'admin_sidebar_nav.php';
?>

        <!-- Admin Profile Content -->
        <div style="background: white; border-radius: 15px; padding: 30px; box-shadow: 0 5px 20px rgba(0,0,0,0.05);">
            
            <!-- Page Header -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <div>
                    <h1 style="color: #2c3e50; margin-bottom: 10px; font-size: 28px;">
                        <i class="fas fa-user-cog" style="color: #e74c3c; margin-right: 10px;"></i>
                        Admin Profile
                    </h1>
                    <p style="color: #7f8c8d;">Manage your administrator account details</p>
                </div>
                
                <div style="background: #fff5f5; color: #e74c3c; padding: 10px 20px; 
                            border-radius: 10px; font-weight: 600; border: 2px solid #e74c3c;">
                    <i class="fas fa-shield-alt" style="margin-right: 8px;"></i>
                    ADMIN
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
            
            <div style="display: flex; gap: 40px; margin-bottom: 30px;">
                <!-- LEFT: Profile Information -->
                <div style="flex: 1;">
                    <h2 style="color: #2c3e50; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #eee;">
                        <i class="fas fa-info-circle" style="color: #3498db; margin-right: 10px;"></i>
                        Profile Information
                    </h2>
                    
                    <!-- NAME Card -->
                    <div style="background: #f8f9fa; border: 1px solid #ddd; border-radius: 8px; padding: 20px; margin-bottom: 20px; border-left: 4px solid #28a745;">
                        <div style="font-size: 14px; color: #777; margin-bottom: 5px; font-weight: bold;">NAME</div>
                        <div style="font-size: 20px; color: #333; font-weight: bold;"><?php echo htmlspecialchars($admin['name']); ?></div>
                    </div>
                    
                    <!-- EMAIL Card -->
                    <div style="background: #f8f9fa; border: 1px solid #ddd; border-radius: 8px; padding: 20px; margin-bottom: 20px; border-left: 4px solid #17a2b8;">
                        <div style="font-size: 14px; color: #777; margin-bottom: 5px; font-weight: bold;">EMAIL</div>
                        <div style="font-size: 20px; color: #333; font-weight: bold;"><?php echo htmlspecialchars($admin['email'] ?? 'Not set'); ?></div>
                    </div>
                    
                    <!-- PASSWORD Card (SHOWING PASSWORD) -->
                    <div style="background: #fff5f5; border: 1px solid #f5c6cb; border-radius: 8px; padding: 20px; margin-bottom: 20px; border-left: 4px solid #e74c3c;">
                        <div style="font-size: 14px; color: #e74c3c; margin-bottom: 5px; font-weight: bold;">
                            <i class="fas fa-key" style="margin-right: 8px;"></i>PASSWORD
                        </div>
                        <div style="font-size: 22px; color: #c0392b; font-weight: bold; font-family: 'Courier New', monospace; letter-spacing: 2px;">
                            <?php echo htmlspecialchars($admin['password']); ?>
                        </div>
                        
                    </div>
                    
                    <!-- ADMIN ID Card (Uneditable) -->
                    <div style="background: #e9ecef; border: 1px solid #ced4da; border-radius: 8px; padding: 20px; margin-bottom: 20px; border-left: 4px solid #6c757d;">
                        <div style="font-size: 14px; color: #495057; margin-bottom: 5px; font-weight: bold;">ADMIN ID</div>
                        <div style="font-size: 24px; color: #343a40; font-weight: bold;">
                            <?php echo $admin['id']; ?> <!-- NOMBOR SAHAJA, TANPA # DAN TANPA ZEROS -->
                        </div>
                    </div>
                    
                    <!-- ROLE Card (Uneditable) -->
                    <div style="background: #e9ecef; border: 1px solid #ced4da; border-radius: 8px; padding: 20px; border-left: 4px solid #6c757d;">
                        <div style="font-size: 14px; color: #495057; margin-bottom: 5px; font-weight: bold;">ROLE</div>
                        <div style="font-size: 20px; color: #343a40; font-weight: bold;">ADMINISTRATOR</div>
                    </div>
                </div>
                
                <!-- RIGHT: Edit Form -->
                <div style="flex: 1;">
                    <h2 style="color: #2c3e50; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #eee;">
                        <i class="fas fa-edit" style="color: #e74c3c; margin-right: 10px;"></i>
                        Edit Profile
                    </h2>
                    
                    <form method="POST" action="">
                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: bold; color: #2c3e50;">
                                <i class="fas fa-user" style="margin-right: 8px; color: #3498db;"></i>Name:
                            </label>
                            <input type="text" name="name" value="<?php echo htmlspecialchars($admin['name']); ?>" required
                                   style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px;">
                        </div>
                        
                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: bold; color: #2c3e50;">
                                <i class="fas fa-envelope" style="margin-right: 8px; color: #17a2b8;"></i>Email:
                            </label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($admin['email'] ?? ''); ?>" required
                                   style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px;">
                        </div>
                        
                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: bold; color: #2c3e50;">
                                <i class="fas fa-lock" style="margin-right: 8px; color: #e74c3c;"></i>New Password:
                                <span style="font-size: 12px; color: #6c757d; font-weight: normal;">(leave blank to keep current)</span>
                            </label>
                            <input type="text" name="password" placeholder="Enter new password"
                                   style="width: 100%; padding: 12px; border: 2px solid #f5c6cb; border-radius: 8px; 
                                          font-size: 16px; background: #fff5f5;">
                            
                        </div>
                        
                        <button type="submit" name="update" 
                                style="width: 100%; padding: 15px; background: linear-gradient(135deg, #2c3e50 0%, #e74c3c 100%); 
                                       color: white; border: none; border-radius: 8px; font-size: 18px; font-weight: 700; 
                                       cursor: pointer; margin-top: 10px; transition: all 0.3s;">
                            <i class="fas fa-save" style="margin-right: 10px;"></i>Update Profile
                        </button>
                    </form>
                    
                    
                </div>
            </div>
            
        </div>
        
    </div> <!-- Close admin-main-content div -->
</body>
</html>