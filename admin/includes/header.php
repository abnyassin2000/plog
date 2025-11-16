<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - PHP Blog</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Custom Admin Theme -->
    <link rel="stylesheet" href="assets/css/admin-theme.css">
</head>

<body class="dashboard-body">
    <nav class="navbar navbar-expand-lg navbar-dark py-3">
        <div class="container-fluid">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-outline-light btn-sm d-lg-none rounded-pill shadow-sm" type="button" id="sidebarToggle">
                    <i class="bi bi-list"></i>
                </button>
                <a class="navbar-brand" href="index.php">
                    <i class="bi bi-speedometer2"></i>
                    <span>Admin Dashboard</span>
                </a>
            </div>
            <div class="d-flex align-items-center gap-3 text-white">
                <div class="d-none d-md-flex flex-column text-end">
                    <span class="fw-semibold">Admin User</span>
                    <small class="text-white-50">Welcome back!</small>
                </div>
                <span class="d-md-none">
                    <i class="bi bi-person-circle"></i>
                </span>
            </div>
        </div>
    </nav>
    <div id="sidebarBackdrop" class="sidebar-backdrop d-lg-none"></div>
    <!-- Main Container with Sidebar -->
    <div class="container-fluid">
        <div class="row">