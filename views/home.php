<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle) ?> – ThongTinDN</title>
<meta name="description" content="<?= e($pageTitle) ?>. Tra cứu thông tin doanh nghiệp, mã số thuế, người đại diện tại Việt Nam.">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<!-- Navbar -->
<nav class="navbar navbar-dark bg-primary mb-4">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">🏢 ThongTinDN</a>
    <a href="admin/crawl.php" class="btn btn-sm btn-outline-light">Admin</a>
  </div>
</nav>

<div class="container">

  <h1 class="h4 mb-3"><?= e($pageTitle) ?></h1>

  <!-- Bộ lọc -->
  <form method="GET" action="index.php" class="card card-body mb-4 shadow-sm">
    <div class="row g-2 align-items-end">

      <div class="col-md-3">
        <label class="form-label form-label-sm">Tỉnh / Thành</label>
        <select name="tinh" class="form-select form-select-sm"
                onchange="this.form.phuong.value=0; this.form.submit()">
          <option value="0">-- Tất cả tỉnh --</option>
          <?php
          $prevMienTay = null;
          foreach ($tinhList as $t):
            // Nhóm Miền Tây lên đầu
            if ($prevMienTay != (bool)$t['mien_tay']):
              if ($t['mien_tay']): ?>
              <optgroup label="── Miền Tây ──">
              <?php else: ?>
              </optgroup><optgroup label="── Tỉnh thành khác ──">
              <?php endif;
              $prevMienTay = (bool)$t['mien_tay'];
            endif;
          ?>
          <option value="<?= $t['id'] ?>"
            <?= $filterTinh == (int)$t['id'] ? 'selected' : '' ?>>
            <?= e($t['ten']) ?>
          </option>
          <?php endforeach; if ($prevMienTay != null) echo '</optgroup>'; ?>
        </select>
      </div>

      <div class="col-md-3">
        <label class="form-label form-label-sm">Phường / Xã</label>
        <select name="phuong" class="form-select form-select-sm"
                <?= empty($phuongList) ? 'disabled' : '' ?>>
          <option value="0">-- Tất cả --</option>
          <?php foreach ($phuongList as $p): ?>
          <option value="<?= $p['id'] ?>"
            <?= $filterPhuong == (int)$p['id'] ? 'selected' : '' ?>>
            <?= e($p['ten']) ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-md-3">
        <label class="form-label form-label-sm">Loại hình DN</label>
        <select name="loai" class="form-select form-select-sm">
          <option value="0">-- Tất cả --</option>
          <?php foreach ($loaiList as $l): ?>
          <option value="<?= $l['id'] ?>"
            <?= $filterLoai == (int)$l['id'] ? 'selected' : '' ?>>
            <?= e($l['ten']) ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-md-2">
        <label class="form-label form-label-sm">Ngành nghề</label>
        <select name="nganh" class="form-select form-select-sm">
          <option value="0">-- Tất cả --</option>
          <?php foreach ($nganhList as $nn): ?>
          <option value="<?= $nn['id'] ?>"
            <?= $filterNganh == (int)$nn['id'] ? 'selected' : '' ?>>
            <?= e($nn['ten']) ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-auto">
        <input type="hidden" name="page" value="1">
        <button type="submit" class="btn btn-primary btn-sm">Lọc</button>
        <a href="index.php" class="btn btn-outline-secondary btn-sm">Xoá lọc</a>
      </div>
    </div>
  </form>

  <!-- Kết quả -->
  <div class="d-flex justify-content-between align-items-center mb-2">
    <small class="text-muted">
      Tìm thấy <strong><?= number_format($total) ?></strong> doanh nghiệp
      <?= $total > 0 ? "(trang {$page}/{$totalPages})" : '' ?>
    </small>
  </div>

  <?php if (empty($rows)): ?>
  <div class="alert alert-info">Không có doanh nghiệp nào phù hợp.</div>
  <?php else: ?>
  <div class="table-responsive shadow-sm">
    <table class="table table-sm table-hover bg-white mb-0">
      <thead class="table-primary">
        <tr>
          <th>#</th>
          <th>Tên công ty</th>
          <th>MST</th>
          <th>Người đại diện</th>
          <th>Ngành nghề</th>
          <th>Tỉnh / Thành</th>
          <th>Tình trạng</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $i => $dn): ?>
        <tr>
          <td class="text-muted"><?= $offset + $i + 1 ?></td>
          <td>
            <a href="chi-tiet.php?mst=<?= urlencode($dn['mst']) ?>" class="fw-semibold text-decoration-none">
              <?= e($dn['ten_cong_ty']) ?>
            </a>
          </td>
          <td><code><?= e($dn['mst']) ?></code></td>
          <td><?= e($dn['nguoi_dai_dien'] ?? '') ?></td>
          <td><small><?= e($dn['nganh_ten'] ?? '') ?></small></td>
          <td><small><?= e($dn['tinh_ten'] ?? '') ?></small></td>
          <td>
            <?php $ts = $dn['tinh_trang'] ?? ''; ?>
            <span class="badge text-bg-<?= str_contains($ts, 'hoạt động') ? 'success' : 'secondary' ?>">
              <?= e($ts) ?>
            </span>
          </td>
          <td>
            <a href="chi-tiet.php?mst=<?= urlencode($dn['mst']) ?>" class="btn btn-xs btn-outline-primary btn-sm">
              Chi tiết
            </a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- Phân trang -->
  <?php if ($totalPages > 1): ?>
  <nav class="mt-3">
    <ul class="pagination pagination-sm justify-content-center flex-wrap">
      <?php if ($page > 1): ?>
      <li class="page-item">
        <a class="page-link" href="<?= e($qs(['page' => $page - 1])) ?>">‹ Trước</a>
      </li>
      <?php endif; ?>

      <?php
      $start = max(1, $page - 3);
      $end   = min($totalPages, $page + 3);
      if ($start > 1):
      ?>
      <li class="page-item"><a class="page-link" href="<?= e($qs(['page' => 1])) ?>">1</a></li>
      <?php if ($start > 2): ?><li class="page-item disabled"><span class="page-link">…</span></li><?php endif; ?>
      <?php endif; ?>

      <?php for ($p = $start; $p <= $end; $p++): ?>
      <li class="page-item <?= $p == $page ? 'active' : '' ?>">
        <a class="page-link" href="<?= e($qs(['page' => $p])) ?>"><?= $p ?></a>
      </li>
      <?php endfor; ?>

      <?php if ($end < $totalPages):
        if ($end < $totalPages - 1):
      ?><li class="page-item disabled"><span class="page-link">…</span></li><?php endif; ?>
      <li class="page-item"><a class="page-link" href="<?= e($qs(['page' => $totalPages])) ?>"><?= $totalPages ?></a></li>
      <?php endif; ?>

      <?php if ($page < $totalPages): ?>
      <li class="page-item">
        <a class="page-link" href="<?= e($qs(['page' => $page + 1])) ?>">Sau ›</a>
      </li>
      <?php endif; ?>
    </ul>
  </nav>
  <?php endif; ?>
  <?php endif; ?>

</div><!-- /container -->

<footer class="text-center text-muted py-4 mt-5 border-top">
  <small>Dữ liệu tổng hợp từ <a href="https://masothue.com" target="_blank" rel="noopener">masothue.com</a> — chỉ phục vụ mục đích tra cứu.</small>
</footer>

</body>
</html>
