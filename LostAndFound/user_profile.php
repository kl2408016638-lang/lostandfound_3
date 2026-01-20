<?php
session_start();
include 'db_connect.php';

// Check jika user logged in DAN role user
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: login.php");
    exit();
}

$message = "";
$user_id = $_SESSION['user_id'];

// Get user data
$sql = "SELECT * FROM accounts WHERE id='$user_id' AND role='user'";
$result = mysqli_query($connect, $sql);
$user = mysqli_fetch_assoc($result);

if(isset($_POST['update'])) {
    $name = mysqli_real_escape_string($connect, $_POST['name'] ?? $user['name']);
    $contactnum = mysqli_real_escape_string($connect, $_POST['contactnum'] ?? $user['contactnum']);
    $password = $_POST['password'] ?? '';
    
    // Determine new password
    if(!empty($password)) {
        // Untuk DEBUG: Simpan sebagai PLAIN TEXT
        $new_password = $password;
    } else {
        $new_password = $user['password']; // Keep current
    }
    
    $update_sql = "UPDATE accounts SET 
                   name='$name', 
                   contactnum='$contactnum', 
                   password='$new_password' 
                   WHERE id='$user_id' AND role='user'";
    
    if(mysqli_query($connect, $update_sql)) {
        $message = "Profile updated successfully!";
        // Refresh user data
        $result = mysqli_query($connect, $sql);
        $user = mysqli_fetch_assoc($result);
        $_SESSION['name'] = $user['name'];
    } else {
        $message = "Error: " . mysqli_error($connect);
    }
}

// Generate user key (simple version)
$user_key = strtoupper(substr(md5($user['id'] . $user['name']), 0, 8));

// Include sidebar navigation
include 'sidebar_nav.php';
?>

        <!-- User Profile Content -->
        <div style="background: white; border-radius: 15px; padding: 30px; box-shadow: 0 5px 20px rgba(0,0,0,0.05);">
            
            <!-- Page Header -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <div>
                    <h1 style="color: #2c3e50; margin-bottom: 10px; font-size: 28px;">
                        <i class="fas fa-user" style="color: #3498db; margin-right: 10px;"></i>
                        User Profile
                    </h1>
                    <p style="color: #7f8c8d;">Manage your personal account details</p>
                </div>
                
                <div style="background: #e8f4fc; color: #3498db; padding: 10px 20px; 
                            border-radius: 10px; font-weight: 600; border: 2px solid #3498db;">
                    <i class="fas fa-user-circle" style="margin-right: 8px;"></i>
                    USER ACCOUNT
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
                        <div style="font-size: 20px; color: #333; font-weight: bold;"><?php echo htmlspecialchars($user['name']); ?></div>
                    </div>
                    
                    <!-- CONTACT NUMBER Card -->
                    <div style="background: #f8f9fa; border: 1px solid #ddd; border-radius: 8px; padding: 20px; margin-bottom: 20px; border-left: 4px solid #17a2b8;">
                        <div style="font-size: 14px; color: #777; margin-bottom: 5px; font-weight: bold;">CONTACT NUMBER</div>
                        <div style="font-size: 20px; color: #333; font-weight: bold;"><?php echo htmlspecialchars($user['contactnum'] ?? 'Not set'); ?></div>
                    </div>
                    
                    <!-- PASSWORD Card (SHOWING PASSWORD) -->
                    <div style="background: #fff5f5; border: 1px solid #f5c6cb; border-radius: 8px; padding: 20px; margin-bottom: 20px; border-left: 4px solid #e74c3c;">
                        <div style="font-size: 14px; color: #e74c3c; margin-bottom: 5px; font-weight: bold;">
                            <i class="fas fa-key" style="margin-right: 8px;"></i>PASSWORD
                        </div>
                        <div style="font-size: 22px; color: #c0392b; font-weight: bold; font-family: 'Courier New', monospace; letter-spacing: 2px;">
                            <?php echo htmlspecialchars($user['password']); ?>
                        </div>
                        
                    </div>
                    
                    <!-- KEY Card (Uneditable) -->
                    <div style="background: #e9ecef; border: 1px solid #ced4da; border-radius: 8px; padding: 20px; margin-bottom: 20px; border-left: 4px solid #6c757d;">
                        <div style="font-size: 14px; color: #495057; margin-bottom: 5px; font-weight: bold;">KEY</div>
                        <div style="font-size: 24px; color: #343a40; font-weight: bold; font-family: 'Courier New', monospace; letter-spacing: 3px;">
                            <?php echo $user_key; ?>
                        </div>
                        
                    </div>
                    
                    <!-- ROLE Card (Uneditable) -->
                    <div style="background: #e9ecef; border: 1px solid #ced4da; border-radius: 8px; padding: 20px; border-left: 4px solid #6c757d;">
                        <div style="font-size: 14px; color: #495057; margin-bottom: 5px; font-weight: bold;">ROLE</div>
                        <div style="font-size: 20px; color: #343a40; font-weight: bold;">USER</div>
                    </div>
                </div>
                
                <!-- RIGHT: Edit Form -->
                <div style="flex: 1;">
                    <h2 style="color: #2c3e50; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #eee;">
                        <i class="fas fa-edit" style="color: #3498db; margin-right: 10px;"></i>
                        Edit Profile
                    </h2>
                    
                    <form method="POST" action="">
                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: bold; color: #2c3e50;">
                                <i class="fas fa-user" style="margin-right: 8px; color: #3498db;"></i>Name:
                            </label>
                            <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required
                                   style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px;">
                        </div>
                        
                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: bold; color: #2c3e50;">
                                <i class="fas fa-phone" style="margin-right: 8px; color: #17a2b8;"></i>Contact Number:
                            </label>
                            <input type="text" name="contactnum" value="<?php echo htmlspecialchars($user['contactnum'] ?? ''); ?>"
                                   style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px;"
                                   placeholder="Enter your phone number">
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
                                style="width: 100%; padding: 15px; background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); 
                                       color: white; border: none; border-radius: 8px; font-size: 18px; font-weight: 700; 
                                       cursor: pointer; margin-top: 10px; transition: all 0.3s;">
                            <i class="fas fa-save" style="margin-right: 10px;"></i>Update Profile
                        </button>
                    </form>
                    
                    
                </div>
            </div>
            
        </div>
        
    </div> <!-- Close main-content div -->
</body>
</html>