<?php
require_once 'includes/config.php';

if(!isLoggedIn()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];
$errors = [];

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = clean($_POST['title']);
    $category = clean($_POST['category']);
    $content = $_POST['content']; // Don't strip tags from content
    
    // Validation
    if(empty($title)) {
        $errors[] = "Title is required";
    }
    if(empty($category)) {
        $errors[] = "Category is required";
    }
    if(empty($content)) {
        $errors[] = "Content is required";
    }
    
    // Handle image upload
    $image_path = null;
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if(in_array($ext, $allowed)) {
            $new_filename = uniqid() . '.' . $ext;
            $upload_path = UPLOAD_DIR . $new_filename;
            
            if(move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $image_path = $new_filename;
            }
        } else {
            $errors[] = "Invalid image format. Allowed: JPG, PNG, GIF";
        }
    }
    
    // If no errors, save guide
    if(empty($errors)) {
        try {
            $stmt = $conn->prepare("INSERT INTO guides (user_id, title, category, content, image, status) VALUES (?, ?, ?, ?, ?, 'pending')");
            $stmt->execute([$user_id, $title, $category, $content, $image_path]);
            
            setMessage("Guide submitted successfully! It will be reviewed by admins.", "success");
            redirect('dashboard.php');
        } catch(PDOException $e) {
            $errors[] = "Failed to submit guide. Please try again.";
        }
    }
}

$categories = ['Crop Farming', 'Livestock', 'Poultry', 'Horticulture', 'Irrigation', 'Soil Management', 'Pest Control', 'Marketing', 'Agribusiness', 'Technology', 'Other'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Guide - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header class="header">
        <div class="container">
            <nav class="nav">
                <div class="logo">
                    <a href="index.php" style="color: white;">🌱 <?php echo SITE_NAME; ?></a>
                </div>
                <ul class="nav-links">
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="view-guides.php">Guides</a></li>
                    <li><a href="view-questions.php">Q&A</a></li>
                    <li><a href="logout.php" class="btn btn-secondary">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container" style="max-width: 800px; margin-top: 3rem; margin-bottom: 3rem;">
        <div style="background: white; padding: 3rem; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
            <h2>📝 Submit a Farming Guide</h2>
            <p style="color: #666; margin-bottom: 2rem;">Share your farming knowledge with the community</p>
            
            <?php if(!empty($errors)): ?>
                <div class="alert alert-error">
                    <?php foreach($errors as $error): ?>
                        <p><?php echo $error; ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Guide Title *</label>
                    <input type="text" id="title" name="title" class="form-control" 
                           placeholder="e.g., How to Grow Tomatoes in Dry Season" 
                           value="<?php echo $_POST['title'] ?? ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="category">Category *</label>
                    <select id="category" name="category" class="form-control" required>
                        <option value="">Select category...</option>
                        <?php foreach($categories as $cat): ?>
                            <option value="<?php echo $cat; ?>" <?php echo (isset($_POST['category']) && $_POST['category'] == $cat) ? 'selected' : ''; ?>>
                                <?php echo $cat; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="image">Featured Image (Optional)</label>
                    <input type="file" id="image" name="image" class="form-control" accept="image/*">
                    <small style="color: #666;">Recommended size: 800x400px. Max 2MB</small>
                </div>
                
                <div class="form-group">
                    <label for="content">Guide Content *</label>
                    <textarea id="content" name="content" class="form-control" rows="15" 
                              placeholder="Write your comprehensive guide here. Include steps, tips, and best practices..." required><?php echo $_POST['content'] ?? ''; ?></textarea>
                    <small style="color: #666;">Be detailed and clear. Include examples and practical tips.</small>
                </div>
                
                <div style="background: #f0f9ff; padding: 1.5rem; border-radius: 5px; margin-bottom: 2rem;">
                    <h4 style="color: #0369a1; margin-bottom: 0.5rem;">📋 Guide Writing Tips:</h4>
                    <ul style="color: #666; margin-left: 1.5rem;">
                        <li>Start with an introduction explaining what the guide covers</li>
                        <li>Use clear headings and bullet points for easy reading</li>
                        <li>Include specific measurements, timelines, and costs where relevant</li>
                        <li>Share personal experiences and lessons learned</li>
                        <li>Mention common mistakes to avoid</li>
                        <li>Consider the Kenyan context (climate, markets, resources)</li>
                    </ul>
                </div>
                
                <div style="display: flex; gap: 1rem;">
                    <button type="submit" class="btn btn-primary">Submit Guide for Review</button>
                    <a href="dashboard.php" class="btn btn-outline">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>