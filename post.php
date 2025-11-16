<?php
require_once 'config/database.php';

// Sanitize and validate post id
$post_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($post_id <= 0) {
    die('Invalid post ID. Make sure you opened the post via its link (e.g. post.php?id=1).');
}

// Get post (only published)
try {
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ? AND status = 'published'");
    $stmt->execute([$post_id]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die('Database error: ' . $e->getMessage());
}

if (!$post) {
    die('Post not found or not published.');
}

// Increase views (ignore errors)
try {
    $pdo->prepare("UPDATE posts SET views = views + 1 WHERE id = ?")->execute([$post_id]);
} catch (Exception $e) {
    // non-fatal: continue showing the post even if increment fails
}

// Handle comment submission
$commentSuccess = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'], $_POST['email'], $_POST['comment'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $comment = trim($_POST['comment']);
    if ($name && $email && $comment) {
        try {
            $pdo->prepare("INSERT INTO comments (post_id, name, email, comment) VALUES (?, ?, ?, ?)")->execute([$post_id, $name, $email, $comment]);
            $commentSuccess = true;
        } catch (Exception $e) {
            // Error handling
        }
    }
}

// Get approved comments
$commentsStmt = $pdo->prepare("SELECT * FROM comments WHERE post_id = ? AND status = 'approved' ORDER BY created_at DESC");
$commentsStmt->execute([$post_id]);
$comments = $commentsStmt->fetchAll(PDO::FETCH_ASSOC);

function formattedDate(?string $date): string
{
    if (!$date) {
        return '—';
    }
    $timestamp = strtotime($date);
    return $timestamp ? date('F j, Y', $timestamp) : $date;
}

function formattedDateTime(?string $date): string
{
    if (!$date) {
        return '—';
    }
    $timestamp = strtotime($date);
    return $timestamp ? date('M j, Y \a\t g:i A', $timestamp) : $date;
}

$defaultImage = 'Macbook_Air_M2_Mockup_1.jpg';
function postImage(?string $image, string $fallback): string
{
    $filename = $image ?: $fallback;
    return htmlspecialchars($filename);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($post['title']); ?> - Onside Blog</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/site-theme.css">
</head>

<body>
    <div class="site-wrapper">
        <header class="site-header">
            <div class="container">
                <div class="navbar">
                    <a class="brand" href="index.php">
                        <span><i class="bi bi-lightning-charge-fill"></i></span>
                        Onside Stories
                    </a>
                    <nav class="nav-links">
                        <a href="index.php" class="active">Home</a>
                        <a href="index.php#insights">Stories</a>
                        <a href="index.php#insights">Insights</a>
                        <a href="admin/index.php">Admin</a>
                    </nav>
                </div>
            </div>
        </header>

        <main class="post-page">
            <div class="container">
                <div class="post-container">
                    <article class="post-article">
                        <div class="post-header">
                            <a href="index.php" class="back-link">
                                <i class="bi bi-arrow-left"></i>
                                <span>Back to Posts</span>
                            </a>
                            <h1 class="post-title"><?= htmlspecialchars($post['title']); ?></h1>
                            <div class="post-meta">
                                <span class="meta-item">
                                    <i class="bi bi-calendar3"></i>
                                    <?= formattedDate($post['created_at']); ?>
                                </span>
                                <span class="meta-item">
                                    <i class="bi bi-eye"></i>
                                    <?= number_format((int) $post['views']); ?> views
                                </span>
                            </div>
                        </div>

                        <?php if (!empty($post['image'])): ?>
                        <div class="post-image-wrapper">
                            <img src="uploads/<?= postImage($post['image'], $defaultImage); ?>" 
                                 alt="<?= htmlspecialchars($post['title']); ?>" 
                                 class="post-image">
                        </div>
                        <?php endif; ?>

                        <div class="post-content">
                            <?= nl2br(htmlspecialchars($post['content'])); ?>
                        </div>
                    </article>

                    <section class="comments-section">
                        <div class="section-header">
                            <h2>
                                <i class="bi bi-chat-dots"></i>
                                Comments
                                <?php if (count($comments) > 0): ?>
                                    <span class="comment-count">(<?= count($comments); ?>)</span>
                                <?php endif; ?>
                            </h2>
                        </div>

                        <?php if ($commentSuccess): ?>
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle"></i>
                            Your comment has been submitted and is pending approval.
                        </div>
                        <?php endif; ?>

                        <div class="comment-form-card">
                            <h3>Leave a Comment</h3>
                            <form method="POST" class="comment-form">
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="comment-name">
                                            <i class="bi bi-person"></i> Name
                                        </label>
                                        <input type="text" id="comment-name" name="name" class="form-control" 
                                               placeholder="Your name" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="comment-email">
                                            <i class="bi bi-envelope"></i> Email
                                        </label>
                                        <input type="email" id="comment-email" name="email" class="form-control" 
                                               placeholder="your.email@example.com" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="comment-text">
                                        <i class="bi bi-chat-text"></i> Comment
                                    </label>
                                    <textarea id="comment-text" name="comment" class="form-control" rows="4" 
                                              placeholder="Share your thoughts..." required></textarea>
                                </div>
                                <button type="submit" class="btn-primary">
                                    <i class="bi bi-send"></i>
                                    Post Comment
                                </button>
                            </form>
                        </div>

                        <div class="comments-list">
                            <?php if (empty($comments)): ?>
                                <div class="no-comments">
                                    <i class="bi bi-chat-left-text"></i>
                                    <p>No comments yet. Be the first to share your thoughts!</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($comments as $comment): ?>
                                <div class="comment-card">
                                    <div class="comment-header">
                                        <div class="comment-author">
                                            <div class="author-avatar">
                                                <i class="bi bi-person-circle"></i>
                                            </div>
                                            <div>
                                                <strong class="author-name"><?= htmlspecialchars($comment['name']); ?></strong>
                                                <span class="comment-date">
                                                    <i class="bi bi-clock"></i>
                                                    <?= formattedDateTime($comment['created_at']); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="comment-body">
                                        <?= nl2br(htmlspecialchars($comment['comment'])); ?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </section>
                </div>
            </div>
        </main>

        <footer class="site-footer">
            <div class="container">
                <p>&copy; 2025 Onside Stories. All rights reserved.</p>
            </div>
        </footer>
    </div>
</body>

</html>