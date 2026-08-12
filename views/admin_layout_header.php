<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($adminTitle ?? 'Admin') ?> | ThongTinDN</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
  body {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
  }
  .admin-wrapper {
    display: flex;
    flex: 1;
  }
  .admin-sidebar {
    width: 240px;
    background-color: #212529;
    color: #fff;
    flex-shrink: 0;
  }
  .admin-content {
    flex-grow: 1;
    background-color: #f8f9fa;
  }
  /* Responsive styling */
  @media (max-width: 991.98px) {
    .admin-sidebar {
      display: none;
    }
  }
</style>
</head>
<body>

<!-- Top Navbar on Mobile -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark d-lg-none">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="index.php">🏢 ThongTinDN Admin</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminMobileSidebar" aria-controls="adminMobileSidebar">
      <span class="navbar-toggler-icon"></span>
    </button>
  </div>
</nav>

<div class="admin-wrapper">
  <!-- Desktop Sidebar -->
  <aside class="admin-sidebar d-none d-lg-flex flex-column p-3 text-bg-dark">
    <a href="../index.php" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
      <span class="fs-4 fw-bold">🏢 ThongTinDN</span>
    </a>
    <hr>
    <?php
    $currentUri = $_SERVER['REQUEST_URI'];
    $menuItems = [
      ['label' => 'Dashboard',    'url' => 'index.php',          'icon' => 'speedometer2'],
      ['label' => 'Crawler',      'url' => 'crawl.php',          'icon' => 'cpu'],
      ['label' => 'Crawl Queue',  'url' => 'queue.php',          'icon' => 'list-task'],
      ['label' => 'Crawl Logs',   'url' => 'logs.php',           'icon' => 'journal-text'],
      ['label' => 'Doanh nghiệp', 'url' => 'doanh-nghiep.php',   'icon' => 'building'],
      ['label' => 'Ngành nghề',   'url' => 'danh-muc.php',       'icon' => 'folder'],
    ];
    ?>
    <ul class="nav nav-pills flex-column mb-auto">
      <?php foreach ($menuItems as $item): 
        // Check if current URL contains the menu URL, or if it is exactly admin/ and menu is index.php
        $isActive = str_contains($currentUri, '/admin/' . $item['url']) 
                 || (str_ends_with(rtrim($currentUri, '/'), '/admin') && $item['url'] === 'index.php');
      ?>
      <li class="nav-item mb-1">
        <a href="<?= $item['url'] ?>" class="nav-link text-white <?= $isActive ? 'active' : '' ?> d-flex align-items-center gap-2">
          <i class="bi bi-<?= $item['icon'] ?>"></i>
          <?= $item['label'] ?>
        </a>
      </li>
      <?php endforeach; ?>
    </ul>
    <hr>
    <div class="dropdown">
      <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-person-circle fs-5 me-2"></i>
        <strong>Admin</strong>
      </a>
      <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
        <li><a class="dropdown-item" href="../index.php">Cài đặt</a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item" href="../index.php">Đăng xuất</a></li>
      </ul>
    </div>
  </aside>

  <!-- Mobile Sidebar (Offcanvas) -->
  <div class="offcanvas offcanvas-start text-bg-dark" tabindex="-1" id="adminMobileSidebar" aria-labelledby="adminMobileSidebarLabel">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title fw-bold" id="adminMobileSidebarLabel">🏢 ThongTinDN Admin</h5>
      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column">
      <ul class="nav nav-pills flex-column mb-auto">
        <?php foreach ($menuItems as $item): 
          $isActive = str_contains($currentUri, '/admin/' . $item['url']) 
                   || (str_ends_with(rtrim($currentUri, '/'), '/admin') && $item['url'] === 'index.php');
        ?>
        <li class="nav-item mb-1">
          <a href="<?= $item['url'] ?>" class="nav-link text-white <?= $isActive ? 'active' : '' ?> d-flex align-items-center gap-2" onclick="bootstrap.Offcanvas.getInstance(document.getElementById('adminMobileSidebar')).hide();">
            <i class="bi bi-<?= $item['icon'] ?>"></i>
            <?= $item['label'] ?>
          </a>
        </li>
        <?php endforeach; ?>
      </ul>
      <hr>
      <div class="dropdown">
        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="bi bi-person-circle fs-5 me-2"></i>
          <strong>Admin</strong>
        </a>
        <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
          <li><a class="dropdown-item" href="../index.php">Cài đặt</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item" href="../index.php">Đăng xuất</a></li>
        </ul>
      </div>
    </div>
  </div>

  <!-- Main Content Wrapper -->
  <main class="admin-content p-3 p-md-4">
