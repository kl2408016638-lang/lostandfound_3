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

$message = "";
$success = false;

// Handle message submission
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_message'])) {
    $subject = mysqli_real_escape_string($connect, $_POST['subject']);
    $message_content = mysqli_real_escape_string($connect, $_POST['message']);
    
    if(empty($subject) || empty($message_content)) {
        $message = "<div class='alert error'>Please fill in all fields!</div>";
    } else {
        $sql = "INSERT INTO user_messages (sender_id, sender_name, subject, message) 
                VALUES ('$user_id', '$user_name', '$subject', '$message_content')";
        
        if(mysqli_query($connect, $sql)) {
            $message = "<div class='alert success'>Message sent successfully to admin!</div>";
            $success = true;
            
            // Clear form fields
            $_POST['subject'] = $_POST['message'] = '';
        } else {
            $message = "<div class='alert error'>Error sending message: " . mysqli_error($connect) . "</div>";
        }
    }
}

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
    <title>Contact Admin</title>
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
        
        .back-link {
            color: #3498db;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            font-size: 14px;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
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
        
        .alert i {
            font-size: 18px;
        }
        
        .form-container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            color: #34495e;
            font-weight: 600;
            font-size: 15px;
        }
        
        .form-label .required {
            color: #e74c3c;
        }
        
        .form-input, .form-textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 15px;
            font-family: inherit;
            transition: all 0.3s;
        }
        
        .form-input:focus, .form-textarea:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }
        
        .form-textarea {
            min-height: 200px;
            resize: vertical;
            line-height: 1.6;
        }
        
        .form-hint {
            font-size: 13px;
            color: #7f8c8d;
            margin-top: 5px;
            display: block;
        }
        
        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        
        .btn-primary {
            background: #3498db;
            color: white;
        }
        
        .btn-primary:hover {
            background: #2980b9;
        }
        
        .btn-secondary {
            background: #95a5a6;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #7f8c8d;
        }
        
        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px solid #eee;
        }
        
        .info-box {
            background: #e8f4fc;
            border-radius: 8px;
            padding: 20px;
            margin-top: 30px;
            border-left: 4px solid #3498db;
        }
        
        .info-title {
            color: #2c3e50;
            font-size: 16px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .info-list {
            list-style: none;
            margin-top: 15px;
        }
        
        .info-list li {
            padding: 8px 0;
            color: #5d6d7e;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }
        
        .info-list li i {
            color: #3498db;
            margin-top: 3px;
        }
        
        .message-preview {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
            border: 1px dashed #ddd;
            display: none;
        }
        
        .message-preview.show {
            display: block;
        }
        
        .preview-title {
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 10px;
            font-size: 16px;
        }
        
        .preview-subject {
            color: #34495e;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .preview-message {
            color: #5d6d7e;
            line-height: 1.6;
            white-space: pre-wrap;
            margin-top: 10px;
        }
        
        .char-count {
            text-align: right;
            font-size: 13px;
            color: #7f8c8d;
            margin-top: 5px;
        }
        
        .char-count.warning {
            color: #f39c12;
        }
        
        .char-count.error {
            color: #e74c3c;
        }
        
        @media (max-width: 768px) {
            .form-actions {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
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
                        <i class="fas fa-envelope"></i>
                        Contact Admin
                    </h1>
                    <a href="user_dashboard.php" class="back-link">
                        <i class="fas fa-arrow-left"></i>
                        Back to Dashboard
                    </a>
                </div>
                
                <!-- Messages -->
                <?php if($message != ""): ?>
                    <?php echo $message; ?>
                <?php endif; ?>
                
                <!-- Contact Form -->
                <div class="form-container">
                    <form method="POST" action="" id="messageForm">
                        <div class="form-group">
                            <label class="form-label" for="subject">
                                Subject <span class="required">*</span>
                            </label>
                            <input type="text" id="subject" name="subject" class="form-input" 
                                   placeholder="Enter message subject" 
                                   value="<?php echo isset($_POST['subject']) ? htmlspecialchars($_POST['subject']) : ''; ?>"
                                   required>
                            <span class="form-hint">Brief description of your inquiry</span>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="message">
                                Message <span class="required">*</span>
                            </label>
                            <textarea id="message" name="message" class="form-textarea" 
                                      placeholder="Type your message here..." 
                                      required><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                            <div class="char-count" id="charCount">0/2000 characters</div>
                            <span class="form-hint">Please provide detailed information about your inquiry</span>
                        </div>
                        
                        <!-- Message Preview -->
                        <div class="message-preview" id="messagePreview">
                            <div class="preview-title">
                                <i class="fas fa-eye"></i> Message Preview
                            </div>
                            <div class="preview-subject" id="previewSubject"></div>
                            <div class="preview-message" id="previewMessage"></div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="button" class="btn btn-secondary" id="previewBtn">
                                <i class="fas fa-eye"></i> Preview
                            </button>
                            <button type="submit" name="send_message" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Send Message
                            </button>
                        </div>
                    </form>
                    
                    <!-- Information Box -->
                    <div class="info-box">
                        <div class="info-title">
                            <i class="fas fa-info-circle"></i>
                            About Contacting Admin
                        </div>
                        <p style="color: #5d6d7e; line-height: 1.6;">
                            Use this form to contact the administrator regarding:
                        </p>
                        <ul class="info-list">
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>Questions about your reported items</span>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>Issues with item status or information</span>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>Report problems or concerns</span>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>General inquiries about the system</span>
                            </li>
                        </ul>
                        <p style="color: #5d6d7e; line-height: 1.6; margin-top: 15px;">
                            <i class="fas fa-clock" style="color: #f39c12;"></i>
                            <strong>Response Time:</strong> Admin typically responds within 24-48 hours.
                        </p>
                        <p style="color: #5d6d7e; line-height: 1.6; margin-top: 10px;">
                            <i class="fas fa-history" style="color: #3498db;"></i>
                            You can view your message history in <strong>My Messages</strong> page.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Character counter
        const messageTextarea = document.getElementById('message');
        const charCount = document.getElementById('charCount');
        const maxChars = 2000;
        
        messageTextarea.addEventListener('input', function() {
            const currentLength = this.value.length;
            charCount.textContent = `${currentLength}/${maxChars} characters`;
            
            // Update color based on length
            if(currentLength > maxChars * 0.9) {
                charCount.className = 'char-count error';
            } else if(currentLength > maxChars * 0.75) {
                charCount.className = 'char-count warning';
            } else {
                charCount.className = 'char-count';
            }
            
            // Limit characters
            if(currentLength > maxChars) {
                this.value = this.value.substring(0, maxChars);
                charCount.textContent = `${maxChars}/${maxChars} characters`;
                charCount.className = 'char-count error';
            }
        });
        
        // Preview functionality
        document.getElementById('previewBtn').addEventListener('click', function() {
            const subject = document.getElementById('subject').value;
            const message = document.getElementById('message').value;
            const preview = document.getElementById('messagePreview');
            
            if(!subject.trim() || !message.trim()) {
                alert('Please fill in both subject and message before previewing.');
                return;
            }
            
            document.getElementById('previewSubject').textContent = 'Subject: ' + subject;
            document.getElementById('previewMessage').textContent = message;
            preview.classList.add('show');
            
            // Scroll to preview
            preview.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        });
        
        // Auto-hide preview when form fields change
        document.getElementById('subject').addEventListener('input', function() {
            document.getElementById('messagePreview').classList.remove('show');
        });
        
        document.getElementById('message').addEventListener('input', function() {
            document.getElementById('messagePreview').classList.remove('show');
        });
        
        // Form validation
        document.getElementById('messageForm').addEventListener('submit', function(e) {
            const subject = document.getElementById('subject').value.trim();
            const message = document.getElementById('message').value.trim();
            
            if(!subject || !message) {
                e.preventDefault();
                alert('Please fill in all required fields.');
                return false;
            }
            
            if(message.length > maxChars) {
                e.preventDefault();
                alert(`Message is too long. Maximum ${maxChars} characters allowed.`);
                return false;
            }
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
            submitBtn.disabled = true;
            
            // Re-enable button after 3 seconds (in case of error)
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 3000);
        });
        
        // Initialize character count on page load
        messageTextarea.dispatchEvent(new Event('input'));
        
        // Auto-focus on subject field if empty
        window.addEventListener('load', function() {
            if(!document.getElementById('subject').value) {
                document.getElementById('subject').focus();
            }
        });
    </script>
</body>
</html>