<?php
$adminTitle = 'Dashboard';
require_once __DIR__ . '/admin_layout_header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-b">
  <h2 class="h3 mb-0">Dashboard</h2>
</div>

<!-- 4 KPI Cards -->
<div class="row g-3">
  <!-- Tổng doanh nghiệp -->
  <div class="col-md-6 col-lg-3">
    <div class="card border-0 shadow-sm text-bg-primary h-100">
      <div class="card-body d-flex align-items-center justify-content-between">
        <div>
          <h6 class="card-title text-white-50 mb-1">Tổng doanh nghiệp</h6>
          <h2 class="card-text fw-bold mb-0"><?= number_format($totalDn) ?></h2>
        </div>
        <div class="fs-1 text-white-50">
          <i class="bi bi-building"></i>
        </div>
      </div>
    </div>
  </div>

  <!-- Đang chờ -->
  <div class="col-md-6 col-lg-3">
    <div class="card border-0 shadow-sm text-bg-warning h-100">
      <div class="card-body d-flex align-items-center justify-content-between">
        <div>
          <h6 class="card-title text-dark-50 mb-1">URL đang chờ cào</h6>
          <h2 class="card-text fw-bold mb-0"><?= number_format($stats['cho'] ?? 0) ?></h2>
        </div>
        <div class="fs-1 text-black-50">
          <i class="bi bi-hourglass-split"></i>
        </div>
      </div>
    </div>
  </div>

  <!-- Thành công -->
  <div class="col-md-6 col-lg-3">
    <div class="card border-0 shadow-sm text-bg-success h-100">
      <div class="card-body d-flex align-items-center justify-content-between">
        <div>
          <h6 class="card-title text-white-50 mb-1">URL thành công</h6>
          <h2 class="card-text fw-bold mb-0"><?= number_format($stats['thanh_cong'] ?? 0) ?></h2>
        </div>
        <div class="fs-1 text-white-50">
          <i class="bi bi-check-circle"></i>
        </div>
      </div>
    </div>
  </div>

  <!-- Thất bại -->
  <div class="col-md-6 col-lg-3">
    <div class="card border-0 shadow-sm text-bg-danger h-100">
      <div class="card-body d-flex align-items-center justify-content-between">
        <div>
          <h6 class="card-title text-white-50 mb-1">URL thất bại</h6>
          <h2 class="card-text fw-bold mb-0"><?= number_format($stats['that_bai'] ?? 0) ?></h2>
        </div>
        <div class="fs-1 text-white-50">
          <i class="bi bi-exclamation-triangle"></i>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
require_once __DIR__ . '/admin_layout_footer.php';
?>
