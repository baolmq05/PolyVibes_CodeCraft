<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle) ?> (MST: <?= e($dn['mst']) ?>) – ThongTinDN</title>
<meta name="description" content="Thông tin doanh nghiệp <?= e($pageTitle) ?>, MST <?= e($dn['mst']) ?>, <?= e($dn['tinh_ten'] ?? '') ?>.">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light">

<!-- Navbar -->
<nav class="navbar navbar-dark bg-primary mb-4">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">🏢 ThongTinDN</a>
    <a href="admin/crawl.php" class="btn btn-sm btn-outline-light">Admin</a>
  </div>
</nav>

<div class="container" style="max-width:960px">

  <!-- Breadcrumb -->
  <nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php">Trang chủ</a></li>
      <?php if ($dn['nganh_ten']): ?>
      <li class="breadcrumb-item">
        <a href="index.php?nganh=<?= (int)$dn['nganh_nghe_id'] ?>">
          <?= e($dn['nganh_ten']) ?>
        </a>
      </li>
      <?php endif; ?>
      <li class="breadcrumb-item active" aria-current="page">
        <?= e(mb_substr($dn['ten_cong_ty'], 0, 50, 'UTF-8')) ?>
        <?= mb_strlen($dn['ten_cong_ty'], 'UTF-8') > 50 ? '…' : '' ?>
      </li>
    </ol>
  </nav>

  <!-- Tiêu đề + trạng thái -->
  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <div class="d-flex align-items-start gap-3">
        <?php if ($dn['nganh_hinh_anh']): ?>
        <img src="<?= $uploadUrl . e($dn['nganh_hinh_anh']) ?>"
             style="width:72px;height:72px;object-fit:cover;border-radius:8px;flex-shrink:0"
             alt="<?= e($dn['nganh_ten'] ?? '') ?>">
        <?php else: ?>
        <div style="width:72px;height:72px;background:#e9ecef;border-radius:8px;
                    display:flex;align-items:center;justify-content:center;font-size:32px;flex-shrink:0">🏢</div>
        <?php endif; ?>
        <div>
          <h1 class="h4 mb-1"><?= e($dn['ten_cong_ty']) ?></h1>
          <?php if ($dn['ten_quoc_te']): ?>
          <div class="text-muted small"><?= e($dn['ten_quoc_te']) ?></div>
          <?php endif; ?>
          <?php if ($dn['ten_viet_tat']): ?>
          <div class="text-muted small">Viết tắt: <?= e($dn['ten_viet_tat']) ?></div>
          <?php endif; ?>
          <div class="mt-2">
            <?php 
            $ts = $dn['tinh_trang'] ?? ''; 
            $statusBg = 'secondary';
            if (str_contains(mb_strtolower($ts, 'UTF-8'), 'hoạt động')) {
                $statusBg = 'success';
            } elseif (str_contains(mb_strtolower($ts, 'UTF-8'), 'chờ xác minh')) {
                $statusBg = 'warning';
            } elseif (str_contains(mb_strtolower($ts, 'UTF-8'), 'ngừng')) {
                $statusBg = 'danger';
            }
            ?>
            <span class="badge text-bg-<?= $statusBg ?> me-2">
              <?= e($ts ?: 'Chưa rõ') ?>
            </span>
            <?php if ($dn['loai_ten']): ?>
            <span class="badge text-bg-info"><?= e($dn['loai_ten']) ?></span>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Thông tin chi tiết -->
  <h2 class="h5 mb-3 text-secondary">Thông tin chi tiết</h2>
  <div class="row g-3 mb-4">
    <?php
    $fields = [
        ['label' => 'Mã số thuế',        'value' => $dn['mst'],             'icon' => 'card-text'],
        ['label' => 'Người đại diện',    'value' => $dn['nguoi_dai_dien'],  'icon' => 'person'],
        ['label' => 'Điện thoại',        'value' => $dn['dien_thoai'],      'icon' => 'telephone'],
        ['label' => 'Ngành nghề chính',  'value' => $dn['nganh_ten'],       'icon' => 'briefcase'],
        ['label' => 'Loại hình DN',      'value' => $dn['loai_ten'],        'icon' => 'building'],
        ['label' => 'Địa chỉ',           'value' => $dn['dia_chi'],         'icon' => 'geo-alt'],
        ['label' => 'Địa chỉ Thuế',      'value' => $dn['dia_chi_thue'],    'icon' => 'receipt'],
        ['label' => 'Tỉnh / Thành phố',  'value' => $dn['tinh_ten'],        'icon' => 'map'],
        ['label' => 'Phường / Xã',       'value' => $dn['phuong_ten'],      'icon' => 'geo'],
        ['label' => 'Ngày hoạt động',    'value' => $dn['ngay_hoat_dong'],  'icon' => 'calendar-event'],
        ['label' => 'Quản lý bởi',       'value' => $dn['quan_ly_boi'],     'icon' => 'shield-check'],
    ];

    foreach ($fields as $field):
        if (empty($field['value'])) continue;
    ?>
    <div class="col-md-6 col-lg-4">
      <div class="card h-100 shadow-sm border-0">
        <div class="card-body">
          <div class="d-flex align-items-center gap-2 mb-2 text-muted">
            <i class="bi bi-<?= $field['icon'] ?> text-primary"></i>
            <small class="fw-bold"><?= e($field['label']) ?></small>
          </div>
          <div class="fw-semibold text-dark text-break"><?= e($field['value']) ?></div>
        </div>
      </div>
    </div>
    <?php endforeach; ?>

    <!-- Nguồn & Ngày cập nhật cuối -->
    <div class="col-md-6 col-lg-4">
      <div class="card h-100 shadow-sm border-0 bg-light-subtle">
        <div class="card-body">
          <div class="d-flex align-items-center gap-2 mb-2 text-muted">
            <i class="bi bi-link-45deg text-primary"></i>
            <small class="fw-bold">Nguồn dữ liệu</small>
          </div>
          <div>
            <a href="<?= e($dn['url_nguon']) ?>" target="_blank" rel="noopener noreferrer nofollow" class="text-decoration-none">
              Xem trên masothue.com ↗
            </a>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-6 col-lg-4">
      <div class="card h-100 shadow-sm border-0 bg-light-subtle">
        <div class="card-body">
          <div class="d-flex align-items-center gap-2 mb-2 text-muted">
            <i class="bi bi-clock-history text-primary"></i>
            <small class="fw-bold">Cập nhật lần cuối</small>
          </div>
          <div class="text-muted fw-semibold">
            <?= e(date('d/m/Y H:i', strtotime($dn['ngay_cap_nhat']))) ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Nút thao tác -->
  <div class="d-flex flex-wrap gap-2 mb-4">
    <a href="index.php" class="btn btn-outline-secondary">Quay lại danh sách</a>
    <?php if ($dn['tinh_thanh_id']): ?>
    <a href="index.php?tinh=<?= (int)$dn['tinh_thanh_id'] ?>" class="btn btn-outline-primary">
      Xem doanh nghiệp khác cùng tỉnh
    </a>
    <?php endif; ?>
    <?php if ($dn['nganh_nghe_id']): ?>
    <a href="index.php?nganh=<?= (int)$dn['nganh_nghe_id'] ?>" class="btn btn-outline-info">
      Xem doanh nghiệp khác cùng ngành
    </a>
    <?php endif; ?>
  </div>

</div><!-- /container -->

<footer class="text-center text-muted py-4 mt-5 border-top">
  <small>Dữ liệu tổng hợp từ <a href="https://masothue.com" target="_blank" rel="noopener">masothue.com</a> — chỉ phục vụ mục đích tra cứu.</small>
</footer>

</body>
</html>
