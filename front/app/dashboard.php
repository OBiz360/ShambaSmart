<?php
require_once 'includes/config.php';

// Check if user is logged in
if(!isLoggedIn()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$role = $_SESSION['role'];

// Fetch user's stats
try {
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM guides WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $my_guides = $stmt->fetch()['total'];
    
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM questions WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $my_questions = $stmt->fetch()['total'];
    
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM answers WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $my_answers = $stmt->fetch()['total'];
    
    // Fetch recent guides by user
    $stmt = $conn->prepare("SELECT * FROM guides WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
    $stmt->execute([$user_id]);
    $recent_guides = $stmt->fetchAll();
    
    // Fetch recent questions by user
    $stmt = $conn->prepare("SELECT * FROM questions WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
    $stmt->execute([$user_id]);
    $recent_questions = $stmt->fetchAll();
    
} catch(PDOException $e) {
    $my_guides = 0;
    $my_questions = 0;
    $my_answers = 0;
    $recent_guides = [];
    $recent_questions = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo SITE_NAME; ?></title>
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
                    <li><a href="partners.php">Partners</a></li>
                    <li><a href="logout.php" class="btn btn-secondary">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="dashboard">
            <!-- Sidebar -->
            <aside class="sidebar">
                <div style="text-align: center; margin-bottom: 2rem;">
                    <div style="width: 80px; height: 80px; border-radius: 50%; background: var(--primary-green); color: white; display: flex; align-items: center; justify-content: center; font-size: 2rem; margin: 0 auto 1rem;">
                        <?php echo strtoupper(substr($user_name, 0, 1)); ?>
                    </div>
                    <h3><?php echo htmlspecialchars($user_name); ?></h3>
                    <p style="color: #666; text-transform: capitalize;"><?php echo $role; ?></p>
                </div>
                
                <ul class="sidebar-nav">
                    <li><a href="dashboard.php" class="active">📊 Dashboard</a></li>
                    <li><a href="submit-guide.php">📝 Submit Guide</a></li>
                    <li><a href="ask-question.php">❓ Ask Question</a></li>
                    <li><a href="view-guides.php">📚 All Guides</a></li>
                    <li><a href="view-questions.php">💬 Q&A Forum</a></li>
                    <li><a href="partners.php">🤝 Partners</a></li>
                    <?php if(isAdmin()): ?>
                        <li><a href="admin-panel.php">⚙️ Admin Panel</a></li>
                    <?php endif; ?>
                    <li><a href="logout.php">🚪 Logout</a></li>
                </ul>
            </aside>

            <!-- Main Content -->
            <main class="main-content">
                <?php displayMessage(); ?>
                
                <h2>Welcome back, <?php echo htmlspecialchars($user_name); ?>! 👋</h2>
                <p style="color: #666; margin-bottom: 2rem;">Here's what's happening in your account</p>

                <!-- Stats Grid -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3><?php echo $my_guides; ?></h3>
                        <p>My Guides</p>
                    </div>
                    <div class="stat-card">
                        <h3><?php echo $my_questions; ?></h3>
                        <p>My Questions</p>
                    </div>
                    <div class="stat-card">
                        <h3><?php echo $my_answers; ?></h3>
                        <p>My Answers</p>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div style="margin: 2rem 0;">
                    <h3>Quick Actions</h3>
                    <div style="display: flex; gap: 1rem; margin-top: 1rem; flex-wrap: wrap;">
                        <a href="submit-guide.php" class="btn btn-primary">📝 Write a Guide</a>
                        <a href="ask-question.php" class="btn btn-secondary">❓ Ask a Question</a>
                        <a href="view-guides.php" class="btn btn-outline">📚 Browse Guides</a>
                    </div>
                </div>

                <!-- Recent Guides -->
                <div style="margin-top: 3rem;">
                    <h3>My Recent Guides</h3>
                    <?php if(count($recent_guides) > 0): ?>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Category</th>
                                        <th>Status</th>
                                        <th>Views</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($recent_guides as $guide): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($guide['title']); ?></td>
                                            <td><?php echo htmlspecialchars($guide['category']); ?></td>
                                            <td>
                                                <span style="padding: 0.3rem 0.8rem; border-radius: 20px; font-size: 0.85rem; 
                                                    background: <?php echo $guide['status'] == 'approved' ? '#d4edda' : ($guide['status'] == 'pending' ? '#fff3cd' : '#f8d7da'); ?>; 
                                                    color: <?php echo $guide['status'] == 'approved' ? '#155724' : ($guide['status'] == 'pending' ? '#856404' : '#721c24'); ?>;">
                                                    <?php echo ucfirst($guide['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo $guide['views']; ?></td>
                                            <td><?php echo date('M j, Y', strtotime($guide['created_at'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p style="color: #666; padding: 2rem; text-align: center; background: var(--gray-light); border-radius: 5px;">
                            You haven't submitted any guides yet. <a href="submit-guide.php">Submit your first guide</a>
                        </p>
                    <?php endif; ?>
                </div>

                <!-- Recent Questions -->
                <div style="margin-top: 3rem;">
                    <h3>My Recent Questions</h3>
                    <?php if(count($recent_questions) > 0): ?>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Question</th>
                                        <th>Category</th>
                                        <th>Status</th>
                                        <th>Views</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($recent_questions as $question): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($question['title']); ?></td>
                                            <td><?php echo htmlspecialchars($question['category']); ?></td>
                                            <td>
                                                <span style="padding: 0.3rem 0.8rem; border-radius: 20px; font-size: 0.85rem; 
                                                    background: <?php echo $question['status'] == 'answered' ? '#d4edda' : '#fff3cd'; ?>; 
                                                    color: <?php echo $question['status'] == 'answered' ? '#155724' : '#856404'; ?>;">
                                                    <?php echo ucfirst($question['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo $question['views']; ?></td>
                                            <td><?php echo date('M j, Y', strtotime($question['created_at'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p style="color: #666; padding: 2rem; text-align: center; background: var(--gray-light); border-radius: 5px;">
                            You haven't asked any questions yet. <a href="ask-question.php">Ask your first question</a>
                        </p>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <footer class="footer" style="margin-top: 4rem;">
        <div class="container">
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="js/main.js"></script>
</body>
</html>