
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
$user_name = $_SESSION['name'];

if(isset($_POST['submit'])) {
    // Get form data
    $type_item = $_POST['type_item'] ?? '';
    $date = $_POST['date'] ?? '';
    $time = $_POST['time'] ?? '';
    $location = $_POST['location'] ?? '';
    $description = $_POST['description'] ?? '';
    
    // Handle file upload (picture)
    $picture = '';
    if(isset($_FILES['picture']) && $_FILES['picture']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
        $file_type = $_FILES['picture']['type'];
        
        if(in_array($file_type, $allowed_types)) {
            // Create uploads directory if not exists
            if(!is_dir('uploads')) {
                mkdir('uploads', 0777, true);
            }
            
            // Generate unique filename
            $file_ext = pathinfo($_FILES['picture']['name'], PATHINFO_EXTENSION);
            $picture = 'item_' . time() . '_' . $user_id . '.' . $file_ext;
            $upload_path = 'uploads/' . $picture;
            
            if(move_uploaded_file($_FILES['picture']['tmp_name'], $upload_path)) {
                // File uploaded successfully
            } else {
                $picture = '';
                $message = "Error: Failed to upload picture!";
            }
        } else {
            $message = "Error: Only JPG, PNG, and GIF images are allowed!";
        }
    }
    
    if(empty($message)) {
        // Insert into database
        $sql = "INSERT INTO found_items 
                (user_id, user_name, type_item, date, time, location, picture, description, status, created_at) 
                VALUES 
                ('$user_id', '$user_name', '$type_item', '$date', '$time', '$location', '$picture', '$description', 'pending', NOW())";
        
        if(mysqli_query($connect, $sql)) {
            $message = "Item reported successfully!";
        } else {
            $message = "Error: " . mysqli_error($connect);
        }
    }
}

// Include sidebar navigation
include 'sidebar_nav.php';

?>

<!DOCTYPE html>
<html>
<head>
    <title>Report Found Item - Surau Ismail Kharofa</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        h1, h2 {
            text-align: center;
            color: #333;
        }
        
        h1 {
            margin-bottom: 5px;
        }
        
        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
        }
        
        .message {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }
        
        input[type="text"],
        input[type="date"],
        input[type="time"],
        textarea,
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }
        
        textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        .submit-btn {
            background: #4CAF50;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
            font-weight: bold;
        }
        
        .submit-btn:hover {
            background: #45a049;
        }
        
        .nav-links {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        
        .nav-links a {
            color: #4CAF50;
            text-decoration: none;
            margin: 0 15px;
        }
        
        .nav-links a:hover {
            text-decoration: underline;
        }
        
        .file-info {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Report Found Item</h1>
    <p class="subtitle">Surau Ismail Kharofa - Lost and Found System</p>
    
    <?php if($message != ""): ?>
        <div class="message <?php echo strpos($message, 'Error') !== false ? 'error' : 'success'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="" enctype="multipart/form-data">
        <!-- Type Item -->
        <div class="form-group">
            <label for="type_item">Type of Item:</label>
            <select name="type_item" id="type_item" required>
                <option value="">-- Select Item Type --</option>
                <option value="wallet">Wallet/Purse</option>
                <option value="phone">Mobile Phone</option>
                <option value="keys">Keys</option>
                <option value="documents">Documents</option>
                <option value="jewelry">Jewelry</option>
                <option value="clothing">Clothing</option>
                <option value="books">Books</option>
                <option value="electronics">Electronics</option>
                <option value="other">Other</option>
            </select>
        </div>
        
        <!-- Date -->
        <div class="form-group">
            <label for="date">Date Found:</label>
            <input type="date" name="date" id="date" required>
        </div>
        
        <!-- Time -->
        <div class="form-group">
            <label for="time">Time Found:</label>
            <input type="time" name="time" id="time" required>
        </div>
        
        <!-- Location -->
        <div class="form-group">
            <label for="location">Location Found:</label>
            <select name="location" id="location" required>
                <option value="">-- Select Location --</option>
                <option value="main_hall">Main Prayer Hall</option>
                <option value="ablution_area">Ablution Area</option>
                <option value="parking">Parking Area</option>
                <option value="office">Office</option>
                <option value="classroom">Classroom</option>
                <option value="library">Library</option>
                <option value="cafeteria">Cafeteria</option>
                <option value="entrance">Main Entrance</option>
                <option value="other">Other Area</option>
            </select>
        </div>
        
        <!-- Picture -->
        <div class="form-group">
            <label for="picture">Picture of Item:</label>
            <input type="file" name="picture" id="picture" accept="image/*">
            <div class="file-info">Optional: Upload photo of the found item (JPG, PNG, GIF)</div>
        </div>
        
        <!-- Description -->
        <div class="form-group">
            <label for="description">Description:</label>
            <textarea name="description" id="description" placeholder="Describe the item in detail (color, brand, size, etc.)" required></textarea>
        </div>
        
        <button type="submit" name="submit" class="submit-btn">
            Submit Found Item Report
        </button>
    </form>
    
</div>

<script>
    // Set default date to today
    document.getElementById('date').valueAsDate = new Date();
    
    // Set default time to current time
    const now = new Date();
    const hours = now.getHours().toString().padStart(2, '0');
    const minutes = now.getMinutes().toString().padStart(2, '0');
    document.getElementById('time').value = `${hours}:${minutes}`;
</script>

</body>
</html>