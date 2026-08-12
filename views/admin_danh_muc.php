<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin – Quản lý ngành nghề | ThongTinDN</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container py-4" style="max-width:1000px">

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">📂 Quản lý ngành nghề</h4>
    <div>
      <a href="crawl.php" class="btn btn-sm btn-outline-primary">Crawl dữ liệu</a>
      <a href="../index.php" class="btn btn-sm btn-outline-secondary ms-1">← Trang chủ</a>
    </div>
  </div>

  <?= $msg ?>

  <div class="row g-4">

    <!-- Cột trái: danh sách -->
    <div class="col-md-7">
      <div class="card shadow-sm">
        <div class="card-header fw-bold">
          Danh sách ngành nghề
          <span class="badge text-bg-secondary ms-2"><?= count($nganhs) ?></span>
        </div>
        <div class="table-responsive">
          <table class="table table-sm table-hover mb-0">
            <thead class="table-light">
              <tr>
                <th>Ảnh</th>
                <th>Tên ngành</th>
                <th class="text-center">DN</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($nganhs as $nn): ?>
              <tr>
                <td style="width:56px">
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
                <td>
                  <div class="fw-semibold"><?= e($nn['ten']) ?></div>
                  <small class="text-muted"><?= e($nn['slug']) ?></small>
                </td>
                <td class="text-center align-middle">
                  <span class="badge text-bg-info"><?= (int)$nn['so_dn'] ?></span>
                </td>
                <td class="align-middle text-end pe-2">
                  <a href="?edit=<?= $nn['id'] ?>" class="btn btn-xs btn-outline-primary btn-sm">
                    Sửa
                  </a>
                  <form method="POST" class="d-inline"
                        onsubmit="return confirm('Xoá ngành «<?= e($nn['ten']) ?>»?\nCác DN sẽ được gỡ liên kết.')">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= $nn['id'] ?>">
                    <button type="submit" class="btn btn-xs btn-outline-danger btn-sm">Xoá</button>
                  </form>
                </td>
              </tr>
              <?php endforeach; ?>
              <?php if (empty($nganhs)): ?>
              <tr><td colspan="4" class="text-center text-muted py-3">Chưa có ngành nghề nào.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Cột phải: form thêm/sửa -->
    <div class="col-md-5">
      <div class="card shadow-sm">
        <div class="card-header fw-bold">
          <?= $editing ? 'Cập nhật ngành nghề' : 'Thêm ngành nghề mới' ?>
        </div>
        <div class="card-body">
          <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="save">
            <?php if ($editing): ?>
            <input type="hidden" name="id" value="<?= $editing['id'] ?>">
            <?php endif; ?>

            <div class="mb-3">
              <label class="form-label">Tên ngành <span class="text-danger">*</span></label>
              <input type="text" name="ten" class="form-control" required
                     value="<?= $editing ? e($editing['ten']) : '' ?>"
                     oninput="autoSlug(this)">
            </div>

            <div class="mb-3">
              <label class="form-label">Slug</label>
              <input type="text" name="slug" id="slug" class="form-control"
                     pattern="[a-z0-9-]+" title="Chỉ gồm a-z, 0-9, dấu gạch ngang"
                     value="<?= $editing ? e($editing['slug']) : '' ?>">
              <div class="form-text">Tự sinh từ tên, có thể sửa thủ công.</div>
            </div>

            <div class="mb-3">
              <label class="form-label">Mô tả</label>
              <textarea name="mo_ta" class="form-control" rows="3"><?= $editing ? e($editing['mo_ta'] ?? '') : '' ?></textarea>
            </div>

            <div class="mb-3">
              <label class="form-label">
                Ảnh danh mục
                <?= $editing && $editing['hinh_anh'] ? '<span class="text-muted">(để trống = giữ ảnh cũ)</span>' : '' ?>
              </label>
              <?php if ($editing && $editing['hinh_anh']): ?>
              <div class="mb-2">
                <img src="<?= $uploadUrl . e($editing['hinh_anh']) ?>"
                     style="height:80px;object-fit:cover;border-radius:4px">
              </div>
              <?php endif; ?>
              <input type="file" name="hinh_anh" class="form-control"
                     accept="image/jpeg,image/png,image/webp,image/gif">
              <div class="form-text">jpg, png, webp, gif — tối đa 2 MB</div>
            </div>

            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary">
                <?= $editing ? '💾 Cập nhật' : '➕ Thêm mới' ?>
              </button>
              <?php if ($editing): ?>
              <a href="danh-muc.php" class="btn btn-outline-secondary">Huỷ</a>
              <?php endif; ?>
            </div>
          </form>
        </div>
      </div>
    </div>

  </div><!-- /row -->
</div>

<script>
function autoSlug(input) {
  // Chỉ sinh slug nếu người dùng chưa tự nhập vào slug field
  const slugField = document.getElementById('slug');
  if (slugField._manualEdit) return;
  slugField.value = input.value
    .toLowerCase()
    .replace(/[àáạảãâầấậẩẫăằắặẳẵ]/g, 'a')
    .replace(/[èéẹẻẽêềếệểễ]/g, 'e')
    .replace(/[ìíịỉĩ]/g, 'i')
    .replace(/[òóọỏõôồốộổỗơờớợởỡ]/g, 'o')
    .replace(/[ùúụủũưừứựửữ]/g, 'u')
    .replace(/[ỳýỵỷỹ]/g, 'y')
    .replace(/đ/g, 'd')
    .replace(/[^a-z0-9\s-]/g, '')
    .trim().replace(/[\s-]+/g, '-');
}
document.getElementById('slug').addEventListener('input', function() {
  this._manualEdit = this.value !== '';
});
</script>
</body>
</html>
