<?php
include 'db_connect.php';

$message = "";

if(isset($_POST['register'])) {
    $role = $_POST['role']; // user/admin
    $name = $_POST['name'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if($role == 'user') {
        $contactnum = $_POST['contactnum'] ?? NULL;
        $email = NULL;
    } else if($role == 'admin') {
        $email = $_POST['email'] ?? NULL;
        $contactnum = NULL;
    }
    
    // Validate
    if(empty($name) || empty($password)) {
        $message = "Error: Name and Password are required!";
    } else {
        $hashed_password = $password;
        
        $sql = "INSERT INTO accounts (role, name, contactnum, email, password)
                VALUES ('$role', '$name', '$contactnum', '$email', '$hashed_password')";

        if(mysqli_query($connect, $sql)){
            $message = "Register successful!";
        } else {
            $message = "Error: ". mysqli_error($connect);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - Surau Ismail Kharofa</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #51863f 0%, #511600 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            display: flex;
            max-width: 1300px;
            width: 100%;
            background: white;
            border-radius: 25px;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
            min-height: 750px;
            position: relative;
        }
        
        /* LEFT SIDE - Design/Visual */
        .design-side {
            flex: 1.2;
            background: linear-gradient(135deg, #223d3c 0%, #8cce72 100%);
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .design-side::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="none" opacity="0.1"><path d="M0,0 L100,0 L100,100 Z" fill="%23ffffff"/></svg>');
            background-size: cover;
            pointer-events: none;
        }
        
        .logo-container {
            margin-bottom: 30px;
            z-index: 1;
            animation: float 6s ease-in-out infinite;
        }
        
        .logo {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            border: 5px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .logo:hover {
            transform: scale(1.05) rotate(5deg);
        }
        
        .surau-title {
            font-size: 42px;
            color: #FFF8F0;
            margin-bottom: 15px;
            font-weight: 800;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            letter-spacing: 1px;
            z-index: 1;
            position: relative;
        }
        
        .surau-title::after {
            content: '';
            display: block;
            width: 80px;
            height: 4px;
            background: #9a4c2e;
            margin: 15px auto;
            border-radius: 2px;
        }
        
        .lost-found {
            font-size: 48px;
            color: #FFE5B4;
            margin-bottom: 40px;
            font-weight: 700;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.4);
            letter-spacing: 2px;
            z-index: 1;
            position: relative;
            background: linear-gradient(45deg, #511600, #77ae41);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: shimmer 3s infinite;
        }
        
        @keyframes shimmer {
            0% { background-position: -200px; }
            100% { background-position: 200px; }
        }
        
        .surau-image-container {
            margin-top: 20px;
            z-index: 1;
            position: relative;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.25);
            transition: all 0.4s ease;
            border: 3px solid rgba(255, 255, 255, 0.2);
        }
        
        .surau-image-container:hover {
            transform: translateY(-10px);
            box-shadow: 0 25px 45px rgba(0, 0, 0, 0.35);
        }
        
        .surau-image {
            width: 100%;
            max-width: 550px;
            height: 250px;
            object-fit: cover;
            display: block;
        }
        
        .design-quote {
            color: rgba(255, 248, 240, 0.9);
            font-size: 16px;
            margin-top: 25px;
            font-style: italic;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
            z-index: 1;
            position: relative;
            max-width: 500px;
            line-height: 1.6;
        }
        
        /* RIGHT SIDE - Form */
        .form-side {
            flex: 1;
            padding: 60px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #FFFfff;
            position: relative;
        }
        
        .form-side::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="none" opacity="0.03"><path d="M0,0 L100,0 L100,100 Z" fill="%238B4513"/></svg>');
            background-size: cover;
            pointer-events: none;
        }
        
        .form-container {
            max-width: 420px;
            width: 100%;
            margin: 0 auto;
            z-index: 1;
        }
        
        .form-title {
            font-size: 32px;
            color: #8B4513;
            margin-bottom: 10px;
            text-align: center;
            font-weight: 700;
        }
        
        .form-subtitle {
            color: #A0522D;
            text-align: center;
            margin-bottom: 35px;
            font-size: 16px;
        }
        
        .role-selector {
            display: flex;
            gap: 20px;
            margin-bottom: 35px;
        }
        
        .role-option {
            flex: 1;
            padding: 18px;
            border: 2px solid #E0C9B8;
            border-radius: 12px;
            cursor: pointer;
            text-align: center;
            transition: all 0.3s ease;
            font-weight: 600;
            color: #8B4513;
            background: #FAF3EB;
            font-size: 16px;
        }
        
        .role-option:hover {
            border-color: #8B4513;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(139, 69, 19, 0.15);
        }
        
        .role-option.selected {
            border-color: #511600;
            background: linear-gradient(135deg, #511600 0%, #A0522D 100%);
            color: white;
            box-shadow: 0 8px 20px rgba(139, 69, 19, 0.25);
        }
        
        .form-group {
            margin-bottom: 25px;
            position: relative;
        }
        
        label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: #8B4513;
            font-size: 15px;
        }
        
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 16px 20px;
            border: 2px solid #E0C9B8;
            border-radius: 12px;
            font-size: 16px;
            margin-bottom: 5px;
            background: #FFF;
            transition: all 0.3s;
            color: #5D4037;
        }
        
        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #8B4513;
            background: #FFF;
            box-shadow: 0 0 0 4px rgba(139, 69, 19, 0.1);
        }
        
        .submit-btn {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, #511600 0%, #A0522D 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 18px;
            font-weight: 700;
            cursor: pointer;
            margin-top: 25px;
            transition: all 0.3s;
            letter-spacing: 0.5px;
            position: relative;
            overflow: hidden;
        }
        
        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 25px rgba(139, 69, 19, 0.3);
        }
        
        .submit-btn:active {
            transform: translateY(-1px);
        }
        
        .submit-btn::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: 0.5s;
        }
        
        .submit-btn:hover::after {
            left: 100%;
        }
        
        .message {
            padding: 18px;
            border-radius: 12px;
            margin-bottom: 25px;
            text-align: center;
            animation: slideIn 0.5s ease;
            border-left: 5px solid;
        }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }
        
        .success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
            border-left-color: #28a745;
        }
        
        .error {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
            border-left-color: #dc3545;
        }
        
        .login-link {
            text-align: center;
            margin-top: 25px;
            color: #8B4513;
            font-size: 15px;
        }
        
        .login-link a {
            color: #A0522D;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }
        
        .login-link a:hover {
            color: #8B4513;
            text-decoration: underline;
        }
        
        /* Responsive */
        @media (max-width: 1100px) {
            .container {
                flex-direction: column;
                max-width: 600px;
            }
            
            .design-side, .form-side {
                padding: 40px 30px;
            }
            
            .surau-title {
                font-size: 36px;
            }
            
            .lost-found {
                font-size: 42px;
            }
        }
    </style>
    <script>
        let currentRole = 'user';
        
        function selectRole(role) {
            currentRole = role;
            
            // Remove selected class
            document.querySelectorAll('.role-option').forEach(option => {
                option.classList.remove('selected');
            });
            
            // Add selected class to clicked
            event.target.classList.add('selected');
            
            // Update hidden role input
            document.getElementById('roleInput').value = role;
            
            // Update form fields visibility
            if(role === 'user') {
                document.getElementById('contactnumField').style.display = 'block';
                document.getElementById('emailField').style.display = 'none';
                document.getElementById('formTitle').textContent = 'Register as User';
                document.getElementById('formSubtitle').textContent = 'Create a user account to report or claim lost items';
            } else {
                document.getElementById('contactnumField').style.display = 'none';
                document.getElementById('emailField').style.display = 'block';
                document.getElementById('formTitle').textContent = 'Register as Admin';
                document.getElementById('formSubtitle').textContent = 'Create an admin account to manage lost and found items';
            }
        }
        
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            // Select user by default
            document.querySelector('.role-option:first-child').classList.add('selected');
            document.getElementById('roleInput').value = 'user';
            document.getElementById('contactnumField').style.display = 'block';
            document.getElementById('emailField').style.display = 'none';
            document.getElementById('formTitle').textContent = 'Register as User';
            document.getElementById('formSubtitle').textContent = 'Create a user account to report or claim lost items';
        });
    </script>
