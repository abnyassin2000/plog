<?php
require_once '../config/database.php';
include './includes/header.php';
include './includes/sidebar.php';

// التحقق من وجود ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid post ID.");
}
$post_id = (int)$_GET['id'];

// جلب بيانات البوست
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->execute([$post_id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    die("Post not found!");
}

$message = '';
$message_type = '';

// عند إرسال النموذج (التعديل)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $category_id = (int)$_POST['category_id'];
    $status = $_POST['status'];

    // تحديث البيانات
    $stmt = $pdo->prepare("UPDATE posts SET title=?, content=?, category_id=?, status=? WHERE id=?");
    $stmt->execute([$title, $content, $category_id, $status, $post_id]);

    $message = "Post updated successfully!";
    $message_type = "success";

    // تحديث البيانات بعد التعديل
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
    $stmt->execute([$post_id]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);
}
$categories_stmt = $pdo->query("SELECT id, name FROM categories ORDER BY name");
$categories = $categories_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Main Content -->
<main class="col-md-9 ms-sm-auto col-lg-10 dashboard-main">
    <div class="page-heading mb-4">
        <div>
            <h1>
                <i class="bi bi-pencil-square"></i>
                Edit Post
            </h1>
            <p class="page-subtitle">
                Update the content, fine-tune the details, and keep your story fresh.
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="posts.php" class="btn btn-outline-secondary btn-sm px-3 shadow-sm">
                <i class="bi bi-arrow-left"></i>
                <span class="ms-1">Back to Posts</span>
            </a>
        </div>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?= $message_type ?> alert-dismissible fade show shadow-sm rounded-pill px-4" role="alert">
            <?= htmlspecialchars($message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="content-card">
        <h2 class="mb-4 d-flex align-items-center gap-2">
            <i class="bi bi-journal-text"></i>
            Post Details
        </h2>
        <form method="POST" class="row g-4">
            <div class="col-12">
                <label class="form-label fw-semibold">Title</label>
                <input type="text" name="title" class="form-control form-control-lg" value="<?= htmlspecialchars($post['title']) ?>"
                    required>
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold">Content</label>
                <textarea name="content" class="form-control" rows="8"
                    required><?= htmlspecialchars($post['content']) ?></textarea>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Category</label>
                <select name="category_id" class="form-select" required>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id']; ?>" <?= $category['id'] == $post['category_id'] ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Status</label>
                <select name="status" class="form-select" required>
                    <option value="draft" <?= $post['status'] == 'draft' ? 'selected' : '' ?>>Draft</option>
                    <option value="published" <?= $post['status'] == 'published' ? 'selected' : '' ?>>Published</option>
                </select>
            </div>

            <div class="col-12 d-flex justify-content-end gap-2">
                <a href="posts.php" class="btn btn-outline-secondary rounded-pill px-4">
                    Cancel
                </a>
                <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                    <i class="bi bi-save"></i>
                    <span class="ms-2">Update Post</span>
                </button>
            </div>
        </form>
    </div>
</main>

<?php include './includes/footer.php'; ?>