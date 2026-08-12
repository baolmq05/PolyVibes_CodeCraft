<?php
$adminTitle = 'Quản lý ngành nghề';
require_once __DIR__ . '/admin_layout_header.php';
$uploadUrl  = '../uploads/nganh-nghe/';
?>

<div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
  <h2 class="h3 mb-0">Quản lý Ngành nghề</h2>
  <a href="danh-muc-edit.php" class="btn btn-success btn-sm">
    <i class="bi bi-plus-lg me-1"></i> Thêm ngành nghề
  </a>
</div>

<?= $msg ?? '' ?>

<div class="card border-0 shadow-sm overflow-hidden mb-4">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th style="width: 60px">#</th>
          <th style="width: 80px">Ảnh minh họa</th>
          <th>Tên ngành nghề</th>
          <th>Slug</th>
          <th>Mô tả</th>
          <th style="width: 100px" class="text-center">Số DN</th>
          <th style="width: 120px" class="text-center">Thao tác</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($nganhs as $i => $nn): ?>
        <tr>
          <td class="text-muted"><?= $i + 1 ?></td>
          <td>
            <?php if ($nn['hinh_anh']): ?>
              <img src="<?= $uploadUrl . e($nn['hinh_anh']) ?>"
                   style="width:48px;height:48px;object-fit:cover;border-radius:4px"
                   alt="<?= e($nn['ten']) ?>">
            <?php else: ?>
              <div style="width:48px;height:48px;background:#dee2e6;border-radius:4px;
                          display:flex;align-items:center;justify-content:center;
                          font-size:20px;">📷</div>
            <?php endif; ?>
          </td>
          <td class="fw-semibold text-dark"><?= e($nn['ten']) ?></td>
          <td><code class="font-mono text-muted"><?= e($nn['slug']) ?></code></td>
          <td class="text-muted small">
            <div style="max-width: 300px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;" title="<?= e($nn['mo_ta'] ?? '') ?>">
              <?= e($nn['mo_ta'] ?: 'Không có mô tả') ?>
            </div>
          </td>
          <td class="text-center">
            <span class="badge text-bg-info px-2.5 py-1 fw-semibold"><?= (int)$nn['so_dn'] ?></span>
          </td>
          <td class="text-center">
            <div class="d-flex justify-content-center gap-1">
              <a href="danh-muc-edit.php?id=<?= $nn['id'] ?>" class="btn btn-xs btn-outline-primary btn-sm py-1 px-2" title="Chỉnh sửa">
                <i class="bi bi-pencil"></i>
              </a>
              <form method="POST" action="danh-muc.php" class="d-inline" onsubmit="return confirm('Xác nhận xóa ngành «<?= e($nn['ten']) ?>»?\nTất cả doanh nghiệp liên kết sẽ được gỡ bỏ ngành nghề này.')">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= $nn['id'] ?>">
                <button type="submit" class="btn btn-xs btn-outline-danger btn-sm py-1 px-2" title="Xóa">
                  <i class="bi bi-trash"></i>
                </button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($nganhs)): ?>
        <tr>
          <td colspan="7" class="text-center text-muted py-4">Chưa có ngành nghề nào được tạo.</td>
        </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php
require_once __DIR__ . '/admin_layout_footer.php';
?>
