<?php
include 'includes/header.php';
include 'includes/sidebar.php';
require_once '../config/database.php';

// الأكشنز هنا قبل أي إخراج HTML
if (isset($_GET['action'], $_GET['id'])) {
    $comment_id = (int)$_GET['id'];
    $action = $_GET['action'];

    switch ($action) {
        case 'approve':
            $stmt = $pdo->prepare("UPDATE comments SET status = 'approved' WHERE id = ?");
            $stmt->execute([$comment_id]);
            break;
        case 'reject':
            $stmt = $pdo->prepare("UPDATE comments SET status = 'rejected' WHERE id = ?");
            $stmt->execute([$comment_id]);
            break;
        case 'delete':
            $stmt = $pdo->prepare("DELETE FROM comments WHERE id = ?");
            $stmt->execute([$comment_id]);
            break;
    }

    header("Location: comments.php");
    exit();
}


// Initialize variables
$message = '';
$message_type = '';

// Fetch all comments
$stmt = $pdo->query("SELECT * FROM `comments`");
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get comment counts
$total_comments = count($comments);
$pending_comments = $pdo->query("SELECT COUNT(*) FROM comments WHERE status = 'pending'")->fetchColumn();
$approved_comments = $pdo->query("SELECT COUNT(*) FROM comments WHERE status = 'approved'")->fetchColumn();
$rejected_comments = $pdo->query("SELECT COUNT(*) FROM comments WHERE status = 'rejected'")->fetchColumn();
?>

<main class="col-md-9 ms-sm-auto col-lg-10 dashboard-main">
    <div class="page-heading mb-4">
        <div>
            <h1>
                <i class="bi bi-chat-dots"></i>
                Comments Management
            </h1>
            <p class="page-subtitle">
                Moderate the conversation and keep your community healthy and engaged.
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="posts.php" class="btn btn-outline-secondary btn-sm px-3 shadow-sm">
                <i class="bi bi-journal-text me-1"></i>
                Go to Posts
            </a>
        </div>
    </div>

    <!-- Inline notifications -->
    <?php if ($message): ?>
        <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show shadow-sm rounded-pill px-4" role="alert">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="glass-card h-100">
                <div class="icon-wrapper icon-bg-purple">
                    <i class="bi bi-chat-text"></i>
                </div>
                <span class="card-label">Total Comments</span>
                <span class="card-value"><?php echo number_format($total_comments); ?></span>
                <span class="trend-indicator text-muted">
                    <i class="bi bi-people"></i>
                    Across all posts
                </span>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="glass-card h-100">
                <div class="icon-wrapper icon-bg-yellow">
                    <i class="bi bi-hourglass"></i>
                </div>
                <span class="card-label">Pending Review</span>
                <span class="card-value"><?php echo number_format($pending_comments); ?></span>
                <span class="trend-indicator text-muted">
                    <i class="bi bi-inbox"></i>
                    Requires action
                </span>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="glass-card h-100">
                <div class="icon-wrapper icon-bg-green">
                    <i class="bi bi-patch-check"></i>
                </div>
                <span class="card-label">Approved</span>
                <span class="card-value"><?php echo number_format($approved_comments); ?></span>
                <span class="trend-indicator trend-up">
                    <i class="bi bi-arrow-up-right"></i>
                    Published live
                </span>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="glass-card h-100">
                <div class="icon-wrapper icon-bg-pink">
                    <i class="bi bi-slash-circle"></i>
                </div>
                <span class="card-label">Rejected</span>
                <span class="card-value"><?php echo number_format($rejected_comments); ?></span>
                <span class="trend-indicator trend-down">
                    <i class="bi bi-shield-exclamation"></i>
                    Flagged items
                </span>
            </div>
        </div>
    </div>

    <!-- Comments Table -->
    <div class="content-card">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <h2 class="mb-0">All Comments</h2>
            <span class="text-muted small">
                <?= date('l, M j, Y'); ?>
            </span>
        </div>
        <div class="table-responsive">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Comment</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Dynamic Comments -->
                    <?php foreach ($comments as $comment): ?>
                    <tr>
                        <td><?php echo $comment['id']; ?></td>
                        <td><?php echo htmlspecialchars($comment['name']); ?></td>
                        <td><?php echo htmlspecialchars($comment['comment']); ?></td>
                        <td>
                            <?php
                            $badgeClass = 'status-badge status-pending';
                            if ($comment['status'] === 'approved') {
                                $badgeClass = 'status-badge status-approved';
                            } elseif ($comment['status'] === 'rejected') {
                                $badgeClass = 'status-badge status-rejected';
                            }
                            ?>
                            <span class="<?php echo $badgeClass; ?>">
                                <i class="bi bi-circle-fill"></i>
                                <?php echo ucfirst($comment['status']); ?>
                            </span>
                        </td>
                        <td><?php echo date('M j, Y', strtotime($comment['created_at'])); ?></td>
                        <td>
                            <div class="d-flex gap-2 flex-wrap">
                                <?php if ($comment['status'] != 'approved'): ?>
                                <a href="comments.php?action=approve&id=<?php echo $comment['id']; ?>"
                                    class="btn btn-outline-success btn-sm rounded-pill px-3">
                                    <i class="bi bi-check"></i>
                                    <span class="ms-1">Approve</span>
                                </a>
                                <?php endif; ?>

                                <?php if ($comment['status'] != 'rejected'): ?>
                                <a href="comments.php?action=reject&id=<?php echo $comment['id']; ?>"
                                    class="btn btn-outline-warning btn-sm rounded-pill px-3">
                                    <i class="bi bi-x"></i>
                                    <span class="ms-1">Reject</span>
                                </a>
                                <?php endif; ?>

                                <a href="comments.php?action=delete&id=<?php echo $comment['id']; ?>"
                                    class="btn btn-outline-danger btn-sm rounded-pill px-3" onclick="return confirm('Delete this comment?')">
                                    <i class="bi bi-trash"></i>
                                    <span class="ms-1">Delete</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</main>

<?php include 'includes/footer.php'; ?>
