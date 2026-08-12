<?php
$adminTitle = $editing ? 'Sửa Ngành nghề' : 'Thêm Ngành nghề';
require_once __DIR__ . '/admin_layout_header.php';
$uploadUrl  = '../uploads/nganh-nghe/';
?>

<div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
  <h2 class="h3 mb-0"><?= $editing ? 'Chỉnh sửa ngành nghề' : 'Thêm ngành nghề mới' ?></h2>
  <a href="danh-muc.php" class="btn btn-outline-secondary btn-sm">
    <i class="bi bi-arrow-left me-1"></i> Quay lại
  </a>
</div>

<?= $msg ?? '' ?>

<div class="card border-0 shadow-sm">
  <div class="card-body bg-white p-4">
    <form method="POST" action="" enctype="multipart/form-data">
      <input type="hidden" name="action" value="save">
      <input type="hidden" name="is_edit" value="<?= $editing ? '1' : '0' ?>">

      <div class="row g-3">
        <!-- Tên ngành -->
        <div class="col-md-6">
          <label class="form-label fw-semibold small text-muted">Tên ngành nghề <span class="text-danger">*</span></label>
          <input type="text" name="ten" class="form-control form-control-sm" required
                 placeholder="Ví dụ: Công nghệ thông tin"
                 value="<?= $editing ? e($editing['ten']) : '' ?>"
                 oninput="autoSlug(this)">
        </div>

        <!-- Slug -->
        <div class="col-md-6">
          <label class="form-label fw-semibold small text-muted">Slug</label>
          <input type="text" name="slug" id="slug" class="form-control form-control-sm font-mono text-muted"
                 pattern="[a-z0-9-]+" title="Chỉ gồm a-z, 0-9, dấu gạch ngang"
                 placeholder="cong-nghe-thong-tin"
                 value="<?= $editing ? e($editing['slug']) : '' ?>">
          <div class="form-text small text-muted">Tự sinh từ tên khi gõ, có thể chỉnh sửa thủ công.</div>
        </div>

        <!-- Mô tả -->
        <div class="col-md-12">
          <label class="form-label fw-semibold small text-muted">Mô tả ngành nghề</label>
          <textarea name="mo_ta" class="form-control form-control-sm" rows="4" 
                    placeholder="Mô tả tóm tắt về ngành nghề kinh doanh này..."><?= $editing ? e($editing['mo_ta'] ?? '') : '' ?></textarea>
        </div>

        <!-- Hình ảnh -->
        <div class="col-md-12">
          <label class="form-label fw-semibold small text-muted">
            Ảnh đại diện minh họa 
            <?= $editing && $editing['hinh_anh'] ? '<span class="text-warning">(để trống nếu muốn giữ nguyên ảnh cũ)</span>' : '' ?>
          </label>
          
          <?php if ($editing && $editing['hinh_anh']): ?>
            <div class="mb-3">
              <img src="<?= $uploadUrl . e($editing['hinh_anh']) ?>"
                   style="height:100px; object-fit:cover; border-radius:4px"
                   alt="Ảnh cũ">
            </div>
          <?php endif; ?>

          <input type="file" name="hinh_anh" class="form-control form-control-sm"
                 accept="image/jpeg,image/png,image/webp,image/gif">
          <div class="form-text small text-muted">Hỗ trợ các định dạng: jpg, png, webp, gif — Tải lên tối đa 2 MB.</div>
        </div>
      </div>

      <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
        <a href="danh-muc.php" class="btn btn-sm btn-outline-secondary px-4">Hủy bỏ</a>
        <button type="submit" class="btn btn-sm btn-primary px-4">Lưu lại</button>
      </div>
    </form>
  </div>
</div>

<script>
function autoSlug(input) {
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

<?php
require_once __DIR__ . '/admin_layout_footer.php';
?>
