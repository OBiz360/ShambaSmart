<?php
require_once 'includes/config.php';

// Fetch all approved guides
$category_filter = isset($_GET['category']) ? clean($_GET['category']) : '';
$search = isset($_GET['search']) ? clean($_GET['search']) : '';

try {
    $sql = "SELECT g.*, u.name as author FROM guides g 
            JOIN users u ON g.user_id = u.id 
            WHERE g.status = 'approved'";
    
    $params = [];
    
    if($category_filter) {
        $sql .= " AND g.category = ?";
        $params[] = $category_filter;
    }
    
    if($search) {
        $sql .= " AND (g.title LIKE ? OR g.content LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    $sql .= " ORDER BY g.created_at DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $guides = $stmt->fetchAll();
    
    // Fetch categories
    $stmt = $conn->query("SELECT DISTINCT category FROM guides WHERE status = 'approved' ORDER BY category");
    $categories = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
} catch(PDOException $e) {
    $guides = [];
    $categories = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farming Guides - <?php echo SITE_NAME; ?></title>
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
                    <li><a href="view-guides.php">Guides</a></li>
                    <li><a href="view-questions.php">Q&A</a></li>
                    <?php if(isLoggedIn()): ?>
                        <li><a href="dashboard.php">Dashboard</a></li>
                        <li><a href="logout.php" class="btn btn-secondary">Logout</a></li>
                    <?php else: ?>
                        <li><a href="login.php" class="btn btn-primary">Login</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container" style="margin-top: 3rem; margin-bottom: 3rem;">
        <h1 style="text-align: center; margin-bottom: 1rem;">📚 Farming Guides</h1>
        <p style="text-align: center; color: #666; margin-bottom: 3rem;">
            Learn from expert guides and practical farming knowledge
        </p>

        <!-- Search and Filter -->
        <div style="background: white; padding: 2rem; border-radius: 10px; margin-bottom: 2rem; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
            <form method="GET" action="">
                <div style="display: grid; grid-template-columns: 2fr 1fr auto; gap: 1rem; align-items: end;">
                    <div class="form-group" style="margin: 0;">
                        <label for="search">Search Guides</label>
                        <input type="text" id="search" name="search" class="form-control" 
                               placeholder="Search by title or content..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="form-group" style="margin: 0;">
                        <label for="category">Filter by Category</label>
                        <select id="category" name="category" class="form-control">
                            <option value="">All Categories</option>
                            <?php foreach($categories as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat); ?>" 
                                        <?php echo $category_filter == $cat ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">🔍 Search</button>
                </div>
            </form>
            
            <?php if($category_filter || $search): ?>
                <div style="margin-top: 1rem;">
                    <a href="view-guides.php" class="btn btn-outline" style="padding: 0.5rem 1rem; font-size: 0.9rem;">
                        Clear Filters
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Results Count -->
        <p style="color: #666; margin-bottom: 2rem;">
            Found <?php echo count($guides); ?> guide<?php echo count($guides) != 1 ? 's' : ''; ?>
        </p>

        <!-- Guides Grid -->
        <?php if(count($guides) > 0): ?>
            <div class="card-grid">
                <?php foreach($guides as $guide): ?>
                    <div class="card">
                        <?php if($guide['image']): ?>
                            <img src="uploads/<?php echo htmlspecialchars($guide['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($guide['title']); ?>"
                                 style="width: 100%; height: 200px; object-fit: cover; border-radius: 5px; margin-bottom: 1rem;">
                        <?php else: ?>
                            <div style="width: 100%; height: 200px; background: linear-gradient(135deg, #90EE90, #228B22); border-radius: 5px; margin-bottom: 1rem; display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem;">
                                📚
                            </div>
                        <?php endif; ?>
                        
                        <div style="background: var(--primary-green); color: white; display: inline-block; padding: 0.3rem 0.8rem; border-radius: 20px; font-size: 0.85rem; margin-bottom: 0.5rem;">
                            <?php echo htmlspecialchars($guide['category']); ?>
                        </div>
                        
                        <h3><?php echo htmlspecialchars($guide['title']); ?></h3>
                        
                        <p style="color: #666; margin-bottom: 1rem;">
                            <?php echo substr(strip_tags($guide['content']), 0, 150); ?>...
                        </p>
                        
                        <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 1rem; border-top: 1px solid #eee;">
                            <div style="font-size: 0.9rem; color: #666;">
                                <strong>By:</strong> <?php echo htmlspecialchars($guide['author']); ?><br>
                                <strong>Views:</strong> <?php echo $guide['views']; ?>
                            </div>
                            <a href="view-guide-detail.php?id=<?php echo $guide['id']; ?>" class="btn btn-primary" style="padding: 0.5rem 1rem;">
                                Read More →
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 4rem 2rem; background: white; border-radius: 10px;">
                <div style="font-size: 4rem; margin-bottom: 1rem;">📭</div>
                <h3>No guides found</h3>
                <p style="color: #666;">Try adjusting your search or filters</p>
                <?php if(isLoggedIn()): ?>
                    <a href="submit-guide.php" class="btn btn-primary" style="margin-top: 1rem;">
                        Be the first to contribute!
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div>
                    <h4>🌱 <?php echo SITE_NAME; ?></h4>
                    <p>Empowering Kenyan agriculture through knowledge sharing.</p>
                </div>
                <div>
                    <h4>Quick Links</h4>
                    <ul class="footer-links">
                        <li><a href="view-guides.php">Browse Guides</a></li>
                        <li><a href="view-questions.php">Q&A</a></li>
                        <?php if(isLoggedIn()): ?>
                            <li><a href="submit-guide.php">Submit Guide</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="js/main.js"></script>
</body>
</html>