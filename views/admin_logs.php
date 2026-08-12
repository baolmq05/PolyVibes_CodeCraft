<?php
$adminTitle = 'Quản lý Crawl Logs';
require_once __DIR__ . '/admin_layout_header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-b">
  <h2 class="h3 mb-0">Lịch sử Crawl Logs</h2>
</div>

<!-- Bộ lọc -->
<form method="GET" class="card card-body mb-4 shadow-sm border-0">
  <div class="row g-3 align-items-end">
    <div class="col-md-5">
      <label class="form-label form-label-sm fw-semibold">Từ khóa URL</label>
      <input type="text" name="search" class="form-control form-control-sm" placeholder="Tìm kiếm URL trong log..." value="<?= e($filterSearch ?? '') ?>">
    </div>
    <div class="col-md-4">
      <label class="form-label form-label-sm fw-semibold">Kết quả cào</label>
      <select name="result" class="form-select form-select-sm">
        <option value="">-- Tất cả --</option>
        <option value="thanh_cong" <?= ($filterResult ?? '') === 'thanh_cong' ? 'selected' : '' ?>>Thành công</option>
        <option value="that_bai" <?= ($filterResult ?? '') === 'that_bai' ? 'selected' : '' ?>>Thất bại</option>
      </select>
    </div>
    <div class="col-md-3 d-flex gap-2">
      <button type="submit" class="btn btn-primary btn-sm w-100">Tìm kiếm</button>
      <a href="logs.php" class="btn btn-outline-secondary btn-sm w-100">Xóa bộ lọc</a>
    </div>
  </div>
</form>

<!-- Kết quả -->
<div class="mb-3 d-flex justify-content-between align-items-center">
  <small class="text-muted">Tìm thấy <strong><?= number_format($total) ?></strong> bản ghi log</small>
</div>

<?php if (empty($rows)): ?>
  <div class="alert alert-info">Không có bản ghi log nào phù hợp.</div>
<?php else: ?>
  <div class="card border-0 shadow-sm overflow-hidden mb-4">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th style="width: 50px">#</th>
            <th>URL đã cào</th>
            <th style="width: 130px">Kết quả</th>
            <th>Chi tiết / Ghi chú</th>
            <th style="width: 180px">Ngày cào</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $i => $row): ?>
          <tr>
            <td class="text-muted"><?= $offset + $i + 1 ?></td>
            <td class="text-break">
              <a href="<?= e($row['url']) ?>" target="_blank" rel="noopener noreferrer" class="font-mono text-xs">
                <?= e($row['url']) ?>
              </a>
            </td>
            <td>
              <?php if ($row['ket_qua'] === 'thanh_cong'): ?>
              <span class="badge text-bg-success">Thành công</span>
              <?php else: ?>
              <span class="badge text-bg-danger">Thất bại</span>
              <?php endif; ?>
            </td>
            <td class="text-secondary small text-break"><?= e($row['ghi_chu'] ?? '') ?></td>
            <td class="text-muted small"><?= e(date('d/m/Y H:i:s', strtotime($row['ngay_tao']))) ?></td>
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

<?php
require_once __DIR__ . '/admin_layout_footer.php';
?>
