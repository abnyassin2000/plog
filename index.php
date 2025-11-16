<?php
require_once "./config/database.php";

function fetchPosts(PDO $pdo): array
{
    try {
        $stmt = $pdo->prepare("SELECT id, title, content, image, created_at, status FROM `posts` WHERE status = 'published' ORDER BY created_at DESC");
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $th) {
        return [];
    }
}

function excerpt(string $text, int $limit = 160): string
{
    $clean = trim(strip_tags($text));

    if (function_exists('mb_strimwidth')) {
        return mb_strimwidth($clean, 0, $limit, '…', 'UTF-8');
    }

    return strlen($clean) > $limit ? substr($clean, 0, $limit - 1) . '…' : $clean;
}

function formattedDate(?string $date): string
{
    if (!$date) {
        return '—';
    }

    $timestamp = strtotime($date);

    return $timestamp ? date('M j, Y', $timestamp) : $date;
}

$defaultImage = 'Macbook_Air_M2_Mockup_1.jpg';

function postImage(?string $image, string $fallback): string
{
    $filename = $image ?: $fallback;

    return htmlspecialchars($filename);
}

$posts = fetchPosts($pdo);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Onside Blog</title>
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
                        <a href="#insights">Stories</a>
                        <a href="#insights">Insights</a>
                        <a href="admin/index.php">Admin</a>
                    </nav>
                </div>
                <div class="hero-content">
                    <div class="hero-text">
                        <div class="hero-highlight">
                            <span><i class="bi bi-stars"></i> Featured community voices</span>
                            <span>Curated weekly</span>
                        </div>
                        <h1>Stories that power the game on and off the field.</h1>
                        <p>
                            Dive into tactical analyses, player journeys, and behind-the-scenes stories written by
                            passionate voices from the Onside community.
                        </p>
                        <div class="hero-actions">
                            <a class="btn-primary-gradient" href="#insights">
                                Explore latest articles
                                <i class="bi bi-arrow-right"></i>
                            </a>
                            <a class="btn-outline-light" href="#insights">
                                Discover insights
                                <i class="bi bi-compass"></i>
                            </a>
                        </div>
                    </div>
                    <div class="hero-visual">
                        <img src="uploads/<?= postImage($posts[0]['image'] ?? null, $defaultImage); ?>" alt="Featured story cover">
                        <div class="hero-bubble top">
                            <i class="bi bi-graph-up-arrow"></i>
                            Trending stories
                        </div>
                        <div class="hero-bubble bottom">
                            <i class="bi bi-people-fill"></i>
                            <?= str_pad((string) count($posts), 2, '0', STR_PAD_LEFT); ?> featured writers
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main class="main-content">
            <div class="container">
                <div class="section-heading" id="insights">
                    <h2>Latest insights from the pitch</h2>
                    <p>Stay informed with in-depth analyses, fresh perspectives, and thoughtful commentary.</p>
                </div>

                <div class="posts-grid">
                    <?php if (!empty($posts)): ?>
                        <?php foreach ($posts as $post): ?>
                            <article class="post-card">
                                <div class="post-card__image">
                                    <img src="uploads/<?= postImage($post['image'] ?? null, $defaultImage); ?>" alt="<?= htmlspecialchars($post['title']); ?>">
                                </div>
                                <div class="post-card__body">
                                    <div class="post-card__meta">
                                        <span>
                                            <i class="bi bi-clock-history"></i>
                                            <?= formattedDate($post['created_at']); ?>
                                        </span>
                                        <span>
                                            <i class="bi bi-journal-richtext"></i>
                                            Feature
                                        </span>
                                    </div>
                                    <h3 class="post-card__title">
                                        <?= htmlspecialchars($post['title']); ?>
                                    </h3>
                                    <p class="post-card__excerpt">
                                        <?= htmlspecialchars(excerpt($post['content'])); ?>
                                    </p>
                                    <div class="post-card__footer">
                                        <a class="btn-read-more" href="./post.php?id=<?= $post['id']; ?>">
                                            Read more
                                            <i class="bi bi-arrow-right-short"></i>
                                        </a>
                                        <time datetime="<?= htmlspecialchars($post['created_at'] ?? ''); ?>">
                                            <?= formattedDate($post['created_at']); ?>
                                        </time>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted text-center">No stories published yet. Check back soon!</p>
                    <?php endif; ?>
                </div>
            </div>
        </main>

        <footer class="site-footer">
            <div class="container">
                <strong>Onside</strong>
                <p>&copy; <?= date('Y'); ?> Onside Blog. Crafted with passion for the beautiful game.</p>
            </div>
        </footer>
    </div>
</body>

</html>