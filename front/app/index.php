<?php
require_once 'includes/config.php';

// If user is logged in, redirect to dashboard
if(isLoggedIn()) {
    redirect('dashboard.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - Kenyan Agribusiness Learning Hub</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <nav class="nav">
                <div class="logo">
                    🌱 <?php echo SITE_NAME; ?>
                </div>
                <ul class="nav-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="view-guides.php">Guides</a></li>
                    <li><a href="view-questions.php">Q&A</a></li>
                    <li><a href="partners.php">Partners</a></li>
                    <li><a href="login.php" class="btn btn-primary">Login</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>🌾 Welcome to ShambaSmart</h1>
            <p>Empowering Kenyan Farmers, Students & Agripreneurs with Knowledge</p>
            <div class="hero-buttons">
                <a href="register.php" class="btn btn-primary">Join the Community</a>
                <a href="view-guides.php" class="btn btn-secondary">Explore Guides</a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="container" style="margin-top: 4rem;">
        <h2 style="text-align: center; margin-bottom: 3rem;">What We Offer</h2>
        <div class="card-grid">
            <div class="card">
                <div class="card-icon">📚</div>
                <h3>Learning Guides</h3>
                <p>Access comprehensive farming guides written by experts and verified farmers across Kenya.</p>
            </div>
            <div class="card">
                <div class="card-icon">💬</div>
                <h3>Q&A Community</h3>
                <p>Ask questions, share experiences, and get answers from experienced farmers and agronomists.</p>
            </div>
            <div class="card">
                <div class="card-icon">📊</div>
                <h3>Market Prices</h3>
                <p>Stay updated with real-time market prices for crops across different counties in Kenya.</p>
            </div>
            <div class="card">
                <div class="card-icon">🤝</div>
                <h3>Partner Network</h3>
                <p>Connect with NGOs, government agencies, and agribusiness organizations for support.</p>
            </div>
            <div class="card">
                <div class="card-icon">🌍</div>
                <h3>Local Knowledge</h3>
                <p>Learn from farming practices tailored to Kenyan climate, soil, and market conditions.</p>
            </div>
            <div class="card">
                <div class="card-icon">📱</div>
                <h3>Mobile Friendly</h3>
                <p>Access ShambaSmart from any device - desktop, tablet, or smartphone.</p>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="container" style="margin: 4rem auto;">
        <h2 style="text-align: center; margin-bottom: 3rem;">Our Impact</h2>
        <div class="stats-grid">
            <?php
            // Fetch real stats from database
            try {
                $stmt = $conn->query("SELECT COUNT(*) as total FROM users WHERE role != 'admin'");
                $users = $stmt->fetch()['total'];
                
                $stmt = $conn->query("SELECT COUNT(*) as total FROM guides WHERE status = 'approved'");
                $guides = $stmt->fetch()['total'];
                
                $stmt = $conn->query("SELECT COUNT(*) as total FROM questions");
                $questions = $stmt->fetch()['total'];
            } catch(PDOException $e) {
                $users = 0;
                $guides = 0;
                $questions = 0;
            }
            ?>
            <div class="stat-card">
                <h3><?php echo $users; ?>+</h3>
                <p>Community Members</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $guides; ?>+</h3>
                <p>Farming Guides</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $questions; ?>+</h3>
                <p>Questions Answered</p>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="hero" style="padding: 3rem 0;">
        <div class="container">
            <h2>Ready to Join Our Community?</h2>
            <p>Start learning, sharing, and growing with ShambaSmart today!</p>
            <a href="register.php" class="btn btn-secondary" style="margin-top: 1rem;">Get Started Free</a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div>
                    <h4>🌱 <?php echo SITE_NAME; ?></h4>
                    <p>Empowering Kenyan agriculture through knowledge sharing and community collaboration.</p>
                </div>
                <div>
                    <h4>Quick Links</h4>
                    <ul class="footer-links">
                        <li><a href="view-guides.php">Farming Guides</a></li>
                        <li><a href="view-questions.php">Q&A Community</a></li>
                        <li><a href="partners.php">Our Partners</a></li>
                        <li><a href="register.php">Join Now</a></li>
                    </ul>
                </div>
                <div>
                    <h4>Resources</h4>
                    <ul class="footer-links">
                        <li><a href="#">Market Prices</a></li>
                        <li><a href="#">Extension Services</a></li>
                        <li><a href="#">Weather Updates</a></li>
                        <li><a href="#">Crop Calendar</a></li>
                    </ul>
                </div>
                <div>
                    <h4>Contact</h4>
                    <ul class="footer-links">
                        <li>📧 info@shambasmart.com</li>
                        <li>📱 +254 700 000 000</li>
                        <li>📍 Nairobi, Kenya</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved. | Built for Kenyan Farmers 🇰🇪</p>
            </div>
        </div>
    </footer>

    <script src="js/main.js"></script>
</body>
</html>