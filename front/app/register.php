<?php
require_once 'includes/config.php';

// If user is logged in, redirect to dashboard
if(isLoggedIn()) {
    redirect('dashboard.php');
}

$errors = [];
$success = '';

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = clean($_POST['name']);
    $email = clean($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = clean($_POST['role']);
    $county = clean($_POST['county']);
    $phone = clean($_POST['phone']);
    
    // Validation
    if(empty($name)) {
        $errors[] = "Name is required";
    }
    
    if(empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required";
    }
    
    if(empty($password) || strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters";
    }
    
    if($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }
    
    if(empty($role)) {
        $errors[] = "Please select your role";
    }
    
    // Check if email already exists
    if(empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if($stmt->fetch()) {
            $errors[] = "Email already registered";
        }
    }
    
    // If no errors, register user
    if(empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        try {
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, county, phone) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $hashed_password, $role, $county, $phone]);
            
            setMessage("Registration successful! Please login to continue.", "success");
            redirect('login.php');
        } catch(PDOException $e) {
            $errors[] = "Registration failed. Please try again.";
        }
    }
}

// Kenyan counties
$counties = ['Nairobi', 'Mombasa', 'Kisumu', 'Nakuru', 'Eldoret', 'Kiambu', 'Murang\'a', 'Nyeri', 'Meru', 'Embu', 'Machakos', 'Makueni', 'Kitui', 'Kajiado', 'Laikipia', 'Nyandarua', 'Kirinyaga', 'Tharaka Nithi', 'Kakamega', 'Bungoma', 'Busia', 'Vihiga', 'Siaya', 'Kisii', 'Nyamira', 'Migori', 'Homa Bay', 'Bomet', 'Kericho', 'Narok', 'Trans Nzoia', 'Uasin Gishu', 'Elgeyo Marakwet', 'Nandi', 'Baringo', 'West Pokot', 'Samburu', 'Turkana', 'Marsabit', 'Isiolo', 'Garissa', 'Wajir', 'Mandera', 'Lamu', 'Tana River', 'Taita Taveta', 'Kwale', 'Kilifi'];
sort($counties);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - <?php echo SITE_NAME; ?></title>
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
                    <li><a href="index.php">Home</a></li>
                    <li><a href="login.php">Login</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="form-container">
        <h2 style="text-align: center; color: var(--primary-green);">Join ShambaSmart</h2>
        <p style="text-align: center; margin-bottom: 2rem;">Create your account and start learning today!</p>
        
        <?php if(!empty($errors)): ?>
            <div class="alert alert-error">
                <?php foreach($errors as $error): ?>
                    <p><?php echo $error; ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="name">Full Name *</label>
                <input type="text" id="name" name="name" class="form-control" 
                       value="<?php echo $_POST['name'] ?? ''; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email Address *</label>
                <input type="email" id="email" name="email" class="form-control" 
                       value="<?php echo $_POST['email'] ?? ''; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" class="form-control" 
                       placeholder="+254..." value="<?php echo $_POST['phone'] ?? ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="role">I am a *</label>
                <select id="role" name="role" class="form-control" required>
                    <option value="">Select your role...</option>
                    <option value="farmer" <?php echo (isset($_POST['role']) && $_POST['role'] == 'farmer') ? 'selected' : ''; ?>>Farmer</option>
                    <option value="student" <?php echo (isset($_POST['role']) && $_POST['role'] == 'student') ? 'selected' : ''; ?>>Student</option>
                    <option value="enthusiast" <?php echo (isset($_POST['role']) && $_POST['role'] == 'enthusiast') ? 'selected' : ''; ?>>Agribusiness Enthusiast</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="county">County</label>
                <select id="county" name="county" class="form-control">
                    <option value="">Select your county...</option>
                    <?php foreach($counties as $c): ?>
                        <option value="<?php echo $c; ?>" <?php echo (isset($_POST['county']) && $_POST['county'] == $c) ? 'selected' : ''; ?>>
                            <?php echo $c; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="password">Password *</label>
                <input type="password" id="password" name="password" class="form-control" 
                       minlength="6" required>
                <small style="color: #666;">Minimum 6 characters</small>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password *</label>
                <input type="password" id="confirm_password" name="confirm_password" 
                       class="form-control" minlength="6" required>
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
                Register Now
            </button>
        </form>
        
        <p style="text-align: center; margin-top: 1.5rem;">
            Already have an account? <a href="login.php">Login here</a>
        </p>
    </div>

    <footer class="footer" style="margin-top: 3rem;">
        <div class="container">
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>