<?php
// Database connection
require_once '../config/database.php';
include 'includes/header.php';
include 'includes/sidebar.php';
if($_SERVER["REQUEST_METHOD"]=="POST"){
        $name = $_POST["name"];
        $desc = $_POST["description"];
        $stmt = $pdo->prepare("INSERT INTO `categories`(`name`, `description`) VALUES (?,?)");
        $stmt->execute([$name,$desc]);
}
if (isset($_GET['del_id'])){
    $del_id = $_GET['del_id'];
    $stmt= $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$del_id]);
    echo "<script>window.location.href='categories.php';</script>";
}
?>
<!-- Main Content Column -->
<main class="col-md-9 ms-sm-auto col-lg-10 dashboard-main">
    <div class="page-heading mb-4">
        <div>
            <h1>
                <i class="bi bi-tags"></i>
                Categories Management
            </h1>
            <p class="page-subtitle">
                Create meaningful groupings to keep your content organised and easy to explore.
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="posts.php" class="btn btn-outline-secondary btn-sm px-3 shadow-sm">
                <i class="bi bi-file-earmark-text me-1"></i>
                View Posts
            </a>
        </div>
    </div>
    <!-- Success/Error Messages -->
    <div id="messageContainer"></div>
    <!-- Add Category Form -->
    <div class="content-card mb-4">
        <h2 class="mb-4">Add New Category</h2>
        <form method="POST" class="row g-3">
            <div class="col-md-5">
                <label for="categoryName" class="form-label">Category Name *</label>
                <input type="text" class="form-control" id="categoryName" name="name" placeholder="News, Guides, Tips..." required>
            </div>

            <div class="col-md-5">
                <label for="categoryDescription" class="form-label">Description</label>
                <input type="text" class="form-control" id="categoryDescription" name="description" placeholder="Optional short description">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100 rounded-pill shadow-sm" name="add_category">
                    <i class="bi bi-plus-circle"></i>
                    <span class="ms-1">Add</span>
                </button>
            </div>
        </form>
    </div> <!-- content-card -->
    <!-- Categories Table -->
    <div class="content-card">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <h2 class="mb-0">All Categories</h2>
            <span class="text-muted small">
                <?= date('l, M j, Y'); ?>
            </span>
        </div>
        <div class="table-responsive">
            <table class="table-modern" id="categoriesTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $stmt = $pdo->prepare("SELECT * FROM `categories`");
                        $stmt->execute();
                        while($row=$stmt->fetch()){
                    ?>
                    <tr>
                        <td><?=$row["id"]?></td>
                        <td><?= htmlspecialchars($row["name"]); ?></td>
                        <td><?= htmlspecialchars($row["description"]); ?></td>
                        <td><?= $row["created_at"]; ?></td>
                        <td>
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="categories.php?edit_id=<?=$row['id']; ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                    <i class="bi bi-pencil"></i>
                                    <span class="ms-1">Edit</span>
                                </a>
                                <a href="categories.php?del_id=<?=$row['id']; ?>" class="btn btn-outline-danger btn-sm rounded-pill px-3"
                                    onclick="return confirm('Are you sure you want to delete this category?');">
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
<?php include 'includes/footer.php'; ?>