<?php
$adminTitle = $editing ? 'Sửa Doanh nghiệp' : 'Thêm Doanh nghiệp';
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
  <h2 class="h3 mb-0"><?= $editing ? 'Chỉnh sửa doanh nghiệp' : 'Thêm doanh nghiệp mới' ?></h2>
  <a href="doanh-nghiep.php" class="btn btn-outline-secondary btn-sm">
    <i class="bi bi-arrow-left me-1"></i> Quay lại
  </a>
</div>

<?= $msg ?? '' ?>

<div class="card border-0 shadow-sm">
  <div class="card-body bg-white p-4">
    <form method="POST" action="">
      <input type="hidden" name="action" value="save">
      <input type="hidden" name="is_edit" value="<?= $editing ? '1' : '0' ?>">

      <div class="row g-3">
        <!-- Mã số thuế -->
        <div class="col-md-4">
          <label class="form-label fw-semibold small text-muted">Mã số thuế <span class="text-danger">*</span></label>
          <input type="text" name="mst" class="form-control form-control-sm font-mono fw-bold" 
                 placeholder="Ví dụ: 0300607067" required
                 value="<?= e($editing['mst'] ?? '') ?>" <?= $editing ? 'readonly' : '' ?>>
          <?php if ($editing): ?>
            <div class="form-text text-warning small">Không thể thay đổi Mã số thuế khi đang chỉnh sửa.</div>
          <?php endif; ?>
        </div>

        <!-- Tên công ty -->
        <div class="col-md-8">
          <label class="form-label fw-semibold small text-muted">Tên công ty <span class="text-danger">*</span></label>
          <input type="text" name="ten_cong_ty" class="form-control form-control-sm" 
                 placeholder="Tên công ty đầy đủ tiếng Việt" required
                 value="<?= e($editing['ten_cong_ty'] ?? '') ?>">
        </div>

        <!-- Tên quốc tế -->
        <div class="col-md-6">
          <label class="form-label fw-semibold small text-muted">Tên quốc tế</label>
          <input type="text" name="ten_quoc_te" class="form-control form-control-sm" 
                 placeholder="Tên giao dịch quốc tế"
                 value="<?= e($editing['ten_quoc_te'] ?? '') ?>">
        </div>

        <!-- Tên viết tắt -->
        <div class="col-md-6">
          <label class="form-label fw-semibold small text-muted">Tên viết tắt</label>
          <input type="text" name="ten_viet_tat" class="form-control form-control-sm" 
                 placeholder="Tên viết tắt / thương hiệu"
                 value="<?= e($editing['ten_viet_tat'] ?? '') ?>">
        </div>

        <!-- Người đại diện -->
        <div class="col-md-4">
          <label class="form-label fw-semibold small text-muted">Người đại diện pháp luật</label>
          <input type="text" name="nguoi_dai_dien" class="form-control form-control-sm" 
                 placeholder="Ví dụ: Nguyễn Văn A"
                 value="<?= e($editing['nguoi_dai_dien'] ?? '') ?>">
        </div>

        <!-- Điện thoại -->
        <div class="col-md-4">
          <label class="form-label fw-semibold small text-muted">Điện thoại</label>
          <input type="text" name="dien_thoai" class="form-control form-control-sm" 
                 placeholder="Số điện thoại liên hệ"
                 value="<?= e($editing['dien_thoai'] ?? '') ?>">
        </div>

        <!-- Tình trạng hoạt động -->
        <div class="col-md-4">
          <label class="form-label fw-semibold small text-muted">Tình trạng hoạt động</label>
          <input type="text" name="tinh_trang" class="form-control form-control-sm" 
                 placeholder="Ví dụ: Đang hoạt động, Ngừng hoạt động..."
                 value="<?= e($editing['tinh_trang'] ?? 'Đang hoạt động') ?>">
        </div>

        <!-- Tỉnh thành -->
        <div class="col-md-3">
          <label class="form-label fw-semibold small text-muted">Tỉnh / Thành phố</label>
          <select name="tinh_thanh_id" class="form-select form-select-sm">
            <option value="0">Tất cả</option>
            <?php foreach ($tinhList as $t): ?>
              <option value="<?= $t['id'] ?>" <?= (isset($editing['tinh_thanh_id']) && (int)$editing['tinh_thanh_id'] == (int)$t['id']) ? 'selected' : '' ?>>
                <?= e($t['ten']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Phường xã -->
        <div class="col-md-3">
          <label class="form-label fw-semibold small text-muted">Phường / Xã / Thị trấn</label>
          <select name="phuong_xa_id" class="form-select form-select-sm" 
                  <?= empty($editing['tinh_thanh_id']) ? 'disabled' : '' ?>>
            <option value="0">Tất cả</option>
            <?php 
            // Nếu đang sửa, tải các phường xã tương ứng
            if (!empty($editing['tinh_thanh_id'])) {
              $currentWards = (new PhuongXaModel())->getByTinhThanhId((int)$editing['tinh_thanh_id']);
              foreach ($currentWards as $p): ?>
                <option value="<?= $p['id'] ?>" <?= ((int)$editing['phuong_xa_id'] == (int)$p['id']) ? 'selected' : '' ?>>
                  <?= e($p['ten']) ?>
                </option>
              <?php endforeach;
            }
            ?>
          </select>
        </div>

        <!-- Loại hình doanh nghiệp -->
        <div class="col-md-3">
          <label class="form-label fw-semibold small text-muted">Loại hình doanh nghiệp</label>
          <select name="loai_hinh_id" class="form-select form-select-sm">
            <option value="0">Tất cả</option>
            <?php foreach ($loaiList as $l): ?>
              <option value="<?= $l['id'] ?>" <?= (isset($editing['loai_hinh_id']) && (int)$editing['loai_hinh_id'] == (int)$l['id']) ? 'selected' : '' ?>>
                <?= e($l['ten']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Ngành nghề chính -->
        <div class="col-md-3">
          <label class="form-label fw-semibold small text-muted">Ngành nghề chính</label>
          <select name="nganh_nghe_id" class="form-select form-select-sm">
            <option value="0">Tất cả</option>
            <?php foreach ($nganhList as $n): ?>
              <option value="<?= $n['id'] ?>" <?= (isset($editing['nganh_nghe_id']) && (int)$editing['nganh_nghe_id'] == (int)$n['id']) ? 'selected' : '' ?>>
                <?= e($n['ten']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Địa chỉ hoạt động -->
        <div class="col-md-12">
          <label class="form-label fw-semibold small text-muted">Địa chỉ hoạt động</label>
          <input type="text" name="dia_chi" class="form-control form-control-sm" 
                 placeholder="Địa chỉ giao dịch cụ thể"
                 value="<?= e($editing['dia_chi'] ?? '') ?>">
        </div>

        <!-- Địa chỉ đăng ký thuế -->
        <div class="col-md-12">
          <label class="form-label fw-semibold small text-muted">Địa chỉ nhận thông báo thuế</label>
          <input type="text" name="dia_chi_thue" class="form-control form-control-sm" 
                 placeholder="Địa chỉ đăng ký thuế (nếu khác địa chỉ hoạt động)"
                 value="<?= e($editing['dia_chi_thue'] ?? '') ?>">
        </div>

        <!-- Ngày bắt đầu hoạt động -->
        <div class="col-md-4">
          <label class="form-label fw-semibold small text-muted">Ngày hoạt động</label>
          <input type="date" name="ngay_hoat_dong" class="form-control form-control-sm" 
                 value="<?= e($editing['ngay_hoat_dong'] ?? '') ?>">
        </div>

        <!-- Chi cục quản lý thuế -->
        <div class="col-md-4">
          <label class="form-label fw-semibold small text-muted">Quản lý bởi chi cục thuế</label>
          <input type="text" name="quan_ly_boi" class="form-control form-control-sm" 
                 placeholder="Ví dụ: Chi cục Thuế Quận Ô Môn"
                 value="<?= e($editing['quan_ly_boi'] ?? '') ?>">
        </div>

        <!-- URL Nguồn -->
        <div class="col-md-4">
          <label class="form-label fw-semibold small text-muted">URL Nguồn dữ liệu</label>
          <input type="url" name="url_nguon" class="form-control form-control-sm" 
                 placeholder="https://masothue.com/..."
                 value="<?= e($editing['url_nguon'] ?? '') ?>">
        </div>
      </div>

      <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
        <a href="doanh-nghiep.php" class="btn btn-sm btn-outline-secondary px-4">Hủy bỏ</a>
        <button type="submit" class="btn btn-sm btn-primary px-4">Lưu lại</button>
      </div>
    </form>
  </div>
</div>

<!-- Lọc phường xã client-side + Tom Select -->
<script>
  const allPhuong = <?= json_encode($allPhuongList) ?>;

  document.addEventListener("DOMContentLoaded", function() {
    const tinhEl = document.querySelector('select[name="tinh_thanh_id"]');
    const phuongEl = document.querySelector('select[name="phuong_xa_id"]');
    const loaiEl = document.querySelector('select[name="loai_hinh_id"]');
    const nganhEl = document.querySelector('select[name="nganh_nghe_id"]');

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
