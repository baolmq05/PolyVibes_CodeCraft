<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle) ?> – ThongTinDN</title>
<meta name="description" content="<?= e($pageTitle) ?>. Tra cứu thông tin doanh nghiệp, mã số thuế, người đại diện tại Việt Nam.">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<!-- Tom Select CSS for Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<style>
  /* Tinh chỉnh chiều cao và cỡ chữ của Tom Select để khớp với form-select-sm */
  .ts-wrapper.form-select-sm .ts-control, .ts-wrapper.form-select-sm .ts-control input {
    font-size: 0.875rem !important;
  }
  .ts-wrapper.form-select-sm .ts-control {
    padding: 0 8px !important;
    height: 31px !important;
    min-height: 31px !important;
    max-height: 31px !important;
    display: flex !important;
    align-items: center !important;
    box-sizing: border-box !important;
    overflow: hidden !important;
  }
  .ts-wrapper.form-select-sm {
    padding: 0 !important;
    border: 0 !important;
    height: 31px !important;
  }
  .ts-dropdown {
    font-size: 0.875rem !important;
  }
  /* Giúp ô input tìm kiếm nằm thẳng dòng không đẩy giao diện xuống */
  .ts-wrapper.form-select-sm .ts-control > input {
    display: inline-block !important;
    height: 100% !important;
    margin: 0 !important;
    padding: 0 !important;
    background: transparent !important;
    border: 0 !important;
  }
  /* Tinh chỉnh bảng gọn gàng hơn, tránh tràn khung gây cuộn ngang trên desktop */
  .table > :not(caption) > * > * {
    padding: 8px 10px;
  }
  .table thead th {
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.3px;
    text-transform: uppercase;
    color: #495057;
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
  }
  .company-link {
    color: #0d6efd;
    font-weight: 600;
    transition: color 0.15s ease-in-out;
  }
  .company-link:hover {
    color: #0a58ca;
    text-decoration: underline !important;
  }
  .fs-7 {
    font-size: 0.8rem;
  }
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

<div class="container">

  <h1 class="h4 mb-3"><?= e($pageTitle) ?></h1>

  <!-- Bộ lọc -->
  <form method="GET" action="index.php" class="card card-body mb-4 shadow-sm">
    <!-- Hàng 1: Các select lọc trải đều chiếm hết các cột -->
    <div class="row g-2 mb-3">
      <div class="col-md-3">
        <label class="form-label form-label-sm fw-semibold">Tỉnh / Thành</label>
        <select name="tinh" class="form-select form-select-sm">
          <option value="0">Tất cả</option>
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
        <label class="form-label form-label-sm fw-semibold">Phường / Xã</label>
        <select name="phuong" class="form-select form-select-sm"
                <?= empty($phuongList) ? 'disabled' : '' ?>>
          <option value="0">Tất cả</option>
          <?php foreach ($phuongList as $p): ?>
          <option value="<?= $p['id'] ?>"
            <?= $filterPhuong == (int)$p['id'] ? 'selected' : '' ?>>
            <?= e($p['ten']) ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-md-3">
        <label class="form-label form-label-sm fw-semibold">Loại hình DN</label>
        <select name="loai" class="form-select form-select-sm">
          <option value="0">Tất cả</option>
          <?php foreach ($loaiList as $l): ?>
          <option value="<?= $l['id'] ?>"
            <?= $filterLoai == (int)$l['id'] ? 'selected' : '' ?>>
            <?= e($l['ten']) ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-md-3">
        <label class="form-label form-label-sm fw-semibold">Ngành nghề</label>
        <select name="nganh" class="form-select form-select-sm">
          <option value="0">Tất cả</option>
          <?php foreach ($nganhList as $nn): ?>
          <option value="<?= $nn['id'] ?>"
            <?= $filterNganh == (int)$nn['id'] ? 'selected' : '' ?>>
            <?= e($nn['ten']) ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <!-- Hàng 2: Thanh tìm kiếm chiếm 9 cột, các nút lọc chiếm 3 cột (căn phải) -->
    <div class="row g-2 align-items-end">
      <div class="col-md-9">
        <label class="form-label form-label-sm fw-semibold">Tìm kiếm</label>
        <input type="text" name="search" class="form-control form-control-sm" 
               placeholder="Nhập tên công ty, mã số thuế hoặc người đại diện..." 
               value="<?= e($filterSearch ?? '') ?>">
      </div>
      <div class="col-md-3 d-flex justify-content-end gap-2">
        <input type="hidden" name="page" value="1">
        <a href="index.php" class="btn btn-outline-secondary btn-sm px-4 flex-grow-1 flex-md-grow-0">Xoá lọc</a>
        <button type="submit" class="btn btn-primary btn-sm px-4 flex-grow-1 flex-md-grow-0">Lọc</button>
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
  <div class="card border-0 shadow-sm overflow-hidden mb-4">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th style="width: 50px">#</th>
            <th>Tên công ty</th>
            <th style="width: 140px">MST</th>
            <th>Người đại diện</th>
            <th>Ngành nghề chính</th>
            <th style="width: 150px">Tỉnh / Thành</th>
            <th style="width: 150px">Tình trạng</th>
            <th style="width: 100px" class="text-end"></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $i => $dn): ?>
          <tr>
            <td class="text-muted"><?= $offset + $i + 1 ?></td>
            <td>
              <a href="chi-tiet.php?mst=<?= urlencode($dn['mst']) ?>" class="company-link text-decoration-none">
                <?= e($dn['ten_cong_ty']) ?>
              </a>
            </td>
            <td>
              <span class="badge text-bg-light border fw-semibold font-mono"><?= e($dn['mst']) ?></span>
            </td>
            <td>
              <span class="small text-dark">
                <i class="bi bi-person text-secondary me-1"></i><?= e($dn['nguoi_dai_dien'] ?: 'Chưa rõ') ?>
              </span>
            </td>
            <td>
              <div class="small text-muted text-break" style="max-width: 250px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; white-space: normal;" title="<?= e($dn['nganh_ten'] ?? '') ?>">
                <?= e($dn['nganh_ten'] ?: 'Chưa rõ') ?>
              </div>
            </td>
            <td>
              <span class="small text-dark">
                <i class="bi bi-geo-alt text-danger me-1"></i><?= e($dn['tinh_ten'] ?: 'Chưa rõ') ?>
              </span>
            </td>
            <td>
              <?php
              $ts = $dn['tinh_trang'] ?? '';
              $badgeClass = 'secondary';
              if (str_contains(mb_strtolower($ts, 'UTF-8'), 'hoạt động')) {
                  $badgeClass = 'success';
              } elseif (str_contains(mb_strtolower($ts, 'UTF-8'), 'chờ xác minh') || str_contains(mb_strtolower($ts, 'UTF-8'), 'tạm ngừng')) {
                  $badgeClass = 'warning';
              } elseif (str_contains(mb_strtolower($ts, 'UTF-8'), 'ngừng hoạt động')) {
                  $badgeClass = 'danger';
              }
              ?>
              <span class="badge text-bg-<?= $badgeClass ?>-subtle text-<?= $badgeClass ?> border border-<?= $badgeClass ?>-subtle px-2 py-1 fs-7">
                <?= e($ts ?: 'Chưa rõ') ?>
              </span>
            </td>
            <td class="text-end">
              <a href="chi-tiet.php?mst=<?= urlencode($dn['mst']) ?>" class="btn btn-sm btn-outline-primary py-1 px-2 fs-7 text-nowrap">
                Chi tiết
              </a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
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

