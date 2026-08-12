<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle) ?> (MST: <?= e($dn['mst']) ?>) – ThongTinDN</title>
<meta name="description" content="Thông tin doanh nghiệp <?= e($pageTitle) ?>, MST <?= e($dn['mst']) ?>, <?= e($dn['tinh_ten'] ?? '') ?>.">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<style>
  .info-label { color: #6c757d; font-size: .85rem; white-space: nowrap; }
  .info-value { font-weight: 500; }
</style>
</head>
<body class="bg-light">

<!-- Navbar -->
<nav class="navbar navbar-dark bg-primary mb-4">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">🏢 ThongTinDN</a>
    <a href="admin/crawl.php" class="btn btn-sm btn-outline-light">Admin</a>
  </div>
</nav>

<div class="container" style="max-width:860px">

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
            <?php $ts = $dn['tinh_trang'] ?? ''; ?>
            <span class="badge text-bg-<?= str_contains($ts, 'hoạt động') ? 'success' : 'secondary' ?> me-2">
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
  <div class="card shadow-sm mb-4">
    <div class="card-header fw-bold">Thông tin doanh nghiệp</div>
    <div class="card-body">
      <dl class="row mb-0">

        <?php
        $fields = [
            'Mã số thuế'        => $dn['mst'],
            'Người đại diện'    => $dn['nguoi_dai_dien'],
            'Điện thoại'        => $dn['dien_thoai'],
            'Ngành nghề chính'  => $dn['nganh_ten'],
            'Loại hình DN'      => $dn['loai_ten'],
            'Địa chỉ'           => $dn['dia_chi'],
            'Địa chỉ Thuế'      => $dn['dia_chi_thue'],
            'Tỉnh / Thành phố'  => $dn['tinh_ten'],
            'Phường / Xã'       => $dn['phuong_ten'],
            'Ngày hoạt động'    => $dn['ngay_hoat_dong'],
            'Quản lý bởi'       => $dn['quan_ly_boi'],
        ];
        foreach ($fields as $label => $value):
            if (empty($value)) continue;
        ?>
        <dt class="col-sm-4 info-label"><?= e($label) ?></dt>
        <dd class="col-sm-8 info-value"><?= e($value) ?></dd>
        <?php endforeach; ?>

        <dt class="col-sm-4 info-label">Nguồn</dt>
        <dd class="col-sm-8">
          <a href="<?= e($dn['url_nguon']) ?>" target="_blank" rel="noopener noreferrer nofollow">
            Xem trên masothue.com ↗
          </a>
        </dd>

        <dt class="col-sm-4 info-label">Cập nhật lần cuối</dt>
        <dd class="col-sm-8 info-value text-muted">
          <?= e(date('d/m/Y H:i', strtotime($dn['ngay_cap_nhat']))) ?>
        </dd>

      </dl>
    </div>
  </div>

  <a href="index.php" class="btn btn-outline-secondary">← Quay lại danh sách</a>
  <?php if ($dn['tinh_thanh_id']): ?>
  <a href="index.php?tinh=<?= (int)$dn['tinh_thanh_id'] ?>" class="btn btn-outline-primary ms-2">
    DN khác tại <?= e($dn['tinh_ten'] ?? '') ?>
  </a>
  <?php endif; ?>
  <?php if ($dn['nganh_nghe_id']): ?>
  <a href="index.php?nganh=<?= (int)$dn['nganh_nghe_id'] ?>" class="btn btn-outline-info ms-2">
    DN ngành <?= e($dn['nganh_ten'] ?? '') ?>
  </a>
  <?php endif; ?>

</div><!-- /container -->

<footer class="text-center text-muted py-4 mt-5 border-top">
  <small>Dữ liệu tổng hợp từ <a href="https://masothue.com" target="_blank" rel="noopener">masothue.com</a> — chỉ phục vụ mục đích tra cứu.</small>
</footer>

</body>
</html>
