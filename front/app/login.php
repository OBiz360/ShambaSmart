<?php
require_once 'includes/config.php';

// If user is logged in, redirect to dashboard
if(isLoggedIn()) {
    redirect('dashboard.php');
}

$errors = [];

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = clean($_POST['email']);
    $password = $_POST['password'];
    
    // Validation
    if(empty($email) || empty($password)) {
        $errors[] = "Email and password are required";
    }
    
    if(empty($errors)) {
        try {
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if($user && password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['profile_image'] = $user['profile_image'];
                
                // Redirect based on role
                if($user['role'] === 'admin') {
                    redirect('admin-panel.php');
                } else {
                    redirect('dashboard.php');
                }
            } else {
                $errors[] = "Invalid email or password";
            }
        } catch(PDOException $e) {
            $errors[] = "Login failed. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo SITE_NAME; ?></title>
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
                    <li><a href="register.php">Register</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="form-container">
        <h2 style="text-align: center; color: var(--primary-green);">Welcome Back!</h2>
        <p style="text-align: center; margin-bottom: 2rem;">Login to your ShambaSmart account</p>
        
        <?php displayMessage(); ?>
        
        <?php if(!empty($errors)): ?>
            <div class="alert alert-error">
                <?php foreach($errors as $error): ?>
                    <p><?php echo $error; ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" 
                       value="<?php echo $_POST['email'] ?? ''; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            
            <div class="form-group" style="display: flex; justify-content: space-between; align-items: center;">
                <label style="display: flex; align-items: center; gap: 0.5rem;">
                    <input type="checkbox" name="remember"> Remember me
                </label>
                <a href="#" style="font-size: 0.9rem;">Forgot password?</a>
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
                Login
            </button>
        </form>
        
        <div style="text-align: center; margin: 1.5rem 0;">
            <p style="color: #666;">Demo Credentials:</p>
            <p style="font-size: 0.9rem;">Admin: admin@shambasmart.com / admin123</p>
        </div>
        
        <p style="text-align: center; margin-top: 1.5rem;">
            Don't have an account? <a href="register.php">Register here</a>
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