</head>
<body>

<div class="container">
    <!-- LEFT SIDE - Design/Visual -->
    <div class="design-side">
        <div class="logo-container">
            <img src="Logo.png" alt="logo" class="logo">
        </div>
        
        <h1 class="surau-title">Surau Ismail Kharofa</h1>
        
        <div class="lost-found">Lost And Found</div>
        
        <div class="surau-image-container">
            <img src="surau_pic.png" alt="surau" class="surau-image">
        </div>
        
        <p class="design-quote">
            "Helping our community reunite with lost belongings through faith and cooperation"
        </p>
    </div>
    
    <!-- RIGHT SIDE - Form -->
    <div class="form-side">
        <div class="form-container">
            <h2 class="form-title" id="formTitle">Create Account</h2>
            <p class="form-subtitle" id="formSubtitle">Join our community to report or claim lost items</p>
            
            <?php if($message != ""): ?>
                <div class="message <?php echo strpos($message, 'Error') !== false ? 'error' : 'success'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <!-- Role Selection -->
            <div class="role-selector">
                <div class="role-option" onclick="selectRole('user')">
                    <i style="margin-right: 8px;">👤</i> User
                </div>
                <div class="role-option" onclick="selectRole('admin')">
                    <i style="margin-right: 8px;">👑</i> Admin
                </div>
            </div>
            
            <form method="POST" action="">
                <!-- Hidden role input -->
                <input type="hidden" name="role" id="roleInput" value="user" required>
                
                <!-- COMMON FIELDS -->
                <div class="form-group">
                    <label>Full Name:</label>
                    <input type="text" name="name" placeholder="Enter your full name" required>
                </div>
                
                <div class="form-group" id="contactnumField">
                    <label>Contact Number (User only):</label>
                    <input type="text" name="contactnum" placeholder="Enter your phone number">
                </div>
                
                <div class="form-group" id="emailField" style="display: none;">
                    <label>Email Address (Admin only):</label>
                    <input type="email" name="email" placeholder="Enter your email address">
                </div>
                
                <div class="form-group">
                    <label>Password:</label>
                    <input type="password" name="password" placeholder="Create a secure password" required>
                </div>
                
                <button type="submit" name="register" class="submit-btn">
                    Create Account
                </button>
                
                <div class="login-link">
                    Already have an account? <a href="login.php">Login Here</a>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>