<!-- Khởi tạo Tom Select tìm kiếm trong dropdown và lọc phường xã client-side -->
<script>
  const allPhuong = <?= json_encode($allPhuongList) ?>;

  document.addEventListener("DOMContentLoaded", function() {
    const tinhEl = document.querySelector('select[name="tinh"]');
    const phuongEl = document.querySelector('select[name="phuong"]');
    const loaiEl = document.querySelector('select[name="loai"]');
    const nganhEl = document.querySelector('select[name="nganh"]');

    let tsPhuong = null;
    if (phuongEl) {
      tsPhuong = new TomSelect(phuongEl, {
        create: false,
        controlInput: '<input>'
      });
    }

    if (tinhEl) {
      new TomSelect(tinhEl, {
        create: false,
        controlInput: '<input>',
        onChange: function(tinhId) {
          tinhId = parseInt(tinhId) || 0;
          if (tsPhuong) {
            // Xóa tất cả các lựa chọn cũ
            tsPhuong.clearOptions();
            // Thêm tùy chọn "Tất cả" mặc định
            tsPhuong.addOption({value: '0', text: 'Tất cả'});

            // Lọc ra các phường xã thuộc tỉnh được chọn
            const filtered = allPhuong.filter(p => parseInt(p.tinh_thanh_id) === tinhId);
            filtered.forEach(p => {
              tsPhuong.addOption({value: p.id, text: p.ten});
            });

            // Set giá trị mặc định là "Tất cả"
            tsPhuong.setValue('0');

            // Bật/tắt trạng thái disable tương ứng
            if (tinhId === 0 || filtered.length === 0) {
              tsPhuong.disable();
            } else {
              tsPhuong.enable();
            }
          }
        }
      });
    }

    if (loaiEl) {
      new TomSelect(loaiEl, {
        create: false,
        controlInput: '<input>'
      });
    }

    if (nganhEl) {
      new TomSelect(nganhEl, {
        create: false,
        controlInput: '<input>'
      });
    }
  });
</script>

</body>
</html>
