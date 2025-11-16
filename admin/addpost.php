<?php
require_once '../config/database.php';
include './includes/header.php';
include './includes/sidebar.php';

// ========================
//  إضافة بوست جديد
// ========================
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST["title"]);
    $content = trim($_POST["content"]);
    $category_id = $_POST["category_id"];
    $status = $_POST["status"];

    // تحقق من وجود الصورة
    $img = "";
    if (!empty($_FILES["image"]["name"])) {
        $img = time() . '_' . basename($_FILES["image"]["name"]); // اسم فريد
        $uploadDir = "../uploads/";
        $path = $uploadDir . $img;

        // تأكد إن مجلد الرفع موجود
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        move_uploaded_file($_FILES["image"]["tmp_name"], $path);
    }

    // إدخال البيانات في قاعدة البيانات
    $stmt = $pdo->prepare("INSERT INTO `posts`(`title`, `content`, `category_id`, `status`, `image`) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$title, $content, $category_id, $status, $img]);

    // إعادة توجيه بعد الإضافة
    header("Location: posts.php?success=1");
    exit;
}
?>

<!-- Main Content Column -->
<main class="col-md-9 ms-sm-auto col-lg-10 dashboard-main">
    <div class="page-heading mb-4">
        <div>
            <h1>
                <i class="bi bi-file-text"></i>
                Add New Post
            </h1>
            <p class="page-subtitle">
                Craft a new story, share an update, or highlight something exciting for your readers.
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="posts.php" class="btn btn-outline-secondary btn-sm px-3 shadow-sm">
                <i class="bi bi-arrow-left"></i>
                <span class="ms-1">Back to Posts</span>
            </a>
        </div>
    </div>

    <!-- Add New Post Form -->
    <div class="content-card">
        <h2 class="mb-4 d-flex align-items-center gap-2">
            <i class="bi bi-plus-circle"></i>
            Create New Blog Post
        </h2>
        <form method="POST" enctype="multipart/form-data" class="row g-4">
            <div class="col-12">
                <label class="form-label fw-semibold">Post Title</label>
                <input type="text" class="form-control form-control-lg" name="title" placeholder="Enter a compelling headline" required>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Content</label>
                <textarea class="form-control" name="content" rows="8" placeholder="Write your story here..." required></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Category</label>
                <select class="form-select" name="category_id" required>
                    <?php
                    // عرض التصنيفات من قاعدة البيانات بدل ما تثبتهم يدويًا
                    $cat_stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
                    while ($cat = $cat_stmt->fetch()) {
                        echo "<option value='{$cat['id']}'>" . htmlspecialchars($cat['name']) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Status</label>
                <select class="form-select" name="status" required>
                    <option value="draft">Draft</option>
                    <option value="published">Published</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Featured Image</label>
                <input type="file" class="form-control" name="image" accept="image/*">
                <small class="text-muted">Optional. Upload a cover image up to 3MB.</small>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary btn-lg rounded-pill px-4 shadow-sm">
                    <i class="bi bi-plus-circle"></i>
                    <span class="ms-2">Create Post</span>
                </button>
            </div>
        </form>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
