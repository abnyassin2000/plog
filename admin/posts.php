<?php
// Database connection
require_once '../config/database.php';
include './includes/header.php';
include './includes/sidebar.php';
// Initialize variables
if (isset($_GET['del_id'])) {
    $del_id = (int)$_GET['del_id'];
    $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->execute([$del_id]);

    // Redirect to refresh page
    header("Location: posts.php");
    exit();
}
$message = '';
$message_type = '';
?>
<!-- Main Content Column -->
<main class="col-md-9 ms-sm-auto col-lg-10 dashboard-main">
    <div class="page-heading mb-4">
        <div>
            <h1>
                <i class="bi bi-file-text"></i>
                Posts Management
            </h1>
            <p class="page-subtitle">
                Review, edit, and curate the stories that power your platform.
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="categories.php" class="btn btn-outline-secondary btn-sm px-3 shadow-sm">
                <i class="bi bi-tags me-1"></i>
                Manage Categories
            </a>
            <a href="addpost.php" class="btn btn-primary btn-sm px-3 shadow-sm">
                <i class="bi bi-plus-circle me-1"></i>
                Add New Post
            </a>
        </div>
    </div>
    <!-- Posts Table -->
    <div class="content-card">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <h2 class="mb-0">All Posts</h2>
            <span class="text-muted small">
                <?= date('l, M j, Y'); ?>
            </span>
        </div>
        <div class="table-responsive">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Views</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $pdo->prepare("SELECT p.*, c.name AS category_name FROM `posts` p LEFT JOIN `categories` c ON c.id = p.category_id ORDER BY p.created_at DESC");
                    $stmt->execute();
                    while($row=$stmt->fetch()){
                    ?>
                    <tr>
                        <td><?=$row["id"]?></td>
                        <td>
                            <strong><?= htmlspecialchars($row["title"]); ?></strong>
                        </td>
                        <td><?= htmlspecialchars($row["category_name"] ?? '—'); ?></td>
                        <td>
                            <?php
                            $statusClass = $row['status'] === 'published'
                                ? 'status-badge status-approved'
                                : ($row['status'] === 'draft'
                                    ? 'status-badge status-pending'
                                    : 'status-badge status-rejected');
                            ?>
                            <span class="<?= $statusClass; ?>">
                                <i class="bi bi-circle-fill"></i>
                                <?= ucfirst($row['status']) ?>
                            </span>

                        </td>
                        <td><?= number_format((int) $row["views"]); ?></td>
                        <td><?= $row["created_at"] ? date('M j, Y', strtotime($row["created_at"])) : '—'; ?></td>
                        <td>
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="editpost.php?id=<?= $row['id'] ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                    <i class="bi bi-pencil"></i>
                                    <span class="ms-1">Edit</span>
                                </a>
                                <a href="posts.php?del_id=<?= $row['id'] ?>" class="btn btn-outline-danger btn-sm rounded-pill px-3"
                                    onclick="return confirm('Are you sure you want to delete this post?')">
                                    <i class="bi bi-trash"></i>
                                    <span class="ms-1">Delete</span>
                                </a>
                            </div>
                        </td>

                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div> <!-- table-responsive -->
    </div> <!-- content-card -->
</main>
<?php include './includes/footer.php'; ?>