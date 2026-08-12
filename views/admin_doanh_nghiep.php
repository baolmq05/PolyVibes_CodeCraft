<?php
$adminTitle = 'Quản lý Doanh nghiệp';
require_once __DIR__ . '/admin_layout_header.php';
?>

<!-- Tom Select CSS for Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<style>
  /* Tinh chỉnh chiều cao Tom Select */
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
  .ts-wrapper.form-select-sm .ts-control > input {
    display: inline-block !important;
    height: 100% !important;
    margin: 0 !important;
    padding: 0 !important;
    background: transparent !important;
    border: 0 !important;
  }
</style>

<div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
  <h2 class="h3 mb-0">Quản lý Doanh nghiệp</h2>
  <a href="doanh-nghiep-edit.php" class="btn btn-success btn-sm">
    <i class="bi bi-plus-lg me-1"></i> Thêm doanh nghiệp
  </a>
</div>

<?= $msg ?? '' ?>

<!-- Bộ lọc -->
<form method="GET" class="card card-body mb-4 shadow-sm border-0">
  <input type="hidden" name="page" value="1">
  <!-- Hàng 1: Các select lọc trải đều chiếm hết các cột -->
  <div class="row g-2 mb-3">
    <div class="col-md-3">
      <label class="form-label form-label-sm fw-semibold">Tỉnh / Thành</label>
      <select name="tinh" class="form-select form-select-sm">
        <option value="0">Tất cả</option>
        <?php foreach ($tinhList as $t): ?>
        <option value="<?= $t['id'] ?>" <?= $filterTinh == (int)$t['id'] ? 'selected' : '' ?>>
          <?= e($t['ten']) ?>
        </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-md-3">
      <label class="form-label form-label-sm fw-semibold">Phường / Xã</label>
      <select name="phuong" class="form-select form-select-sm" <?= empty($phuongList) ? 'disabled' : '' ?>>
        <option value="0">Tất cả</option>
        <?php foreach ($phuongList as $p): ?>
        <option value="<?= $p['id'] ?>" <?= $filterPhuong == (int)$p['id'] ? 'selected' : '' ?>>
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
        <option value="<?= $l['id'] ?>" <?= $filterLoai == (int)$l['id'] ? 'selected' : '' ?>>
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
        <option value="<?= $nn['id'] ?>" <?= $filterNganh == (int)$nn['id'] ? 'selected' : '' ?>>
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
             placeholder="Tìm theo tên, MST, người đại diện..." 
             value="<?= e($filterSearch ?? '') ?>">
    </div>
    <div class="col-md-3 d-flex justify-content-end gap-2">
      <a href="doanh-nghiep.php" class="btn btn-outline-secondary btn-sm px-4 flex-grow-1 flex-md-grow-0">Xoá lọc</a>
      <button type="submit" class="btn btn-primary btn-sm px-4 flex-grow-1 flex-md-grow-0">Lọc</button>
    </div>
  </div>
</form>

<!-- Kết quả -->
<div class="mb-3 d-flex justify-content-between align-items-center">
  <small class="text-muted">Tìm thấy <strong><?= number_format($total) ?></strong> doanh nghiệp</small>
</div>

<?php if (empty($rows)): ?>
  <div class="alert alert-info">Không có doanh nghiệp nào phù hợp.</div>
<?php else: ?>
  <div class="card border-0 shadow-sm overflow-hidden mb-4">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Tên công ty</th>
            <th>MST</th>
            <th>Người đại diện</th>
            <th>Tỉnh / Thành</th>
            <th>Ngành nghề chính</th>
            <th>Tình trạng</th>
            <th class="text-center">Thao tác</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $i => $dn): ?>
          <tr>
            <td class="text-muted"><?= $offset + $i + 1 ?></td>
            <td class="fw-semibold text-dark"><?= e($dn['ten_cong_ty']) ?></td>
            <td><code class="font-mono"><?= e($dn['mst']) ?></code></td>
            <td><?= e($dn['nguoi_dai_dien'] ?: 'Chưa rõ') ?></td>
            <td><?= e($dn['tinh_ten'] ?: 'Chưa rõ') ?></td>
            <td class="text-muted small max-w-xs truncate" title="<?= e($dn['nganh_ten'] ?? '') ?>"><?= e($dn['nganh_ten'] ?: 'Chưa rõ') ?></td>
            <td>
              <?php 
              $ts = $dn['tinh_trang'] ?? ''; 
              $color = 'secondary';
              if (str_contains(mb_strtolower($ts, 'UTF-8'), 'hoạt động')) {
                  $color = 'success';
              } elseif (str_contains(mb_strtolower($ts, 'UTF-8'), 'chờ xác minh')) {
                  $color = 'warning';
              } elseif (str_contains(mb_strtolower($ts, 'UTF-8'), 'ngừng')) {
                  $color = 'danger';
              }
              ?>
              <span class="badge text-bg-<?= $color ?>"><?= e($ts ?: 'Chưa rõ') ?></span>
            </td>
            <td class="text-center">
              <div class="d-flex justify-content-center gap-1">
                <a href="../chi-tiet.php?mst=<?= urlencode($dn['mst']) ?>" target="_blank" class="btn btn-xs btn-outline-info btn-sm py-1 px-2" title="Xem trên web">
                  <i class="bi bi-eye"></i>
                </a>
                <a href="doanh-nghiep-edit.php?mst=<?= urlencode($dn['mst']) ?>" class="btn btn-xs btn-outline-primary btn-sm py-1 px-2" title="Chỉnh sửa">
                  <i class="bi bi-pencil"></i>
                </a>
                <form method="POST" action="doanh-nghiep.php" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa doanh nghiệp này? Hành động này không thể hoàn tác!')">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="mst" value="<?= e($dn['mst']) ?>">
                  <button type="submit" class="btn btn-xs btn-outline-danger btn-sm py-1 px-2" title="Xóa">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Phân trang -->
  <?php if ($totalPages > 1): ?>
  <nav class="d-flex justify-content-center">
    <ul class="pagination pagination-sm">
      <?php if ($page > 1): ?>
      <li class="page-item"><a class="page-link" href="<?= e($qs(['page' => $page - 1])) ?>">‹ Trước</a></li>
      <?php endif; ?>

      <?php
      $start = max(1, $page - 3);
      $end   = min($totalPages, $page + 3);
      for ($p = $start; $p <= $end; $p++):
      ?>
      <li class="page-item <?= $p == $page ? 'active' : '' ?>">
        <a class="page-link" href="<?= e($qs(['page' => $p])) ?>"><?= $p ?></a>
      </li>
      <?php endfor; ?>

      <?php if ($page < $totalPages): ?>
      <li class="page-item"><a class="page-link" href="<?= e($qs(['page' => $page + 1])) ?>">Sau ›</a></li>
      <?php endif; ?>
    </ul>
  </nav>
  <?php endif; ?>
<?php endif; ?>

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
            tsPhuong.clearOptions();
            tsPhuong.addOption({value: '0', text: 'Tất cả'});

            const filtered = allPhuong.filter(p => parseInt(p.tinh_thanh_id) === tinhId);
            filtered.forEach(p => {
              tsPhuong.addOption({value: p.id, text: p.ten});
            });

            tsPhuong.setValue('0');
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

<?php
require_once __DIR__ . '/admin_layout_footer.php';
?>
