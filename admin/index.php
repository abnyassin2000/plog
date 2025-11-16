<?php
require_once '../config/database.php';

function fetchCount(PDO $pdo, string $sql, array $params = []): int
{
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    } catch (Throwable $th) {
        return 0;
    }
}

function fetchRows(PDO $pdo, string $sql, array $params = []): array
{
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $th) {
        return [];
    }
}

function excerpt(string $text, int $limit = 120): string
{
    $clean = trim(strip_tags($text));

    if (function_exists('mb_strimwidth')) {
        return mb_strimwidth($clean, 0, $limit, '…', 'UTF-8');
    }

    return strlen($clean) > $limit
        ? substr($clean, 0, max(0, $limit - 1)) . '…'
        : $clean;
}

$stats = [
    'total_posts' => fetchCount($pdo, "SELECT COUNT(*) FROM posts"),
    'published_posts' => fetchCount($pdo, "SELECT COUNT(*) FROM posts WHERE status = 'published'"),
    'draft_posts' => fetchCount($pdo, "SELECT COUNT(*) FROM posts WHERE status = 'draft'"),
    'total_categories' => fetchCount($pdo, "SELECT COUNT(*) FROM categories"),
    'total_comments' => fetchCount($pdo, "SELECT COUNT(*) FROM comments"),
    'pending_comments' => fetchCount($pdo, "SELECT COUNT(*) FROM comments WHERE status = 'pending'"),
];

$recentPosts = fetchRows(
    $pdo,
    "SELECT id, title, status, views, created_at 
     FROM posts 
     ORDER BY created_at DESC 
     LIMIT 5"
);

$recentComments = fetchRows(
    $pdo,
    "SELECT id, name, comment, status, created_at 
     FROM comments 
     ORDER BY created_at DESC 
     LIMIT 5"
);

include 'includes/header.php';
include 'includes/sidebar.php';
?>
<!-- Main Content Column -->
<main class="col-md-9 ms-sm-auto col-lg-10 dashboard-main">
    <div class="page-heading mb-4">
        <div>
            <h1>
                <i class="bi bi-speedometer2"></i>
                Dashboard Overview
            </h1>
            <p class="page-subtitle">
                A quick summary of your content performance and moderation queue.
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="addpost.php" class="btn btn-light btn-sm px-3 shadow-sm">
                <i class="bi bi-pencil-square me-1"></i>
                New Post
            </a>
            <a href="comments.php" class="btn btn-primary btn-sm px-3 shadow-sm">
                <i class="bi bi-chat-text me-1"></i>
                Review Comments
            </a>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="glass-card h-100">
                <div class="icon-wrapper icon-bg-purple">
                    <i class="bi bi-collection-play"></i>
                </div>
                <span class="card-label">Published Posts</span>
                <span class="card-value">
                    <?= number_format($stats['published_posts']); ?>
                </span>
                <span class="trend-indicator trend-up">
                    <i class="bi bi-arrow-up-right"></i>
                    <?= number_format($stats['total_posts']); ?> total
                </span>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="glass-card h-100">
                <div class="icon-wrapper icon-bg-green">
                    <i class="bi bi-folder2-open"></i>
                </div>
                <span class="card-label">Categories</span>
                <span class="card-value">
                    <?= number_format($stats['total_categories']); ?>
                </span>
                <span class="trend-indicator text-muted">
                    <i class="bi bi-diagram-3"></i>
                    Organized content
                </span>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="glass-card h-100">
                <div class="icon-wrapper icon-bg-yellow">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <span class="card-label">Drafts Pending</span>
                <span class="card-value">
                    <?= number_format($stats['draft_posts']); ?>
                </span>
                <span class="trend-indicator text-muted">
                    <i class="bi bi-journal-richtext"></i>
                    Ready for review
                </span>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="glass-card h-100">
                <div class="icon-wrapper icon-bg-pink">
                    <i class="bi bi-chat-dots"></i>
                </div>
                <span class="card-label">Comments Queue</span>
                <span class="card-value">
                    <?= number_format($stats['pending_comments']); ?>
                </span>
                <span class="trend-indicator trend-down">
                    <i class="bi bi-inbox"></i>
                    <?= number_format($stats['total_comments']); ?> total
                </span>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="content-card h-100">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                    <h2 class="mb-0">Latest Posts</h2>
                    <a href="posts.php" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                        View all
                    </a>
                </div>
                <?php if (!empty($recentPosts)): ?>
                    <div class="table-responsive">
                        <table class="table-modern">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Status</th>
                                    <th>Views</th>
                                    <th>Published</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentPosts as $post): ?>
                                    <tr>
                                        <td>
                                            <strong><?= htmlspecialchars($post['title']); ?></strong>
                                        </td>
                                        <td>
                                            <?php
                                            $statusClass = 'status-badge status-pending';
                                            if ($post['status'] === 'published') {
                                                $statusClass = 'status-badge status-approved';
                                            } elseif ($post['status'] === 'draft') {
                                                $statusClass = 'status-badge status-pending';
                                            } else {
                                                $statusClass = 'status-badge status-rejected';
                                            }
                                            ?>
                                            <span class="<?= $statusClass; ?>">
                                                <i class="bi bi-circle-fill"></i>
                                                <?= ucfirst($post['status']); ?>
                                            </span>
                                        </td>
                                        <td><?= number_format((int) ($post['views'] ?? 0)); ?></td>
                                        <td>
                                            <?= $post['created_at'] ? date('M j, Y', strtotime($post['created_at'])) : '—'; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-0">No posts found. Start by creating a new post.</p>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="content-card h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0">Moderation Queue</h2>
                    <a href="comments.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                        Manage
                    </a>
                </div>
                <?php if (!empty($recentComments)): ?>
                    <div class="d-flex flex-column gap-3">
                        <?php foreach ($recentComments as $comment): ?>
                            <div class="p-3 rounded-4" style="background: rgba(255, 255, 255, 0.6); border: 1px solid rgba(114, 91, 255, 0.12);">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <strong><?= htmlspecialchars($comment['name']); ?></strong>
                                        <div class="text-muted small">
                                            <?= $comment['created_at'] ? date('M j, Y • g:i A', strtotime($comment['created_at'])) : '—'; ?>
                                        </div>
                                    </div>
                                    <?php
                                    $commentStatusClass = 'status-badge status-pending';
                                    if ($comment['status'] === 'approved') {
                                        $commentStatusClass = 'status-badge status-approved';
                                    } elseif ($comment['status'] === 'rejected') {
                                        $commentStatusClass = 'status-badge status-rejected';
                                    }
                                    ?>
                                    <span class="<?= $commentStatusClass; ?>">
                                        <i class="bi bi-circle-fill"></i>
                                        <?= ucfirst($comment['status']); ?>
                                    </span>
                                </div>
                                <p class="mb-0 text-secondary" style="line-height: 1.5;">
                                    <?= htmlspecialchars(excerpt($comment['comment'])); ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-0">No comments yet. Engagement will appear here once people interact.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>
<?php include 'includes/footer.php'; ?>