<?php
require_once 'includes/config.php';

// Check if user is admin
if(!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

// Handle approve/reject actions
if(isset($_GET['action']) && isset($_GET['id']) && isset($_GET['type'])) {
    $action = $_GET['action'];
    $id = (int)$_GET['id'];
    $type = $_GET['type'];
    
    try {
        if($type == 'guide' && in_array($action, ['approve', 'reject'])) {
            $status = $action == 'approve' ? 'approved' : 'rejected';
            $stmt = $conn->prepare("UPDATE guides SET status = ? WHERE id = ?");
            $stmt->execute([$status, $id]);
            setMessage("Guide has been " . $status, "success");
        }
        redirect('admin-panel.php');
    } catch(PDOException $e) {
        setMessage("Action failed", "error");
    }
}

// Fetch statistics
try {
    $stmt = $conn->query("SELECT COUNT(*) as total FROM users WHERE role != 'admin'");
    $total_users = $stmt->fetch()['total'];
    
    $stmt = $conn->query("SELECT COUNT(*) as total FROM guides WHERE status = 'pending'");
    $pending_guides = $stmt->fetch()['total'];
    
    $stmt = $conn->query("SELECT COUNT(*) as total FROM guides WHERE status = 'approved'");
    $approved_guides = $stmt->fetch()['total'];
    
    $stmt = $conn->query("SELECT COUNT(*) as total FROM questions");
    $total_questions = $stmt->fetch()['total'];
    
    // Fetch pending guides
    $stmt = $conn->query("SELECT g.*, u.name as author FROM guides g JOIN users u ON g.user_id = u.id WHERE g.status = 'pending' ORDER BY g.created_at DESC LIMIT 10");
    $pending_guides_list = $stmt->fetchAll();
    
    // Fetch recent users
    $stmt = $conn->query("SELECT * FROM users WHERE role != 'admin' ORDER BY created_at DESC LIMIT 10");
    $recent_users = $stmt->fetchAll();
    
} catch(PDOException $e) {
    $total_users = 0;
    $pending_guides = 0;
    $approved_guides = 0;
    $total_questions = 0;
    $pending_guides_list = [];
    $recent_users = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header class="header">
        <div class="container">
            <nav class="nav">
                <div class="logo">
                    <a href="index.php" style="color: white;">🌱 <?php echo SITE_NAME; ?> - Admin</a>
                </div>
                <ul class="nav-links">
                    <li><a href="admin-panel.php">Admin Panel</a></li>
                    <li><a href="dashboard.php">My Dashboard</a></li>
                    <li><a href="logout.php" class="btn btn-secondary">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="dashboard">
            <!-- Sidebar -->
            <aside class="sidebar">
                <h3 style="margin-bottom: 1.5rem;">⚙️ Admin Menu</h3>
                <ul class="sidebar-nav">
                    <li><a href="admin-panel.php" class="active">📊 Overview</a></li>
                    <li><a href="#pending-guides">📝 Pending Guides</a></li>
                    <li><a href="#users">👥 Users</a></li>
                    <li><a href="#questions">💬 Questions</a></li>
                    <li><a href="#market-prices">💰 Market Prices</a></li>
                    <li><a href="dashboard.php">🏠 User Dashboard</a></li>
                    <li><a href="logout.php">🚪 Logout</a></li>
                </ul>
            </aside>

            <!-- Main Content -->
            <main class="main-content">
                <?php displayMessage(); ?>
                
                <h2>Admin Dashboard</h2>
                <p style="color: #666; margin-bottom: 2rem;">Manage ShambaSmart platform</p>

                <!-- Stats Grid -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3><?php echo $total_users; ?></h3>
                        <p>Total Users</p>
                    </div>
                    <div class="stat-card" style="background: linear-gradient(135deg, #ff6b6b, #c92a2a);">
                        <h3><?php echo $pending_guides; ?></h3>
                        <p>Pending Guides</p>
                    </div>
                    <div class="stat-card" style="background: linear-gradient(135deg, #4ecdc4, #44a8a0);">
                        <h3><?php echo $approved_guides; ?></h3>
                        <p>Approved Guides</p>
                    </div>
                    <div class="stat-card" style="background: linear-gradient(135deg, #ffe66d, #f9ca24);">
                        <h3><?php echo $total_questions; ?></h3>
                        <p>Total Questions</p>
                    </div>
                </div>

                <!-- Pending Guides -->
                <div id="pending-guides" style="margin-top: 3rem;">
                    <h3>📝 Pending Guides</h3>
                    <?php if(count($pending_guides_list) > 0): ?>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Author</th>
                                        <th>Category</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($pending_guides_list as $guide): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($guide['title']); ?></td>
                                            <td><?php echo htmlspecialchars($guide['author']); ?></td>
                                            <td><?php echo htmlspecialchars($guide['category']); ?></td>
                                            <td><?php echo date('M j, Y', strtotime($guide['created_at'])); ?></td>
                                            <td>
                                                <a href="?action=approve&type=guide&id=<?php echo $guide['id']; ?>" 
                                                   class="btn btn-primary" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;">
                                                    ✓ Approve
                                                </a>
                                                <a href="?action=reject&type=guide&id=<?php echo $guide['id']; ?>" 
                                                   class="btn" style="padding: 0.4rem 0.8rem; font-size: 0.85rem; background: #dc3545; color: white;"
                                                   onclick="return confirm('Reject this guide?');">
                                                    ✗ Reject
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p style="padding: 2rem; text-align: center; background: var(--gray-light); border-radius: 5px;">
                            No pending guides to review
                        </p>
                    <?php endif; ?>
                </div>

                <!-- Recent Users -->
                <div id="users" style="margin-top: 3rem;">
                    <h3>👥 Recent Users</h3>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>County</th>
                                    <th>Joined</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($recent_users as $user): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td style="text-transform: capitalize;"><?php echo htmlspecialchars($user['role']); ?></td>
                                        <td><?php echo htmlspecialchars($user['county'] ?? 'N/A'); ?></td>
                                        <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div style="margin-top: 3rem; padding: 2rem; background: #f0f9ff; border-radius: 10px;">
                    <h3 style="color: #0369a1;">Quick Actions</h3>
                    <div style="display: flex; gap: 1rem; margin-top: 1rem; flex-wrap: wrap;">
                        <button onclick="location.href='#market-prices'" class="btn btn-primary">💰 Update Prices</button>
                        <button onclick="location.href='#pending-guides'" class="btn btn-secondary">📝 Review Guides</button>
                        <button onclick="window.print()" class="btn btn-outline">🖨️ Print Report</button>
                    </div>
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